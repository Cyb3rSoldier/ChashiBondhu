<?php
session_start();
if (!isset($_SESSION['vendor_id'])) {
    header('Location: vendor/vendorLogin.php');
    exit();
}
require_once 'config.php';

$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tool — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('vendorNavRoot.php'); ?>  <!-- CHANGE THIS LINE -->

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-green-950">Add New Tool / Equipment</h1>
            <p class="text-stone-400 text-sm mt-1">List your farming tools, machines, seeds or fertilizers</p>
        </div>

        <?php if (isset($_SESSION['tool_error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-5 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <?php echo $_SESSION['tool_error']; unset($_SESSION['tool_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['tool_success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-5 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <?php echo $_SESSION['tool_success']; unset($_SESSION['tool_success']); ?>
            </div>
        <?php endif; ?>

        <form action="process-tool.php" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Photo -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-image"></i></div>
                    <h2 class="font-bold text-green-950">Tool Photo</h2>
                </div>

                <label for="product_image" class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-stone-200 rounded-xl cursor-pointer bg-stone-50 hover:bg-amber-50 hover:border-amber-400 transition relative overflow-hidden">
                    <div id="upload-placeholder" class="flex flex-col items-center gap-2 text-center">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-stone-300"></i>
                        <p class="text-sm font-semibold text-stone-500">Click to upload photo</p>
                        <p class="text-xs text-stone-400">JPG, PNG, WEBP — max 5MB</p>
                    </div>
                    <img id="image-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                    <input type="file" id="product_image" name="product_image" accept="image/*" required class="hidden" onchange="previewImage(event)">
                </label>

                <p id="change-photo" class="hidden text-center mt-2">
                    <label for="product_image" class="text-amber-700 text-xs font-semibold cursor-pointer hover:text-amber-900 transition">
                        <i class="fa-solid fa-rotate mr-1"></i>Change Photo
                    </label>
                </p>
            </div>

            <!-- Details -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-sm"><i class="fa-solid fa-tag"></i></div>
                    <h2 class="font-bold text-green-950">Product Details</h2>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Product Name <span class="text-red-400">*</span></label>
                        <input type="text" name="product_name" required placeholder="e.g. Power Tiller Machine"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 placeholder-stone-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Category <span class="text-red-400">*</span></label>
                            <select name="category" required
                                class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                                <option value="">Select category</option>
                                <option>Tractors & Power Tillers</option>
                                <option>Plows & Harrows</option>
                                <option>Seeders & Planters</option>
                                <option>Irrigation Equipment</option>
                                <option>Harvesters & Threshers</option>
                                <option>Sprayers & Spreaders</option>
                                <option>Hand Tools</option>
                                <option>Seeds</option>
                                <option>Fertilizers & Pesticides</option>
                                <option>Protective Gear</option>
                                <option>Spare Parts</option>
                                <option>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Brand</label>
                            <input type="text" name="brand" placeholder="e.g. ACI, Metal, RFL"
                                class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 placeholder-stone-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Condition <span class="text-red-400">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="condition_status" value="new" checked class="accent-amber-600"> New
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="condition_status" value="used" class="accent-amber-600"> Used
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea name="description" required rows="4" placeholder="Describe your tool — specifications, usage, condition details..."
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 placeholder-stone-400 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing & Stock -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-sm"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <h2 class="font-bold text-green-950">Pricing & Stock</h2>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Price <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold text-sm">৳</span>
                                <input type="number" name="price" required step="0.01" min="0" placeholder="0.00"
                                    class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-8 pr-4 py-3 text-sm text-stone-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Unit <span class="text-red-400">*</span></label>
                            <select name="unit" required
                                class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                                <option value="">Select</option>
                                <option>piece</option>
                                <option>set</option>
                                <option>pack</option>
                                <option>kg</option>
                                <option>liter</option>
                                <option>box</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Quantity Available <span class="text-red-400">*</span></label>
                        <input type="number" name="quantity" required min="1" placeholder="e.g. 10"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition">
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-amber-600 hover:bg-amber-500 active:bg-amber-700 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-circle-check"></i> Publish Tool to Marketplace
            </button>

            <p class="text-center text-xs text-stone-400 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-shield-halved text-amber-400"></i>
                Your tool will be visible to farmers immediately
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