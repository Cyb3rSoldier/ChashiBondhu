<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit();
}

$csrf_token = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('farmerNav.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-green-950">Add New Product</h1>
            <p class="text-stone-400 text-sm mt-1">List your fresh produce on the marketplace</p>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['product_error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <?php echo htmlspecialchars($_SESSION['product_error']); unset($_SESSION['product_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['product_success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <?php echo htmlspecialchars($_SESSION['product_success']); unset($_SESSION['product_success']); ?>
            </div>
        <?php endif; ?>

        <form action="process-product.php" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Photo -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <h2 class="font-bold text-green-950">Product Photo</h2>
                </div>

                <label for="product_image" class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-stone-200 rounded-xl cursor-pointer bg-stone-50 hover:bg-green-50 hover:border-green-400 transition relative overflow-hidden">
                    <div id="upload-placeholder" class="flex flex-col items-center gap-2 text-center">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-stone-300"></i>
                        <p class="text-sm font-semibold text-stone-500">Click to upload photo</p>
                        <p class="text-xs text-stone-400">JPG, PNG, WEBP — max 5MB</p>
                    </div>
                    <img id="image-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                    <input type="file" id="product_image" name="product_image" accept="image/*" required class="hidden" onchange="previewImage(event)">
                </label>

                <p id="change-photo" class="hidden text-center mt-2">
                    <label for="product_image" class="text-green-700 text-xs font-semibold cursor-pointer hover:text-green-900 transition">
                        <i class="fa-solid fa-rotate mr-1"></i>Change Photo
                    </label>
                </p>
            </div>

            <!-- Details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-tag"></i>
                    </div>
                    <h2 class="font-bold text-green-950">Product Details</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" name="product_name" required placeholder="e.g. Fresh Organic Tomato"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 placeholder-stone-400 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Category <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <select name="category" required
                                class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                                <option value="">Select a category</option>
                                <option>Vegetables</option>
                                <option>Fruits</option>
                                <option>Grains & Rice</option>
                                <option>Dairy & Eggs</option>
                                <option>Spices</option>
                                <option>Fish & Meat</option>
                                <option>Organic</option>
                                <option>Others</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea name="description" required rows="4" placeholder="Describe your product — freshness, harvest date, farming method..."
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 placeholder-stone-400 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition resize-none"></textarea>
                    </div>

                    <label for="is_organic" class="flex items-center gap-3 bg-green-50 border border-green-100 rounded-xl px-4 py-3 cursor-pointer hover:bg-green-100 transition">
                        <input type="checkbox" name="is_organic" value="1" id="is_organic" class="w-4 h-4 accent-green-600">
                        <div>
                            <p class="text-sm font-semibold text-green-900">🌿 Organic Product</p>
                            <p class="text-xs text-green-600">Grown without synthetic chemicals</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                    <h2 class="font-bold text-green-950">Pricing & Stock</h2>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Your Price <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold text-sm">৳</span>
                                <input type="number" name="price" required step="0.01" min="0" placeholder="0.00"
                                    class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-8 pr-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Unit <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="unit" required
                                    class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                                    <option value="">Select</option>
                                    <option>kg</option>
                                    <option>piece</option>
                                    <option>dozen</option>
                                    <option>liter</option>
                                    <option>gram</option>
                                    <option>packet</option>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Quantity Available <span class="text-red-400">*</span></label>
                        <input type="number" name="quantity" required min="1" placeholder="e.g. 50"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">
                            Government Market Price
                            <span class="normal-case font-normal text-stone-400">(optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold text-sm">৳</span>
                            <input type="number" name="government_price" step="0.01" min="0" placeholder="0.00"
                                class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-8 pr-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                        </div>
                        <p class="text-xs text-stone-400 mt-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-blue-400"></i>
                            Helps buyers compare your price with the official market rate
                        </p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-circle-check"></i> Publish Product to Marketplace
            </button>

            <p class="text-center text-xs text-stone-400 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-green-400"></i>
                Your product will be visible to consumers immediately
            </p>

        </form>
    </main>

    <?php include('footer.php'); ?>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('upload-placeholder').classList.add('hidden');
                const preview = document.getElementById('image-preview');
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                document.getElementById('change-photo').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    </script>

</body>
</html>