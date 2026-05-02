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

    <!-- CONTACT SECTION -->
    <section class="bg-green-100 py-24 px-6">
        <div class="max-w-7xl mx-auto">

            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-green-950 leading-tight">
                    <u>Get in <span class="text-green-900">Touch</u></span>
                </h2>
                <p class="text-black mt-4 max-w-xl mx-auto text-1xl md:text-2xl leading-relaxed">
                    Have any question? We'd love to hear from you.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

                <div>
                    <div class="bg-green-900 rounded-3xl p-10 text-white mb-6">
                        <h3 class="text-2xl font-bold mb-2">Let's Talk</h3>
                        <p class="text-green-200 text-sm leading-relaxed mb-8">
                            Whether you're a farmer wanting to join the platform or a consumer with feedback — we're here and happy to help!
                        </p>

                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-location-dot text-green-300"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-green-400 font-bold uppercase tracking-widest mb-1">Address</p>
                                    <p class="text-green-100 text-sm">Dhaka, Bangladesh</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-phone text-green-300"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-green-400 font-bold uppercase tracking-widest mb-1">Phone</p>
                                    <a href="tel:+8801835314263" class="text-green-100 text-sm hover:text-white transition">+880 1700-000000</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-envelope text-green-300"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-green-400 font-bold uppercase tracking-widest mb-1">Email</p>
                                    <a href="mailto:hello@chashibondhu.com" class="text-green-100 text-sm hover:text-white transition">hello@chashibondhu.com</a>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl bg-green-700 flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-clock text-green-300"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-green-400 font-bold uppercase tracking-widest mb-1">Support Hours</p>
                                    <p class="text-green-100 text-sm">Sat – Thu &nbsp;|&nbsp; 9:00 AM – 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-stone-100 rounded-3xl p-6 shadow-sm">
                        <p class="text-xs text-stone-400 font-bold uppercase tracking-widest mb-4">Follow Us</p>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 rounded-xl bg-green-300 hover:bg-green-700 text-green-700 hover:text-white flex items-center justify-center transition duration-200">
                                <i class="fa-brands fa-facebook-f text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-green-300 hover:bg-green-700 text-green-700 hover:text-white flex items-center justify-center transition duration-200">
                                <i class="fa-brands fa-instagram text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-green-300 hover:bg-green-700 text-green-700 hover:text-white flex items-center justify-center transition duration-200">
                                <i class="fa-brands fa-youtube text-sm"></i>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-xl bg-green-300 hover:bg-green-700 text-green-700 hover:text-white flex items-center justify-center transition duration-200">
                                <i class="fa-brands fa-whatsapp text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white border border-stone-100 rounded-3xl p-8 md:p-10 shadow-sm">
                    <h3 class="text-xl font-bold text-green-950 mb-1">Send a Message</h3>
                    <p class="text-stone-400 text-sm mb-8">We'll get back to you within 24 hours.</p>

                    <!---------- CONTACT FORM ---------->

                    <form action="controlContact.php" method="POST" id="contact" class="space-y-5">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Full Name</label>
                                <input type="text" name="name" placeholder="Your name" required
                                    class="w-full bg-stone-100 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Email</label>
                                <input type="email" name="email" placeholder="Your email" required
                                    class="w-full bg-stone-100 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Phone <span class="text-stone-300 normal-case font-normal">(optional)</span></label>
                            <input type="tel" name="phone" placeholder="+880 1700-000000"
                                class="w-full bg-stone-100 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">I am a</label>
                            <select name="role"
                                class="w-full bg-stone-100 border border-stone-200 text-stone-600 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                                <option value="">Select your role</option>
                                <option value="consumer">Consumer</option>
                                <option value="farmer">Farmer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Subject</label>
                            <input type="text" name="subject" placeholder="What is this about?" required
                                class="w-full bg-stone-100 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-widest mb-2">Message</label>
                            <textarea name="message" rows="5" placeholder="Write your message here..." required
                                class="w-full bg-stone-100 border border-stone-200 text-stone-800 placeholder-stone-400 text-sm px-4 py-3 rounded-xl outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition duration-200 resize-none"></textarea>
                        </div>

                        <!-- Success message -->
                        <div class="form-message">
                            <?php if (isset($_SESSION['message_success'])): ?>
                                <div class="bg-green-50 border border-green-200 text-green-700 text-sm font-semibold px-4 py-3 rounded-xl flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <?php echo $_SESSION['message_success'];
                                    unset($_SESSION['message_success']); ?>
                                </div>
                            <?php endif; ?>
                        </div>


                        <button type="submit"
                            class="w-full bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                            <i class="fa-solid fa-paper-plane"></i> Send Message
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <?php include 'footer.php' ?>
</body>

</html>