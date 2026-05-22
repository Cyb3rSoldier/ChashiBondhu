
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: consumerLogin.php');
    exit();
}

$consumerId = $_SESSION['consumer_id'];

// Get tool cart items
$stmt = $conn->prepare("
    SELECT tc.id, tc.quantity, tc.tool_id,
           tp.product_name, tp.price, tp.unit, tp.image_path, tp.vendor_id,
           v.business_name AS vendor_name, v.full_name
    FROM tool_cart tc
    JOIN tool_products tp ON tc.tool_id = tp.id
    LEFT JOIN vendors v ON tp.vendor_id = v.id
    WHERE tc.consumer_id = ?
");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit();
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = 100; // Higher delivery fee for tools
$total = $subtotal + $deliveryFee;

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $notes = trim($_POST['notes'] ?? '');

    if (empty($address) || empty($phone)) {
        $error = 'Please fill in all required fields.';
    } elseif (!preg_match('/^01[0-9]{9}$/', $phone)) {
        $error = 'Please enter a valid Bangladeshi phone number (01XXXXXXXXX).';
    } else {
        $conn->begin_transaction();
        try {
            $orderNumber = 'T' . date('Ymd') . rand(1000, 9999);

            // Insert tool order
            $stmt = $conn->prepare("
                INSERT INTO tool_orders (
                    order_number, consumer_id, total_amount, 
                    delivery_fee, final_amount, payment_method, 
                    delivery_address, phone, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sidddssss",
                $orderNumber, $consumerId, $subtotal,
                $deliveryFee, $total, $paymentMethod,
                $address, $phone, $notes
            );
            $stmt->execute();
            $orderId = $conn->insert_id;
            $stmt->close();

            // Insert order items and update stock
            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];

                // Insert tool order item
                $stmt = $conn->prepare("
                    INSERT INTO tool_order_items (
                        order_id, tool_id, product_name, 
                        vendor_id, quantity, unit_price, total_price
                    ) VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "iisiddd",
                    $orderId, $item['tool_id'], $item['product_name'],
                    $item['vendor_id'], $item['quantity'],
                    $item['price'], $itemTotal
                );
                $stmt->execute();
                $stmt->close();

                // Reduce stock
                $updateStock = $conn->prepare(
                    "UPDATE tool_products SET quantity = quantity - ? WHERE id = ? AND quantity >= ?"
                );
                $updateStock->bind_param("iii", $item['quantity'], $item['tool_id'], $item['quantity']);
                $updateStock->execute();
                
                if ($updateStock->affected_rows === 0) {
                    throw new Exception("Insufficient stock for tool: " . $item['product_name']);
                }
                $updateStock->close();
            }

            // Clear tool cart
            $stmt = $conn->prepare("DELETE FROM tool_cart WHERE consumer_id = ?");
            $stmt->bind_param("i", $consumerId);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            $_SESSION['tool_order_success'] = $orderNumber;
            header('Location: tool-order-success.php');
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            error_log("Tool checkout error: " . $e->getMessage());
            $error = 'Order failed. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Checkout — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <a href="tool-cart.php" class="inline-flex items-center gap-2 text-amber-700 text-sm font-semibold hover:text-amber-900 transition mb-6">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Cart
        </a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">Tool Checkout</h1>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-5">
            <h3 class="font-bold text-green-950 mb-4 text-sm uppercase tracking-wider">Order Summary</h3>
            <div class="space-y-2 mb-4">
                <?php foreach ($cartItems as $item): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-600">
                            <?php echo htmlspecialchars($item['product_name']); ?> × <?php echo intval($item['quantity']); ?>
                        </span>
                        <span class="font-semibold">৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="border-t border-stone-100 pt-3 space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-stone-500">Subtotal</span>
                    <span>৳<?php echo number_format($subtotal, 0); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-stone-500">Delivery Fee (Tools)</span>
                    <span>৳<?php echo number_format($deliveryFee, 0); ?></span>
                </div>
                <div class="flex justify-between font-bold text-base pt-1">
                    <span class="text-green-950">Total</span>
                    <span class="text-amber-700">৳<?php echo number_format($total, 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Delivery Form -->
        <form method="POST" class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 space-y-4">
            <h3 class="font-bold text-green-950 text-sm uppercase tracking-wider">Delivery Information</h3>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">
                    Delivery Address <span class="text-red-400">*</span>
                </label>
                <textarea name="address" rows="3" required placeholder="Full address with district..."
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-amber-500 transition resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">
                    Phone Number <span class="text-red-400">*</span>
                </label>
                <input type="tel" name="phone" required placeholder="01XXXXXXXXX" pattern="01[0-9]{9}"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-amber-500 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Payment Method</label>
                <select name="payment_method" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-amber-500 transition">
                    <option value="cod">💵 Cash on Delivery</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Notes <span class="text-stone-400">(optional)</span></label>
                <input type="text" name="notes" placeholder="Any special instructions..."
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-amber-500 transition">
            </div>

            <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white font-bold py-4 rounded-2xl transition text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check"></i> Place Tool Order — ৳<?php echo number_format($total, 0); ?>
            </button>
        </form>
    </main>

    <?php include('footer.php'); ?>
</body>
</html>