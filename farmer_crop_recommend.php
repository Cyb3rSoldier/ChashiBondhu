<?php
// Optional: email signup handling
$signup_success = false;
$signup_error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Save to a waiting list table or send email (optional)
        // For now, just show success message
        $signup_success = true;
    } else {
        $signup_error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600;700&display=swap');
        body {
            font-family: 'DM Sans', sans-serif;
        }
        .serif {
            font-family: 'Playfair Display', serif;
        }
        .hero-bg-pattern {
            background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%234ade80" fill-opacity="0.05"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');
        }
        .countdown-number {
            transition: transform 0.2s ease;
        }
        .countdown-number:hover {
            transform: translateY(-3px);
        }

        /* Loading Screen Styles */
        #loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #14532d 0%, #166534 50%, #14532d 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.8s ease, visibility 0.8s ease;
        }
        .loader-content {
            text-align: center;
            animation: fadeInUp 0.6s ease;
        }
        .loader-spinner {
            width: 80px;
            height: 80px;
            border: 4px solid rgba(255,255,255,0.2);
            border-top: 4px solid #4ade80;
            border-right: 4px solid #f59e0b;
            border-bottom: 4px solid #4ade80;
            border-left: 4px solid rgba(255,255,255,0.2);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 25px;
        }
        .loader-text {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            letter-spacing: 4px;
            margin-bottom: 10px;
        }
        .loader-sub {
            color: #bbf7d0;
            font-size: 0.9rem;
            letter-spacing: 2px;
        }
        .loader-leaf {
            font-size: 3rem;
            animation: bounce 1s ease infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .hidden-loading {
            opacity: 0;
            visibility: hidden;
        }
        /* Main content initially hidden */
        #main-content {
            opacity: 0;
            transition: opacity 0.6s ease;
        }
        #main-content.visible {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 to-white min-h-screen">

    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <div class="loader-text">
                <i class="fa-solid fa-leaf loader-leaf"></i> Coming Soon
            </div>
            <p class="loader-sub">ChashiBondhu is preparing something fresh for you</p>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Floating Leaves Decoration -->
        <div class="fixed top-20 left-5 text-green-200 text-6xl opacity-30 rotate-12 select-none pointer-events-none z-0">
            <i class="fa-solid fa-leaf"></i>
        </div>
        <div class="fixed bottom-20 right-5 text-green-200 text-8xl opacity-30 -rotate-12 select-none pointer-events-none z-0">
            <i class="fa-solid fa-seedling"></i>
        </div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 py-16 md:py-24 text-center">
            
            <!-- Logo -->
            <div class="flex justify-center mb-8">
                <div class="bg-white rounded-2xl shadow-lg p-3 inline-flex items-center gap-3">
                    <img class="w-12 h-12 rounded-xl" src="asset/img/ChashiBondhu logo.png" alt="ChashiBondhu">
                    <p class="text-2xl font-bold text-green-800 font-serif tracking-wide">ChashiBondhu</p>
                </div>
            </div>

            <!-- Badge -->
            <div class="inline-block bg-green-100 text-green-700 text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-6">
                <i class="fa-regular fa-clock mr-1"></i> Launching Soon
            </div>

            <!-- Title -->
            <h1 class="serif text-5xl md:text-7xl font-extrabold text-green-950 mb-4">
                Something <span class="text-green-600">Fresh</span> is Growing
            </h1>

            <!-- Subtitle -->
            <p class="text-stone-500 text-lg md:text-xl max-w-2xl mx-auto mb-12">
                We're working hard to bring you the best platform connecting farmers and consumers across Bangladesh.
                Stay tuned for the harvest!
            </p>

            <!-- Countdown Timer -->
            <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-xl p-6 md:p-8 mb-12 border border-green-100">
                <h2 class="text-2xl font-bold text-green-800 mb-6">🚜 Launch Countdown</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 max-w-2xl mx-auto">
                    <div class="countdown-number bg-green-700 rounded-2xl p-4 text-white shadow-lg">
                        <span id="days" class="text-4xl md:text-5xl font-bold block">00</span>
                        <span class="text-xs uppercase tracking-wider opacity-90">Days</span>
                    </div>
                    <div class="countdown-number bg-green-600 rounded-2xl p-4 text-white shadow-lg">
                        <span id="hours" class="text-4xl md:text-5xl font-bold block">00</span>
                        <span class="text-xs uppercase tracking-wider opacity-90">Hours</span>
                    </div>
                    <div class="countdown-number bg-green-500 rounded-2xl p-4 text-white shadow-lg">
                        <span id="minutes" class="text-4xl md:text-5xl font-bold block">00</span>
                        <span class="text-xs uppercase tracking-wider opacity-90">Minutes</span>
                    </div>
                    <div class="countdown-number bg-green-400 rounded-2xl p-4 text-white shadow-lg">
                        <span id="seconds" class="text-4xl md:text-5xl font-bold block">00</span>
                        <span class="text-xs uppercase tracking-wider opacity-90">Seconds</span>
                    </div>
                </div>
            </div>

            <!-- Email Signup (Optional) -->
            <div class="bg-white rounded-2xl shadow-sm border border-green-100 p-6 md:p-8 max-w-xl mx-auto mb-12">
                <h3 class="font-bold text-green-950 text-lg mb-2">🌾 Get Notified First</h3>
                <p class="text-stone-500 text-sm mb-5">We'll let you know when we go live. No spam, just fresh updates.</p>
                
                <?php if ($signup_success): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-3 mb-4 text-sm">
                        <i class="fa-solid fa-check-circle mr-2"></i> Thanks! We'll keep you posted.
                    </div>
                <?php endif; ?>
                <?php if ($signup_error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-600 rounded-xl p-3 mb-4 text-sm">
                        <i class="fa-solid fa-exclamation-circle mr-2"></i> Please enter a valid email address.
                    </div>
                <?php endif; ?>

                <form method="POST" class="flex flex-col sm:flex-row gap-3">
                    <input type="email" name="email" required placeholder="Your email address"
                           class="flex-1 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    <button type="submit" class="bg-green-700 hover:bg-green-600 text-white font-semibold px-6 py-3 rounded-xl transition text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bell"></i> Notify Me
                    </button>
                </form>
            </div>

            <!-- Social Links -->
            <div class="flex justify-center gap-4">
                <a href="#" class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center hover:bg-green-200 transition">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>

            <p class="text-stone-400 text-xs mt-8">
                © 2026 ChashiBondhu. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Set launch date (change this to your actual launch date)
        const launchDate = new Date(2026, 5, 15, 10, 0, 0); // June 15, 2026, 10:00 AM

        function updateCountdown() {
            const now = new Date().getTime();
            const distance = launchDate - now;

            if (distance < 0) {
                document.getElementById('days').innerHTML = '00';
                document.getElementById('hours').innerHTML = '00';
                document.getElementById('minutes').innerHTML = '00';
                document.getElementById('seconds').innerHTML = '00';
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').innerHTML = days < 10 ? '0' + days : days;
            document.getElementById('hours').innerHTML = hours < 10 ? '0' + hours : hours;
            document.getElementById('minutes').innerHTML = minutes < 10 ? '0' + minutes : minutes;
            document.getElementById('seconds').innerHTML = seconds < 10 ? '0' + seconds : seconds;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();

        // Loading screen animation: fade out after 1.5 seconds
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loader = document.getElementById('loading-screen');
                const mainContent = document.getElementById('main-content');
                loader.classList.add('hidden-loading');
                mainContent.classList.add('visible');
                // Remove loader from DOM after transition
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 800);
            }, 1500); // Shows loading screen for 1.5 seconds
        });
    </script>
</body>
</html>