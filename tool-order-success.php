
<?php
session_start();

if (!isset($_SESSION['tool_order_success'])) {
    header('Location: index.php');
    exit();
}

$orderNumber = $_SESSION['tool_order_success'];
unset($_SESSION['tool_order_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool Order Placed — ChashiBondhu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen flex items-center justify-center px-5">
    <div class="bg-white rounded-3xl p-10 shadow-sm border border-stone-100 text-center max-w-md w-full">
        <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-5">
            <i class="fa-solid fa-circle-check text-amber-600 text-4xl"></i>
        </div>
        <h1 class="text-2xl font-bold text-green-950 mb-2">Tool Order Placed!</h1>
        <p class="text-stone-500 text-sm mb-4">Your tool order has been successfully placed.</p>
        <div class="bg-amber-50 border border-amber-100 rounded-2xl px-5 py-3 mb-6 inline-block">
            <p class="text-xs text-stone-400 mb-1">Order Number</p>
            <p class="font-bold text-amber-700 text-lg"><?php echo htmlspecialchars($orderNumber); ?></p>
        </div>
        <div class="flex flex-col gap-3">
            <a href="vendor-orders.php" class="bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-xl transition text-sm text-center">
                Track Order
            </a>
            <a href="index.php#tools-marketplace" class="border border-amber-200 text-amber-700 hover:bg-amber-50 font-bold py-3 rounded-xl transition text-sm text-center">
                Continue Shopping Tools
            </a>
        </div>
    </div>
</body>
</html>