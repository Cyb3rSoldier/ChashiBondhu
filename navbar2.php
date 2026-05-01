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

<body class="bg-stone-50 overflow-x-hidden">

    <!-- NAVBAR -->
    <header>
        <div id="navbar" class="bg-[#14532d] fixed top-0 left-0 w-full z-50 transition-all duration-500">
            <nav class="px-6 flex py-4 justify-between items-center max-w-7xl mx-auto">
                <div class="flex items-center gap-3 text-white">
                    <img class="w-10 h-10 rounded-xl shadow-md" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                    <p class="text-xl md:text-2xl font-bold font-serif tracking-wide">ChashiBondhu</p>
                </div>

                <ul class="hidden md:flex text-white/90 font-semibold gap-8 items-center">
                    <li><a class="nav-link hover:text-white transition duration-200 text-sm tracking-wide"
                            href="index.php">Home</a></li>
                    <li><a class="nav-link hover:text-white transition duration-200 text-sm tracking-wide"
                            href="about.php">About Us</a></li>
                    <li><a class="nav-link hover:text-white transition duration-200 text-sm tracking-wide"
                            href="contact.php">Contact</a></li>
                    <li class="group relative">
    <a class="flex items-center gap-1 hover:text-white transition duration-200 text-sm tracking-wide" href="#">
        Login <i class="fa-solid fa-chevron-down text-xs mt-0.5 transition-transform duration-200 group-hover:rotate-180"></i>
    </a>
    <ul class="w-48 p-2 mt-3 rounded-xl font-semibold text-neutral-800 text-sm bg-white shadow-2xl absolute right-0 
        opacity-0 group-hover:opacity-100 
        scale-95 group-hover:scale-100 
        origin-top-right 
        transition-all duration-200 
        pointer-events-none group-hover:pointer-events-auto
        delay-0 group-hover:delay-0"
        style="transition-delay: 0ms; padding-top: 8px;">
        <div class="absolute -top-3 left-0 right-0 h-3"></div>
        <li><a class="flex items-center gap-2 hover:bg-green-50 rounded-lg p-2.5 transition duration-150" href="consumerLogin.php">
            <i class="fa-solid fa-user text-green-700 text-xs w-4"></i> Login as Consumer
        </a></li>
        <li><a class="flex items-center gap-2 hover:bg-green-50 rounded-lg p-2.5 transition duration-150" href="farmerLogin.php">
            <i class="fa-solid fa-tractor text-green-700 text-xs w-4"></i> Login as Farmer
        </a></li>
    </ul>
</li>
                </ul>

                <button class="p-2 md:hidden text-white" onclick="handelMenu()">
                    <i class="text-2xl fa-solid fa-bars"></i>
                </button>

                <!-- Mobile Menu -->
                <div id="nav-dialog" class="hidden fixed inset-0 bg-green-900 top-0 left-0 w-full z-50 md:hidden">
                    <div class="px-6 flex py-5 justify-between items-center border-b border-green-700">
                        <div class="flex items-center gap-3 text-amber-50">
                            <img class="w-10 h-10 rounded-xl" src="./asset/img/ChashiBondhu logo.png" alt="Logo">
                            <p class="text-xl font-bold font-serif">ChashiBondhu</p>
                        </div>
                        <button class="p-2 text-white" onclick="handelMenu()">
                            <i class="text-2xl fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <ul class="text-white flex flex-col mt-4 px-4 gap-1">
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="index.php"><i class="fa-solid fa-house mr-3 text-green-400"></i>Home</a></li>
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="about.php"><i class="fa-solid fa-circle-info mr-3 text-green-400"></i>About Us</a></li>
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="contact.php"><i class="fa-solid fa-envelope mr-3 text-green-400"></i>Contact</a></li>
                        <div class="border-t border-green-700 my-2"></div>
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="farmerLogin.php"><i class="fa-solid fa-tractor mr-3 text-green-400"></i>Login as
                                Farmer</a></li>
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="consumerLogin.php"><i class="fa-solid fa-user mr-3 text-green-400"></i>Login as
                                Consumer</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <script>
        const navvDialog = document.getElementById('nav-dialog');
        const navbar = document.getElementById('navbar');

        function handelMenu() {
            navvDialog.classList.toggle('hidden');
        }

        window.addEventListener('scroll', () => {
            if (window.scrollY > 60) {
                navbar.classList.add('nav-scrolled');
            } else {
                navbar.classList.remove('nav-scrolled');
            }
        });
    </script>

</body>

</html>