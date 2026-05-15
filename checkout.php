<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: consumerLogin.php');
    exit();
}

$consumerId = $_SESSION['consumer_id'];

// Get cart items
$stmt = $conn->prepare("
    SELECT c.id, c.quantity, c.product_id,
           p.product_name, p.price, p.unit, p.image_path, p.farmer_id,
           f.full_name AS farmer_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    LEFT JOIN farmers f ON p.farmer_id = f.id
    WHERE c.consumer_id = ?
");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($cartItems) === 0) {
    header('Location: cart.php');
    exit();
}

$subtotal    = 0;
foreach ($cartItems as $item) $subtotal += $item['price'] * $item['quantity'];
$deliveryFee = 50;
$total       = $subtotal + $deliveryFee;

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address       = trim($_POST['address'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';
    $notes         = trim($_POST['notes'] ?? '');

    if (empty($address) || empty($phone)) {
        $error = 'Please fill in all required fields.';
    } else {
        $conn->begin_transaction();
        try {
            $orderNumber = 'CB' . date('Ymd') . rand(1000, 9999);

            $stmt = $conn->prepare("
                INSERT INTO orders (order_number, consumer_id, total_amount, delivery_fee, final_amount, payment_method, delivery_address, phone, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sidddssss", $orderNumber, $consumerId, $subtotal, $deliveryFee, $total, $paymentMethod, $address, $phone, $notes);
            $stmt->execute();
            $orderId = $conn->insert_id;
            $stmt->close();

            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];

                $stmt = $conn->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, farmer_id, quantity, unit_price, total_price)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iisiidd", $orderId, $item['product_id'], $item['product_name'], $item['farmer_id'], $item['quantity'], $item['price'], $itemTotal);
                $stmt->execute();
                $stmt->close();

                // Reduce stock
                $conn->query("UPDATE products SET quantity = quantity - {$item['quantity']} WHERE id = {$item['product_id']}");
            }

            // Clear cart
            $stmt = $conn->prepare("DELETE FROM cart WHERE consumer_id = ?");
            $stmt->bind_param("i", $consumerId);
            $stmt->execute();
            $stmt->close();

            $conn->commit();

            $_SESSION['order_success'] = $orderNumber;
            header('Location: orderSuccess.php');
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Checkout error: " . $e->getMessage());
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
    <title>Consumer Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
</style>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <a href="cart.php" class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-6">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Cart
        </a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">Checkout</h1>

        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Order Summary -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-5">
            <h3 class="font-bold text-green-950 mb-4 text-sm uppercase tracking-wider">Order Summary</h3>
            <div class="space-y-2 mb-4">
                <?php foreach ($cartItems as $item): ?>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-600 truncate max-w-xs">
                            <?php echo htmlspecialchars($item['product_name']); ?> × <?php echo $item['quantity']; ?>
                        </span>
                        <span class="font-semibold shrink-0 ml-4">৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="border-t border-stone-100 pt-3 space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-stone-500">Subtotal</span><span>৳<?php echo number_format($subtotal, 0); ?></span></div>
                <div class="flex justify-between"><span class="text-stone-500">Delivery</span><span>৳<?php echo number_format($deliveryFee, 0); ?></span></div>
                <div class="flex justify-between font-bold text-base pt-1 border-t border-stone-100">
                    <span class="text-green-950">Total</span>
                    <span class="text-green-700">৳<?php echo number_format($total, 0); ?></span>
                </div>
            </div>
        </div>

        <!-- Delivery Form -->
        <form method="POST" class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 space-y-4">

            <h3 class="font-bold text-green-950 text-sm uppercase tracking-wider">Delivery Information</h3>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Delivery Address <span class="text-red-400">*</span></label>
                <textarea name="address" rows="3" required placeholder="Full address with district..."
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition resize-none"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Phone Number <span class="text-red-400">*</span></label>
                <input type="tel" name="phone" required placeholder="01XXXXXXXXX"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Payment Method</label>
                <div class="relative">
                    <select name="payment_method" class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-green-500 transition">
                        <option value="cod">💵 Cash on Delivery</option>
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Notes <span class="text-stone-400 normal-case font-normal">(optional)</span></label>
                <input type="text" name="notes" placeholder="Any special instructions..."
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
            </div>

            <button type="submit"
                class="w-full bg-green-700 hover:bg-green-600 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-md text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-check"></i> Place Order — ৳<?php echo number_format($total, 0); ?>
            </button>

        </form>
    </main>

</body>

</html>