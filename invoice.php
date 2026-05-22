<?php
session_start();
if(!isset($_SESSION['consumer_id'])) die('Login required');
require_once 'config.php';
$order_id = intval($_GET['id']);
$type = $_GET['type'] ?? 'product'; // product or tool
if($type == 'product'){
    $order = $conn->query("SELECT * FROM orders WHERE id=$order_id AND consumer_id={$_SESSION['consumer_id']}")->fetch_assoc();
    if(!$order) die('Order not found');
    $items = $conn->query("SELECT oi.*, p.image_path FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=$order_id")->fetch_all(MYSQLI_ASSOC);
    $title = "Product Order Invoice";
} else {
    $order = $conn->query("SELECT * FROM tool_orders WHERE id=$order_id AND consumer_id={$_SESSION['consumer_id']}")->fetch_assoc();
    if(!$order) die('Order not found');
    $items = $conn->query("SELECT toi.*, tp.image_path FROM tool_order_items toi JOIN tool_products tp ON toi.tool_id=tp.id WHERE toi.order_id=$order_id")->fetch_all(MYSQLI_ASSOC);
    $title = "Tool Order Invoice";
}
?>
<!DOCTYPE html>
<html><head><title>Invoice #<?php echo $order['order_number']; ?></title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-white p-8">
<div class="max-w-3xl mx-auto">
    <div class="text-center mb-8"><img src="asset/img/ChashiBondhu logo.png" class="h-12 mx-auto"><h1 class="text-2xl font-bold mt-2">ChashiBondhu</h1><p class="text-stone-500">Invoice</p></div>
    <div class="border rounded-lg p-6">
        <div class="flex justify-between border-b pb-4"><div><p><strong>Order #:</strong> <?php echo $order['order_number']; ?></p><p><strong>Date:</strong> <?php echo date('d M Y', strtotime($order['created_at'])); ?></p></div><div><p><strong>Payment:</strong> <?php echo strtoupper($order['payment_method']); ?></p><p><strong>Status:</strong> <?php echo ucfirst($order['order_status']); ?></p></div></div>
        <div class="mt-4"><p><strong>Delivery Address:</strong><br><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p><p><strong>Phone:</strong> <?php echo $order['phone']; ?></p></div>
        <table class="w-full mt-6 border-collapse"><thead><tr class="border-b"><th class="text-left py-2">Item</th><th class="text-left">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead><tbody>
        <?php foreach($items as $item): ?>
        <tr class="border-b"><td class="py-2"><?php echo htmlspecialchars($item['product_name']); ?></td><td><?php echo $item['quantity']; ?></td><td class="text-right">৳<?php echo number_format($item['unit_price'],2); ?></td><td class="text-right">৳<?php echo number_format($item['total_price'],2); ?></td></tr>
        <?php endforeach; ?>
        </tbody><tfoot><tr><td colspan="3" class="text-right font-bold">Subtotal:</td><td class="text-right">৳<?php echo number_format($order['total_amount'],2); ?></td></tr><tr><td colspan="3" class="text-right font-bold">Delivery Fee:</td><td class="text-right">৳<?php echo number_format($order['delivery_fee'],2); ?></td></tr><tr class="border-t"><td colspan="3" class="text-right font-bold text-lg">Grand Total:</td><td class="text-right font-bold text-lg">৳<?php echo number_format($order['final_amount'],2); ?></td></tr></tfoot></table>
    </div>
    <div class="text-center mt-6"><button onclick="window.print()" class="bg-green-700 text-white px-4 py-2 rounded-xl"><i class="fa-solid fa-print"></i> Print Invoice</button></div>
</div>
</body></html>