
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['vendor_id'])) {
    header('Location: vendor/vendorLogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add-tool.php');
    exit();
}

$vendorId = $_SESSION['vendor_id'];

// Collect inputs
$productName      = trim($_POST['product_name'] ?? '');
$category         = trim($_POST['category'] ?? '');
$price            = floatval($_POST['price'] ?? 0);
$unit             = trim($_POST['unit'] ?? '');
$quantity         = intval($_POST['quantity'] ?? 0);
$brand            = trim($_POST['brand'] ?? '');
$conditionStatus  = $_POST['condition_status'] ?? 'new';
$description      = trim($_POST['description'] ?? '');

// Validate
$errors = [];
if (empty($productName))  $errors[] = 'Product name is required.';
if (empty($category))     $errors[] = 'Category is required.';
if ($price <= 0)          $errors[] = 'Price must be greater than 0.';
if (empty($unit))         $errors[] = 'Unit is required.';
if ($quantity < 0)        $errors[] = 'Quantity cannot be negative.';
if (empty($description))  $errors[] = 'Description is required.';

// Handle image upload
$imagePath = null;

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    $ext          = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'Invalid image format. Use JPG, PNG or WebP.';
    } elseif ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Image size must be less than 5MB.';
    } else {
        $uploadDir = __DIR__ . '/uploads/tools/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename    = 'tool_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
            $imagePath = 'uploads/tools/' . $filename;
        } else {
            $errors[] = 'Failed to upload image. Check folder permissions.';
        }
    }
} else {
    $errors[] = 'Product image is required.';
}

if (!empty($errors)) {
    $_SESSION['tool_error'] = implode('<br>', $errors);
    header('Location: add-tool.php');
    exit();
}

// Generate slug
function createSlug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return 'tool-' . $text . '-' . time();
}

$slug = createSlug($productName);

// Insert into database
$stmt = $conn->prepare("
    INSERT INTO tool_products 
        (vendor_id, product_name, slug, category, price, quantity, unit, description, image_path, brand, condition_status, status) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
");

$stmt->bind_param(
    "isssdisssss",
    $vendorId, $productName, $slug, $category, $price,
    $quantity, $unit, $description, $imagePath, $brand, $conditionStatus
);

if ($stmt->execute()) {
    $_SESSION['tool_success'] = 'Tool added successfully! It is now live in the marketplace.';
    $stmt->close();
    $conn->close();
    header('Location: add-tool.php');
    exit();
} else {
    error_log("Tool save error: " . $stmt->error);
    $_SESSION['tool_error'] = 'Failed to save tool. Please try again.';
    $stmt->close();
    $conn->close();
    header('Location: add-tool.php');
    exit();
}