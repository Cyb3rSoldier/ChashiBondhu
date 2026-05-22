<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['vendor_id'])) {
    header('Location: vendorLogin.php');  // ✅ CORRECT - file is in root
    exit();
}

$vendorId   = $_SESSION['vendor_id'];
$vendorName = $_SESSION['vendor_name'];

// Get vendor's tools
$stmt = $conn->prepare("SELECT * FROM tool_products WHERE vendor_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $vendorId);
$stmt->execute();
$tools = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tools — ChashiBondhu</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-stone-100 min-h-screen overflow-x-hidden">

    <?php include('vendorNavRoot.php'); ?>  <!-- CHANGE THIS LINE -->

    <main class="max-w-6xl mx-auto px-5 pt-24 pb-16">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-green-950">My Tools</h1>
                <p class="text-stone-400 text-sm mt-0.5"><?php echo count($tools); ?> tool<?php echo count($tools) !== 1 ? 's' : ''; ?> listed</p>
            </div>
            <a href="add-tool.php" class="bg-amber-600 hover:bg-amber-500 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition duration-200 shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Tool
            </a>
        </div>

        <?php if (isset($_SESSION['tool_success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-check shrink-0"></i>
                <?php echo $_SESSION['tool_success']; unset($_SESSION['tool_success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['tool_error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl mb-6 flex items-center gap-3 text-sm font-medium">
                <i class="fa-solid fa-circle-exclamation shrink-0"></i>
                <?php echo $_SESSION['tool_error']; unset($_SESSION['tool_error']); ?>
            </div>
        <?php endif; ?>

        <?php if (count($tools) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php foreach ($tools as $tool): ?>
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100 hover:shadow-md transition duration-200 flex flex-col">
                    <div class="relative">
                        <img src="<?php echo $tool['image_path'] ? htmlspecialchars($tool['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                            alt="<?php echo htmlspecialchars($tool['product_name']); ?>" class="w-full h-44 object-cover">
                        <span class="absolute top-2 left-2 text-xs font-bold px-2.5 py-1 rounded-full
                            <?php echo $tool['status'] === 'active' ? 'bg-green-100 text-green-700' : ($tool['status'] === 'out_of_stock' ? 'bg-red-100 text-red-600' : 'bg-stone-100 text-stone-500'); ?>">
                            ● <?php echo ucfirst(str_replace('_', ' ', $tool['status'])); ?>
                        </span>
                        <span class="absolute top-2 right-2 text-xs font-bold px-2.5 py-1 rounded-full
                            <?php echo $tool['condition_status'] === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700'; ?>">
                            <?php echo ucfirst($tool['condition_status']); ?>
                        </span>
                    </div>
                    <div class="p-4 flex flex-col flex-1">
                        <h3 class="font-bold text-green-950 text-base"><?php echo htmlspecialchars($tool['product_name']); ?></h3>
                        <p class="text-xs text-stone-400 mt-0.5"><?php echo htmlspecialchars($tool['category']); ?></p>
                        <?php if ($tool['brand']): ?>
                            <p class="text-xs text-stone-500">Brand: <?php echo htmlspecialchars($tool['brand']); ?></p>
                        <?php endif; ?>
                        <p class="text-amber-600 font-bold text-lg mt-1">৳<?php echo number_format($tool['price'], 0); ?> <span class="text-stone-400 font-normal text-sm">/ <?php echo $tool['unit']; ?></span></p>
                        <p class="text-xs text-stone-500 mt-1">Stock: <?php echo $tool['quantity']; ?> <?php echo $tool['unit']; ?></p>
                        <div class="flex gap-2 mt-auto pt-3">
                            <a href="edit-tool.php?id=<?php echo $tool['id']; ?>" class="flex-1 text-center text-xs bg-amber-50 border border-amber-200 text-amber-700 px-3 py-2 rounded-xl font-semibold hover:bg-amber-100 transition">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </a>
                            <a href="delete-tool.php?id=<?php echo $tool['id']; ?>" 
                                onclick="return confirm('Are you sure you want to delete this tool?')"
                                class="flex-1 text-center text-xs bg-red-50 border border-red-200 text-red-600 px-3 py-2 rounded-xl font-semibold hover:bg-red-100 transition">
                                <i class="fa-solid fa-trash mr-1"></i> Delete
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 shadow-sm text-center py-20 px-6">
                <div class="w-16 h-16 bg-stone-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-wrench text-3xl text-stone-300"></i>
                </div>
                <h3 class="font-bold text-green-950 text-lg mb-2">No tools yet</h3>
                <p class="text-stone-400 text-sm mb-6">Start listing your farming tools and equipment.</p>
                <a href="add-tool.php" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-bold px-6 py-3 rounded-xl text-sm transition">
                    <i class="fa-solid fa-plus"></i> Add Your First Tool
                </a>
            </div>
        <?php endif; ?>
    </main>

    <?php include('footer.php'); ?>
</body>
</html>