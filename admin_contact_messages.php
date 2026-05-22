<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

if ($_GET['delete'] ?? 0) {
    $id = (int)$_GET['delete'];
    $conn->prepare("DELETE FROM contact_messages WHERE id = ?")->bind_param("i", $id)->execute();
    $_SESSION['admin_msg'] = "Message deleted.";
    header("Location: admin_contact_messages.php"); exit();
}
$msgs = $conn->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html><head><title>Contact Messages</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script></head>
<body class="bg-stone-100"><?php include('admin_nav.php'); ?><div class="p-6">
    <h1 class="text-2xl font-bold mb-5">Contact Messages</h1>
    <?php if(isset($_SESSION['admin_msg'])): ?><div class="bg-green-50 p-3 rounded-xl mb-4"><?php echo $_SESSION['admin_msg']; unset($_SESSION['admin_msg']); ?></div><?php endif; ?>
    <div class="space-y-4">
        <?php foreach($msgs as $m): ?>
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-stone-100">
            <div class="flex justify-between items-start">
                <div><p class="font-bold text-green-950"><?php echo htmlspecialchars($m['name']); ?></p><p class="text-xs text-stone-400"><?php echo $m['email']; ?> <?php echo $m['phone'] ? '| '.$m['phone'] : ''; ?> | Role: <?php echo $m['role'] ?: 'Not specified'; ?></p></div>
                <div class="text-right"><span class="text-xs text-stone-400"><?php echo date('d M Y, h:i A', strtotime($m['created_at'])); ?></span><br><a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this message?')" class="text-red-500 text-xs"><i class="fa-solid fa-trash"></i> Delete</a></div>
            </div>
            <div class="mt-3"><p class="font-semibold text-stone-700"><?php echo htmlspecialchars($m['subject']); ?></p><p class="text-stone-600 text-sm mt-1"><?php echo nl2br(htmlspecialchars($m['message'])); ?></p></div>
        </div>
        <?php endforeach; ?>
        <?php if(count($msgs)==0): ?><p class="text-stone-400 text-center py-10">No messages yet.</p><?php endif; ?>
    </div>
</div></body></html>