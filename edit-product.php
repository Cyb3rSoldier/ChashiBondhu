<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit();
}

$farmerId  = $_SESSION['farmer_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get product — make sure it belongs to this farmer
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $productId, $farmerId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header('Location: my-products.php');
    exit();
}

// ── Handle form submission ───────────────────────────────
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $productName     = trim($_POST['product_name'] ?? '');
    $category        = trim($_POST['category'] ?? '');
    $price           = floatval($_POST['price'] ?? 0);
    $unit            = trim($_POST['unit'] ?? '');
    $quantity        = intval($_POST['quantity'] ?? 0);
    $governmentPrice = !empty($_POST['government_price']) ? floatval($_POST['government_price']) : null;
    $isOrganic       = isset($_POST['is_organic']) ? 1 : 0;
    $description     = trim($_POST['description'] ?? '');
    $status          = $_POST['status'] ?? 'active';

    // Validate
    $errors = [];
    if (empty($productName)) $errors[] = 'Product name is required.';
    if (empty($category))    $errors[] = 'Category is required.';
    if ($price <= 0)         $errors[] = 'Price must be greater than 0.';
    if (empty($unit))        $errors[] = 'Unit is required.';
    if (empty($description)) $errors[] = 'Description is required.';

    // Handle new image upload (optional on edit)
    $imagePath = $product['image_path']; // keep existing by default

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {

        $ext          = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowedTypes)) {
            $errors[] = 'Invalid image format. Use JPG, PNG or WebP.';
        } elseif ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Image size must be less than 5MB.';
        } else {
            $uploadDir = __DIR__ . '/uploads/products/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $filename    = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $destination = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
                // Delete old image file if exists
                if ($product['image_path'] && file_exists(__DIR__ . '/' . $product['image_path'])) {
                    unlink(__DIR__ . '/' . $product['image_path']);
                }
                $imagePath = 'uploads/products/' . $filename;
            } else {
                $errors[] = 'Failed to upload image. Check folder permissions.';
            }
        }
    }

    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        // Update badge
        $badge = 'new';
        if ($governmentPrice && $price < $governmentPrice) {
            $badge = 'sale';
        }

        $stmt = $conn->prepare("
            UPDATE products SET
                product_name    = ?,
                category        = ?,
                price           = ?,
                unit            = ?,
                quantity        = ?,
                government_price= ?,
                is_organic      = ?,
                description     = ?,
                status          = ?,
                image_path      = ?,
                badge           = ?
            WHERE id = ? AND farmer_id = ?
        ");

        $stmt->bind_param(
            "ssdsidisssiii",
            $productName,
            $category,
            $price,
            $unit,
            $quantity,
            $governmentPrice,
            $isOrganic,
            $description,
            $status,
            $imagePath,
            $badge,
            $productId,
            $farmerId
        );

        if ($stmt->execute()) {
            $_SESSION['product_success'] = 'Product updated successfully!';
            $stmt->close();
            header('Location: my-products.php');
            exit();
        } else {
            error_log("Product update error: " . $stmt->error);
            $error = 'Update failed. Please try again.';
        }
        $stmt->close();
    }
}
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
            <a href="my-products.php" class="inline-flex items-center gap-2 text-green-700 text-sm font-semibold hover:text-green-900 transition mb-5">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to My Products
            </a>
            <h1 class="text-2xl font-bold text-green-950">Edit Product</h1>
            <p class="text-stone-400 text-sm mt-1">Update your product details</p>
        </div>

        <!-- Error Alert -->
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-5">

            <!-- Current Image + Upload New -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-stone-100">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center text-sm">
                        <i class="fa-solid fa-image"></i>
                    </div>
                    <h2 class="font-bold text-green-950">Product Photo</h2>
                </div>

                <!-- Current image -->
                <?php if ($product['image_path']): ?>
                    <div class="mb-4">
                        <p class="text-xs text-stone-400 font-semibold uppercase tracking-wider mb-2">Current Photo</p>
                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                            alt="Current product"
                            class="w-full h-44 object-cover rounded-xl border border-stone-100">
                    </div>
                <?php endif; ?>

                <!-- Upload new -->
                <label for="product_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-stone-200 rounded-xl cursor-pointer bg-stone-50 hover:bg-green-50 hover:border-green-400 transition relative overflow-hidden">
                    <div id="upload-placeholder" class="flex flex-col items-center gap-1 text-center">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-stone-300"></i>
                        <p class="text-sm font-semibold text-stone-500">Click to upload a new photo</p>
                        <p class="text-xs text-stone-400">Leave empty to keep current photo</p>
                    </div>
                    <img id="image-preview" src="#" alt="Preview" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl">
                    <input type="file" id="product_image" name="product_image" accept="image/*" class="hidden" onchange="previewImage(event)">
                </label>

                <p id="change-photo" class="hidden text-center mt-2">
                    <label for="product_image" class="text-green-700 text-xs font-semibold cursor-pointer hover:text-green-900 transition">
                        <i class="fa-solid fa-rotate mr-1"></i>Choose Different Photo
                    </label>
                </p>
            </div>

            <!-- Product Details -->
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
                        <input type="text" name="product_name" required
                            value="<?php echo htmlspecialchars($product['product_name']); ?>"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Category <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <select name="category" required class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                                <?php
                                $cats = ['Vegetables', 'Fruits', 'Grains & Rice', 'Dairy & Eggs', 'Spices', 'Fish & Meat', 'Organic', 'Others'];
                                foreach ($cats as $cat): ?>
                                    <option <?php echo $product['category'] === $cat ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Description <span class="text-red-400">*</span></label>
                        <textarea name="description" required rows="4"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition resize-none"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <label for="is_organic" class="flex items-center gap-3 bg-green-50 border border-green-100 rounded-xl px-4 py-3 cursor-pointer hover:bg-green-100 transition">
                        <input type="checkbox" name="is_organic" value="1" id="is_organic"
                            <?php echo $product['is_organic'] ? 'checked' : ''; ?>
                            class="w-4 h-4 accent-green-600">
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
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Price <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold text-sm">৳</span>
                                <input type="number" name="price" required step="0.01" min="0"
                                    value="<?php echo $product['price']; ?>"
                                    class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-8 pr-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Unit <span class="text-red-400">*</span></label>
                            <div class="relative">
                                <select name="unit" required class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                                    <?php
                                    $units = ['kg', 'piece', 'dozen', 'liter', 'gram', 'packet'];
                                    foreach ($units as $u): ?>
                                        <option <?php echo $product['unit'] === $u ? 'selected' : ''; ?>>
                                            <?php echo $u; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Quantity Available <span class="text-red-400">*</span></label>
                        <input type="number" name="quantity" required min="0"
                            value="<?php echo $product['quantity']; ?>"
                            class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">
                            Government Market Price
                            <span class="normal-case font-normal text-stone-400">(optional)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-stone-500 font-bold text-sm">৳</span>
                            <input type="number" name="government_price" step="0.01" min="0"
                                value="<?php echo $product['government_price'] ?? ''; ?>"
                                class="w-full bg-stone-50 border border-stone-200 rounded-xl pl-8 pr-4 py-3 text-sm text-stone-800 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Status</label>
                        <div class="relative">
                            <select name="status" class="w-full appearance-none bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm text-stone-700 outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition">
                                <option value="active" <?php echo $product['status'] === 'active'       ? 'selected' : ''; ?>>● Active</option>
                                <option value="out_of_stock" <?php echo $product['status'] === 'out_of_stock' ? 'selected' : ''; ?>>● Out of Stock</option>
                                <option value="hidden" <?php echo $product['status'] === 'hidden'       ? 'selected' : ''; ?>>● Hidden</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-stone-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-green-700 hover:bg-green-600 active:bg-green-800 text-white font-bold py-4 rounded-2xl transition duration-200 shadow-md flex items-center justify-center gap-2 text-sm">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>

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