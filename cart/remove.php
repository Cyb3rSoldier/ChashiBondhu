
<?php
/**
 * Remove Item from Cart
 */
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$cartId = intval($_POST['cart_id'] ?? 0);

try {
    $conn = Database::getConnection();
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND consumer_id = ?");
    $stmt->bind_param("ii", $cartId, $consumerId);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Item removed']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error']);
}