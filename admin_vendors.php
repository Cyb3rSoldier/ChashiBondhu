<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

if ($_GET['delete'] ?? 0) {
    $id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM vendors WHERE id = ?")->bind_param("i", $id)->execute();
    $_SESSION['admin_msg'] = "Vendor deleted.";
    header("Location: admin_vendors.php"); exit();
}
$vendors = $conn->query("SELECT id, full_name, business_name, email, phone, district, account_status, created_at FROM vendors ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html><head><title>Manage Vendors</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-stone-100"><?php include('admin_nav.php'); ?><div class="p-6">
    <h1 class="text-2xl font-bold mb-5">Tool Vendors</h1>
    <?php if(isset($_SESSION['admin_msg'])): ?><div class="bg-green-50 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div><?php endif; ?>
    <div class="bg-white rounded-2xl overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-stone-50"><tr><th class="px-5 py-3">ID</th><th>Full Name</th><th>Business</th><th>Email</th><th>Phone</th><th>District</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($vendors as $v): ?><tr class="border-b"><td class="px-5 py-3"><?php echo $v['id']; ?></td><td><?php echo htmlspecialchars($v['full_name']); ?></td><td><?php echo htmlspecialchars($v['business_name']); ?></td><td><?php echo htmlspecialchars($v['email']); ?></td><td><?php echo htmlspecialchars($v['phone']); ?></td><td><?php echo htmlspecialchars($v['district']); ?></td><td><span class="px-2 py-1 rounded-full text-xs <?php echo $v['account_status']=='active'?'bg-green-100':'bg-red-100'; ?>"><?php echo $v['account_status']; ?></span></td><td><a href="?delete=<?php echo $v['id']; ?>" onclick="return confirm('Delete vendor?')" class="text-red-600"><i class="fa-solid fa-trash"></i> Delete</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div></body></html>