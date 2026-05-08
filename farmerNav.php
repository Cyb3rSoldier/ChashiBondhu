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

                <ul class="hidden md:flex text-white font-bold gap-8 items-center">
                    <li><a class="nav-link hover:text-white transition duration-200 text-sm tracking-wide"
                            href="farmerDash.php">Dashboard</a></li>
                    <li><a class="nav-link hover:text-white transition duration-200 text-sm tracking-wide"
                            href="farmerLogout.php">Logout</a></li>
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
                                href="farmerDash.php"><i class="fa-solid fa-house mr-3 text-green-400"></i>Dashboard</a></li>
                        <li><a class="hover:bg-green-800 p-3.5 rounded-xl block font-semibold text-base transition"
                                href="farmerLogout.php"><i class="fa-solid fa-right-from-bracket mr-3 text-green-400"></i>Logout</a></li>
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