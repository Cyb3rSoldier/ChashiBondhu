
<?php
session_start();
require_once 'config.php';

$toolId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
    SELECT t.*, v.full_name AS vendor_name, v.business_name, v.district, v.phone AS vendor_phone
    FROM tool_products t
    LEFT JOIN vendors v ON t.vendor_id = v.id
    WHERE t.id = ? AND t.status = 'active'
");
$stmt->bind_param("i", $toolId);
$stmt->execute();
$tool = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tool) {
    header('Location: marketplace-tools.php');
    exit();
}

// Increment views
$updateViews = $conn->prepare("UPDATE tool_products SET views = views + 1 WHERE id = ?");
$updateViews->bind_param("i", $toolId);
$updateViews->execute();
$updateViews->close();

// Get related tools
$cat = $tool['category'];
$stmt = $conn->prepare("
    SELECT t.*, v.business_name
    FROM tool_products t
    LEFT JOIN vendors v ON t.vendor_id = v.id
    WHERE t.category = ? AND t.id != ? AND t.status = 'active'
    ORDER BY t.created_at DESC
    LIMIT 6
");
$stmt->bind_param("si", $cat, $toolId);
$stmt->execute();
$relatedTools = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tool['product_name']); ?> — ChashiBondhu</title>
    <link rel="stylesheet" href="design.css">
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-green-50 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <main class="max-w-5xl mx-auto px-5 pt-24 pb-36">
        <a href="marketplace-tools.php" class="inline-flex items-center gap-2 text-amber-700 text-sm font-semibold hover:text-amber-900 transition mb-6">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Tools Marketplace        </a>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div>
                <img src="<?php echo $tool['image_path'] ? htmlspecialchars($tool['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                    alt="<?php echo htmlspecialchars($tool['product_name']); ?>"
                    class="w-full h-72 md:h-96 object-cover rounded-2xl shadow-sm border border-stone-100">
                <div class="flex gap-2 mt-3 flex-wrap">
                    <span class="text-xs font-bold px-3 py-1 rounded-full <?php echo $tool['condition_status'] === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'; ?>">
                        <?php echo ucfirst($tool['condition_status']); ?>
                    </span>
                    <span class="bg-stone-100 text-stone-500 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($tool['category']); ?></span>
                    <?php if ($tool['brand']): ?>
                        <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($tool['brand']); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex flex-col">
                <h1 class="text-2xl md:text-3xl font-bold text-green-950 leading-snug mb-3"><?php echo htmlspecialchars($tool['product_name']); ?></h1>

                <div class="flex items-end gap-3 mb-5">
                    <span class="text-3xl font-extrabold text-amber-600">
                        ৳<?php echo number_format($tool['price'], 0); ?>
                        <span class="text-base font-semibold text-stone-400">/ <?php echo $tool['unit']; ?></span>
                    </span>
                </div>

                <div class="space-y-2 mb-5">
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-shop text-amber-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">Sold by</p>
                            <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($tool['business_name'] ?? $tool['vendor_name'] ?? 'Unknown'); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-location-dot text-amber-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">From</p>
                            <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($tool['district'] ?? ''); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-stone-100">
                        <i class="fa-solid fa-box text-amber-600 w-4 text-center"></i>
                        <div>
                            <p class="text-xs text-stone-400">Available Stock</p>
                            <p class="text-sm font-semibold <?php echo $tool['quantity'] > 0 ? 'text-green-700' : 'text-red-500'; ?>">
                                <?php echo $tool['quantity'] > 0 ? $tool['quantity'] . ' ' . $tool['unit'] . ' available' : 'Out of stock'; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <?php if ($tool['description']): ?>
                    <div class="bg-white rounded-xl px-4 py-4 border border-stone-100">
                        <h3 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Description</h3>
                        <p class="text-stone-600 text-sm leading-relaxed"><?php echo nl2br(htmlspecialchars($tool['description'])); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Tools -->
        <?php if (count($relatedTools) > 0): ?>
            <div>
                <h3 class="font-bold text-lg text-green-950 mb-4">Related Tools</h3>
                <div class="flex gap-4 overflow-x-auto pb-2" style="scrollbar-width:none;">
                    <?php foreach ($relatedTools as $rel): ?>
                        <div class="bg-white rounded-2xl overflow-hidden border border-stone-100 shadow-sm hover:shadow-md transition cursor-pointer shrink-0 w-40"
                            onclick="window.location='tool-details.php?id=<?php echo $rel['id']; ?>'">
                            <img src="<?php echo $rel['image_path'] ? htmlspecialchars($rel['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                                alt="<?php echo htmlspecialchars($rel['product_name']); ?>" class="w-full h-32 object-cover">
                            <div class="p-2.5">
                                <p class="text-xs font-bold text-green-950 truncate"><?php echo htmlspecialchars($rel['product_name']); ?></p>
                                <p class="text-sm font-bold text-amber-600 mt-0.5">৳<?php echo number_format($rel['price'], 0); ?><span class="text-stone-400 text-xs font-normal">/<?php echo $rel['unit']; ?></span></p>
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
            <div class="flex items-center gap-0 bg-stone-100 rounded-xl overflow-hidden shrink-0">
                <button onclick="changeQty(-1)" class="w-10 h-10 flex items-center justify-center text-amber-800 font-bold text-lg hover:bg-stone-200 transition">−</button>
                <span id="qtyDisplay" class="w-10 text-center font-bold text-green-950 text-sm">1</span>
                <button onclick="changeQty(1)" class="w-10 h-10 flex items-center justify-center text-amber-800 font-bold text-lg hover:bg-stone-200 transition">+</button>
            </div>

            <div class="hidden sm:block shrink-0">
                <p class="text-xs text-stone-400">Total</p>
                <p class="font-bold text-amber-600 text-sm" id="totalPrice">৳<?php echo number_format($tool['price'], 0); ?></p>
            </div>

            <?php if (isset($_SESSION['consumer_id'])): ?>
                <?php if ($tool['quantity'] > 0): ?>
                    <button onclick="addToCart()" id="addToCartBtn"
                        class="flex-1 bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white font-bold py-3 rounded-xl transition duration-200 text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                <?php else: ?>
                    <button disabled class="flex-1 bg-stone-200 text-stone-400 font-bold py-3 rounded-xl text-sm cursor-not-allowed">Out of Stock</button>
                <?php endif; ?>
            <?php else: ?>
                <a href="consumerLogin.php" class="flex-1 bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket"></i> Login to Add to Cart
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let quantity = 1;
        const maxStock = <?php echo intval($tool['quantity']); ?>;
        const unitPrice = <?php echo floatval($tool['price']); ?>;

        function changeQty(delta) {
            quantity = Math.max(1, Math.min(quantity + delta, maxStock));
            document.getElementById('qtyDisplay').textContent = quantity;
            document.getElementById('totalPrice').textContent = '৳' + (quantity * unitPrice).toLocaleString('en-BD');
        }

        function addToCart() {
            const btn = document.getElementById('addToCartBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

            fetch('addToToolCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tool_id: <?php echo $toolId; ?>, quantity: quantity })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Added to Cart!';
                    btn.classList.replace('bg-amber-600', 'bg-emerald-600');
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> Add to Cart';
                        btn.classList.replace('bg-emerald-600', 'bg-amber-600');
                        btn.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message || 'Failed to add.');
                    btn.innerHTML = '<i class="fa-solid fa-cart-plus"></i> Add to Cart';
                    btn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>