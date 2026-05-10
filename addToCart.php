<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$consumerId = $_SESSION['consumer_id'];

// Read JSON body (from fetch with Content-Type: application/json)
$body       = json_decode(file_get_contents('php://input'), true);
$productId  = intval($body['product_id'] ?? $_POST['product_id'] ?? 0);
$quantity   = intval($body['quantity']   ?? $_POST['quantity']   ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

// Check product exists and is active
$stmt = $conn->prepare("SELECT id, quantity FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not available']);
    exit;
}

if ($product['quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
    exit;
}

// Check if already in cart
$stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE consumer_id = ? AND product_id = ?");
$stmt->bind_param("ii", $consumerId, $productId);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    $stmt   = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $newQty, $existing['id']);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO cart (consumer_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $consumerId, $productId, $quantity);
    $stmt->execute();
    $stmt->close();
}

// Get updated cart count
$countStmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE consumer_id = ?");
$countStmt->bind_param("i", $consumerId);
$countStmt->execute();
$cartCount = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$countStmt->close();

echo json_encode(['success' => true, 'message' => 'Added to cart!', 'count' => $cartCount]);
?>