<?php
session_start();

if (!isset($_SESSION['order_success'])) {
    header('Location: index.php');
    exit();
}

$orderNumber = $_SESSION['order_success'];
unset($_SESSION['order_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed — ChashiBondhu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen flex items-center justify-center px-5">
    <div class="bg-white rounded-3xl p-10 shadow-sm border border-stone-100 text-center max-w-md w-full">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-circle-check text-green-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-green-950 mb-2">Order Placed!</h1>
        <p class="text-stone-500 text-sm mb-4">Your order has been successfully placed and is being processed.</p>
        <div class="bg-green-50 border border-green-100 rounded-2xl px-5 py-3 mb-6 inline-block">
            <p class="text-xs text-stone-400 mb-1">Order Number</p>
            <p class="font-bold text-green-700 text-lg"><?php echo htmlspecialchars($orderNumber); ?></p>
        </div>
        <div class="flex flex-col gap-3">
            <a href="my-orders.php" class="bg-green-700 hover:bg-green-600 text-white font-bold py-3 rounded-xl transition text-sm">
                <i class="fa-solid fa-bag-shopping mr-2"></i> View My Orders
            </a>
            <a href="index.php#marketplace" class="border border-green-200 text-green-700 hover:bg-green-50 font-bold py-3 rounded-xl transition text-sm">
                <i class="fa-solid fa-store mr-2"></i> Continue Shopping
            </a>
        </div>
    </div>
</body>
</html>