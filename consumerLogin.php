<?php
session_start();
require_once 'config.php';
?>

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

    <?php include('navbar2.php'); ?>

    <section class="min-h-screen flex items-center justify-center px-6 py-28">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">

            <div class="bg-white p-10 flex flex-col justify-center">
                <div class="mb-8">
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-3">Consumer Portal</span>
                    <h3 class="text-2xl font-bold text-green-950">Sign in to your account</h3>
                    <p class="text-stone-400 text-sm mt-1">Enter your credentials to continue</p>
                </div>

                <?php if (isset($_SESSION['login_error'])): ?>
                    <div class="mb-5 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span><?= $_SESSION['login_error']; ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['login_error']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="mb-5 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check"></i>
                            <span><?= $_SESSION['success_message']; ?></span>
                        </div>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <form action="consumerLoginSubmit.php" method="POST" class="space-y-5">

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" placeholder="your@email.com" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="consumerPassword" placeholder="Enter your password" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            <button type="button" onclick="togglePassword('consumerPassword', 'consumerEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="consumerEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 text-stone-500 cursor-pointer">
                            <input type="checkbox" name="remember" class="accent-green-600 w-4 h-4">
                            Remember me
                        </label>
                        <a href="forgotPassword.php" class="text-green-600 hover:text-green-800 font-semibold transition">Forgot password?</a>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-right-to-bracket"></i> Login as Consumer
                    </button>

                    <p class="text-center text-stone-400 text-sm">
                        Don't have an account?
                        <a href="consumerReg.php" class="text-green-600 font-bold hover:text-green-800 transition">Register here</a>
                    </p>

                    <div class="border-t border-stone-100 pt-4 text-center">
                        <p class="text-stone-400 text-xs mb-3">Are you a farmer?</p>
                        <a href="farmerLogin.php"
                            class="inline-flex items-center gap-2 border border-green-200 text-green-700 hover:bg-green-50 font-semibold px-5 py-2.5 rounded-xl text-sm transition duration-200">
                            <i class="fa-solid fa-tractor"></i> Switch to Farmer Login
                        </a>
                    </div>

                </form>
            </div>

            <div class="bg-green-900 p-10 flex flex-col justify-between text-white">
                <div>
                    <div class="flex items-center gap-3 mb-10">
                        <img class="w-10 h-10 rounded-xl" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                        <p class="text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
                        Welcome Back,<br><span class="text-green-400">Shopper</span>
                    </h2>
                    <p class="text-green-200 text-sm leading-relaxed">
                        Log in to browse fresh produce, order directly from farmers and get the best prices with full transparency.
                    </p>
                </div>

                <div class="space-y-4 mt-10">
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-basket-shopping text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Order fresh produce directly from farmers</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-tags text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Compare prices with government market rates</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-comments text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Chat directly with the farmer</span>
                    </div>
                </div>

                <p class="text-green-400 text-xs mt-10">© 2026 ChashiBondhu. All rights reserved.</p>
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