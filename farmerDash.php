<?php
session_start();

if (!isset($_SESSION['farmer_id'])) {
    header("Location: farmerLogin.php");
    exit();
}

require_once 'config.php';

$name = $_SESSION['farmer_name'];
$farmerId = $_SESSION['farmer_id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ?");
$stmt->bind_param("i", $farmerId);
$stmt->execute();

$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();

// ==========================
// TOTAL PRODUCTS
// ==========================
$totalProducts = count($products);

// ==========================
// TOTAL ORDERS RECEIVED
// ==========================
$orderStmt = $conn->prepare("
    SELECT COUNT(DISTINCT oi.order_id) as total_orders
    FROM order_items oi
    WHERE oi.farmer_id = ?
");
$orderStmt->bind_param("i", $farmerId);
$orderStmt->execute();
$orderResult  = $orderStmt->get_result()->fetch_assoc();
$totalOrders  = $orderResult['total_orders'] ?? 0;
$orderStmt->close();

// ==========================
// PENDING ORDERS
// ==========================
$pendingStmt = $conn->prepare("
    SELECT COUNT(DISTINCT oi.order_id) as pending_orders
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.farmer_id = ?
    AND o.order_status = 'pending'
");
$pendingStmt->bind_param("i", $farmerId);
$pendingStmt->execute();
$pendingResult = $pendingStmt->get_result()->fetch_assoc();
$pendingOrders = $pendingResult['pending_orders'] ?? 0;
$pendingStmt->close();

// ==========================
// TOTAL EARNINGS
// ==========================
$earnStmt = $conn->prepare("
    SELECT SUM(oi.total_price) as total_earnings
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.farmer_id = ?
    AND o.order_status != 'cancelled'
");
$earnStmt->bind_param("i", $farmerId);
$earnStmt->execute();
$earnResult    = $earnStmt->get_result()->fetch_assoc();
$totalEarnings = $earnResult['total_earnings'] ?? 0;
$earnStmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>

<body class="bg-[#f2f5f0] overflow-x-hidden min-h-screen">

    <!-- NAVBAR -->
    <?php include('farmerNav.php'); ?>

    <main class="max-w-7xl mx-auto px-5 md:px-8 pt-24 pb-14">

        <div class="fade-up bg-gradient-to-br from-green-950 via-green-900 to-green-700
                    rounded-3xl p-8 md:p-12 text-white shadow-2xl relative overflow-hidden">

            <!-- Decorative tractor bg icon -->
            <div class="absolute -right-8 -top-6 text-[220px] text-white/[0.04] select-none pointer-events-none">
                <i class="fa-solid fa-tractor"></i>
            </div>
            <div class="absolute bottom-6 right-52 text-[90px] text-white/[0.04] select-none pointer-events-none hidden lg:block">
                <i class="fa-solid fa-seedling"></i>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">

                <!-- Left -->
                <div class="flex-1">
                    <p class="uppercase tracking-[4px] text-green-300 text-xs font-bold mb-3">
                        Farmer Dashboard
                    </p>

                    <h1 class="serif text-4xl md:text-5xl font-extrabold leading-tight">
                        Welcome Back,<br>
                        <span class="text-green-300"><?php echo htmlspecialchars($name); ?></span>
                    </h1>

                    <p class="text-green-100/75 mt-4 max-w-xl text-sm leading-7">
                        Manage your products, track orders, monitor earnings and connect
                        directly with customers across Bangladesh.
                    </p>

                    <div class="flex flex-wrap gap-3 mt-7">
                        <a href="add-product.php"
                            class="bg-white text-green-900 hover:bg-green-50 transition font-bold text-sm
                                  px-6 py-3 rounded-xl flex items-center gap-2 shadow-md">
                            <i class="fa-solid fa-plus"></i>Add Product
                        </a>
                        <a href="orders.php"
                            class="border border-white/25 hover:bg-white/10 transition font-semibold text-sm
                                  px-6 py-3 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i>View Orders
                        </a>
                    </div>
                </div>

                <!-- Profile card -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-7
                            w-full lg:w-[300px] shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg leading-tight">
                                <?php echo htmlspecialchars($name); ?>
                            </h3>
                            <span class="text-xs text-green-300 font-semibold uppercase tracking-wider">
                                ✓ Verified Farmer
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold">0</p>
                            <p class="text-xs text-green-200 mt-1">Products</p>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold">0</p>
                            <p class="text-xs text-green-200 mt-1">Orders</p>
                        </div>
                    </div>

                    <a href="farmer/profile.php"
                        class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold
                              border border-white/20 rounded-xl py-2.5 hover:bg-white/10 transition">
                        <i class="fa-solid fa-pen-to-square text-green-300"></i>Edit Profile
                    </a>
                    <a href="farmerLogout.php"
                        class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold
          border border-red-500/30 text-white rounded-xl py-2.5
          bg-red-900/90 hover:text-red-200 transition">
                        <i class="fa-solid fa-right-from-bracket text-red-400"></i>
                        Logout
                    </a>
                </div>

            </div>
        </div>

        <!-- ── STAT CARDS ──────────────────────────── -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-7">

            <div class="stat-card fade-up delay-1 bg-white rounded-2xl p-6 shadow-sm border border-green-100 cursor-default">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Total Products</p>
                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none"><?php echo $totalProducts; ?></p>

                        <p class="text-xs text-stone-400 mt-2">Listed in marketplace</p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
                <div class="mt-4 h-1 rounded-full bg-green-100">
                    <div class="h-1 rounded-full bg-green-500 w-0"></div>
                </div>
            </div>

            <div class="stat-card fade-up delay-2 bg-white rounded-2xl p-6 shadow-sm border border-amber-100 cursor-default">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Orders Received</p>
                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            <?php echo $totalOrders; ?>
                        </h2>
                        <p class="text-xs text-stone-400 mt-2">All time orders</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                </div>
                <div class="mt-4 h-1 rounded-full bg-amber-100">
                    <div class="h-1 rounded-full bg-amber-400 w-0"></div>
                </div>
            </div>

            <div class="stat-card fade-up delay-3 bg-white rounded-2xl p-6 shadow-sm border border-blue-100 cursor-default">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Total Earnings</p>
                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            ৳<?php echo number_format($totalEarnings, 0); ?>
                        </h2>
                        <p class="text-xs text-stone-400 mt-2">Lifetime revenue</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="mt-4 h-1 rounded-full bg-blue-100">
                    <div class="h-1 rounded-full bg-blue-400 w-0"></div>
                </div>
            </div>

            <div class="stat-card fade-up delay-4 bg-white rounded-2xl p-6 shadow-sm border border-purple-100 cursor-default">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Pending Orders</p>
                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            <?php echo $pendingOrders; ?>
                        </h2>
                        <p class="text-xs text-stone-400 mt-2">Awaiting fulfillment</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                </div>
                <div class="mt-4 h-1 rounded-full bg-purple-100">
                    <div class="h-1 rounded-full bg-purple-400 w-0"></div>
                </div>
            </div>

        </div>

        <!-- ── MAIN GRID ────────────────────────────── -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-7">

            <!-- Quick Actions -->
            <div class="lg:col-span-2 fade-up delay-5 bg-white rounded-3xl p-8 shadow-sm border border-stone-100">

                <div class="mb-7">
                    <h2 class="serif text-2xl font-bold text-green-950">Quick Actions</h2>
                    <p class="text-stone-400 text-sm mt-0.5">Everything you need, one tap away</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- Add Product -->
                    <a href="add-product.php"
                        class="action-card group bg-green-50 hover:bg-green-100 border border-green-100
                              rounded-2xl p-6 transition duration-300 flex flex-col no-underline">
                        <div class="action-icon w-14 h-14 rounded-2xl bg-green-600 text-white
                                    flex items-center justify-center text-xl mb-5 shadow-md shadow-green-200">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <h3 class="font-bold text-base text-green-950">Add Product</h3>
                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            Upload fresh crops & produce to the marketplace.
                        </p>
                        <span class="mt-4 text-green-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                            Get started <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </a>

                    <!-- My Products -->
                    <a href="my-products.php"
                        class="action-card group bg-amber-50 hover:bg-amber-100 border border-amber-100
                              rounded-2xl p-6 transition duration-300 flex flex-col no-underline">
                        <div class="action-icon w-14 h-14 rounded-2xl bg-amber-500 text-white
                                    flex items-center justify-center text-xl mb-5 shadow-md shadow-amber-200">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="font-bold text-base text-green-950">My Products</h3>
                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            View, edit or remove your listed items easily.
                        </p>
                        <span class="mt-4 text-amber-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                            Manage <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </a>

                    <!-- Orders -->
                    <a href="orders.php"
                        class="action-card group bg-blue-50 hover:bg-blue-100 border border-blue-100
                              rounded-2xl p-6 transition duration-300 flex flex-col no-underline">
                        <div class="action-icon w-14 h-14 rounded-2xl bg-blue-600 text-white
                                    flex items-center justify-center text-xl mb-5 shadow-md shadow-blue-200">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <h3 class="font-bold text-base text-green-950">Orders</h3>
                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            Track customer orders and delivery in real-time.
                        </p>
                        <span class="mt-4 text-blue-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                            Track now <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </a>

                </div>

                <!-- Secondary row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">

                    <a href="farmer/earnings.php"
                        class="flex items-center gap-4 bg-stone-50 hover:bg-stone-100 border border-stone-100
                              rounded-2xl p-4 transition no-underline group">
                        <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-green-950">Earnings Report</p>
                            <p class="text-xs text-stone-400">View revenue breakdown</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-stone-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                    </a>

                    <a href="farmer/profile.php"
                        class="flex items-center gap-4 bg-stone-50 hover:bg-stone-100 border border-stone-100
                              rounded-2xl p-4 transition no-underline group">
                        <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-pen"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-sm text-green-950">My Profile</p>
                            <p class="text-xs text-stone-400">Update account details</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-stone-300 ml-auto group-hover:translate-x-1 transition-transform"></i>
                    </a>

                </div>
            </div>

            <!-- Tips + CTA -->
            <div class="flex flex-col gap-5">

                <!-- Tips -->
                <div class="bg-white rounded-3xl p-7 shadow-sm border border-stone-100 flex-1">
                    <h2 class="serif text-2xl font-bold text-green-950 mb-5">Farmer Tips 🌱</h2>

                    <div class="flex flex-col gap-4">

                        <div class="flex items-start gap-4 bg-green-50 border border-green-100 rounded-2xl p-4">
                            <div class="w-10 h-10 rounded-xl bg-green-200 text-green-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-seedling"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-green-950">Keep Stock Updated</h3>
                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                    Refresh stock and pricing regularly to boost visibility.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                            <div class="w-10 h-10 rounded-xl bg-amber-200 text-amber-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-green-950">Earn Better Ratings</h3>
                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                    Fast delivery and fresh produce earn top reviews.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-200 text-blue-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-camera"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-green-950">Use Quality Photos</h3>
                                <p class="text-xs text-stone-500 mt-1 leading-relaxed">
                                    Clear product images attract more buyers.
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- CTA Banner -->
                <div class="bg-gradient-to-br from-green-800 to-green-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 text-[90px] text-white/10 select-none pointer-events-none">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-widest text-green-300 mb-2">Pro Tip</p>
                    <h3 class="font-bold text-base leading-snug mb-2">
                        List before the morning rush!
                    </h3>
                    <p class="text-xs text-green-100/80 leading-relaxed mb-4">
                        Most buyers shop 7–10 AM. Add your listings the night before to get ahead.
                    </p>
                    <a href="add-product.php"
                        class="inline-flex items-center gap-2 bg-white text-green-900 font-bold text-xs
                              px-4 py-2.5 rounded-xl hover:bg-green-50 transition">
                        <i class="fa-solid fa-plus"></i>Add Product Now
                    </a>
                </div>

            </div>
        </div>

    </main>

    <!-- FOOTER -->
    <?php include 'footer.php' ?>

    <script>
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('hamburger-icon');
            menu.classList.toggle('hidden');
            icon.className = menu.classList.contains('hidden') ?
                'fa-solid fa-bars' :
                'fa-solid fa-xmark';
        }
    </script>

</body>

</html>