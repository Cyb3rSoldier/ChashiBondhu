
<?php
session_start();
require_once '../config/database.php';
require_once '../config/session.php';
require_once '../includes/marketplace-functions.php';

Session::start();

// Check if consumer is logged in
if (!isset($_SESSION['consumer_id'])) {
    header('Location: ../consumerLogin.php');
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$consumerName = $_SESSION['consumer_name'];
$cartItems = getCartItems($consumerId);

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$deliveryFee = 50;
$total = $subtotal + $deliveryFee;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="stylesheet" href="../asset/css/marketplace.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .cart-item {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            gap: 14px;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .cart-item__img {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .cart-item__info { flex: 1; min-width: 0; }
        .cart-item__name {
            font-size: 15px;
            font-weight: 600;
            color: #171717;
            margin-bottom: 4px;
        }
        .cart-item__farmer {
            font-size: 12px;
            color: #737373;
            margin-bottom: 4px;
        }
        .cart-item__price {
            font-size: 16px;
            font-weight: 700;
            color: #16a34a;
        }
        .cart-item__actions {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 8px;
        }
        .qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1.5px solid #e5e5e5;
            background: white;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-val {
            font-weight: 600;
            min-width: 24px;
            text-align: center;
        }
        .remove-btn {
            background: #fee2e2;
            color: #dc2626;
            border: none;
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
        }
        @media (min-width: 768px) {
            .cart-container {
                max-width: 700px;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body class="bg-stone-50 min-h-screen">

    <?php include('../navbar2.php'); ?>

    <div class="pt-24 pb-24 px-4 cart-container">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-green-950">🛒 My Cart</h1>
            <span class="text-stone-500 text-sm"><?php echo count($cartItems); ?> items</span>
        </div>

        <?php if (count($cartItems) > 0): ?>
            
            <!-- Cart Items -->
            <div id="cartItems">
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" id="cart-item-<?php echo $item['id']; ?>">
                    <img src="<?php echo $item['image_path'] ? $item['image_path'] : '../asset/img/placeholder-product.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                         class="cart-item__img">
                    <div class="cart-item__info">
                        <div class="cart-item__name"><?php echo htmlspecialchars($item['product_name']); ?></div>
                        <div class="cart-item__farmer">By: <?php echo htmlspecialchars($item['farmer_name']); ?></div>
                        <div class="cart-item__price">৳<?php echo number_format($item['price'], 0); ?>/<?php echo $item['unit']; ?></div>
                        <div class="cart-item__actions">
                            <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">−</button>
                            <span class="qty-val" id="qty-<?php echo $item['id']; ?>"><?php echo $item['quantity']; ?></span>
                            <button class="qty-btn" onclick="updateQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                            <span style="margin-left:auto;font-weight:700;color:#16a34a;">
                                ৳<?php echo number_format($item['price'] * $item['quantity'], 0); ?>
                            </span>
                        </div>
                        <button class="remove-btn" style="margin-top:6px;" onclick="removeItem(<?php echo $item['id']; ?>)">
                            <i class="fa-solid fa-trash mr-1"></i> Remove
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-2xl p-5 mt-4 shadow-sm">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-stone-500">Subtotal</span>
                    <span class="font-semibold">৳<?php echo number_format($subtotal, 0); ?></span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-stone-500">Delivery Fee</span>
                    <span class="font-semibold">৳<?php echo number_format($deliveryFee, 0); ?></span>
                </div>
                <div class="border-t pt-2 mt-2 flex justify-between">
                    <span class="font-bold text-green-950">Total</span>
                    <span class="font-bold text-green-700 text-lg">৳<?php echo number_format($total, 0); ?></span>
                </div>
            </div>

            <!-- Checkout Button -->
            <a href="checkout.php" class="btn-primary" style="display:block;text-align:center;margin-top:16px;text-decoration:none;">
                Proceed to Checkout →
            </a>

        <?php else: ?>
            <div class="empty-cart">
                <i class="fa-solid fa-cart-shopping text-5xl text-stone-300 mb-4"></i>
                <p class="text-stone-500 text-lg mb-2">Your cart is empty</p>
                <a href="../index.php#marketplace" class="text-green-600 font-semibold hover:underline">Browse Products</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function updateQty(cartId, newQty) {
            if (newQty < 0) return;
            
            fetch('update.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'cart_id=' + cartId + '&quantity=' + newQty
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        function removeItem(cartId) {
            if (!confirm('Remove this item from cart?')) return;
            
            fetch('remove.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'cart_id=' + cartId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>