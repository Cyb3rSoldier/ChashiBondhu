
<?php
require_once '../config/session.php';
require_once '../config/database.php';

Session::start();

if (!isset($_SESSION['farmer_id'])) {
    header('Location: ../farmerLogin.php');
    exit;
}

$farmerId = $_SESSION['farmer_id'];
$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($productId > 0) {
    $conn = Database::getConnection();
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ? AND farmer_id = ?");
    $stmt->bind_param("ii", $productId, $farmerId);
    $stmt->execute();
    $_SESSION['product_success'] = 'Product deleted.';
}

header('Location: my-products.php');
exit;