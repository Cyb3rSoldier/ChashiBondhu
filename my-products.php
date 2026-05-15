<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit();
}

$farmerId   = $_SESSION['farmer_id'];
$farmerName = $_SESSION['farmer_name'];

// Get farmer's products
$stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $farmerId);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

    <main class="max-w-6xl mx-auto px-5 pt-24 pb-16">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-green-950">My Products</h1>
                <p class="text-stone-400 text-sm mt-0.5">
                    <?php echo count($products); ?> product<?php echo count($products) !== 1 ? 's' : ''; ?> listed
                </p>
            </div>
            <a href="add-product.php"
                class="bg-green-700 hover:bg-green-600 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Product
            </a>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['product_success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <?php echo htmlspecialchars($_SESSION['product_success']);
                unset($_SESSION['product_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['product_error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <?php echo htmlspecialchars($_SESSION['product_error']);
                unset($_SESSION['product_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($products) > 0): ?>

            <!-- Summary Bar -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <?php
                $active      = array_filter($products, fn($p) => $p['status'] === 'active');
                $outOfStock  = array_filter($products, fn($p) => $p['status'] === 'out_of_stock');
                $hidden      = array_filter($products, fn($p) => $p['status'] === 'hidden');
                $organic     = array_filter($products, fn($p) => $p['is_organic'] == 1);
                ?>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                    <p class="text-2xl font-bold text-green-700"><?php echo count($products); ?></p>
                    <p class="text-stone-400 text-xs mt-1">Total Products</p>
                </div>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                    <p class="text-2xl font-bold text-green-600"><?php echo count($active); ?></p>
                    <p class="text-stone-400 text-xs mt-1">Active</p>
                </div>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                    <p class="text-2xl font-bold text-red-400"><?php echo count($outOfStock); ?></p>
                    <p class="text-stone-400 text-xs mt-1">Out of Stock</p>
                </div>
                <div class="bg-white rounded-2xl p-4 shadow-sm border border-stone-100 text-center">
                    <p class="text-2xl font-bold text-emerald-600"><?php echo count($organic); ?></p>
                    <p class="text-stone-400 text-xs mt-1">Organic</p>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100 hover:shadow-md transition duration-200 flex flex-col">

                        <!-- Image -->
                        <div class="relative">

                            <?php
                            $image = 'asset/img/placeholder-product.jpg';

                            if (!empty($product['image_path'])) {

                                $fullPath = __DIR__ . '/' . $product['image_path'];

                                if (file_exists($fullPath)) {
                                    $image = 'http://localhost/Web-Project/ChashiBondhu/' . $product['image_path'];
                                }
                            }
                            ?>

                            <img
                                src="<?php echo $image; ?>"
                                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                class="w-full h-44 object-cover">

                            <!-- Status Badge -->
                            <span class="absolute top-2 left-2 text-xs font-bold px-2.5 py-1 rounded-full
    <?php
                    if ($product['status'] === 'active') {
                        echo 'bg-green-100 text-green-700';
                    } elseif ($product['status'] === 'out_of_stock') {
                        echo 'bg-red-100 text-red-600';
                    } else {
                        echo 'bg-stone-100 text-stone-500';
                    }
    ?>">

                                <?php
                                if ($product['status'] === 'active') {
                                    echo '● Active';
                                } elseif ($product['status'] === 'out_of_stock') {
                                    echo '● Out of Stock';
                                } else {
                                    echo '● Hidden';
                                }
                                ?>

                            </span>

                            <!-- Organic Badge -->
                            <?php if ($product['is_organic']): ?>
                                <span class="absolute top-2 right-2 text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700">
                                    🌿 Organic
                                </span>
                            <?php endif; ?>

                        </div>

                        <!-- Info -->
                        <div class="p-4 flex flex-col flex-1">
                            <div class="mb-1 flex items-start justify-between gap-2">
                                <h3 class="font-bold text-green-950 text-base leading-snug">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </h3>
                                <span class="text-xs text-stone-400 shrink-0"><?php echo $product['category']; ?></span>
                            </div>

                            <p class="text-green-700 font-bold text-lg mb-1">
                                ৳<?php echo number_format($product['price'], 0); ?>
                                <span class="text-stone-400 font-normal text-sm">/ <?php echo $product['unit']; ?></span>
                            </p>

                            <?php if ($product['government_price']): ?>
                                <p class="text-xs text-stone-400 mb-1">
                                    Market price: <span class="line-through">৳<?php echo number_format($product['government_price'], 0); ?></span>
                                </p>
                            <?php endif; ?>

                            <p class="text-xs text-stone-500 mb-3">
                                Stock: <span class="font-semibold text-stone-700"><?php echo $product['quantity']; ?> <?php echo $product['unit']; ?></span>
                            </p>

                            <!-- Actions -->
                            <div class="flex gap-2 mt-auto">
                                <a href="edit-product.php?id=<?php echo $product['id']; ?>"
                                    class="flex-1 text-center text-xs bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded-xl font-semibold hover:bg-amber-100 transition">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                                </a>
                                <a href="delete-product.php?id=<?php echo $product['id']; ?>"
                                    class="flex-1 text-center text-xs bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl font-semibold hover:bg-red-100 transition"
                                    onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fa-solid fa-trash mr-1"></i> Delete
                                </a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>

            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-box-open text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">No products yet</h3>
                <p class="text-stone-400 text-sm mb-6 max-w-xs mx-auto">
                    Start listing your fresh produce to reach consumers across Bangladesh.
                </p>
                <a href="add-product.php"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-600 text-white font-bold px-6 py-3 rounded-xl text-sm transition duration-200 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Add Your First Product
                </a>
            </div>

        <?php endif; ?>

    </main>

    <?php include('footer.php'); ?>

</body>

</html>