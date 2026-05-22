<?php
session_start();

if (!isset($_SESSION['vendor_id'])) {
    header("Location: vendorLogin.php");
    exit();
}

require_once 'config.php';

$vendorId = $_SESSION['vendor_id'];
$vendorName = $_SESSION['vendor_name'];

// Get all tool orders for this vendor
$ordersStmt = $conn->prepare("
    SELECT DISTINCT 
        o.id, 
        o.order_number, 
        o.order_status, 
        o.payment_method,
        o.final_amount, 
        o.delivery_address,
        o.phone,
        o.created_at,
        COUNT(toi.id) as item_count,
        SUM(toi.total_price) as vendor_total
    FROM tool_orders o
    JOIN tool_order_items toi ON o.id = toi.order_id
    WHERE toi.vendor_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$ordersStmt->bind_param("i", $vendorId);
$ordersStmt->execute();
$orders = $ordersStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$ordersStmt->close();

// Get order items for each order
foreach ($orders as &$order) {
    $itemsStmt = $conn->prepare("
        SELECT toi.*, tp.product_name, tp.brand, tp.condition_status, tp.image_path
        FROM tool_order_items toi
        JOIN tool_products tp ON toi.tool_id = tp.id
        WHERE toi.order_id = ? AND toi.vendor_id = ?
    ");
    $itemsStmt->bind_param("ii", $order['id'], $vendorId);
    $itemsStmt->execute();
    $order['items'] = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $itemsStmt->close();
}

// Calculate stats
$totalOrders = count($orders);
$pendingOrders = count(array_filter($orders, fn($o) => $o['order_status'] === 'pending'));
$completedOrders = count(array_filter($orders, fn($o) => $o['order_status'] === 'completed'));
$totalEarnings = array_sum(array_column($orders, 'vendor_total'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Tool Orders — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#f2f5f0] overflow-x-hidden min-h-screen">

    <?php include('vendorNav.php'); ?>

    <main class="max-w-6xl mx-auto px-5 md:px-8 pt-24 pb-14">

        <div class="mb-8">
            <a href="vendorDash.php" class="inline-flex items-center gap-2 text-amber-600 text-sm font-semibold hover:text-amber-800 transition mb-4">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-green-950">Tool Orders Received</h1>
            <p class="text-stone-400 text-sm mt-1">Manage and track all tool orders from customers</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700"><?php echo $totalOrders; ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Tool Orders</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-amber-500"><?php echo $pendingOrders; ?></p>
                <p class="text-stone-400 text-xs mt-1">Pending</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-emerald-600"><?php echo $completedOrders; ?></p>
                <p class="text-stone-400 text-xs mt-1">Completed</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700">৳<?php echo number_format($totalEarnings, 0); ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Earnings</p>
            </div>
        </div>

        <?php if (count($orders) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden hover:shadow-md transition duration-200">
                    
                    <!-- Order Header -->
                    <div class="flex flex-wrap items-center justify-between px-5 py-3 bg-stone-50/60 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">
                                #<?php echo htmlspecialchars($order['order_number']); ?>
                            </span>
                            <span class="text-stone-300 text-xs">•</span>
                            <span class="text-xs text-stone-400">
                                <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold px-3 py-1 rounded-full
                                <?php
                                switch($order['order_status']) {
                                    case 'pending':    echo 'bg-amber-100 text-amber-700'; break;
                                    case 'processing': echo 'bg-blue-100 text-blue-700'; break;
                                    case 'completed':  echo 'bg-green-100 text-green-700'; break;
                                    case 'cancelled':  echo 'bg-red-100 text-red-600'; break;
                                    default:           echo 'bg-stone-100 text-stone-500';
                                }
                                ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                            <span class="text-stone-300 text-xs">•</span>
                            <span class="text-xs font-semibold text-stone-600"><?php echo strtoupper($order['payment_method']); ?></span>
                        </div>
                    </div>

                    <!-- Order Body -->
                    <div class="p-5">
                        <!-- Order Items -->
                        <div class="space-y-3 mb-4">
                            <?php foreach ($order['items'] as $item): ?>
                            <div class="flex items-center gap-3 border-b border-stone-50 pb-3 last:border-0">
                                <?php 
                                // FIXED: Proper image path handling
                                $imagePath = 'asset/img/placeholder-product.jpg';
                                if (!empty($item['image_path'])) {
                                    // Check if path already has asset/ or uploads/ prefix
                                    if (strpos($item['image_path'], 'asset/') === 0 || strpos($item['image_path'], 'uploads/') === 0) {
                                        $imagePath = $item['image_path'];
                                    } else {
                                        $imagePath = 'uploads/tools/' . $item['image_path'];
                                    }
                                }
                                ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     class="w-12 h-12 rounded-lg object-cover"
                                     onerror="this.src='asset/img/placeholder-product.jpg'">
                                <div class="flex-1">
                                    <p class="font-semibold text-stone-800 text-sm"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                    <p class="text-xs text-stone-400">
                                        Qty: <?php echo $item['quantity']; ?> × ৳<?php echo number_format($item['unit_price'], 0); ?>
                                        <?php if (!empty($item['brand'])): ?> • Brand: <?php echo htmlspecialchars($item['brand']); ?><?php endif; ?>
                                    </p>
                                    <p class="text-xs text-stone-400">
                                        Condition: <span class="font-semibold <?php echo $item['condition_status'] === 'new' ? 'text-green-600' : 'text-orange-600'; ?>">
                                            <?php echo ucfirst($item['condition_status']); ?>
                                        </span>
                                    </p>
                                </div>
                                <p class="font-bold text-amber-700 text-sm ml-4">৳<?php echo number_format($item['total_price'], 0); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Delivery Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 pt-3 border-t border-stone-100">
                            <div class="flex items-start gap-2 text-sm">
                                <i class="fa-solid fa-location-dot text-amber-500 text-xs mt-0.5"></i>
                                <div>
                                    <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider">Delivery Address</p>
                                    <p class="text-xs text-stone-600"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-2 text-sm">
                                <i class="fa-solid fa-phone text-amber-500 text-xs mt-0.5"></i>
                                <div>
                                    <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider">Customer Phone</p>
                                    <p class="text-xs text-stone-600"><?php echo htmlspecialchars($order['phone']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Total & Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-stone-100 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <p class="text-xs text-stone-400">Your Earnings (this order)</p>
                                <p class="text-xl font-bold text-amber-700">৳<?php echo number_format($order['vendor_total'], 0); ?></p>
                            </div>
                            
                            <?php if ($order['order_status'] === 'pending'): ?>
                            <div class="flex gap-2">
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'processing')"
                                    class="text-xs bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                                    <i class="fa-solid fa-check"></i> Mark Processing
                                </button>
                                <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'cancelled')"
                                    class="text-xs bg-red-500 hover:bg-red-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                                    <i class="fa-solid fa-times"></i> Cancel
                                </button>
                            </div>
                            <?php elseif ($order['order_status'] === 'processing'): ?>
                            <button onclick="updateOrderStatus(<?php echo $order['id']; ?>, 'completed')"
                                class="text-xs bg-green-500 hover:bg-green-600 text-white font-semibold px-4 py-2 rounded-lg transition">
                                <i class="fa-solid fa-check-double"></i> Mark Completed
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">No tool orders yet</h3>
                <p class="text-stone-400 text-sm mb-6 max-w-xs mx-auto">
                    When customers order your tools, they'll appear here.
                </p>
                <a href="add-tool.php" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-plus"></i> Add More Tools
                </a>
            </div>
        <?php endif; ?>

    </main>

    <?php include('footer.php'); ?>

    <script>
        function updateOrderStatus(orderId, status) {
            if (!confirm('Update order status to ' + status.toUpperCase() + '?')) return;
            
            fetch('update-order-status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update status');
                }
            })
            .catch(() => alert('Something went wrong'));
        }
    </script>
</body>
</html>