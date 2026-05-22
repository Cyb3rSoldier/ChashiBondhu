<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit();
}
require_once 'config.php';

$delete_id = $_GET['delete'] ?? 0;
if ($delete_id) {
    $stmt = $conn->prepare("DELETE FROM farmers WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['admin_msg'] = "Farmer deleted successfully.";
    header("Location: admin_farmers.php");
    exit();
}

$search = $_GET['search'] ?? '';
$query = "SELECT id, full_name, email, phone, district, account_status, created_at FROM farmers";
if ($search) {
    $query .= " WHERE full_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%'";
}
$query .= " ORDER BY created_at DESC";
$farmers = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Farmers — Admin</title>
    <link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-stone-100">
    <?php include('admin_nav.php'); ?>
    <div class="p-6">
        <div class="flex justify-between items-center mb-5">
            <h1 class="text-2xl font-bold text-green-950">Farmers</h1>
            <form method="GET" class="flex gap-2">
                <input type="text" name="search" placeholder="Search farmers..." value="<?php echo htmlspecialchars($search); ?>" class="border border-stone-200 rounded-xl px-4 py-2 text-sm">
                <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded-xl text-sm"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <?php if (isset($_SESSION['admin_msg'])): ?>
            <div class="bg-green-50 text-green-700 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div>
        <?php endif; ?>
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-100">
            <table class="min-w-full text-sm">
                <thead class="bg-stone-50 border-b">
                    <tr>
                        <th class="px-5 py-3 text-left">ID</th><th>Name</th><th>Email</th><th>Phone</th><th>District</th><th>Status</th><th>Joined</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($farmers as $f): ?>
                    <tr class="border-b hover:bg-stone-50">
                        <td class="px-5 py-3"><?php echo $f['id']; ?></td>
                        <td><?php echo htmlspecialchars($f['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($f['email']); ?></td>
                        <td><?php echo htmlspecialchars($f['phone']); ?></td>
                        <td><?php echo htmlspecialchars($f['district']); ?></td>
                        <td><span class="px-2 py-1 rounded-full text-xs <?php echo $f['account_status']=='active'?'bg-green-100 text-green-700':'bg-red-100 text-red-600'; ?>"><?php echo $f['account_status']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($f['created_at'])); ?></td>
                        <td><a href="?delete=<?php echo $f['id']; ?>" onclick="return confirm('Delete this farmer? All products and orders will be removed.')" class="text-red-600 hover:text-red-800"><i class="fa-solid fa-trash"></i> Delete</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>