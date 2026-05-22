
<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$cartId = intval($_POST['cart_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($cartId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart item']);
    exit;
}

if ($quantity <= 0) {
    $stmt = $conn->prepare("DELETE FROM tool_cart WHERE id = ? AND consumer_id = ?");
    $stmt->bind_param("ii", $cartId, $consumerId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Item removed']);
} else {
    // Check stock
    $stmt = $conn->prepare("SELECT tp.quantity FROM tool_cart tc JOIN tool_products tp ON tc.tool_id = tp.id WHERE tc.id = ? AND tc.consumer_id = ?");
    $stmt->bind_param("ii", $cartId, $consumerId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || $quantity > $row['quantity']) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE tool_cart SET quantity = ? WHERE id = ? AND consumer_id = ?");
    $stmt->bind_param("iii", $quantity, $cartId, $consumerId);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true, 'message' => 'Updated']);
}
?>