<?php
session_start();
if (!isset($_SESSION['consumer_id'])) {
    die('Login required');
}
require_once 'config.php';

$product_Id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$review = isset($_POST['review']) ? trim($_POST['review']) : '';
$consumer_id = $_SESSION['consumer_id'];

// 1. Validate product exists
$checkProduct = $conn->prepare("SELECT id FROM products WHERE id = ?");
$checkProduct->bind_param("i", $product_Id);
$checkProduct->execute();
$productResult = $checkProduct->get_result();
if ($productResult->num_rows === 0) {
    die('Invalid product. The product may have been removed or does not exist.');
}
$checkProduct->close();

// 2. Validate rating range
if ($rating < 1 || $rating > 5) {
    die('Rating must be between 1 and 5.');
}

// 3. Insert or update review
$stmt = $conn->prepare("INSERT INTO product_reviews (product_id, consumer_id, rating, review) 
                        VALUES (?, ?, ?, ?) 
                        ON DUPLICATE KEY UPDATE rating = ?, review = ?");
if (!$stmt) {
    die('Database error: ' . $conn->error);
}

$stmt->bind_param("iiisss", $product_Id, $consumer_id, $rating, $review, $rating, $review);

if ($stmt->execute()) {
    // Success – redirect back to product page
    header("Location: details.php?id=" . $product_Id);
    exit();
} else {
    die('Failed to save review: ' . $stmt->error);
}
$stmt->close();
?>