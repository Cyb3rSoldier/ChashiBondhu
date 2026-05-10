
<?php
session_start();
require_once '../config/database.php';
require_once '../includes/marketplace-functions.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: ../consumerLogin.php');
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$consumerName = $_SESSION['consumer_name'];
$cartItems = getCartItems($consumerId);

// Redirect if cart is empty
if (count($cartItems) == 0) {
    header('Location: view.php');
    exit;
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = 50;
$total = $subtotal + $deliveryFee;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $notes = $_POST['notes'] ?? '';
    
    if (empty($address) || empty($phone)) {
        $error = "Please fill in all required fields";
    } else {
        try {
            $conn = Database::getConnection();
            $conn->begin_transaction();
            
            // Generate order number
            $orderNumber = 'CB' . date('Ymd') . rand(1000, 9999);
            
            // Insert order
            $stmt = $conn->prepare(
                "INSERT INTO orders (order_number, consumer_id, total_amount, delivery_fee, final_amount, payment_method, delivery_address, phone, notes) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("sidddssss", $orderNumber, $consumerId, $subtotal, $deliveryFee, $total, $paymentMethod, $address, $phone, $notes);
            $stmt->execute();
            $orderId = $conn->insert_id;
            
            // Insert order items and update product stock
            foreach ($cartItems as $item) {
                // Insert order item
                $stmt = $conn->prepare(
                    "INSERT INTO order_items (order_id, product_id, product_name, farmer_id, quantity, unit_price, total_price) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)"
                );
                $itemTotal = $item['price'] * $item['quantity'];
                $farmerId = 0; // We'll get this from product
                
                // Get farmer_id from product
                $pStmt = $conn->prepare("SELECT farmer_id FROM products WHERE id = ?");
                $pStmt->bind_param("i", $item['product_id']);
                $pStmt->execute();
                $pResult = $pStmt->get_result()->fetch_assoc();
                $farmerId = $pResult['farmer_id'];
                
                $stmt->bind_param("iisiid", $orderId, $item['product_id'], $item['product_name'], $farmerId, $item['quantity'], $item['price'], $itemTotal);
                $stmt->execute();
                
                // Update product stock
                $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?");
                $stmt->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
                $stmt->execute();
            }
            
            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE consumer_id = ?");
            $stmt->bind_param("i", $consumerId);
            $stmt->execute();
            
            $conn->commit();
            
            // Redirect to success
            $_SESSION['order_success'] = $orderNumber;
            header('Location: ../consumer/my-orders.php');
            exit;
            
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Order failed. Please try again.";
            error_log("Checkout error: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="stylesheet" href="../asset/css/marketplace.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-50 min-h-screen">

    <?php include('../navbar2.php'); ?>

    <div class="pt-24 pb-12 px-4" style="max-width:600px;margin:0 auto;">
        <a href="view.php" class="text-green-600 text-sm mb-4 inline-block">← Back to Cart</a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">📦 Checkout</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl p-5 mb-4 shadow-sm">
            <h3 class="font-bold text-green-950 mb-3">Order Summary (<?php echo count($cartItems); ?> items)</h3>
            <?php foreach ($cartItems as $item): ?>
                <div class="flex justify-between text-sm py-2 border-b border-stone-100">
                    <span><?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?></span>
                    <span class="font-semibold">৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                </div>
            <?php endforeach; ?>
            <div class="flex justify-between text-sm mt-2"><span>Subtotal</span><span>৳<?php echo number_format($subtotal, 0); ?></span></div>
            <div class="flex justify-between text-sm"><span>Delivery</span><span>৳<?php echo number_format($deliveryFee, 0); ?></span></div>
            <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t"><span>Total</span><span class="text-green-700">৳<?php echo number_format($total, 0); ?></span></div>
        </div>

        <!-- Delivery Form -->
        <form method="POST" class="bg-white rounded-2xl p-5 shadow-sm">
            <h3 class="font-bold text-green-950 mb-4">Delivery Information</h3>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-stone-500 mb-2">Delivery Address *</label>
                <textarea name="address" rows="3" required placeholder="Full address with district"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none resize-none"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-stone-500 mb-2">Phone Number *</label>
                <input type="tel" name="phone" required placeholder="01XXXXXXXXX" value="<?php echo $_SESSION['consumer_email'] ?? ''; ?>"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-stone-500 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
                    <option value="cod">Cash on Delivery</option>
                    <option value="bkash">bKash</option>
                    <option value="nagad">Nagad</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-stone-500 mb-2">Notes (optional)</label>
                <input type="text" name="notes" placeholder="Any special instructions..."
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
            </div>

            <button type="submit" class="btn-primary">
                ✅ Place Order - ৳<?php echo number_format($total, 0); ?>
            </button>
        </form>
    </div>
</body>
</html>