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

<body>
    <!-- NAV BAR -->
    <?php include 'navbar2.php' ?>

    <!-- ABOUT SECTION -->
    <section class="bg-green-100/70 py-24 px-6 overflow-hidden">
        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-20">
                <p class="text-green-950 mt-5 mx-auto text-1xl md:text-2xl">
                    ChashiBondhu is Bangladesh's farmer-consumer digital marketplace — built to cut out the middlemen, ensure fair prices, and bring fresh produce straight from the farm to your door.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-24">
                <div>
                    <h3 class="text-3xl md:text-4xl font-bold text-green-950 leading-snug mb-6">
                        From the Farmer To You...!
                    </h3>
                    <p class="text-stone-500 text-base leading-relaxed mb-5">
                        For generations, Bangladesh's farmers have worked tirelessly under the open sky — only to sell their harvest to middlemen at unfairly low prices. Those same vegetables then travel to city markets where consumers pay two, three, sometimes five times more. Both ends of the chain suffer while the middle profits.
                    </p>
                    <p class="text-stone-500 text-base leading-relaxed mb-8">
                        ChashiBondhu was built to change that. By connecting farmers and consumers directly through a simple digital platform, we eliminate unnecessary intermediaries, ensure farmers are paid fairly, and give consumers access to fresh produce at honest, transparent prices.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="farmerLogin.html" class="bg-green-700 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-xl text-sm transition duration-200 shadow-md flex items-center gap-2">
                            <i class="fa-solid fa-tractor"></i> Join as Farmer
                        </a>
                        <a href="consumerLogin.html" class="border border-green-700 text-green-700 hover:bg-green-50 font-semibold px-6 py-3 rounded-xl text-sm transition duration-200 flex items-center gap-2">
                            <i class="fa-solid fa-basket-shopping"></i> Shop as Consumer
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div class="bg-green-900 rounded-3xl p-7 text-white flex flex-col justify-between min-h-[160px]">
                        <i class="fa-solid fa-tractor text-green-400 text-2xl"></i>
                        <div>
                            <p class="text-4xl font-bold mt-4">5,000+</p>
                            <p class="text-green-300 text-sm mt-1">Farmers Registered</p>
                        </div>
                    </div>
                    <div class="bg-green-600 rounded-3xl p-7 text-white flex flex-col justify-between min-h-[160px]">
                        <i class="fa-solid fa-users text-green-100 text-2xl"></i>
                        <div>
                            <p class="text-4xl font-bold mt-4">20,000+</p>
                            <p class="text-green-100 text-sm mt-1">Happy Consumers</p>
                        </div>
                    </div>
                    <div class="bg-amber-100 border border-stone-200 rounded-3xl p-7 text-green-950 flex flex-col justify-between min-h-[160px]">
                        <i class="fa-solid fa-map-location-dot text-green-600 text-2xl"></i>
                        <div>
                            <p class="text-4xl font-bold mt-4">64</p>
                            <p class="text-black text-sm mt-1">Districts Covered</p>
                        </div>
                    </div>
                    <div class="bg-green-950 rounded-3xl p-7 text-white flex flex-col justify-between min-h-[160px]">
                        <i class="fa-solid fa-handshake text-green-400 text-2xl"></i>
                        <div>
                            <p class="text-4xl font-bold mt-4">Zero</p>
                            <p class="text-green-300 text-sm mt-1">Middlemen Needed</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-900 rounded-3xl p-10 md:p-14">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-block bg-green-700 text-green-200 text-xs font-bold tracking-widest uppercase px-3 py-1 rounded-full mb-4">Competitive Edge</span>
                        <h3 class="text-3xl md:text-4xl font-bold text-white leading-snug mb-5">
                            Why Choose <span class="text-green-400">ChashiBondhu</span>?
                        </h3>
                        <p class="text-green-200 text-base leading-relaxed">
                            Unlike traditional markets that rely on middlemen, or online grocery platforms that buy from suppliers, ChashiBondhu puts farmers in control. Every product listed comes directly from a verified farmer — with full price transparency and a direct line of communication between the grower and the buyer.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between bg-green-800/60 rounded-2xl px-5 py-4">
                            <span class="text-green-100 text-sm font-medium">Direct farmer selling</span>
                            <div class="flex gap-8 text-xs font-bold">
                                <span class="text-red-400"><i class="fa-solid fa-xmark mr-1"></i>Traditional</span>
                                <span class="text-green-400"><i class="fa-solid fa-check mr-1"></i>ChashiBondhu</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-green-800/60 rounded-2xl px-5 py-4">
                            <span class="text-green-100 text-sm font-medium">Price transparency</span>
                            <div class="flex gap-8 text-xs font-bold">
                                <span class="text-red-400"><i class="fa-solid fa-xmark mr-1"></i>Traditional</span>
                                <span class="text-green-400"><i class="fa-solid fa-check mr-1"></i>ChashiBondhu</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-green-800/60 rounded-2xl px-5 py-4">
                            <span class="text-green-100 text-sm font-medium">Farmer-consumer chat</span>
                            <div class="flex gap-8 text-xs font-bold">
                                <span class="text-red-400"><i class="fa-solid fa-xmark mr-1"></i>Traditional</span>
                                <span class="text-green-400"><i class="fa-solid fa-check mr-1"></i>ChashiBondhu</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-green-800/60 rounded-2xl px-5 py-4">
                            <span class="text-green-100 text-sm font-medium">Digital farmer dashboard</span>
                            <div class="flex gap-8 text-xs font-bold">
                                <span class="text-red-400"><i class="fa-solid fa-xmark mr-1"></i>Traditional</span>
                                <span class="text-green-400"><i class="fa-solid fa-check mr-1"></i>ChashiBondhu</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between bg-green-800/60 rounded-2xl px-5 py-4">
                            <span class="text-green-100 text-sm font-medium">Consumer marketplace</span>
                            <div class="flex gap-8 text-xs font-bold">
                                <span class="text-red-400"><i class="fa-solid fa-xmark mr-1"></i>Traditional</span>
                                <span class="text-green-400"><i class="fa-solid fa-check mr-1"></i>ChashiBondhu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'footer.php' ?>
</body>

</html>