<?php
session_start();
require_once 'config.php';

// Active category filter
$activeCategory = isset($_GET['category']) ? trim($_GET['category']) : 'All';

// Fetch distinct categories
$catResult  = $conn->query("SELECT DISTINCT category FROM products WHERE status = 'active' ORDER BY category ASC");
$categories = ['All'];
while ($row = $catResult->fetch_assoc()) {
    if ($row['category']) $categories[] = $row['category'];
}

// Fetch products
if ($activeCategory === 'All') {
    $stmt = $conn->prepare("
        SELECT p.*, f.full_name AS farmer_name, f.district
        FROM products p
        LEFT JOIN farmers f ON p.farmer_id = f.id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $stmt->execute();
} else {
    $stmt = $conn->prepare("
        SELECT p.*, f.full_name AS farmer_name, f.district
        FROM products p
        LEFT JOIN farmers f ON p.farmer_id = f.id
        WHERE p.status = 'active' AND p.category = ?
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $stmt->bind_param("s", $activeCategory);
    $stmt->execute();
}
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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

<body class="bg-green-50/50 overflow-x-hidden">

    <!-- NAV BAR -->
    <?php include 'navbar.php' ?>

    <!-- HERO SECTION -->
    <section class="relative w-full h-screen min-h-[600px] overflow-hidden">

        <img class="hero-bg scale-105" id="hero-img" src="asset/farmer1.png" alt="Farmer">

        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/20"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-green-950/40 to-transparent"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">

            <div class="hero-badge flex mt-3 items-center gap-2 bg-green-500/20 border border-green-400/40 text-green-300 text-xs font-semibold tracking-widest uppercase px-5 py-2 rounded-full mb-6 backdrop-blur-sm">
                <span class="live-dot"></span>
                Bangladeshi Farmer's Platform
            </div>

            <h1 class="hero-title font-bold text-4xl md:text-6xl lg:text-7xl text-white leading-tight max-w-4xl mb-2">
                From the Farmer to <span class="text-green-400/80">You!</span>
            </h1>

            <p class="hero-subtitle text-base md:text-lg text-stone-300 mt-5 max-w-lg leading-relaxed">
                ChashiBondhu connects farmers directly with consumers <br>
                <span class="text-[15px] md:text-[20px]">"Fresh products, fair prices, and zero middlemen"</span>
            </p>

            <div class="hero-btns flex flex-col sm:flex-row gap-4 mt-8">
                <a href="consumerReg.php"
                    class="btn-primary text-white font-semibold px-8 py-3.5 rounded-xl text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-basket-shopping"></i> Shop as Consumer
                </a>
                <a href="farmerReg.php"
                    class="btn-secondary text-white font-semibold px-8 py-3.5 rounded-xl text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-seedling"></i> Join as Farmer
                </a>
            </div>

            <div class="hero-stats flex flex-wrap justify-center gap-3 mt-10">
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">5,000+</p>
                    <p class="text-stone-400 text-xs mt-0.5">Farmers Joined</p>
                </div>
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">20,000+</p>
                    <p class="text-stone-400 text-xs mt-0.5">Happy Consumers</p>
                </div>
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">64 Districts</p>
                    <p class="text-stone-400 text-xs mt-0.5">Across Bangladesh</p>
                </div>
            </div>
        </div>

        <div class="scroll-hint absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/50 text-xs">
            <span class="tracking-widest uppercase text-[10px]">Scroll</span>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
    </section>

    <!-- MARKETPLACE -->
    <section id="marketplace" class="py-16 px-4">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-10">
                <span class="inline-block bg-green-100 text-green-700 text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Marketplace</span>
                <h2 class="text-3xl md:text-4xl font-bold text-green-950 mb-3">🛒 Fresh From Farms</h2>
                <p class="text-stone-500 text-base max-w-xl mx-auto">
                    Buy directly from farmers across Bangladesh — fresh, fair and chemical-free
                </p>
            </div>

            <!-- Search Bar -->
            <div class="max-w-xl mx-auto mb-6">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-stone-400 text-sm"></i>
                    <input type="text" id="productSearch"
                        placeholder="Search vegetables, fruits, grains..."
                        onkeyup="searchProducts()"
                        class="w-full bg-white border border-stone-200 rounded-2xl pl-11 pr-4 py-3.5 text-sm text-stone-700 placeholder-stone-400 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 shadow-sm transition">
                </div>
            </div>

            <!-- Category Chips -->
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <?php foreach ($categories as $cat): ?>
                    <a href="?category=<?php echo urlencode($cat); ?>#marketplace"
                        class="px-4 py-2 rounded-full text-sm font-semibold border transition duration-200
                        <?php echo ($activeCategory === $cat)
                            ? 'bg-green-700 text-white border-green-700'
                            : 'bg-white text-green-700 border-green-200 hover:bg-green-50'; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4" id="productGrid">

                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>

                        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-300 hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col cursor-pointer"
                            onclick="window.location='details.php?id=<?php echo $product['id']; ?>'">

                            <!-- Image -->
                            <div class="relative">
                                <img src="<?php echo $product['image_path'] ? htmlspecialchars($product['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                                    alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                    class="w-full h-40 object-cover"
                                    loading="lazy">

                                <!-- Badge -->
                                <?php if ($product['badge']): ?>
                                    <span class="absolute top-2 left-2 text-xs font-bold px-2 py-0.5 rounded-full
                                        <?php echo $product['badge'] === 'sale' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'; ?>">
                                        <?php echo ucfirst($product['badge']); ?>
                                    </span>
                                <?php endif; ?>

                                <!-- Organic -->
                                <?php if ($product['is_organic']): ?>
                                    <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                                        🌿 Organic
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Info -->
                            <div class="p-3 flex flex-col flex-1">
                                <h3 class="font-bold text-green-950 text-sm leading-snug mb-1 truncate">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h3>

                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-green-700 font-bold text-base">
                                        ৳<?php echo number_format($product['price'], 0); ?>
                                    </span>
                                    <span class="text-stone-400 text-xs">/ <?php echo $product['unit']; ?></span>
                                    <?php if ($product['government_price']): ?>
                                        <span class="text-stone-400 text-xs line-through">
                                            ৳<?php echo number_format($product['government_price'], 0); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <p class="text-xs text-stone-500 truncate">
                                    <i class="fa-solid fa-user text-stone-300 mr-1"></i>
                                    <?php echo htmlspecialchars($product['farmer_name'] ?? 'Unknown'); ?>
                                </p>
                                <p class="text-xs text-stone-400 mt-0.5 truncate">
                                    📍 <?php echo htmlspecialchars($product['district'] ?? ''); ?>
                                </p>

                                <!-- Add to Cart (consumers only) -->
                                <?php if (isset($_SESSION['consumer_id'])): ?>
                                    <button
                                        onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, this)"
                                        class="mt-auto mt-3 w-full bg-green-700 hover:bg-green-600 text-white text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-plus text-xs"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="consumerLogin.php"
                                        onclick="event.stopPropagation()"
                                        class="mt-3 w-full border border-green-200 text-green-700 hover:bg-green-50 text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                                        <i class="fa-solid fa-right-to-bracket text-xs"></i> Login to Buy
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-box-open text-3xl text-stone-300"></i>
                        </div>
                        <p class="text-stone-500 font-semibold mb-1">No products found</p>
                        <p class="text-stone-400 text-sm">
                            <?php echo $activeCategory !== 'All' ? "No products in \"$activeCategory\" yet." : 'Check back soon!'; ?>
                        </p>
                        <?php if ($activeCategory !== 'All'): ?>
                            <a href="?#marketplace" class="inline-block mt-4 text-green-600 text-sm font-semibold hover:underline">
                                Browse all products
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Load More -->
            <?php if (count($products) >= 8): ?>
                <div class="text-center mt-10">
                    <button onclick="loadMore()"
                        id="loadMoreBtn"
                        class="bg-white border border-green-200 text-green-700 hover:bg-green-50 font-bold px-8 py-3 rounded-2xl text-sm transition duration-200 shadow-sm">
                        <i class="fa-solid fa-arrow-down mr-2"></i> Load More Products
                    </button>
                </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'footer.php' ?>

    <!-- JS -->
    <script>
        window.addEventListener('load', () => {
            const img = document.getElementById('hero-img');
            img.style.transform = 'scale(1)';
        });

        // Live search
        function searchProducts() {
            const query = document.getElementById('productSearch').value.toLowerCase();
            const cards = document.querySelectorAll('#productGrid > div');
            cards.forEach(card => {
                const name = card.querySelector('h3')?.textContent.toLowerCase() || '';
                card.style.display = name.includes(query) ? '' : 'none';
            });
        }

        // Add to cart
        function addToCart(productId, btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-xs"></i>';

            fetch('addToCart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: 1 })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-check text-xs"></i> Added!';
                    btn.classList.replace('bg-green-700', 'bg-emerald-600');
                    setTimeout(() => {
                        btn.innerHTML = '<i class="fa-solid fa-plus text-xs"></i> Add to Cart';
                        btn.classList.replace('bg-emerald-600', 'bg-green-700');
                        btn.disabled = false;
                    }, 2000);
                } else {
                    showToast(data.message || 'Failed to add.', 'error');
                    btn.innerHTML = '<i class="fa-solid fa-plus text-xs"></i> Add to Cart';
                    btn.disabled = false;
                }
            })
            .catch(() => {
                showToast('Something went wrong.', 'error');
                btn.innerHTML = '<i class="fa-solid fa-plus text-xs"></i> Add to Cart';
                btn.disabled = false;
            });
        }

        // Load more
        let offset = 8;
        function loadMore() {
            const btn    = document.getElementById('loadMoreBtn');
            const category = new URLSearchParams(window.location.search).get('category') || 'All';
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Loading...';
            btn.disabled  = true;

            fetch(`loadMoreProducts.php?offset=${offset}&category=${encodeURIComponent(category)}`)
                .then(res => res.text())
                .then(html => {
                    if (html.trim()) {
                        document.getElementById('productGrid').insertAdjacentHTML('beforeend', html);
                        offset += 8;
                        btn.innerHTML = '<i class="fa-solid fa-arrow-down mr-2"></i> Load More Products';
                        btn.disabled  = false;
                    } else {
                        btn.innerHTML = 'No more products';
                        btn.disabled  = true;
                    }
                });
        }

        // Toast
        function showToast(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.textContent = msg;
            toast.style.cssText = `
                position:fixed;bottom:24px;right:24px;
                background:${type === 'success' ? '#15803d' : '#dc2626'};
                color:white;padding:12px 20px;border-radius:14px;
                font-size:14px;font-weight:600;
                box-shadow:0 4px 20px rgba(0,0,0,0.15);
                z-index:9999;opacity:0;
                transition:opacity 0.3s ease;
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.style.opacity = '1', 10);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>

</body>
</html>