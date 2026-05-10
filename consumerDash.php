<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['consumer_id'])) {
    header("Location: consumerLogin.php");
    exit();
}

$name = $_SESSION['consumer_name'];
$email = $_SESSION['consumer_email'];

// Fetch all orders
$stmt = $conn->prepare("
    SELECT *
    FROM orders
    WHERE consumer_id = ?
");

$stmt->bind_param("i", $_SESSION['consumer_id']);
$stmt->execute();

$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();


// Count pending orders
$pendingStmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM orders
    WHERE consumer_id = ?
    AND order_status = 'pending'
");

$pendingStmt->bind_param("i", $_SESSION['consumer_id']);
$pendingStmt->execute();

$pendingResult = $pendingStmt->get_result()->fetch_assoc();

$pendingOrders = $pendingResult['total'];

$pendingStmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Dashboard — ChashiBondhu</title>

    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap"
        rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-[#f2f5f0] overflow-x-hidden min-h-screen">

    <?php include('consumerNav.php'); ?>

    <main class="max-w-7xl mx-auto px-5 md:px-8 pt-24 pb-14">

        <!-- HERO -->
        <div class="bg-gradient-to-br from-green-950 via-green-900 to-green-700
                    rounded-3xl p-8 md:p-12 text-white shadow-2xl relative overflow-hidden">

            <div class="absolute -right-8 -top-6 text-[220px] text-white/[0.04] select-none pointer-events-none">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>

            <div class="absolute bottom-6 right-52 text-[90px] text-white/[0.04] select-none pointer-events-none hidden lg:block">
                <i class="fa-solid fa-basket-shopping"></i>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-10">

                <!-- LEFT -->
                <div class="flex-1">

                    <p class="uppercase tracking-[4px] text-green-300 text-xs font-bold mb-3">
                        Consumer Dashboard
                    </p>

                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                        Welcome Back,<br>
                        <span class="text-green-300">
                            <?php echo htmlspecialchars($name); ?>
                        </span>
                    </h1>

                    <p class="text-green-100/75 mt-4 max-w-xl text-sm leading-7">
                        Discover fresh produce directly from trusted farmers
                        across Bangladesh and track all your orders easily.
                    </p>

                    <div class="flex flex-wrap gap-3 mt-7">

                        <a href="index.php#marketplace"
                            class="bg-white text-green-900 hover:bg-green-50 transition font-bold text-sm
                                   px-6 py-3 rounded-xl flex items-center gap-2 shadow-md">

                            <i class="fa-solid fa-store"></i>
                            Browse Products

                        </a>

                        <a href="my-orders.php"
                            class="border border-white/25 hover:bg-white/10 transition font-semibold text-sm
                                   px-6 py-3 rounded-xl flex items-center gap-2">

                            <i class="fa-solid fa-bag-shopping"></i>
                            My Orders

                        </a>

                    </div>

                </div>

                <!-- PROFILE CARD -->
                <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-7
                            w-full lg:w-[300px] shrink-0">

                    <div class="flex items-center gap-4">

                        <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <h3 class="font-bold text-lg leading-tight">
                                <?php echo htmlspecialchars($name); ?>
                            </h3>

                            <span class="text-xs text-green-300 font-semibold uppercase tracking-wider">
                                ✓ Verified Consumer
                            </span>
                        </div>

                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">

                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold">0</p>
                            <p class="text-xs text-green-200 mt-1">Orders</p>
                        </div>

                        <div class="bg-white/10 rounded-2xl p-4 text-center">
                            <p class="text-2xl font-bold">0</p>
                            <p class="text-xs text-green-200 mt-1">Wishlist</p>
                        </div>

                    </div>

                    <a href="consumer/profile.php"
                        class="mt-5 flex items-center justify-center gap-2 text-sm font-semibold
                               border border-white/20 rounded-xl py-2.5 hover:bg-white/10 transition">

                        <i class="fa-solid fa-pen-to-square text-green-300"></i>
                        Edit Profile

                    </a>

                    <a href="consumerLogout.php"
                        class="mt-4 flex items-center justify-center gap-2 text-sm font-semibold
                               border border-red-500/30 text-white rounded-xl py-2.5
                               bg-red-900/90 hover:text-red-200 transition">

                        <i class="fa-solid fa-right-from-bracket text-red-400"></i>
                        Logout

                    </a>

                </div>

            </div>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mt-7">

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-green-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">
                            Orders Placed
                        </p>

                        <p class="text-5xl font-extrabold text-green-950 mt-2 leading-none"><?php echo count($orders); ?></p>

                        <p class="text-xs text-stone-400 mt-2">
                            Total purchases
                        </p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-700
                                flex items-center justify-center text-xl">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-amber-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">
                            Pending Delivery
                        </p>

                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            <?php echo $pendingOrders; ?>
                        </h2>

                        <p class="text-xs text-stone-400 mt-2">
                            Awaiting delivery
                        </p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-700
                                flex items-center justify-center text-xl">

                        <i class="fa-solid fa-truck"></i>

                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-blue-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">
                            Saved Items
                        </p>

                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            0
                        </h2>

                        <p class="text-xs text-stone-400 mt-2">
                            Wishlist products
                        </p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-700
                                flex items-center justify-center text-xl">

                        <i class="fa-solid fa-heart"></i>

                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-purple-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider">
                            Farmers Connected
                        </p>

                        <h2 class="text-5xl font-extrabold text-green-950 mt-2 leading-none">
                            0
                        </h2>

                        <p class="text-xs text-stone-400 mt-2">
                            Trusted sellers
                        </p>
                    </div>

                    <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-700
                                flex items-center justify-center text-xl">

                        <i class="fa-solid fa-user-group"></i>

                    </div>
                </div>
            </div>

        </div>

        <!-- QUICK ACTIONS -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-7">

            <div class="lg:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-stone-100">

                <div class="mb-7">
                    <h2 class="text-2xl font-bold text-green-950">
                        Quick Actions
                    </h2>

                    <p class="text-stone-400 text-sm mt-0.5">
                        Shop smarter and faster
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    <!-- SHOP -->
                    <a href="index.php#marketplace"
                        class="group bg-green-50 hover:bg-green-100 border border-green-100
                               rounded-2xl p-6 transition duration-300 flex flex-col">

                        <div class="w-14 h-14 rounded-2xl bg-green-600 text-white
                                    flex items-center justify-center text-xl mb-5">

                            <i class="fa-solid fa-store"></i>

                        </div>

                        <h3 class="font-bold text-base text-green-950">
                            Browse Products
                        </h3>

                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            Explore fresh fruits, vegetables and farm products.
                        </p>

                    </a>

                    <!-- ORDERS -->
                    <a href="my-orders.php"
                        class="group bg-amber-50 hover:bg-amber-100 border border-amber-100
                               rounded-2xl p-6 transition duration-300 flex flex-col">

                        <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white
                                    flex items-center justify-center text-xl mb-5">

                            <i class="fa-solid fa-bag-shopping"></i>

                        </div>

                        <h3 class="font-bold text-base text-green-950">
                            My Orders
                        </h3>

                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            Track and manage all your purchases.
                        </p>

                    </a>

                    <!-- FARMERS -->
                    <a href="index.php#farmers"
                        class="group bg-blue-50 hover:bg-blue-100 border border-blue-100
                               rounded-2xl p-6 transition duration-300 flex flex-col">

                        <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white
                                    flex items-center justify-center text-xl mb-5">

                            <i class="fa-solid fa-user-group"></i>

                        </div>

                        <h3 class="font-bold text-base text-green-950">
                            Farmers
                        </h3>

                        <p class="text-xs text-stone-500 mt-1.5 leading-relaxed">
                            Connect directly with trusted local farmers.
                        </p>

                    </a>

                </div>

            </div>

            <!-- SIDE CARD -->
            <div class="bg-gradient-to-br from-green-800 to-green-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">

                <div class="absolute -right-4 -bottom-4 text-[90px] text-white/10">
                    <i class="fa-solid fa-leaf"></i>
                </div>

                <p class="text-xs font-bold uppercase tracking-widest text-green-300 mb-2">
                    Smart Shopping
                </p>

                <h3 class="font-bold text-base leading-snug mb-2">
                    Buy directly from farmers!
                </h3>

                <p class="text-xs text-green-100/80 leading-relaxed mb-4">
                    Get fresher produce at better prices while supporting local farmers.
                </p>

                <a href="index.php#marketplace"
                    class="inline-flex items-center gap-2 bg-white text-green-900 font-bold text-xs
                           px-4 py-2.5 rounded-xl hover:bg-green-50 transition">

                    <i class="fa-solid fa-cart-shopping"></i>
                    Start Shopping

                </a>

            </div>

        </div>

    </main>

    <?php include 'footer.php'; ?>

</body>

</html>