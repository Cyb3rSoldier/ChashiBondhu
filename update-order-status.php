<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['vendor_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$orderId = intval($body['order_id'] ?? 0);
$status = $body['status'] ?? '';

$allowedStatuses = ['processing', 'completed', 'cancelled'];
if (!in_array($status, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$vendorId = $_SESSION['vendor_id'];

// Verify this vendor has items in this tool order
$checkStmt = $conn->prepare("
    SELECT COUNT(*) as count FROM tool_order_items 
    WHERE order_id = ? AND vendor_id = ?
");
$checkStmt->bind_param("ii", $orderId, $vendorId);
$checkStmt->execute();
$result = $checkStmt->get_result()->fetch_assoc();
$checkStmt->close();

if ($result['count'] == 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Update tool order status
$stmt = $conn->prepare("UPDATE tool_orders SET order_status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
$stmt->close();
$conn->close();
?>