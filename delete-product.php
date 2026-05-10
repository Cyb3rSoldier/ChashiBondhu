<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['farmer_id'])) {
    header('Location: farmerLogin.php');
    exit();
}

$farmerId  = $_SESSION['farmer_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId > 0) {

    // Get image path before deleting so we can remove the file
    $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $productId, $farmerId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
        $stmt->bind_param("ii", $productId, $farmerId);
        $stmt->execute();
        $stmt->close();

        // Delete image file from server
        if (!empty($row['image_path'])) {
            $fullPath = __DIR__ . '/' . $row['image_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $_SESSION['product_success'] = 'Product deleted successfully.';
    } else {
        // Product not found or doesn't belong to this farmer
        $_SESSION['product_error'] = 'Product not found or access denied.';
    }

} else {
    $_SESSION['product_error'] = 'Invalid product.';
}

$conn->close();
header('Location: my-products.php');
exit();
?>