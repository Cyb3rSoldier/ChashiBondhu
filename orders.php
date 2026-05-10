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
<style> body { font-family: 'Roboto', sans-serif; } </style>

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

</body>
</html>