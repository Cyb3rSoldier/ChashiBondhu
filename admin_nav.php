<?php
if (!isset($_SESSION['admin_id'])) return;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <aside class="fixed left-0 top-0 h-full w-64 bg-green-950 text-white z-40 shadow-xl overflow-y-auto">
        <div class="p-5 border-b border-green-800">
            <div class="flex items-center gap-3">
                <img class="w-10 h-10 rounded-xl" src="asset/img/ChashiBondhu logo.png" alt="Logo">
                <p class="text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
            </div>
            <p class="text-xs text-green-400 mt-2">Admin Panel</p>
        </div>

        <nav class="p-4 space-y-1">
            <a href="admin_dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-chart-line w-5"></i> Dashboard
            </a>
            <a href="admin_farmers.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_farmers.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-tractor w-5"></i> Farmers
            </a>
            <a href="admin_consumers.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_consumers.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-users w-5"></i> Consumers
            </a>
            <a href="admin_vendors.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_vendors.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-wrench w-5"></i> Vendors
            </a>
            <div class="border-t border-green-800 my-3"></div>
            <a href="admin_products.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-apple-alt w-5"></i> Farmer Products
            </a>
            <a href="admin_tools.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_tools.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-wrench w-5"></i> Tools & Equipment
            </a>
            <div class="border-t border-green-800 my-3"></div>
            <a href="admin_orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_orders.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-truck w-5"></i> Product Orders
            </a>
            <a href="admin_tool_orders.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_tool_orders.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-toolbox w-5"></i> Tool Orders
            </a>

            <a href="admin_farming_tips.php"class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'bg-green-800' : ''; ?>">
   <i class="fa-solid fa-apple-alt w-5"></i> Tips for Farmers
             </a>
                <a href="admin_announcements.php"class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_products.php' ? 'bg-green-800' : ''; ?>">
   <i class="fa-solid fa-apple-alt w-5"></i> Announcements
             </a>
             
            <div class="border-t border-green-800 my-3"></div>
            <a href="admin_contact_messages.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-800 transition <?php echo basename($_SERVER['PHP_SELF']) == 'admin_contact_messages.php' ? 'bg-green-800' : ''; ?>">
                <i class="fa-solid fa-envelope w-5"></i> Contact Messages
            </a>
            <a href="adminLogout.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-800 transition text-red-300 mt-5">
                <i class="fa-solid fa-sign-out-alt w-5"></i> Logout
            </a>
        </nav>
    </aside>

    <div class="ml-64">
        <!-- Top bar with admin info -->
        <div class="bg-white shadow-sm border-b border-stone-200 px-8 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-green-950">
                <?php 
                $pageTitle = basename($_SERVER['PHP_SELF'], '.php');
                echo ucwords(str_replace('admin_', '', str_replace('_', ' ', $pageTitle)));
                ?>
            </h2>
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-user-shield text-green-700"></i>
                <span class="text-sm font-medium text-stone-700"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
            </div>
        </div>
        <div class="p-6">
</body>
</html>