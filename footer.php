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

<body>
    <!-- FOOTER -->
    <footer class="bg-green-950 text-stone-300">

        <!-- Main Footer -->
        <div class="max-w-7xl mx-auto px-6 py-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">

            <!-- Brand Column -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-4">
                    <img class="w-10 h-10 rounded-xl" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                    <p class="text-white text-xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                </div>
                <p class="text-sm text-stone-400 leading-relaxed mb-5">
                    Bridging the gap between farmers and consumers across Bangladesh. Fresh produce, fair prices, no middlemen.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-9 h-9 rounded-xl bg-green-800 hover:bg-green-600 flex items-center justify-center transition duration-200">
                        <i class="fa-brands fa-facebook-f text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-green-800 hover:bg-green-600 flex items-center justify-center transition duration-200">
                        <i class="fa-brands fa-instagram text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-green-800 hover:bg-green-600 flex items-center justify-center transition duration-200">
                        <i class="fa-brands fa-youtube text-white text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 rounded-xl bg-green-800 hover:bg-green-600 flex items-center justify-center transition duration-200">
                        <i class="fa-brands fa-whatsapp text-white text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">Quick Links</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="index.php" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-green-600 text-xs"></i>Home</a></li>
                    <li><a href="about.php" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-green-600 text-xs"></i>About Us</a></li>
                    <li><a href="contact.php" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-green-600 text-xs"></i>Contact Us</a></li>
                    <li><a href="#" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-chevron-right text-green-600 text-xs"></i>Privacy Policy</a></li>
                </ul>
            </div>

            <!-- For Users -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">For Users</h3>
                <ul class="space-y-3 text-sm">
                    <li><a href="consumerLogin.php" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-user text-green-600 text-xs"></i>Login as Consumer</a></li>
                    <li><a href="farmerLogin.php" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-tractor text-green-600 text-xs"></i>Login as Farmer</a></li>
                    <li><a href="index.php#marketplace" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-shop text-green-600 text-xs"></i>Browse Products</a></li>
                    <li><a href="#" class="hover:text-green-400 transition duration-200 flex items-center gap-2"><i class="fa-solid fa-headset text-green-600 text-xs"></i>Support Center</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-white font-bold text-sm uppercase tracking-widest mb-5">Contact Us</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot text-green-500 mt-0.5"></i>
                        <span class="text-stone-400">Dhaka, Bangladesh</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-green-500"></i>
                        <a href="tel:+8801835314263" class="text-stone-400 hover:text-green-400 transition">+880 1835314263</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-green-500"></i>
                        <a href="mailto:hello@chashibondhu.com" class="text-stone-400 hover:text-green-400 transition">hello@chashibondhu.com</a>
                    </li>
                </ul>
            </div>

        </div>

        <!-- Divider -->
        <div class="border-t border-green-900">
            <div class="max-w-7xl mx-auto px-6 py-5 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-white">
                <p>© 2026 ChashiBondhu. All rights reserved.</p>
                <div class="flex items-center gap-1">
                    <i class="fa-solid fa-seedling text-green-600"></i>
                    <span>Made with love for Bangladesh's farmers</span>
                </div>
            </div>
        </div>

    </footer>
</body>

</html>