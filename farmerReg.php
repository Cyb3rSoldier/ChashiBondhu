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

    <?php include('navbar2.php'); ?>

    <section class="min-h-screen flex items-center justify-center px-6 py-28">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">

            <!-- Left Panel — Form -->
            <div class="bg-white p-10 flex flex-col justify-center">
                <div class="mb-8">
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-3">Farmer Registration</span>
                    <h3 class="text-2xl font-bold text-green-950">Create your farmer account</h3>
                    <p class="text-stone-400 text-sm mt-1">Fill in your details to start selling directly</p>
                </div>

                <?php if (isset($_SESSION['reg_error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-semibold px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <?php echo $_SESSION['reg_error'];
                        unset($_SESSION['reg_error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($_SESSION['reg_success'])): ?>
                    <div class="bg-red-50 border border-red-200 text-green-600 text-sm font-semibold px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <?php echo $_SESSION['reg_success'];
                        unset($_SESSION['reg_success']); ?>
                    </div>
                <?php endif; ?>

                <form action="farmerRegisterSubmit.php" method="POST" enctype="multipart/form-data" class="space-y-4">

                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Full Name</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            <input type="text" name="name" placeholder="Your full name" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-envelope text-sm"></i>
                            </span>
                            <input type="email" name="email" placeholder="your@email.com" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Phone Number</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </span>
                            <input type="tel" name="phone" placeholder="+880 1700-000000" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <!-- District + Land Size -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">District</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                    <i class="fa-solid fa-map-location-dot text-sm"></i>
                                </span>
                                <select name="district" required
                                    class="w-full bg-stone-50 border border-stone-200 text-stone-600 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                                    <option value="">Select district</option>
                                    <option>Dhaka</option>
                                    <option>Chittagong</option>
                                    <option>Rajshahi</option>
                                    <option>Sylhet</option>
                                    <option>Khulna</option>
                                    <option>Barishal</option>
                                    <option>Mymensingh</option>
                                    <option>Rangpur</option>
                                    <option>Comilla</option>
                                    <option>Narayanganj</option>
                                    <option>Gazipur</option>
                                    <option>Tangail</option>
                                    <option>Bogura</option>
                                    <option>Dinajpur</option>
                                    <option>Jessore</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Land Size</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                    <i class="fa-solid fa-seedling text-sm"></i>
                                </span>
                                <select name="land_size" required
                                    class="w-full bg-stone-50 border border-stone-200 text-stone-600 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                                    <option value="">Select size</option>
                                    <option>Less than 1 acre</option>
                                    <option>1–5 acres</option>
                                    <option>5–10 acres</option>
                                    <option>More than 10 acres</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Crop Types -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Main Crops / Products</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-wheat-awn text-sm"></i>
                            </span>
                            <input type="text" name="crops" placeholder="e.g. Rice, Tomato, Potato, Lemon" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <!-- NID -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">National ID Number</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-id-card text-sm"></i>
                            </span>
                            <input type="text" name="nid" placeholder="Your NID number" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="farmerPass" placeholder="Create a password" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            <button type="button" onclick="togglePassword('farmerPass','farmerPassEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="farmerPassEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="confirm_password" id="farmerConfirm" placeholder="Repeat your password" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            <button type="button" onclick="togglePassword('farmerConfirm','farmerConfirmEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="farmerConfirmEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p id="farmerMatchMsg" class="text-xs mt-1 hidden"></p>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="terms" id="farmerTerms" required class="accent-green-600 w-4 h-4 mt-0.5">
                        <label for="farmerTerms" class="text-stone-500 cursor-pointer">
                            I agree to the <a href="#" class="text-green-600 font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-green-600 font-semibold hover:underline">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-tractor"></i> Register as Farmer
                    </button>

                    <p class="text-center text-stone-400 text-sm">
                        Already have an account?
                        <a href="farmerLogin.php" class="text-green-600 font-bold hover:text-green-800 transition">Sign in</a>
                    </p>

                    <div class="border-t border-stone-100 pt-4 text-center">
                        <p class="text-stone-400 text-xs mb-3">Are you a consumer?</p>
                        <a href="consumerReg.php"
                            class="inline-flex items-center gap-2 border border-green-200 text-green-700 hover:bg-green-50 font-semibold px-5 py-2.5 rounded-xl text-sm transition duration-200">
                            <i class="fa-solid fa-user"></i> Register as Consumer
                        </a>
                    </div>

                </form>
            </div>

            <!-- Right Panel -->
            <div class="bg-green-900 p-10 flex flex-col justify-between text-white">
                <div>
                    <div class="flex items-center gap-3 mb-10">
                        <img class="w-10 h-10 rounded-xl" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                        <p class="text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
                        Join as a<br><span class="text-green-400">Farmer</span>
                    </h2>
                    <p class="text-green-200 text-sm leading-relaxed">
                        Register your farm and start selling directly to consumers. Set your own prices, manage your inventory and get paid fairly for your hard work.
                    </p>
                </div>

                <div class="space-y-4 mt-10">
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-store text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">List your crops and set your own prices</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-money-bill-wave text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Get paid fairly — no middlemen</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-chart-line text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Track orders and earnings easily</span>
                    </div>
                    <div class="flex items-center gap-3 bg-green-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-users text-green-400 w-5 text-center"></i>
                        <span class="text-green-100 text-sm">Reach consumers across 64 districts</span>
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

        const farmerPass = document.getElementById('farmerPass');
        const farmerConfirm = document.getElementById('farmerConfirm');
        const farmerMsg = document.getElementById('farmerMatchMsg');

        farmerConfirm.addEventListener('input', () => {
            if (farmerConfirm.value === '') {
                farmerMsg.classList.add('hidden');
            } else if (farmerPass.value === farmerConfirm.value) {
                farmerMsg.textContent = '✓ Passwords match';
                farmerMsg.className = 'text-xs mt-1 text-green-600 font-semibold';
            } else {
                farmerMsg.textContent = '✗ Passwords do not match';
                farmerMsg.className = 'text-xs mt-1 text-red-500 font-semibold';
            }
        });
    </script>

</body>

</html>