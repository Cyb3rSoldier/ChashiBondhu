<?php
session_start();
if (!isset($_SESSION['farmer_id'])) { header('Location: farmerLogin.php'); exit(); }
require_once 'config.php';
$tips = $conn->query("SELECT * FROM farming_tips ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Farming Tips</title><link rel="stylesheet" href="design.css"><link rel="website icon" type="png" href="asset/img/ChashiBondhu logo.png"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"><script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script><script>tailwind.config={darkMode:'class'}</script></head>
<body class="bg-green-50"><?php include 'farmerNav.php'; ?>
<div class="max-w-6xl mx-auto px-5 pt-28 pb-16">
    <h1 class="text-3xl font-bold text-green-950 mb-6">📚 Farming Tips & Guides</h1>
    <div class="grid md:grid-cols-2 gap-6">
    <?php foreach($tips as $tip): ?>
        <div class="bg-white rounded-2xl shadow overflow-hidden">
            <?php if($tip['image'] && file_exists($tip['image'])): ?><img src="<?php echo htmlspecialchars($tip['image']); ?>" class="w-full h-48 object-cover"><?php else: ?><div class="w-full h-48 bg-green-100 flex items-center justify-center"><i class="fa-solid fa-seedling text-4xl text-green-400"></i></div><?php endif; ?>
            <div class="p-5"><h2 class="font-bold text-xl"><?php echo htmlspecialchars($tip['title']); ?></h2>
            <p class="text-stone-600 mt-2"><?php echo nl2br(htmlspecialchars($tip['description'])); ?></p></div>
        </div>
    <?php endforeach; ?>
    <?php if(count($tips)==0): ?><div class="col-span-2 text-center py-16"><p class="text-stone-400">No farming tips available yet. Check back later!</p></div><?php endif; ?>
    </div>
</div>
<?php include 'footer.php'; ?></body></html>