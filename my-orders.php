<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['consumer_id'])) {
    header('Location: consumerLogin.php');
    exit;
}

$consumerId   = $_SESSION['consumer_id'];
$consumerName = $_SESSION['consumer_name'];

$successOrder = $_SESSION['order_success'] ?? null;
if ($successOrder) unset($_SESSION['order_success']);

// Fetch orders with item count
$stmt = $conn->prepare("
    SELECT o.*,
           COUNT(oi.id) AS item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.consumer_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$pending   = array_filter($orders, fn($o) => $o['order_status'] === 'pending');
$completed = array_filter($orders, fn($o) => $o['order_status'] === 'completed');
$totalSpent = array_sum(array_column($orders, 'final_amount'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('consumerNav.php'); ?>

    <main class="max-w-3xl mx-auto px-5 pt-24 pb-16">

        <!-- Header -->
        <div class="mb-8">
            <a href="index.php" class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-4">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Home
            </a>
            <h1 class="text-2xl font-bold text-green-950">My Orders</h1>
            <p class="text-stone-400 text-sm mt-1"><?php echo count($orders); ?> order<?php echo count($orders) !== 1 ? 's' : ''; ?> placed</p>
        </div>

        <!-- Success Alert -->
        <?php if ($successOrder): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                </div>
                <div>
                    <p class="font-bold text-sm">Order Placed Successfully!</p>
                    <p class="text-xs text-green-600 mt-0.5">Order #<?php echo htmlspecialchars($successOrder); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700"><?php echo count($orders); ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Orders</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-amber-500"><?php echo count($pending); ?></p>
                <p class="text-stone-400 text-xs mt-1">Pending</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700">৳<?php echo number_format($totalSpent, 0); ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Spent</p>
            </div>
        </div>

        <!-- Orders -->
        <?php if (count($orders) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden hover:shadow-md transition duration-200">

                    <!-- Order Header -->
                    <div class="flex items-center justify-between px-5 py-3 bg-stone-50/60 border-b border-stone-100">
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">
                                #<?php echo $order['order_number']; ?>
                            </span>
                            <span class="text-stone-300 text-xs">•</span>
                            <span class="text-xs text-stone-400">
                                <?php echo date('d M Y', strtotime($order['created_at'])); ?>
                            </span>
                        </div>
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
                    </div>

                    <!-- Order Body -->
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-stone-400">
                                    <?php echo $order['item_count']; ?> item<?php echo $order['item_count'] !== 1 ? 's' : ''; ?>
                                    &nbsp;•&nbsp; <?php echo strtoupper($order['payment_method']); ?>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-700 font-bold text-lg">৳<?php echo number_format($order['final_amount'], 0); ?></p>
                                <p class="text-xs text-stone-400">incl. ৳<?php echo number_format($order['delivery_fee'], 0); ?> delivery</p>
                            </div>
                        </div>

                        <!-- Delivery Address -->
                        <div class="bg-stone-50 rounded-xl px-4 py-3 flex items-start gap-3 mb-4">
                            <i class="fa-solid fa-location-dot text-green-500 text-sm mt-0.5 shrink-0"></i>
                            <div>
                                <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider mb-0.5">Delivery Address</p>
                                <p class="text-xs text-stone-600 leading-relaxed"><?php echo htmlspecialchars($order['delivery_address']); ?></p>
                            </div>
                        </div>

                        <!-- View Items -->
                        <button onclick="toggleItems(<?php echo $order['id']; ?>)"
                            class="w-full text-xs text-green-700 font-semibold border border-green-100 hover:bg-green-50 py-2 rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-list text-xs"></i>
                            <span id="toggle-text-<?php echo $order['id']; ?>">View Items</span>
                            <i class="fa-solid fa-chevron-down text-xs" id="toggle-icon-<?php echo $order['id']; ?>"></i>
                        </button>

                        <!-- Items (hidden by default) -->
                        <div id="items-<?php echo $order['id']; ?>" class="hidden mt-3">
                            <?php
                            $iStmt = $conn->prepare("
                                SELECT oi.*, f.full_name AS farmer_name
                                FROM order_items oi
                                LEFT JOIN farmers f ON oi.farmer_id = f.id
                                WHERE oi.order_id = ?
                            ");
                            $iStmt->bind_param("i", $order['id']);
                            $iStmt->execute();
                            $items = $iStmt->get_result()->fetch_all(MYSQLI_ASSOC);
                            $iStmt->close();
                            ?>
                            <div class="space-y-2 mt-2">
                                <?php foreach ($items as $item): ?>
                                <div class="flex items-center justify-between bg-stone-50 rounded-xl px-4 py-3">
                                    <div>
                                        <p class="text-sm font-semibold text-stone-800"><?php echo htmlspecialchars($item['product_name']); ?></p>
                                        <p class="text-xs text-stone-400 mt-0.5">
                                            By <?php echo htmlspecialchars($item['farmer_name'] ?? 'Unknown'); ?>
                                            &nbsp;•&nbsp; Qty: <?php echo $item['quantity']; ?>
                                            &nbsp;•&nbsp; ৳<?php echo number_format($item['unit_price'], 0); ?> each
                                        </p>
                                    </div>
                                    <span class="text-green-700 font-bold text-sm shrink-0 ml-4">
                                        ৳<?php echo number_format($item['total_price'], 0); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-receipt text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">No orders yet</h3>
                <p class="text-stone-400 text-sm mb-6 max-w-xs mx-auto">
                    Start shopping fresh produce directly from farmers.
                </p>
                <a href="index.php#marketplace"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-store"></i> Browse Marketplace
                </a>
            </div>
        <?php endif; ?>

    </main>

    <?php include('footer.php'); ?>

    <script>
        function toggleItems(orderId) {
            const items = document.getElementById('items-' + orderId);
            const text  = document.getElementById('toggle-text-' + orderId);
            const icon  = document.getElementById('toggle-icon-' + orderId);

            if (items.classList.contains('hidden')) {
                items.classList.remove('hidden');
                text.textContent = 'Hide Items';
                icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
            } else {
                items.classList.add('hidden');
                text.textContent = 'View Items';
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
            }
        }
    </script>

</body>
</html>