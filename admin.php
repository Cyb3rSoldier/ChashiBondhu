<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChashiBondhu</title>
    <link rel="stylesheet" href="design.css">
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Roboto', sans-serif;
    }
</style>

<body class="bg-green-100 min-h-screen overflow-x-hidden">

<?php include('navbar2.php');?>

    <section class="min-h-screen flex items-center justify-center mt-14 px-6 py-10">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">

            <!-- Left Panel — Dark Admin Panel -->
            <div class="bg-green-950 p-10 flex flex-col justify-between text-white">
                <div>
                    <div class="flex items-center gap-3 mb-10">
                        <img class="w-10 h-10 rounded-xl" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                        <p class="text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                    </div>
                    <div class="inline-flex items-center gap-2 bg-red-900/50 border border-red-700/50 text-red-300 text-xs font-bold tracking-widest uppercase px-3 py-1.5 rounded-full mb-6">
                        <i class="fa-solid fa-shield-halved"></i> Admin Access Only
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
                        Admin<br><span class="text-green-400">Control Panel</span>
                    </h2>
                    <p class="text-green-200 text-sm leading-relaxed">
                        Restricted access for platform administrators only. Unauthorised login attempts are monitored and logged.
                    </p>
                </div>

                <div class="space-y-4 mt-10">
                    <div class="flex items-center gap-3 bg-green-900/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-users text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Manage farmers and consumers</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-900/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-list-check text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Verify and moderate product listings</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-900/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-flag text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Handle reports and complaints</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-900/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-chart-bar text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Monitor platform analytics</span>
                    </div>
                </div>

                <p class="text-green-600 text-xs mt-10">© 2026 ChashiBondhu. All rights reserved.</p>
            </div>

            <div class="bg-white p-10 flex flex-col justify-center">
                <div class="mb-8">
                    <span class="inline-block bg-red-100 text-red-700 text-xs font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-3">Restricted Area</span>
                    <h3 class="text-2xl font-bold text-green-950">Admin Sign In</h3>
                    <p class="text-stone-400 text-sm mt-1">Authorised personnel only</p>
                </div>

                <form action="adminLoginSubmit.php" method="POST" class="space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Admin Email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-user-shield text-sm"></i>
                            </span>
                            <input type="email" name="email" placeholder="admin@chashibondhu.com" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="adminPassword" placeholder="Enter admin password" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            <button type="button" onclick="togglePassword('adminPassword', 'adminEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="adminEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Admin Secret Key</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-key text-sm"></i>
                            </span>
                            <input type="password" name="secret_key" id="secretKey" placeholder="Enter secret key" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            <button type="button" onclick="togglePassword('secretKey', 'secretEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="secretEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-stone-500 cursor-pointer">
                            <input type="checkbox" name="remember" class="accent-green-600 w-4 h-4">
                            Remember me
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-800 hover:bg-green-700 active:bg-green-900 text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-shield-halved"></i> Login as Admin
                    </button>

                    <div class="border-t border-stone-100 pt-4 text-center">
                        <p class="text-stone-400 text-xs mb-3">Not an admin?</p>
                        <div class="flex justify-center gap-3 flex-wrap">
                            <a href="farmerLogin.php"
                                class="inline-flex items-center gap-2 border border-green-200 text-green-700 hover:bg-green-50 font-semibold px-4 py-2 rounded-xl text-xs transition duration-200">
                                <i class="fa-solid fa-tractor"></i> Farmer Login
                            </a>
                            <a href="consumerLogin.php"
                                class="inline-flex items-center gap-2 border border-green-200 text-green-700 hover:bg-green-50 font-semibold px-4 py-2 rounded-xl text-xs transition duration-200">
                                <i class="fa-solid fa-user"></i> Consumer Login
                            </a>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </section>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

</body>

</html>