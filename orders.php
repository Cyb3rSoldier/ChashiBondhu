<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit;
}

$farmerId   = $_SESSION['farmer_id'];
$farmerName = $_SESSION['farmer_name'];

$stmt = $conn->prepare("
    SELECT oi.*, o.order_number, o.order_status, o.payment_method, o.created_at,
           o.delivery_address, o.phone, c.full_name AS consumer_name
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN consumers c ON o.consumer_id = c.id
    WHERE oi.farmer_id = ?
    ORDER BY o.created_at DESC
    LIMIT 50
");
$stmt->bind_param("i", $farmerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Stats
$pending   = array_filter($orders, fn($o) => $o['order_status'] === 'pending');
$completed = array_filter($orders, fn($o) => $o['order_status'] === 'completed');
$totalEarned = array_sum(array_column($orders, 'total_price'));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmers Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
</style>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('farmerNav.php'); ?>

    <main class="max-w-4xl mx-auto px-5 pt-24 pb-16">

        <!-- Header -->
        <div class="mb-8">
            <a href="farmerDash.php" class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-4">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-green-950">Incoming Orders</h1>
            <p class="text-stone-400 text-sm mt-1"><?php echo count($orders); ?> order<?php echo count($orders) !== 1 ? 's' : ''; ?> received</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700"><?php echo count($orders); ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Orders</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-amber-500"><?php echo count($pending); ?></p>
                <p class="text-stone-400 text-xs mt-1">Pending</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-emerald-600"><?php echo count($completed); ?></p>
                <p class="text-stone-400 text-xs mt-1">Completed</p>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                <p class="text-2xl font-bold text-green-700">৳<?php echo number_format($totalEarned, 0); ?></p>
                <p class="text-stone-400 text-xs mt-1">Total Earned</p>
            </div>
        </div>

        <!-- Orders List -->
        <?php if (count($orders) > 0): ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden hover:shadow-md transition duration-200">

                        <!-- Order Header -->
                        <div class="flex items-center justify-between px-5 py-3 border-b border-stone-50 bg-stone-50/60">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold text-stone-400 uppercase tracking-wider">
                                    #<?php echo $order['order_number']; ?>
                                </span>
                                <span class="text-xs text-stone-300">•</span>
                                <span class="text-xs text-stone-400">
                                    <?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?>
                                </span>
                            </div>
                            <span class="text-xs font-bold px-3 py-1 rounded-full
                            <?php
                            switch ($order['order_status']) {
                                case 'pending':
                                    echo 'bg-amber-100 text-amber-700';
                                    break;
                                case 'processing':
                                    echo 'bg-blue-100 text-blue-700';
                                    break;
                                case 'completed':
                                    echo 'bg-green-100 text-green-700';
                                    break;
                                case 'cancelled':
                                    echo 'bg-red-100 text-red-600';
                                    break;
                                default:
                                    echo 'bg-stone-100 text-stone-500';
                            }
                            ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>

                        <!-- Order Body -->
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4 mb-4">
                                <div class="flex-1">
                                    <h3 class="font-bold text-green-950 text-base">
                                        <?php echo htmlspecialchars($order['product_name']); ?>
                                    </h3>
                                    <p class="text-stone-400 text-xs mt-0.5">
                                        Qty: <span class="font-semibold text-stone-600"><?php echo $order['quantity']; ?></span>
                                        &nbsp;•&nbsp;
                                        Unit price: <span class="font-semibold text-stone-600">৳<?php echo number_format($order['unit_price'], 0); ?></span>
                                    </p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-green-700 font-bold text-lg">৳<?php echo number_format($order['total_price'], 0); ?></p>
                                    <p class="text-xs text-stone-400"><?php echo strtoupper($order['payment_method']); ?></p>
                                </div>
                            </div>

                            <!-- Consumer + Delivery Info -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="bg-stone-50 rounded-xl px-4 py-3">
                                    <p class="text-xs text-stone-400 mb-1 font-semibold uppercase tracking-wider">Consumer</p>
                                    <p class="text-sm font-semibold text-stone-800 flex items-center gap-2">
                                        <i class="fa-solid fa-user text-green-500 text-xs"></i>
                                        <?php echo htmlspecialchars($order['consumer_name']); ?>
                                    </p>
                                    <p class="text-xs text-stone-500 mt-1 flex items-center gap-2">
                                        <i class="fa-solid fa-phone text-green-500 text-xs"></i>
                                        <?php echo htmlspecialchars($order['phone']); ?>
                                    </p>
                                </div>
                                <div class="bg-stone-50 rounded-xl px-4 py-3">
                                    <p class="text-xs text-stone-400 mb-1 font-semibold uppercase tracking-wider">Delivery Address</p>
                                    <p class="text-xs text-stone-600 leading-relaxed flex items-start gap-2">
                                        <i class="fa-solid fa-location-dot text-green-500 text-xs mt-0.5 shrink-0"></i>
                                        <?php echo htmlspecialchars($order['delivery_address']); ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Actions — only show if order is pending -->
                            <?php if ($order['order_status'] === 'pending'): ?>
                                <div class="flex gap-3 mt-4">
                                    <button
                                        onclick="updateOrder(<?php echo $order['order_id']; ?>, 'processing', this)"
                                        class="flex-1 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-truck-fast"></i> Confirm & Process
                                    </button>
                                    <button
                                        onclick="updateOrder(<?php echo $order['order_id']; ?>, 'completed', this)"
                                        class="flex-1 bg-green-700 hover:bg-green-600 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-circle-check"></i> Mark Delivered
                                    </button>
                                    <button
                                        onclick="updateOrder(<?php echo $order['order_id']; ?>, 'cancelled', this)"
                                        class="flex-1 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-xmark"></i> Cancel
                                    </button>
                                </div>
                            <?php elseif ($order['order_status'] === 'processing'): ?>
                                <div class="flex gap-3 mt-4">
                                    <button
                                        onclick="updateOrder(<?php echo $order['order_id']; ?>, 'completed', this)"
                                        class="flex-1 bg-green-700 hover:bg-green-600 text-white text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-circle-check"></i> Mark as Delivered
                                    </button>
                                    <button
                                        onclick="updateOrder(<?php echo $order['order_id']; ?>, 'cancelled', this)"
                                        class="flex-1 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 text-xs font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-xmark"></i> Cancel
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">No orders yet</h3>
                <p class="text-stone-400 text-sm mb-6 max-w-xs mx-auto">
                    When consumers order your products, they'll appear here.
                </p>
                <a href="myProducts.php"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-store"></i> View My Products
                </a>
            </div>
        <?php endif; ?>

    </main>

    <?php include('footer.php'); ?>

    <script>
        function updateOrder(orderId, status, btn) {
            const labels = {
                processing: 'Confirm & Process',
                completed: 'Mark Delivered',
                cancelled: 'Cancel'
            };

            const confirmMsg = {
                processing: 'Confirm this order and start processing?',
                completed: 'Mark this order as delivered?',
                cancelled: 'Cancel this order? Stock will be restored.'
            };

            if (!confirm(confirmMsg[status])) return;

            // Disable all buttons in the same row
            const row = btn.closest('div');
            row.querySelectorAll('button').forEach(b => b.disabled = true);
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Updating...';

            const form = new FormData();
            form.append('order_id', orderId);
            form.append('status', status);

            fetch('confirmOrder.php', {
                    method: 'POST',
                    body: form
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge
                        const card = btn.closest('.bg-white');
                        const badge = card.querySelector('.rounded-full');

                        const badgeClasses = {
                            processing: 'bg-blue-100 text-blue-700',
                            completed: 'bg-green-100 text-green-700',
                            cancelled: 'bg-red-100 text-red-600'
                        };

                        badge.className = 'text-xs font-bold px-3 py-1 rounded-full ' + badgeClasses[status];
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);

                        // Remove action buttons
                        row.remove();

                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message || 'Update failed', 'error');
                        row.querySelectorAll('button').forEach(b => b.disabled = false);
                        btn.innerHTML = '<i class="fa-solid fa-' + (status === 'cancelled' ? 'xmark' : 'circle-check') + '"></i> ' + labels[status];
                    }
                })
                .catch(() => {
                    showToast('Something went wrong', 'error');
                    row.querySelectorAll('button').forEach(b => b.disabled = false);
                });
        }

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