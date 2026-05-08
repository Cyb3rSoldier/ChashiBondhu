
<?php
require_once '../config/session.php';
require_once '../config/security.php';
require_once '../config/database.php';

Session::start();

if (!isset($_SESSION['farmer_id'])) {
    header('Location: ../farmerLogin.php');
    exit;
}

$farmerId = $_SESSION['farmer_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$csrf_token = Security::generateCSRFToken();

// Get product
$conn = Database::getConnection();
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $productId, $farmerId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: my-products.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Security validation failed';
    } else {
        $productName = trim($_POST['product_name'] ?? '');
        $category = $_POST['category'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $unit = $_POST['unit'] ?? '';
        $quantity = intval($_POST['quantity'] ?? 0);
        $governmentPrice = !empty($_POST['government_price']) ? floatval($_POST['government_price']) : null;
        $isOrganic = isset($_POST['is_organic']) ? 1 : 0;
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        $stmt = $conn->prepare(
            "UPDATE products SET product_name=?, category=?, price=?, unit=?, quantity=?, government_price=?, is_organic=?, description=?, status=? WHERE id=? AND farmer_id=?"
        );
        $stmt->bind_param("ssdsidisiii", $productName, $category, $price, $unit, $quantity, $governmentPrice, $isOrganic, $description, $status, $productId, $farmerId);
        
        if ($stmt->execute()) {
            $_SESSION['product_success'] = 'Product updated successfully!';
            header('Location: my-products.php');
            exit;
        } else {
            $error = 'Update failed';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="stylesheet" href="../asset/css/marketplace.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-50 min-h-screen">
    <?php include('../navbar2.php'); ?>
    <div class="pt-24 pb-12 px-4" style="max-width:650px;margin:0 auto;">
        <a href="my-products.php" class="text-green-600 text-sm mb-4 inline-block">← Back</a>
        <h1 class="text-2xl font-bold text-green-950 mb-6">✏️ Edit Product</h1>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white rounded-2xl p-6 shadow-sm space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Product Name *</label>
                <input type="text" name="product_name" required value="<?php echo htmlspecialchars($product['product_name']); ?>"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Category *</label>
                <select name="category" required class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
                    <?php $cats = ['Vegetables','Fruits','Grains & Rice','Dairy & Eggs','Spices','Fish & Meat','Organic','Others'];
                    foreach($cats as $cat): ?>
                        <option <?php echo $product['category'] == $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Price *</label>
                    <input type="number" name="price" required step="0.01" value="<?php echo $product['price']; ?>"
                        class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Unit *</label>
                    <select name="unit" required class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
                        <?php $units = ['kg','piece','dozen','liter','gram','packet'];
                        foreach($units as $u): ?>
                            <option <?php echo $product['unit'] == $u ? 'selected' : ''; ?>><?php echo $u; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Quantity *</label>
                <input type="number" name="quantity" required value="<?php echo $product['quantity']; ?>"
                    class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Status</label>
                <select name="status" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none">
                    <option value="active" <?php echo $product['status']=='active'?'selected':''; ?>>Active</option>
                    <option value="out_of_stock" <?php echo $product['status']=='out_of_stock'?'selected':''; ?>>Out of Stock</option>
                    <option value="hidden" <?php echo $product['status']=='hidden'?'selected':''; ?>>Hidden</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_organic" value="1" <?php echo $product['is_organic']?'checked':''; ?> class="w-4 h-4 accent-green-600">
                <label class="text-sm text-stone-700">🌿 Organic product</label>
            </div>
            <div>
                <label class="block text-xs font-bold text-stone-500 uppercase mb-2">Description *</label>
                <textarea name="description" required rows="4" class="w-full bg-stone-50 border border-stone-200 rounded-xl px-4 py-3 text-sm focus:border-green-500 outline-none resize-none"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <button type="submit" class="btn-primary">💾 Save Changes</button>
        </form>
    </div>
</body>
</html>