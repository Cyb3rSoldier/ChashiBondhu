<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Registration — ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap');
    body { font-family: 'Roboto', sans-serif; }
</style>

<body class="bg-green-100 min-h-screen overflow-x-hidden">

    <?php include('navbar2.php'); ?>

    <section class="min-h-screen flex items-center justify-center px-6 py-28">
        <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-2 rounded-3xl overflow-hidden shadow-2xl">

            <!-- Left Panel — Form -->
            <div class="bg-white p-10 flex flex-col justify-center">
                <div class="mb-8">
                    <span class="inline-block bg-amber-100 text-amber-700 text-xs font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-3">Vendor Registration</span>
                    <h3 class="text-2xl font-bold text-green-950">Sell Your Tools & Equipment</h3>
                    <p class="text-stone-400 text-sm mt-1">Register as a vendor to sell farming tools, machines, seeds & more</p>
                </div>

                <?php if (isset($_SESSION['reg_error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 text-sm font-semibold px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <?php echo $_SESSION['reg_error']; unset($_SESSION['reg_error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['reg_success'])): ?>
                    <div class="bg-green-50 border border-green-200 text-green-600 text-sm font-semibold px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <?php echo $_SESSION['reg_success']; unset($_SESSION['reg_success']); ?>
                    </div>
                <?php endif; ?>

                <!-- FIXED: Form action points to the correct file -->
                <form action="vendorRegisterSubmit.php" method="POST" class="space-y-4">

                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Full Name <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-user text-sm"></i></span>
                            <input type="text" name="full_name" placeholder="Your full name" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Business Name -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Business Name <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-shop text-sm"></i></span>
                            <input type="text" name="business_name" placeholder="Your shop or business name" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Email Address <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-envelope text-sm"></i></span>
                            <input type="email" name="email" placeholder="your@email.com" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Phone Number <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-phone text-sm"></i></span>
                            <input type="tel" name="phone" placeholder="01XXXXXXXXX" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                        </div>
                    </div>

                    <!-- District + Business Type -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">District <span class="text-red-400">*</span></label>
                            <select name="district" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-600 text-sm px-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                                <option value="">Select district</option>
                                <option>Dhaka</option><option>Chittagong</option><option>Rajshahi</option>
                                <option>Sylhet</option><option>Khulna</option><option>Barishal</option>
                                <option>Mymensingh</option><option>Rangpur</option><option>Comilla</option>
                                <option>Narayanganj</option><option>Gazipur</option><option>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Business Type <span class="text-red-400">*</span></label>
                            <select name="business_type" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-600 text-sm px-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                                <option value="">Select type</option>
                                <option>Retail Shop</option>
                                <option>Distributor</option>
                                <option>Manufacturer</option>
                                <option>Wholesaler</option>
                                <option>Online Seller</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <!-- NID -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">National ID Number <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-id-card text-sm"></i></span>
                            <input type="text" name="nid" placeholder="Your NID number" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-4 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-lock text-sm"></i></span>
                            <input type="password" name="password" id="vendorPass" placeholder="Create a password (min 6 characters)" required minlength="6"
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                            <button type="button" onclick="togglePassword('vendorPass','vendorPassEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="vendorPassEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Confirm Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-400"><i class="fa-solid fa-lock text-sm"></i></span>
                            <input type="password" name="confirm_password" id="vendorConfirm" placeholder="Repeat your password" required
                                class="w-full bg-stone-50 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm pl-10 pr-12 py-3 rounded-xl outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition duration-200">
                            <button type="button" onclick="togglePassword('vendorConfirm','vendorConfirmEye')"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                                <i id="vendorConfirmEye" class="fa-solid fa-eye text-sm"></i>
                            </button>
                        </div>
                        <p id="vendorMatchMsg" class="text-xs mt-1 hidden"></p>
                    </div>

                    <!-- Terms -->
                    <div class="flex items-start gap-2 text-sm">
                        <input type="checkbox" name="terms" id="vendorTerms" required class="accent-amber-600 w-4 h-4 mt-0.5">
                        <label for="vendorTerms" class="text-stone-500 cursor-pointer">
                            I agree to the <a href="#" class="text-amber-600 font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-amber-600 font-semibold hover:underline">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-wrench"></i> Register as Vendor
                    </button>

                    <p class="text-center text-stone-400 text-sm">
                        Already have an account?
                        <a href="vendorLogin.php" class="text-amber-600 font-bold hover:text-amber-800 transition">Sign in</a>
                    </p>

                    <div class="border-t border-stone-100 pt-4 text-center">
                        <p class="text-stone-400 text-xs mb-3">Are you a farmer?</p>
                        <a href="farmerReg.php"
                            class="inline-flex items-center gap-2 border border-green-200 text-green-700 hover:bg-green-50 font-semibold px-5 py-2.5 rounded-xl text-sm transition duration-200">
                            <i class="fa-solid fa-tractor"></i> Register as Farmer
                        </a>
                    </div>

                </form>
            </div>

            <!-- Right Panel -->
            <div class="bg-amber-900 p-10 flex flex-col justify-between text-white">
                <div>
                    <div class="flex items-center gap-3 mb-10">
                        <img class="w-10 h-10 rounded-xl" src="asset/img/ChashiBondhu logo.png" alt="Logo">
                        <p class="text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold leading-tight mb-4">
                        Join as a<br><span class="text-amber-400">Vendor</span>
                    </h2>
                    <p class="text-amber-200 text-sm leading-relaxed">
                        Sell farming tools, machines, seeds, fertilizers and equipment directly to farmers across Bangladesh.
                    </p>
                </div>

                <div class="space-y-4 mt-10">
                    <div class="flex items-center gap-3 bg-amber-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-store text-amber-400 w-5 text-center"></i>
                        <span class="text-amber-100 text-sm">List your tools and equipment</span>
                    </div>
                    <div class="flex items-center gap-3 bg-amber-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-chart-line text-amber-400 w-5 text-center"></i>
                        <span class="text-amber-100 text-sm">Track sales and manage inventory</span>
                    </div>
                    <div class="flex items-center gap-3 bg-amber-800/60 rounded-2xl px-4 py-3">
                        <i class="fa-solid fa-users text-amber-400 w-5 text-center"></i>
                        <span class="text-amber-100 text-sm">Reach farmers across 64 districts</span>
                    </div>
                </div>

                <p class="text-amber-400 text-xs mt-10">© 2026 ChashiBondhu. All rights reserved.</p>
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

        const vendorPass = document.getElementById('vendorPass');
        const vendorConfirm = document.getElementById('vendorConfirm');
        const vendorMsg = document.getElementById('vendorMatchMsg');

        vendorConfirm.addEventListener('input', () => {
            if (vendorConfirm.value === '') {
                vendorMsg.classList.add('hidden');
            } else if (vendorPass.value === vendorConfirm.value) {
                vendorMsg.textContent = '✓ Passwords match';
                vendorMsg.className = 'text-xs mt-1 text-green-600 font-semibold';
            } else {
                vendorMsg.textContent = '✗ Passwords do not match';
                vendorMsg.className = 'text-xs mt-1 text-red-500 font-semibold';
            }
        });
    </script>

</body>
</html>