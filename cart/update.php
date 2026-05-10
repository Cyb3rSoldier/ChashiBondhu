
<?php
/**
 * Update Cart Item Quantity
 */
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$cartId = intval($_POST['cart_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($cartId <= 0 || $quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    $conn = Database::getConnection();
    
    if ($quantity == 0) {
        // Remove item
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND consumer_id = ?");
        $stmt->bind_param("ii", $cartId, $consumerId);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Item removed', 'quantity' => 0]);
    } else {
        // Update quantity
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND consumer_id = ?");
        $stmt->bind_param("iii", $quantity, $cartId, $consumerId);
        $stmt->execute();
        echo json_encode(['success' => true, 'message' => 'Quantity updated', 'quantity' => $quantity]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}