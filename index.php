<?php
session_start();
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

<body class="bg-green-100/70 overflow-x-hidden">

    <!-- NAV BAR -->
    <?php include 'navbar.php' ?>

    <!-- HERO SECTION -->
    <section class="relative w-full h-screen min-h-[600px] overflow-hidden">

        <img class="hero-bg scale-105" id="hero-img" src="asset/farmer1.png" alt="Farmer">

        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/20"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-green-950/40 to-transparent"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-6">

            <div
                class="hero-badge flex mt-3 items-center gap-2 bg-green-500/20 border border-green-400/40 text-green-300 text-xs font-semibold tracking-widest uppercase px-5 py-2 rounded-full mb-6 backdrop-blur-sm">
                <span class="live-dot"></span>
                Bangladeshi Farmer's Platform
            </div>

            <h1 class="hero-title font-bold text-4xl md:text-6xl lg:text-7xl text-white leading-tight max-w-4xl mb-2">
                From the Farmer to <span class="text-green-400/80">You!</span>
            </h1>

            <p class="hero-subtitle text-base md:text-lg text-stone-300 mt-5 max-w-lg leading-relaxed">
                ChashiBondhu connects farmers directly with consumers <br>
                <span class="text-[15px] md:text-[20px]">"Fresh products, fair prices, and zero middlemen"</span>
            </p>

            <div class="hero-btns flex flex-col sm:flex-row gap-4 mt-8">
                <a href="consumerReg.php"
                    class="btn-primary text-white font-semibold px-8 py-3.5 rounded-xl text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-basket-shopping"></i> Shop as Consumer
                </a>
                <a href="farmerReg.php"
                    class="btn-secondary text-white font-semibold px-8 py-3.5 rounded-xl text-sm flex items-center justify-center gap-2">
                    <i class="fa-solid fa-seedling"></i> Join as Farmer
                </a>
            </div>

            <div class="hero-stats flex flex-wrap justify-center gap-3 mt-10">
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">5,000+</p>
                    <p class="text-stone-400 text-xs mt-0.5">Farmers Joined</p>
                </div>
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">20,000+</p>
                    <p class="text-stone-400 text-xs mt-0.5">Happy Consumers</p>
                </div>
                <div class="stat-card px-5 py-3 rounded-2xl text-center">
                    <p class="text-white font-bold text-lg md:text-xl">64 Districts</p>
                    <p class="text-stone-400 text-xs mt-0.5">Across Bangladesh</p>
                </div>
            </div>
        </div>

        <div
            class="scroll-hint absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-white/50 text-xs">
            <span class="tracking-widest uppercase text-[10px]">Scroll</span>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
    </section>

    <!-- Marketplace -->

    <section id="marketplace" class="py-16">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="text-center mb-8 px-6">
                <h2 class="text-3xl md:text-4xl font-bold text-green-950 mb-3">
                    🛒 Fresh From Farms
                </h2>
                <p class="text-stone-500 text-base md:text-lg">
                    Buy directly from farmers across Bangladesh — fresh, fair, and chemical-free
                </p>
            </div>

            <!-- Search Bar -->
            <div class="search-bar bg-amber-50  max-w-xl mx-auto mb-6">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 -translate-y-1/2 text-stone-600"></i>
                    <input type="text"
                        id="productSearch"
                        placeholder="Search fresh vegetables, fruits, grains..."
                        onkeyup="searchProducts()"
                        class="w-full pl-10 pr-4 py-3 border border-stone-700 rounded-xl text-sm focus:outline-none focus:border-green-500 focus:ring-2">
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'footer.php' ?>

    <!-- JS CODE -->
    <script>
        window.addEventListener('load', () => {
            const img = document.getElementById('hero-img');
            img.style.transform = 'scale(1)';
        });
    </script>

</body>

</html>