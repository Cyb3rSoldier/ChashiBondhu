
<?php
require_once '../config/session.php';
require_once '../config/database.php';

Session::start();

if (!isset($_SESSION['consumer_id'])) {
    header('Location: ../consumerLogin.php');
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$consumerName = $_SESSION['consumer_name'];

$conn = Database::getConnection();
$stmt = $conn->prepare("SELECT * FROM orders WHERE consumer_id = ? ORDER BY created_at DESC LIMIT 30");
$stmt->bind_param("i", $consumerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Check for success message
$successOrder = isset($_SESSION['order_success']) ? $_SESSION['order_success'] : null;
if ($successOrder) unset($_SESSION['order_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-50 min-h-screen">
    <?php include('../navbar2.php'); ?>
    <div class="pt-24 pb-12 px-4" style="max-width:700px;margin:0 auto;">
        <a href="../consumerDashboard.php" class="text-green-600 text-sm mb-4 inline-block">← Dashboard</a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">📋 My Orders</h1>

        <?php if ($successOrder): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 text-center">
                <p class="font-bold">🎉 Order Placed Successfully!</p>
                <p class="text-sm">Order #<?php echo $successOrder; ?></p>
            </div>
        <?php endif; ?>

        <?php if (count($orders) > 0): ?>
            <?php foreach ($orders as $order): ?>
            <div class="bg-white rounded-2xl p-5 mb-3 shadow-sm">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-stone-400">#<?php echo $order['order_number']; ?></span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full 
                        <?php echo $order['order_status'] == 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'; ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                    </span>
                </div>
                <div class="text-sm">
                    <p class="font-bold text-green-700">৳<?php echo number_format($order['final_amount'], 0); ?></p>
                    <p class="text-stone-500 text-xs">Payment: <?php echo strtoupper($order['payment_method']); ?></p>
                    <p class="text-stone-500 text-xs">📍 <?php echo htmlspecialchars($order['delivery_address']); ?></p>
                    <p class="text-stone-400 text-xs mt-1"><?php echo date('d M Y', strtotime($order['created_at'])); ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fa-solid fa-receipt text-5xl text-stone-300 mb-4"></i>
                <p class="text-stone-500 mb-4">No orders yet</p>
                <a href="../index.php#marketplace" class="text-green-600 font-semibold">Start Shopping →</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>