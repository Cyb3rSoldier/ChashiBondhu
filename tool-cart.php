
<?php
session_start();
require_once 'config.php';

// Prevent caching
header("Cache-Control: no-cache, must-revalidate, no-store, private");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Rest of your code...

if (!isset($_SESSION['consumer_id'])) {
    header('Location: consumerLogin.php');
    exit();
}

$consumerId = $_SESSION['consumer_id'];

// Get tool cart items
$stmt = $conn->prepare("
    SELECT tc.id, tc.quantity, tc.tool_id,
           tp.product_name, tp.price, tp.unit, tp.image_path,
           v.business_name AS vendor_name
    FROM tool_cart tc
    JOIN tool_products tp ON tc.tool_id = tp.id
    LEFT JOIN vendors v ON tp.vendor_id = v.id
    WHERE tc.consumer_id = ?
    ORDER BY tc.added_at DESC
");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = 100;
$total = $subtotal + $deliveryFee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Cart — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <div class="mb-6">
            <a href="index.php#tools-marketplace" class="inline-flex items-center gap-2 text-amber-700 text-sm font-semibold hover:text-amber-900 transition mb-2">
                <i class="fa-solid fa-arrow-left text-xs"></i> Continue Shopping Tools
            </a>
            <h1 class="text-2xl font-bold text-green-950">Tool Cart</h1>
        </div>

        <?php if (count($cartItems) > 0): ?>

            <div class="space-y-3 mb-5">
                <?php foreach ($cartItems as $item): ?>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 flex gap-4 items-start" id="cart-item-<?php echo $item['id']; ?>">
                    <img src="<?php echo !empty($item['image_path']) ? htmlspecialchars($item['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                        alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                        class="w-20 h-20 rounded-xl object-cover shrink-0">

                    <div class="flex-1">
                        <h3 class="font-bold text-green-950 text-sm"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <p class="text-xs text-stone-400">By <?php echo htmlspecialchars($item['vendor_name'] ?? 'Vendor'); ?></p>
                        <p class="text-amber-600 font-bold text-base mt-1">
                            ৳<?php echo number_format($item['price'], 0); ?>
                            <span class="text-stone-400 font-normal text-xs">/ <?php echo $item['unit']; ?></span>
                        </p>

                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center gap-0 bg-stone-100 rounded-xl overflow-hidden">
                                <button onclick="updateToolQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)"
                                    class="w-8 h-8 flex items-center justify-center text-amber-800 font-bold hover:bg-stone-200 transition">−</button>
                                <span class="w-8 text-center font-bold text-green-950 text-sm" id="qty-<?php echo $item['id']; ?>">
                                    <?php echo $item['quantity']; ?>
                                </span>
                                <button onclick="updateToolQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)"
                                    class="w-8 h-8 flex items-center justify-center text-amber-800 font-bold hover:bg-stone-200 transition">+</button>
                            </div>
                            <span class="font-bold text-amber-600 text-sm" id="item-total-<?php echo $item['id']; ?>">
                                ৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                            </span>
                            <button onclick="removeToolItem(<?php echo $item['id']; ?>)"
                                class="text-xs bg-red-50 border border-red-100 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-xl font-semibold transition">
                                <i class="fa-solid fa-trash text-xs mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 mb-5">
                <h3 class="font-bold text-green-950 mb-4 text-sm uppercase tracking-wider">Order Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Subtotal</span>
                        <span class="font-semibold">৳<?php echo number_format($subtotal, 0); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Delivery Fee (Tools)</span>
                        <span class="font-semibold">৳<?php echo number_format($deliveryFee, 0); ?></span>
                    </div>
                    <div class="border-t border-stone-100 pt-2 flex justify-between">
                        <span class="font-bold text-green-950">Total</span>
                        <span class="font-bold text-amber-600 text-lg">৳<?php echo number_format($total, 0); ?></span>
                    </div>
                </div>
            </div>

            <a href="tool-checkout.php"
                class="block w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-4 rounded-2xl transition text-sm text-center">
                <i class="fa-solid fa-lock mr-2"></i> Proceed to Checkout — ৳<?php echo number_format($total, 0); ?>
            </a>

        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-toolbox text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">Your tool cart is empty</h3>
                <p class="text-stone-400 text-sm mb-6">Add some farming tools from our marketplace.</p>
                <a href="index.php#tools-marketplace"
                    class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-wrench"></i> Browse Tools
                </a>
            </div>
        <?php endif; ?>

    </main>

    <script>
        function updateToolQty(cartId, newQty) {
            if (newQty < 0) return;
            fetch('updateToolCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId + '&quantity=' + newQty
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message);
            });
        }

        function removeToolItem(cartId) {
            if (!confirm('Remove this item from cart?')) return;
            fetch('removeFromToolCart.php', {
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