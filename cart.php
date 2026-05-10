<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: consumerLogin.php');
    exit();
}

$consumerId = $_SESSION['consumer_id'];

// Get cart items with product info
$stmt = $conn->prepare("
    SELECT c.id, c.quantity, c.product_id,
           p.product_name, p.price, p.unit, p.image_path, p.quantity AS stock,
           f.full_name AS farmer_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    LEFT JOIN farmers f ON p.farmer_id = f.id
    WHERE c.consumer_id = ?
    ORDER BY c.added_at DESC
");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$subtotal    = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = 50;
$total       = $subtotal + $deliveryFee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="index.php#marketplace" class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Continue Shopping
                </a>
                <h1 class="text-2xl font-bold text-green-950">My Cart</h1>
            </div>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full">
                <?php echo count($cartItems); ?> item<?php echo count($cartItems) !== 1 ? 's' : ''; ?>
            </span>
        </div>

        <?php if (isset($_SESSION['cart_message'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 text-sm font-medium flex items-center gap-2">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <?php echo $_SESSION['cart_message']; unset($_SESSION['cart_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($cartItems) > 0): ?>

            <!-- Cart Items -->
            <div class="space-y-3 mb-5" id="cartItems">
                <?php foreach ($cartItems as $item): ?>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex gap-4 items-start" id="cart-item-<?php echo $item['id']; ?>">

                    <img src="<?php echo $item['image_path'] ? htmlspecialchars($item['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                        class="w-20 h-20 rounded-xl object-cover shrink-0">

                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-green-950 text-sm truncate"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <p class="text-xs text-stone-400 mt-0.5">By <?php echo htmlspecialchars($item['farmer_name'] ?? 'Unknown'); ?></p>
                        <p class="text-green-700 font-bold text-base mt-1">
                            ৳<?php echo number_format($item['price'], 0); ?>
                            <span class="text-stone-400 font-normal text-xs">/ <?php echo $item['unit']; ?></span>
                        </p>

                        <div class="flex items-center justify-between mt-2">
                            <!-- Qty controls -->
                            <div class="flex items-center gap-0 bg-stone-100 rounded-xl overflow-hidden">
                                <button onclick="updateQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)"
                                    class="w-8 h-8 flex items-center justify-center text-green-800 font-bold hover:bg-stone-200 transition text-base">−</button>
                                <span class="w-8 text-center font-bold text-green-950 text-sm" id="qty-<?php echo $item['id']; ?>">
                                    <?php echo $item['quantity']; ?>
                                </span>
                                <button onclick="updateQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                    class="w-8 h-8 flex items-center justify-center text-green-800 font-bold hover:bg-stone-200 transition text-base">+</button>
                            </div>

                            <!-- Item total -->
                            <span class="font-bold text-green-700 text-sm" id="item-total-<?php echo $item['id']; ?>">
                                ৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                            </span>

                            <!-- Remove -->
                            <button onclick="removeItem(<?php echo $item['id']; ?>)"
                                class="text-xs bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-xl font-semibold transition">
                                <i class="fa-solid fa-trash text-xs mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-5">
                <h3 class="font-bold text-green-950 mb-4 text-sm uppercase tracking-wider">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold">৳<?php echo number_format($subtotal, 0); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Delivery Fee</span>
                        <span class="font-semibold">৳<?php echo number_format($deliveryFee, 0); ?></span>
                    </div>
                    <div class="border-t border-stone-100 pt-2 flex justify-between">
                        <span class="font-bold text-green-950">Total</span>
                        <span class="font-bold text-green-700 text-lg">৳<?php echo number_format($total, 0); ?></span>
                    </div>
                </div>
            </div>

            <a href="checkout.php"
                class="block w-full bg-green-700 hover:bg-green-600 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-md text-sm text-center">
                <i class="fa-solid fa-lock mr-2"></i> Proceed to Checkout — ৳<?php echo number_format($total, 0); ?>
            </a>

        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-cart-shopping text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">Your cart is empty</h3>
                <p class="text-stone-400 text-sm mb-6">Add some fresh produce from our marketplace.</p>
                <a href="index.php#marketplace"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-store"></i> Browse Products
                </a>
            </div>
        <?php endif; ?>

    </main>

    <script>
        function updateQty(cartId, newQty) {
            if (newQty < 0) return;
            fetch('updateCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId + '&quantity=' + newQty
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
            });
        }

        function removeItem(cartId) {
            if (!confirm('Remove this item from cart?')) return;
            fetch('removeFromCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
            });
        }
    </script>

</body>
</html>