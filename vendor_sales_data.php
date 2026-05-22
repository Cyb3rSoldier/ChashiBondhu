<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['vendor_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}
require_once 'config.php';

$vendorId = $_SESSION['vendor_id'];

// Daily sales (last 7 days)
$daily = $conn->query("
    SELECT DATE(o.created_at) as date, COALESCE(SUM(toi.total_price), 0) as total
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = $vendorId 
      AND o.order_status NOT IN ('cancelled')
      AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(o.created_at)
")->fetch_all(MYSQLI_ASSOC);

// Monthly sales (last 6 months)
$monthly = $conn->query("
    SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, COALESCE(SUM(toi.total_price), 0) as total
    FROM tool_order_items toi
    JOIN tool_orders o ON toi.order_id = o.id
    WHERE toi.vendor_id = $vendorId 
      AND o.order_status NOT IN ('cancelled')
    GROUP BY DATE_FORMAT(o.created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
")->fetch_all(MYSQLI_ASSOC);

// Top selling tool
$top = $conn->query("
    SELECT tp.product_name, SUM(toi.total_price) as revenue
    FROM tool_order_items toi
    JOIN tool_products tp ON toi.tool_id = tp.id
    WHERE toi.vendor_id = $vendorId
    GROUP BY toi.tool_id
    ORDER BY revenue DESC
    LIMIT 1
")->fetch_assoc();

echo json_encode(['daily' => $daily, 'monthly' => $monthly, 'top' => $top]);
?>