<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}
require_once 'config.php';

// Counts
$farmerCount   = $conn->query("SELECT COUNT(*) as c FROM farmers")->fetch_assoc()['c'];
$consumerCount = $conn->query("SELECT COUNT(*) as c FROM consumers")->fetch_assoc()['c'];
$vendorCount   = $conn->query("SELECT COUNT(*) as c FROM vendors")->fetch_assoc()['c'];
$productCount  = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'];
$toolCount     = $conn->query("SELECT COUNT(*) as c FROM tool_products")->fetch_assoc()['c'];
$orderCount    = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$toolOrderCount= $conn->query("SELECT COUNT(*) as c FROM tool_orders")->fetch_assoc()['c'];
$messageCount  = $conn->query("SELECT COUNT(*) as c FROM contact_messages")->fetch_assoc()['c'];

// Revenue from farmer products (5% commission)
$farmerRev = 0;
$revQuery = "SELECT SUM(oi.total_price * 0.05) as revenue 
             FROM order_items oi 
             JOIN orders o ON oi.order_id = o.id 
             WHERE o.order_status NOT IN ('cancelled')";
$result = $conn->query($revQuery);
if ($result && $row = $result->fetch_assoc()) {
    $farmerRev = $row['revenue'] ?? 0;
}

// Revenue from tool orders (5% commission)
$toolRev = 0;
$toolRevQuery = "SELECT SUM(toi.total_price * 0.05) as revenue 
                 FROM tool_order_items toi 
                 JOIN tool_orders to2 ON toi.order_id = to2.id 
                 WHERE to2.order_status NOT IN ('cancelled')";
$result2 = $conn->query($toolRevQuery);
if ($result2 && $row2 = $result2->fetch_assoc()) {
    $toolRev = $row2['revenue'] ?? 0;
}

$totalRevenue = $farmerRev + $toolRev;

// Monthly revenue (last 6 months)
$monthlyData = [];
$monthQuery = "
    SELECT 
        DATE_FORMAT(o.created_at, '%Y-%m') as month,
        SUM(oi.total_price * 0.05) as revenue
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE o.order_status NOT IN ('cancelled')
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    UNION ALL
    SELECT 
        DATE_FORMAT(to2.created_at, '%Y-%m') as month,
        SUM(toi.total_price * 0.05) as revenue
    FROM tool_order_items toi
    JOIN tool_orders to2 ON toi.order_id = to2.id
    WHERE to2.order_status NOT IN ('cancelled')
    GROUP BY DATE_FORMAT(to2.created_at, '%Y-%m')
    ORDER BY month DESC LIMIT 6
";
$monthRes = $conn->query($monthQuery);
if ($monthRes) {
    while ($row = $monthRes->fetch_assoc()) {
        $monthlyData[$row['month']] = ($monthlyData[$row['month']] ?? 0) + $row['revenue'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-100 min-h-screen">
    <?php include('admin_nav.php'); ?>

    <div class="p-6">
        <div class="bg-gradient-to-r from-green-800 to-green-700 rounded-2xl p-6 text-white mb-6">
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
            <p class="text-green-200 text-sm mt-1">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-green-700"><?php echo $farmerCount; ?></p><p class="text-xs text-stone-500">Farmers</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-green-700"><?php echo $consumerCount; ?></p><p class="text-xs text-stone-500">Consumers</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-green-700"><?php echo $vendorCount; ?></p><p class="text-xs text-stone-500">Vendors</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-emerald-600"><?php echo $productCount; ?></p><p class="text-xs text-stone-500">Products</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-amber-600"><?php echo $toolCount; ?></p><p class="text-xs text-stone-500">Tools</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-blue-600"><?php echo $orderCount + $toolOrderCount; ?></p><p class="text-xs text-stone-500">Total Orders</p></div>
            <div class="bg-white rounded-xl p-4 shadow-sm text-center"><p class="text-2xl font-bold text-purple-600"><?php echo $messageCount; ?></p><p class="text-xs text-stone-500">Messages</p></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-green-950 mb-4">💰 Platform Revenue (5% Commission)</h2>
                <p class="text-3xl font-extrabold text-green-700">৳<?php echo number_format($totalRevenue, 2); ?></p>
                <div class="flex gap-4 mt-3 text-sm">
                    <div><span class="font-semibold">Farmer products:</span> ৳<?php echo number_format($farmerRev, 2); ?></div>
                    <div><span class="font-semibold">Tools:</span> ৳<?php echo number_format($toolRev, 2); ?></div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="font-bold text-green-950 mb-4">📊 Last 6 Months Revenue</h2>
                <?php if (count($monthlyData) > 0): ?>
                    <?php $maxRev = max($monthlyData); ?>
                    <div class="space-y-2">
                        <?php foreach ($monthlyData as $month => $rev): ?>
                        <div class="flex items-center gap-3">
                            <span class="text-xs w-20 text-stone-500"><?php echo date('M Y', strtotime($month . '-01')); ?></span>
                            <div class="flex-1 bg-stone-100 rounded-full h-5 overflow-hidden">
                                <div class="bg-green-600 h-full rounded-full" style="width: <?php echo min(100, ($rev / max(1, $maxRev)) * 100); ?>%"></div>
                            </div>
                            <span class="text-xs font-bold text-green-700 w-20 text-right">৳<?php echo number_format($rev, 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-stone-400 text-center py-4">No revenue data yet</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-6 text-center text-xs text-stone-400">
            <i class="fa-regular fa-clock"></i> Last login: <?php echo date('d M Y, h:i A'); ?>
        </div>
    </div>
</body>
</html>