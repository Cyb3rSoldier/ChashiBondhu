<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['farmer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$farmerId = $_SESSION['farmer_id'];
$orderId  = intval($_POST['order_id'] ?? 0);
$status   = trim($_POST['status'] ?? '');

$allowedStatuses = ['processing', 'completed', 'cancelled'];

if ($orderId <= 0 || !in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid order or status']);
    exit;
}

// Verify this order contains items from this farmer
$check = $conn->prepare("
    SELECT oi.id FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    WHERE oi.order_id = ? AND oi.farmer_id = ?
    LIMIT 1
");
$check->bind_param("ii", $orderId, $farmerId);
$check->execute();
$valid = $check->get_result()->fetch_assoc();
$check->close();

if (!$valid) {
    echo json_encode(['success' => false, 'message' => 'Order not found or access denied']);
    exit;
}

// If cancelling, restore product stock
if ($status === 'cancelled') {
    $items = $conn->prepare("
        SELECT product_id, quantity FROM order_items
        WHERE order_id = ? AND farmer_id = ?
    ");
    $items->bind_param("ii", $orderId, $farmerId);
    $items->execute();
    $orderItems = $items->get_result()->fetch_all(MYSQLI_ASSOC);
    $items->close();

    foreach ($orderItems as $item) {
        $conn->query("
            UPDATE products
            SET quantity = quantity + {$item['quantity']}
            WHERE id = {$item['product_id']}
        ");
    }
}

// Update order status
$stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    $stmt->close();
    echo json_encode([
        'success' => true,
        'message' => 'Order ' . ucfirst($status),
        'status'  => $status
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>