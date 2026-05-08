
<?php
require_once '../config/session.php';
require_once '../config/database.php';

Session::start();

if (!isset($_SESSION['farmer_id'])) {
    header('Location: ../farmerLogin.php');
    exit;
}

$farmerId = $_SESSION['farmer_id'];
$farmerName = $_SESSION['farmer_name'];

// Get orders for this farmer's products
$conn = Database::getConnection();
$stmt = $conn->prepare(
    "SELECT oi.*, o.order_number, o.order_status, o.payment_method, o.created_at, o.delivery_address, o.phone,
            c.full_name as consumer_name
     FROM order_items oi
     JOIN orders o ON oi.order_id = o.id
     JOIN consumers c ON o.consumer_id = c.id
     WHERE oi.farmer_id = ?
     ORDER BY o.created_at DESC
     LIMIT 50"
);
$stmt->bind_param("i", $farmerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="stylesheet" href="../asset/css/marketplace.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-50 min-h-screen">
    <?php include('../navbar2.php'); ?>
    <div class="pt-24 pb-12 px-4" style="max-width:900px;margin:0 auto;">
        <a href="../farmerDashboard.php" class="text-green-600 text-sm mb-4 inline-block">← Dashboard</a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">📦 Orders (<?php echo count($orders); ?>)</h1>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
            <div class="bg-white rounded-2xl p-5 mb-3 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <span class="text-xs text-stone-400">Order #<?php echo $order['order_number']; ?></span>
                        <h4 class="font-semibold"><?php echo htmlspecialchars($order['product_name']); ?> × <?php echo $order['quantity']; ?></h4>
                    </div>
                    <span class="text-green-600 font-bold">৳<?php echo number_format($order['total_price'], 0); ?></span>
                </div>
                <div class="text-xs text-stone-500 space-y-1">
                    <p>👤 Consumer: <?php echo htmlspecialchars($order['consumer_name']); ?></p>
                    <p>📞 <?php echo htmlspecialchars($order['phone']); ?></p>
                    <p>📍 <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p>💰 <?php echo strtoupper($order['payment_method']); ?></p>
                    <p>Status: <span class="font-semibold text-amber-600"><?php echo ucfirst($order['order_status']); ?></span></p>
                    <p class="text-stone-400"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fa-solid fa-inbox text-5xl text-stone-300 mb-4"></i>
                <p class="text-stone-500">No orders yet</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>