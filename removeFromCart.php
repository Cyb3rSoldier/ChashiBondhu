<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$cartId     = intval($_POST['cart_id'] ?? 0);

if ($cartId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid item']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND consumer_id = ?");
$stmt->bind_param("ii", $cartId, $consumerId);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true, 'message' => 'Item removed']);
?>