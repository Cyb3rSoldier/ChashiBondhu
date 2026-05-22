<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

if ($_GET['delete'] ?? 0) {
    $id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM tool_orders WHERE id = ?")->bind_param("i", $id)->execute();
    $_SESSION['admin_msg'] = "Tool order deleted.";
    header("Location: admin_tool_orders.php"); exit();
}
if ($_POST['update_status'] ?? 0) {
    $oid = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE tool_orders SET order_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $oid);
    $stmt->execute();
    $_SESSION['admin_msg'] = "Tool order status updated.";
    header("Location: admin_tool_orders.php"); exit();
}
$orders = $conn->query("SELECT o.*, c.full_name as consumer FROM tool_orders o LEFT JOIN consumers c ON o.consumer_id = c.id ORDER BY o.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html><head><title>Tool Orders</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-stone-100"><?php include('admin_nav.php'); ?><div class="p-6">
    <h1 class="text-2xl font-bold mb-5">Tool Orders</h1>
    <?php if(isset($_SESSION['admin_msg'])): ?><div class="bg-green-50 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div><?php endif; ?>
    <div class="bg-white rounded-2xl overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-stone-50"><tr><th>ID</th><th>Order #</th><th>Consumer</th><th>Total</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($orders as $o): ?><tr class="border-b"><td class="px-5 py-3"><?php echo $o['id']; ?></td><td><?php echo $o['order_number']; ?></td><td><?php echo htmlspecialchars($o['consumer']); ?></td><td>৳<?php echo number_format($o['final_amount'],2); ?></td><td>
        <form method="POST" class="inline-flex gap-1"><input type="hidden" name="order_id" value="<?php echo $o['id']; ?>"><select name="status" class="text-xs border rounded px-1 py-0.5"><option <?php echo $o['order_status']=='pending'?'selected':''; ?>>pending</option><option <?php echo $o['order_status']=='processing'?'selected':''; ?>>processing</option><option <?php echo $o['order_status']=='completed'?'selected':''; ?>>completed</option><option <?php echo $o['order_status']=='cancelled'?'selected':''; ?>>cancelled</option></select><button type="submit" name="update_status" class="text-blue-600"><i class="fa-solid fa-save"></i></button></form>
    </td><td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td><td><a href="?delete=<?php echo $o['id']; ?>" onclick="return confirm('Delete order?')" class="text-red-600"><i class="fa-solid fa-trash"></i></a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div></body></html>