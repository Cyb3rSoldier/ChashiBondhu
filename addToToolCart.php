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

// Read JSON body
$body = json_decode(file_get_contents('php://input'), true);
$toolId = intval($body['tool_id'] ?? $_POST['tool_id'] ?? 0);
$quantity = intval($body['quantity'] ?? $_POST['quantity'] ?? 1);

if ($toolId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid tool or quantity']);
    exit;
}

// Check tool exists and is active
$stmt = $conn->prepare("SELECT id, quantity, product_name FROM tool_products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $toolId);
$stmt->execute();
$result = $stmt->get_result();
$tool = $result->fetch_assoc();
$stmt->close();

if (!$tool) {
    echo json_encode(['success' => false, 'message' => 'Tool not available']);
    exit;
}

if ($tool['quantity'] < $quantity) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock available. Only ' . $tool['quantity'] . ' left.']);
    exit;
}

// Check if already in tool cart
$stmt = $conn->prepare("SELECT id, quantity FROM tool_cart WHERE consumer_id = ? AND tool_id = ?");
$stmt->bind_param("ii", $consumerId, $toolId);
$stmt->execute();
$result = $stmt->get_result();
$existing = $result->fetch_assoc();
$stmt->close();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    if ($newQty > $tool['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Cannot add more than available stock']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE tool_cart SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $newQty, $existing['id']);
    $stmt->execute();
    $stmt->close();
} else {
    $stmt = $conn->prepare("INSERT INTO tool_cart (consumer_id, tool_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $consumerId, $toolId, $quantity);
    $stmt->execute();
    $stmt->close();
}

// Get updated cart count for all tools
$countStmt = $conn->prepare("SELECT SUM(quantity) as total FROM tool_cart WHERE consumer_id = ?");
$countStmt->bind_param("i", $consumerId);
$countStmt->execute();
$toolCartCount = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
$countStmt->close();

// Also get regular cart count
$regCountStmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE consumer_id = ?");
$regCountStmt->bind_param("i", $consumerId);
$regCountStmt->execute();
$regularCartCount = $regCountStmt->get_result()->fetch_assoc()['total'] ?? 0;
$regCountStmt->close();

echo json_encode([
    'success' => true, 
    'message' => 'Added to cart!',
    'tool_cart_count' => $toolCartCount,
    'cart_count' => $regularCartCount
]);
?>