<?php
session_start(); header('Content-Type: application/json');
if(!isset($_SESSION['farmer_id'])) die(json_encode([]));
require_once 'config.php';
$farmerId = $_SESSION['farmer_id'];
$daily = $conn->query("SELECT DATE(o.created_at) as date, COALESCE(SUM(oi.total_price),0) as total FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE oi.farmer_id=$farmerId AND o.order_status NOT IN ('cancelled') AND o.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(o.created_at)")->fetch_all(MYSQLI_ASSOC);
$monthly = $conn->query("SELECT DATE_FORMAT(o.created_at, '%Y-%m') as month, COALESCE(SUM(oi.total_price),0) as total FROM order_items oi JOIN orders o ON oi.order_id=o.id WHERE oi.farmer_id=$farmerId AND o.order_status NOT IN ('cancelled') GROUP BY DATE_FORMAT(o.created_at, '%Y-%m') ORDER BY month DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);
$top = $conn->query("SELECT oi.product_name, SUM(oi.total_price) as revenue FROM order_items oi WHERE oi.farmer_id=$farmerId GROUP BY oi.product_id ORDER BY revenue DESC LIMIT 1")->fetch_assoc();
echo json_encode(['daily'=>$daily,'monthly'=>$monthly,'top'=>$top]);
?>