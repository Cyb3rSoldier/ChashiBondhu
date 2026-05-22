
<?php
session_start();
require_once 'config.php';

// Active category filter
$activeCategory = isset($_GET['category']) ? trim($_GET['category']) : 'All';

// Fetch distinct categories
$catResult = $conn->query("SELECT DISTINCT category FROM tool_products WHERE status = 'active' ORDER BY category ASC");
$categories = ['All'];
while ($row = $catResult->fetch_assoc()) {
    if ($row['category']) $categories[] = $row['category'];
}

// Fetch tools
if ($activeCategory === 'All') {
    $stmt = $conn->prepare("
        SELECT t.*, v.full_name AS vendor_name, v.business_name, v.district
        FROM tool_products t
        LEFT JOIN vendors v ON t.vendor_id = v.id
        WHERE t.status = 'active'
        ORDER BY t.created_at DESC
        
    ");
    $stmt->execute();
} else {
    $stmt = $conn->prepare("
        SELECT t.*, v.full_name AS vendor_name, v.business_name, v.district
        FROM tool_products t
        LEFT JOIN vendors v ON t.vendor_id = v.id
        WHERE t.status = 'active' AND t.category = ?
        ORDER BY t.created_at DESC
        
    ");
    $stmt->bind_param("s", $activeCategory);
    $stmt->execute();
}
$tools = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools Marketplace — ChashiBondhu</title>
    <link rel="stylesheet" href="design.css">
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-green-50/50 overflow-x-hidden">

    <?php include 'navbar2.php'; ?>

    <!-- HERO -->
    <section class="bg-gradient-to-br from-amber-900 via-amber-800 to-amber-700 pt-24 pb-16 px-6 text-center text-white">
        <span class="inline-block bg-amber-500/20 border border-amber-400/40 text-amber-300 text-xs font-semibold tracking-widest uppercase px-5 py-2 rounded-full mb-6">
            <i class="fa-solid fa-wrench mr-2"></i> Tools & Equipment
        </span>
        <h1 class="text-3xl md:text-5xl font-bold mb-3">Farming Tools Marketplace</h1>
        <p class="text-amber-100/80 text-base max-w-xl mx-auto">
            Find tractors, plows, seeds, fertilizers and all farming equipment from trusted vendors across Bangladesh
        </p>
    </section>

    <!-- MARKETPLACE -->
    <section class="py-12 px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Search -->
            <div class="max-w-xl mx-auto mb-6">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="text" id="toolSearch" placeholder="Search tools, machines, seeds..."
                        onkeyup="searchTools()"
                        class="w-full bg-white border border-stone-200 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-stone-700 placeholder-stone-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 shadow-sm transition">
                </div>
            </div>

            <!-- Category Chips -->
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?php echo urlencode($cat); ?>"
                        class="px-4 py-2 rounded-full text-sm font-semibold border transition duration-200
                        <?php echo ($activeCategory === $cat)
                            ? 'bg-amber-600 text-white border-amber-600'
                            : 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tools Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="toolGrid">
                <?php if (count($tools) > 0): ?>
                    <?php foreach ($tools as $tool): ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-200 hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col cursor-pointer"
                            onclick="window.location='tool-details.php?id=<?php echo $tool['id']; ?>'">

                            <div class="relative">
                                <img src="<?php echo $tool['image_path'] ? htmlspecialchars($tool['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($tool['product_name']); ?>"
                                    class="w-full h-40 object-cover" loading="lazy">

                                <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full
                                    <?php echo $tool['condition_status'] === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'; ?>">
                                    <?php echo ucfirst($tool['condition_status']); ?>
                                </span>
                            </div>

                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="font-bold text-green-950 text-sm leading-snug mb-1 truncate">
                                    <?php echo htmlspecialchars($tool['product_name']); ?>
                                </h3>
                                <?php if ($tool['brand']): ?>
                                    <p class="text-xs text-stone-400 truncate"><?php echo htmlspecialchars($tool['brand']); ?></p>
                                <?php endif; ?>

                                <div class="flex items-center gap-2 mb-1 mt-1">
                                    <span class="text-amber-600 font-bold text-base">৳<?php echo number_format($tool['price'], 0); ?></span>
                                    <span class="text-stone-400 text-xs">/ <?php echo $tool['unit']; ?></span>
                                </div>

                                <p class="text-xs text-stone-500 truncate">
                                    <i class="fa-solid fa-shop text-stone-300 mr-1"></i>
                                    <?php echo htmlspecialchars($tool['business_name'] ?? $tool['vendor_name'] ?? 'Unknown'); ?>
                                </p>
                                <p class="text-xs text-stone-400 mt-0.5 truncate">
                                    📍 <?php echo htmlspecialchars($tool['district'] ?? ''); ?>
                                </p>
                    <a href="chat.php?id=<?php echo $tool['vendor_id']; ?>&role=vendor"
   onclick="event.stopPropagation()"
   class="mt-2 w-full bg-green-600 hover:bg-green-500 text-white text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1">

    <i class="fa-solid fa-message"></i>Message Vendor</a>

                                <?php if (isset($_SESSION['consumer_id'])): ?>
                                    <button onclick="event.stopPropagation(); addToToolCart(<?php echo $tool['id']; ?>, this)"
                                        class="mt-auto mt-3 w-full bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-cart-plus text-xs"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="consumerLogin.php" onclick="event.stopPropagation()"
                                        class="mt-3 w-full border border-amber-200 text-amber-700 hover:bg-amber-50 text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-right-to-bracket text-xs"></i> Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-wrench text-3xl text-stone-300"></i>
                        </div>
                        <p class="text-stone-500 font-semibold mb-1">No tools found</p>
                        <p class="text-stone-400 text-sm">Check back soon for new listings!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA for Vendors -->
    <section class="py-12 px-4 bg-amber-50">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-green-950 mb-3">Are You a Tool Vendor?</h2>
            <p class="text-stone-500 text-sm mb-6">List your farming tools, machines, seeds and equipment to reach thousands of farmers across Bangladesh.</p>
            <a href="vendorReg.php" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-8 py-3 rounded-xl text-sm transition shadow-md">
                <i class="fa-solid fa-wrench mr-2"></i> Register as Vendor
            </a>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        function searchTools() {
            const query = document.getElementById('toolSearch').value.toLowerCase();
            const cards = document.querySelectorAll('#toolGrid > div');
            cards.forEach(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                card.style.display = name.includes(query) ? '' : 'none';
            });
        }

        function addToToolCart(toolId, btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

            fetch('addToToolCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ tool_id: toolId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Added!';
                    btn.classList.replace('bg-amber-600', 'bg-emerald-600');
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i> Add to Cart';
                        btn.classList.replace('bg-emerald-600', 'bg-amber-600');
                        btn.disabled = false;
                    }, 2000);
                } else {
                    alert(data.message || 'Failed to add.');
                    btn.innerHTML = '<i class="fa-solid fa-cart-plus text-xs"></i> Add to Cart';
                    btn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>