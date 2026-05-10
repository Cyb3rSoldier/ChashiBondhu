<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add-product.php');
    exit();
}

$farmerId = $_SESSION['farmer_id'];

// ── Collect inputs ───────────────────────────────────────

$productName     = trim($_POST['product_name'] ?? '');
$category        = trim($_POST['category'] ?? '');
$price           = floatval($_POST['price'] ?? 0);
$unit            = trim($_POST['unit'] ?? '');
$quantity        = intval($_POST['quantity'] ?? 0);
$governmentPrice = !empty($_POST['government_price']) ? floatval($_POST['government_price']) : null;
$isOrganic       = isset($_POST['is_organic']) ? 1 : 0;
$description     = trim($_POST['description'] ?? '');

// ── Validate ─────────────────────────────────────────────

$errors = [];

if (empty($productName))  $errors[] = 'Product name is required.';
if (empty($category))     $errors[] = 'Category is required.';
if ($price <= 0)          $errors[] = 'Price must be greater than 0.';
if (empty($unit))         $errors[] = 'Unit is required.';
if ($quantity < 0)        $errors[] = 'Quantity cannot be negative.';
if (empty($description))  $errors[] = 'Description is required.';

// ── Handle image upload ──────────────────────────────────

$imagePath = null;

if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {

    $ext          = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'Invalid image format. Use JPG, PNG or WebP.';
    } elseif ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Image size must be less than 5MB.';
    } else {
        // Use absolute path for saving
        $uploadDir = __DIR__ . '/uploads/products/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename    = 'product_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        $destination = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
            // Store relative path for display in browser
            $imagePath = 'uploads/products/' . $filename;
        } else {
            $errors[] = 'Failed to upload image. Check folder permissions.';
        }
    }
} else {
    $errors[] = 'Product image is required.';
}

// ── Return errors if any ─────────────────────────────────

if (!empty($errors)) {
    $_SESSION['product_error'] = implode('<br>', $errors);
    header('Location: add-product.php');
    exit();
}

// ── Generate slug ────────────────────────────────────────

function createSlug(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return $text . '-' . time();
}

$slug = createSlug($productName);

// ── Determine badge ──────────────────────────────────────

$badge = 'new';
if ($governmentPrice && $price < $governmentPrice) {
    $badge = 'sale';
}

// ── Insert into database ─────────────────────────────────

$stmt = $conn->prepare("
    INSERT INTO products 
        (farmer_id, product_name, slug, category, price, quantity, unit, description, image_path, government_price, is_organic, badge, status) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
");

$stmt->bind_param(
    "isssdissdiss",
    $farmerId,
    $productName,
    $slug,
    $category,
    $price,
    $quantity,
    $unit,
    $description,
    $imagePath,
    $governmentPrice,
    $isOrganic,
    $badge
);

if ($stmt->execute()) {
    $_SESSION['product_success'] = 'Product added successfully! It is now live in the marketplace.';
    $stmt->close();
    $conn->close();
    header('Location: add-product.php');
    exit();
} else {
    error_log("Product save error: " . $stmt->error);
    $_SESSION['product_error'] = 'Failed to save product. Please try again.';
    $stmt->close();
    $conn->close();
    header('Location: add-product.php');
    exit();
}
