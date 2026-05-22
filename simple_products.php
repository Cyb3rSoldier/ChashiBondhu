
<?php
require_once 'config.php';

// Get all products
$result = $conn->query("SELECT id, product_name, image_path FROM products WHERE image_path IS NOT NULL AND image_path != '0' AND image_path != '' ORDER BY id DESC");

echo "<h1>All Products with Images</h1>";
echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";

while ($product = $result->fetch_assoc()) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; width: 200px; text-align: center;'>";
    echo "<h3>" . htmlspecialchars($product['product_name']) . "</h3>";
    
    // Direct image tag - simplest possible
    $imagePath = $product['image_path'];
    echo "<img src='$imagePath' style='width: 100%; height: 150px; object-fit: cover;' onerror='this.src=\"asset/img/placeholder-product.jpg\"'>";
    echo "<p>Path: $imagePath</p>";
    echo "</div>";
}

echo "</div>";
?>