<?php
session_start();

if (!isset($_SESSION['vendor_id'])) {
    header("Location: vendorLogin.php");
    exit();
}

require_once 'config.php';

$vendorName = $_SESSION['vendor_name'];
$vendorId   = $_SESSION['vendor_id'];

// Get vendor info
$vendorStmt = $conn->prepare("SELECT * FROM vendors WHERE id = ?");
$vendorStmt->bind_param("i", $vendorId);
$vendorStmt->execute();
$vendor = $vendorStmt->get_result()->fetch_assoc();
$vendorStmt->close();

// Total Tools
$toolStmt = $conn->prepare("SELECT COUNT(*) as total FROM tool_products WHERE vendor_id = ?");
$toolStmt->bind_param("i", $vendorId);
$toolStmt->execute();
$totalTools = $toolStmt->get_result()->fetch_assoc()['total'] ?? 0;
$toolStmt->close();

// Active Tools
$activeStmt = $conn->prepare("SELECT COUNT(*) as total FROM tool_products WHERE vendor_id = ? AND status = 'active'");
$activeStmt->bind_param("i", $vendorId);
$activeStmt->execute();
$activeTools = $activeStmt->get_result()->fetch_assoc()['total'] ?? 0;
$activeStmt->close();

// Total Orders Received
$orderStmt = $conn->prepare("
    SELECT COUNT(DISTINCT toi.order_id) as total_orders
    FROM tool_order_items toi
    WHERE toi.vendor_id = ?
");
$orderStmt->bind_param("i", $vendorId);
$orderStmt->execute();
$totalOrdersResult = $orderStmt->get_result()->fetch_assoc();
$totalOrders = $totalOrdersResult['total_orders'] ?? 0;
$orderStmt->close();

// Pending Orders
$pendingStmt = $conn->prepare("
    SELECT COUNT(DISTINCT toi.order_id) as pending_orders
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = ? AND o.order_status = 'pending'
");
$pendingStmt->bind_param("i", $vendorId);
$pendingStmt->execute();
$pendingResult = $pendingStmt->get_result()->fetch_assoc();
$pendingOrders = $pendingResult['pending_orders'] ?? 0;
$pendingStmt->close();

// Total Earnings
$earnStmt = $conn->prepare("
    SELECT SUM(toi.total_price) as total_earnings
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = ? AND o.order_status IN ('completed', 'processing')
");
$earnStmt->bind_param("i", $vendorId);
$earnStmt->execute();
$earnResult = $earnStmt->get_result()->fetch_assoc();
$totalEarnings = $earnResult['total_earnings'] ?? 0;
$earnStmt->close();

// Recent Tools - FIXED IMAGE PATH
$recentStmt = $conn->prepare("
    SELECT * FROM tool_products 
    WHERE vendor_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$recentStmt->bind_param("i", $vendorId);
$recentStmt->execute();
$recentTools = $recentStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$recentStmt->close();

// Get recent orders for display
$recentOrdersStmt = $conn->prepare("
    SELECT DISTINCT o.id, o.order_number, o.order_status, o.final_amount, o.created_at,
           COUNT(toi.id) as item_count
    FROM tool_orders o
    JOIN tool_order_items toi ON o.id = toi.order_id
    WHERE toi.vendor_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recentOrdersStmt->bind_param("i", $vendorId);
$recentOrdersStmt->execute();
$recentOrders = $recentOrdersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$recentOrdersStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#f2f5f0] overflow-x-hidden min-h-screen">

    <?php include('vendorNav.php'); ?>

    <main class="max-w-7xl mx-auto px-5 md:px-8 pt-24 pb-14">

        <!-- HERO -->
        <div class="bg-gradient-to-br from-amber-950 via-amber-900 to-amber-700 rounded-3xl p-8 md:p-12 text-white shadow-2xl relative overflow-hidden">

            <div class="absolute -right-8 -top-6 text-[220px] text-white/[0.04] select-none pointer-events-none">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <div class="absolute bottom-6 right-52 text-[90px] text-white/[0.04] select-none pointer-events-none hidden lg:block">
                <i class="fa-solid fa-gear"></i>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">
                <div class="flex-1">
                    <p class="uppercase tracking-[4px] text-amber-300 text-xs font-bold mb-3">Vendor Dashboard</p>
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                        Welcome Back,<br>
                        <span class="text-amber-300"><?php echo htmlspecialchars($vendorName); ?></span>
                    </h1>
                    <p class="text-amber-100/75 mt-4 max-w-xl text-sm leading-7">
                        Manage your farming tools, equipment, seeds and fertilizers. Track orders and grow your business across Bangladesh.
                    </p>
                    <div class="flex flex-wrap gap-3 mt-7">
                        <a href="add-tool.php" class="bg-white text-amber-900 hover:bg-amber-50 transition font-bold text-sm px-6 py-3 rounded-xl flex items-center gap-2 shadow-md">
                            <i class="fa-solid fa-plus"></i> Add New Tool
                        </a>
                        <a href="vendor-orders.php" class="border border-white/25 hover:bg-white/10 transition font-semibold text-sm px-6 py-3 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-cart-shopping"></i> View Orders
                        </a>

                        <a href="inbox.php" class="border border-white/25 hover:bg-white/10 transition font-semibold text-sm px-6 py-3 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-inbox"></i> Inbox
                        </a>
                    </div>
                </div>

                <!-- Profile Card -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-7 w-full lg:w-[300px] shrink-0">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg leading-tight"><?php echo htmlspecialchars($vendorName); ?></h3>
                            <span class="text-xs text-amber-300 font-semibold uppercase tracking-wider">✓ Verified Vendor</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold"><?php echo $totalTools; ?></p>
                            <p class="text-xs text-amber-200 mt-1">Tools</p>
                        </div>
                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold"><?php echo $totalOrders; ?></p>
                            <p class="text-xs text-amber-200 mt-1">Orders</p>
                        </div>
                    </div>
                    <a href="vendorLogout.php" class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold border border-red-500/30 text-white rounded-xl py-2.5 bg-red-900/90 hover:text-red-200 transition">
                        <i class="fa-solid fa-right-from-bracket text-red-400"></i> Logout
                    </a>
                </div>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-7">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Total Tools</p>
                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none"><?php echo $totalTools; ?></p>
                        <p class="text-xs text-stone-400 mt-2">Listed in marketplace</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl"><i class="fa-solid fa-wrench"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-orange-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Orders Received</p>
                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none"><?php echo $totalOrders; ?></p>
                        <p class="text-xs text-stone-400 mt-2">All time orders</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl"><i class="fa-solid fa-cart-shopping"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-emerald-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Total Earnings</p>
                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none">৳<?php echo number_format($totalEarnings, 0); ?></p>
                        <p class="text-xs text-stone-400 mt-2">Lifetime revenue</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl"><i class="fa-solid fa-money-bill-wave"></i></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">Pending Orders</p>
                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none"><?php echo $pendingOrders; ?></p>
                        <p class="text-xs text-stone-400 mt-2">Awaiting fulfillment</p>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl"><i class="fa-solid fa-clock"></i></div>
                </div>
            </div>
        </div>

        <!-- QUICK ACTIONS + RECENT ORDERS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-7">
            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-stone-100">
                <div class="mb-7">
                    <h2 class="text-2xl font-bold text-green-950">Quick Actions</h2>
                    <p class="text-stone-400 text-sm mt-0.5">Manage your tools and equipment</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="add-tool.php" class="group bg-amber-50 hover:bg-amber-100 border border-amber-100 rounded-2xl p-6 transition duration-300 flex flex-col">
                        <div class="w-14 h-14 rounded-2xl bg-amber-600 text-white flex items-center justify-center text-xl mb-5"><i class="fa-solid fa-plus"></i></div>
                        <h3 class="font-bold text-base text-green-950">Add New Tool</h3>
                        <p class="text-xs text-stone-500 mt-1.5">List farming tools, machines, seeds & more</p>
                        <span class="mt-4 text-amber-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">Get started <i class="fa-solid fa-arrow-right"></i></span>
                    </a>

                    <a href="vendor-tools.php" class="group bg-orange-50 hover:bg-orange-100 border border-orange-100 rounded-2xl p-6 transition duration-300 flex flex-col">
                        <div class="w-14 h-14 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-xl mb-5"><i class="fa-solid fa-box-open"></i></div>
                        <h3 class="font-bold text-base text-green-950">My Tools</h3>
                        <p class="text-xs text-stone-500 mt-1.5">View, edit or remove your listed items</p>
                        <span class="mt-4 text-orange-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">Manage <i class="fa-solid fa-arrow-right"></i></span>
                    </a>

                    <a href="vendor_earnings.php" class="group bg-orange-50 hover:bg-orange-100 border border-orange-100 rounded-2xl p-6 transition duration-300 flex flex-col">
                        <div class="w-14 h-14 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-xl mb-5">
                            <i class="fa-solid fa-chart-line"></i>
                        </div>
                        <h3 class="font-bold text-base text-green-950">Earnings Report</h3>
                        <p class="text-xs text-stone-500 mt-1.5">View your sales analytics and revenue charts</p>
                        <span class="mt-4 text-orange-700 text-xs font-bold flex items-center gap-1 group-hover:gap-2 transition-all">
                            View Report <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </a>
                </div>

                <!-- Recent Orders Section -->
                <?php if (count($recentOrders) > 0): ?>
                <div class="mt-8">
                    <h3 class="font-bold text-green-950 mb-4">Recent Orders</h3>
                    <div class="space-y-3">
                        <?php foreach ($recentOrders as $order): ?>
                        <div class="flex items-center justify-between bg-stone-50 rounded-xl px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-stone-800">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
                                <p class="text-xs text-stone-400"><?php echo date('d M Y', strtotime($order['created_at'])); ?> • <?php echo $order['item_count']; ?> item(s)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-700">৳<?php echo number_format($order['final_amount'], 0); ?></p>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full 
                                    <?php echo $order['order_status'] === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'; ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-4">
                        <a href="vendor-orders.php" class="text-amber-600 text-sm font-semibold hover:underline">View All Orders →</a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Recent Tools - FIXED IMAGE PATHS -->
                <?php if (count($recentTools) > 0): ?>
                <div class="mt-8">
                    <h3 class="font-bold text-green-950 mb-4">Recent Listings</h3>
                    <div class="space-y-3">
                        <?php foreach ($recentTools as $tool): ?>
                        <div class="flex items-center justify-between bg-stone-50 rounded-xl px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo (!empty($tool['image_path']) && file_exists($tool['image_path'])) ? htmlspecialchars($tool['image_path']) : 'asset/img/placeholder-product.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($tool['product_name']); ?>" 
                                     class="w-10 h-10 rounded-lg object-cover"
                                     onerror="this.src='asset/img/placeholder-product.jpg'">
                                <div>
                                    <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($tool['product_name']); ?></p>
                                    <p class="text-xs text-stone-400">৳<?php echo number_format($tool['price'], 0); ?> • <?php echo $tool['condition_status']; ?></p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full <?php echo $tool['status'] === 'active' ? 'bg-green-100 text-green-700' : 'bg-stone-100 text-stone-500'; ?>">
                                <?php echo ucfirst($tool['status']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tips -->
            <div class="bg-gradient-to-br from-amber-800 to-amber-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
                <div class="absolute -right-4 -bottom-4 text-[90px] text-white/10"><i class="fa-solid fa-lightbulb"></i></div>
                <p class="text-xs font-bold uppercase tracking-widest text-amber-300 mb-2">Vendor Tips</p>
                <h3 class="font-bold text-base leading-snug mb-2">Sell smarter, grow faster!</h3>
                <ul class="space-y-2 text-xs text-amber-100/80 leading-relaxed mb-4">
                    <li>• Use clear photos with good lighting</li>
                    <li>• Mention brand and condition clearly</li>
                    <li>• Update stock regularly</li>
                    <li>• Offer competitive prices</li>
                </ul>
                <a href="add-tool.php" class="inline-flex items-center gap-2 bg-white text-amber-900 font-bold text-xs px-4 py-2.5 rounded-xl hover:bg-amber-50 transition">
                    <i class="fa-solid fa-plus"></i> Add Tool Now
                </a>
            </div>
        </div>

    </main>

    <?php include('footer.php'); ?>  <!-- FIXED: removed ../ -->
</body>
</html>