<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

if ($_GET['delete'] ?? 0) {
    $id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM tool_products WHERE id = ?")->bind_param("i", $id)->execute();
    $_SESSION['admin_msg'] = "Tool deleted.";
    header("Location: admin_tools.php"); exit();
}
$tools = $conn->query("SELECT t.*, v.business_name as vendor FROM tool_products t LEFT JOIN vendors v ON t.vendor_id = v.id ORDER BY t.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html><head><title>Manage Tools</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-stone-100"><?php include('admin_nav.php'); ?><div class="p-6">
    <h1 class="text-2xl font-bold mb-5">Tools & Equipment</h1>
    <?php if(isset($_SESSION['admin_msg'])): ?><div class="bg-green-50 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div><?php endif; ?>
    <div class="bg-white rounded-2xl overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-stone-50"><tr><th>ID</th><th>Tool Name</th><th>Vendor</th><th>Price</th><th>Stock</th><th>Condition</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($tools as $t): ?><tr class="border-b"><td class="px-5 py-3"><?php echo $t['id']; ?></td><td><?php echo htmlspecialchars($t['product_name']); ?></td><td><?php echo htmlspecialchars($t['vendor']); ?></td><td>৳<?php echo number_format($t['price'],2); ?></td><td><?php echo $t['quantity']; ?></td><td><?php echo $t['condition_status']; ?></td><td><a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Delete tool?')" class="text-red-600"><i class="fa-solid fa-trash"></i> Delete</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div></body></html>