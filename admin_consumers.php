<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

if ($_GET['delete'] ?? 0) {
    $id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM consumers WHERE id = ?")->bind_param("i", $id)->execute();
    $_SESSION['admin_msg'] = "Consumer deleted.";
    header("Location: admin_consumers.php"); exit();
}

$search = $_GET['search'] ?? '';
$sql = "SELECT id, full_name, email, phone, address, account_status, created_at FROM consumers";
if ($search) $sql .= " WHERE full_name LIKE '%$search%' OR email LIKE '%$search%'";
$sql .= " ORDER BY created_at DESC";
$consumers = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html><head><title>Manage Consumers</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-stone-100"><?php include('admin_nav.php'); ?><div class="p-6">
    <div class="flex justify-between mb-5"><h1 class="text-2xl font-bold">Consumers</h1><form method="GET"><input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" class="border rounded-xl px-4 py-2"><button class="bg-green-700 text-white px-4 py-2 rounded-xl ml-2"><i class="fa-solid fa-search"></i></button></form></div>
    <?php if(isset($_SESSION['admin_msg'])): ?><div class="bg-green-50 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div><?php endif; ?>
    <div class="bg-white rounded-2xl overflow-x-auto"><table class="min-w-full text-sm"><thead class="bg-stone-50"><tr><th class="px-5 py-3 text-left">ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Status</th><th>Actions</th></tr></thead><tbody>
    <?php foreach($consumers as $c): ?><tr class="border-b"><td class="px-5 py-3"><?php echo $c['id']; ?></td><td><?php echo htmlspecialchars($c['full_name']); ?></td><td><?php echo htmlspecialchars($c['email']); ?></td><td><?php echo htmlspecialchars($c['phone']); ?></td><td><?php echo htmlspecialchars($c['address']); ?></td><td><span class="px-2 py-1 rounded-full text-xs <?php echo $c['account_status']=='active'?'bg-green-100 text-green-700':'bg-red-100 text-red-600'; ?>"><?php echo $c['account_status']; ?></span></td><td><a href="?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete consumer?')" class="text-red-600"><i class="fa-solid fa-trash"></i> Delete</a></td></tr><?php endforeach; ?>
    </tbody></table></div>
</div></body></html>