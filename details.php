<?php
session_start();
require_once 'config.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product with farmer info
$stmt = $conn->prepare("
    SELECT p.*, f.full_name AS farmer_name, f.district, f.phone AS farmer_phone
    FROM products p
    LEFT JOIN farmers f ON p.farmer_id = f.id
    WHERE p.id = ? AND p.status = 'active'
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: index.php');
    exit();
}

// Increment views
$conn->query("UPDATE products SET views = views + 1 WHERE id = $productId");

// Get related products
$cat  = $product['category'];
$stmt = $conn->prepare("
    SELECT p.*, f.full_name AS farmer_name
    FROM products p
    LEFT JOIN farmers f ON p.farmer_id = f.id
    WHERE p.category = ? AND p.id != ? AND p.status = 'active'
    ORDER BY p.created_at DESC
    LIMIT 6
");
$stmt->bind_param("si", $cat, $productId);
$stmt->execute();
$relatedProducts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChashiBondhu</title>
    <link rel="stylesheet" href="design.css">
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-green-50 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-5xl mx-auto px-5 pt-24 pb-36">

        <!-- Back -->
        <a href="index.php#marketplace"
            class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-6">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Marketplace
        </a>

        <!-- Product Detail Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">

            <!-- Image -->
            <div>
                <img src="<?php echo $product['image_path'] ? htmlspecialchars($product['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="w-full h-72 md:h-96 object-cover rounded-2xl shadow-sm border border-stone-100">

                <!-- Badges under image -->
                <div class="flex gap-2 mt-3 flex-wrap">
                    <?php if ($product['is_organic']): ?>
                        <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full">🌿 Organic</span>
                    <?php endif; ?>
                    <?php if ($product['badge']): ?>
                        <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full">
                            <?php echo ucfirst($product['badge']); ?>
                        </span>
                    <?php endif; ?>
                    <span class="bg-stone-100 text-stone-500 text-xs font-bold px-3 py-1 rounded-full">
                        <?php echo htmlspecialchars($product['category']); ?>
                    </span>
                </div>
            </div>

            <!-- Info -->
            <div class="flex flex-col">

                <h1 class="text-2xl md:text-3xl font-bold text-green-950 leading-snug mb-3">
                    <?php echo htmlspecialchars($product['product_name']); ?>
                </h1>

                <!-- Price -->
                <div class="flex items-end gap-3 mb-5">
                    <span class="text-3xl font-extrabold text-green-700">
                        ৳<?php echo number_format($product['price'], 0); ?>
                        <span class="text-base font-semibold text-stone-400">/ <?php echo $product['unit']; ?></span>
                    </span>
                    <?php if ($product['government_price']): ?>
                        <span class="text-stone-400 text-sm line-through mb-1">
                            ৳<?php echo number_format($product['government_price'], 0); ?>
                        </span>
                        <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full mb-1">
                            Save <?php echo round(($product['government_price'] - $product['price']) / $product['government_price'] * 100); ?>%
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Info Cards -->
                <div class="space-y-2 mb-5">
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-user text-green-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">Sold by</p>
                            <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($product['farmer_name'] ?? 'Unknown'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-location-dot text-green-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">From</p>
                            <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($product['district'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-box text-green-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">Available Stock</p>
                            <p class="text-sm font-semibold <?php echo $product['quantity'] > 0 ? 'text-green-700' : 'text-red-500'; ?>">
                                <?php echo $product['quantity'] > 0 ? $product['quantity'] . ' ' . $product['unit'] . ' available' : 'Out of stock'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <?php if ($product['description']): ?>
                    <div class="bg-white rounded-xl px-4 py-4 border border-stone-100">
                        <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Description</h3>
                        <p class="text-stone-600 text-sm leading-relaxed">
                            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                        </p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Related Products -->
        <?php if (count($relatedProducts) > 0): ?>
            <div>
                <h3 class="font-bold text-lg text-green-950 mb-4">Related Products</h3>
                <div class="flex gap-4 overflow-x-auto pb-2" style="scrollbar-width:none;">
                    <?php foreach ($relatedProducts as $rel): ?>
                        <div class="bg-white rounded-2xl overflow-hidden border border-stone-100 shadow-sm hover:shadow-md transition cursor-pointer shrink-0 w-40"
                            onclick="window.location='productDetails.php?id=<?php echo $rel['id']; ?>'">
                            <img src="<?php echo $rel['image_path'] ? htmlspecialchars($rel['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                                alt="<?php echo htmlspecialchars($rel['product_name']); ?>"
                                class="w-full h-32 object-cover">
                            <div class="p-2.5">
                                <p class="text-xs font-bold text-green-950 truncate"><?php echo htmlspecialchars($rel['product_name']); ?></p>
                                <p class="text-sm font-bold text-green-700 mt-0.5">৳<?php echo number_format($rel['price'], 0); ?><span class="text-stone-400 text-xs font-normal">/<?php echo $rel['unit']; ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Sticky Add to Cart Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-stone-100 shadow-xl px-5 py-4 z-50">
        <div class="max-w-5xl mx-auto flex items-center gap-4">

            <!-- Quantity -->
            <div class="flex items-center gap-0 bg-stone-100 rounded-xl overflow-hidden shrink-0">
                <button onclick="changeQty(-1)"
                    class="w-10 h-10 flex items-center justify-center text-green-800 font-bold text-lg hover:bg-stone-200 transition">
                    −
                </button>
                <span id="qtyDisplay" class="w-10 text-center font-bold text-green-950 text-sm">1</span>
                <button onclick="changeQty(1)"
                    class="w-10 h-10 flex items-center justify-center text-green-800 font-bold text-lg hover:bg-stone-200 transition">
                    +
                </button>
            </div>

            <!-- Total price preview -->
            <div class="hidden sm:block shrink-0">
                <p class="text-xs text-stone-400">Total</p>
                <p class="font-bold text-green-700 text-sm" id="totalPrice">
                    ৳<?php echo number_format($product['price'], 0); ?>
                </p>
            </div>

            <?php if (isset($_SESSION['consumer_id'])): ?>
                <?php if ($product['quantity'] > 0): ?>
                    <button onclick="addToCart()" id="addToCartBtn"
                        class="flex-1 bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-3 rounded-xl transition duration-200 text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-basket-shopping"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <button disabled
                        class="flex-1 bg-stone-200 text-stone-400 font-bold py-3 rounded-xl text-sm cursor-not-allowed">
                        Out of Stock
                    </button>
                <?php endif; ?>
            <?php else: ?>
                <a href="consumerLogin.php"
                    class="flex-1 bg-green-700 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Login to Add to Cart
                </a>
            <?php endif; ?>

        </div>
    </div>


    <script>
        let quantity = 1;
        const maxStock = <?php echo intval($product['quantity']); ?>;
        const unitPrice = <?php echo floatval($product['price']); ?>;

        function changeQty(delta) {
            quantity = Math.max(1, Math.min(quantity + delta, maxStock));
            document.getElementById('qtyDisplay').textContent = quantity;
            document.getElementById('totalPrice').textContent = '৳' + (quantity * unitPrice).toLocaleString('en-BD');
        }

        function addToCart() {
            const btn = document.getElementById('addToCartBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

            fetch('addToCart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: <?php echo $productId; ?>,
                        quantity: quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Added to Cart!';
                        btn.classList.replace('bg-green-700', 'bg-emerald-600');
                        setTimeout(() => {
                            btn.innerHTML = '<i class="fa-solid fa-basket-shopping"></i> Add to Cart';
                            btn.classList.replace('bg-emerald-600', 'bg-green-700');
                            btn.disabled = false;
                        }, 2000);
                    } else {
                        alert(data.message || 'Failed to add to cart.');
                        btn.innerHTML = '<i class="fa-solid fa-basket-shopping"></i> Add to Cart';
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Something went wrong.');
                    btn.innerHTML = '<i class="fa-solid fa-basket-shopping"></i> Add to Cart';
                    btn.disabled = false;
                });
        }
    </script>

</body>

</html>