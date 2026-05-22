<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: admin.php'); exit(); }
require_once 'config.php';

// Create uploads directory if not exists
if (!file_exists('uploads/tips')) {
    mkdir('uploads/tips', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $image = '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $target = 'uploads/tips/' . time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['image']['name']));
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image = $target;
            }
        }
    }
    
    $stmt = $conn->prepare("INSERT INTO farming_tips (title, description, image) VALUES (?,?,?)");
    $stmt->bind_param("sss", $title, $desc, $image);
    $stmt->execute();
    header("Location: admin_farming_tips.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Delete image file if exists
    $imgResult = $conn->query("SELECT image FROM farming_tips WHERE id=$id")->fetch_assoc();
    if ($imgResult && $imgResult['image'] && file_exists($imgResult['image'])) {
        unlink($imgResult['image']);
    }
    $conn->query("DELETE FROM farming_tips WHERE id=$id");
    header("Location: admin_farming_tips.php");
    exit();
}

$tips = $conn->query("SELECT * FROM farming_tips ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Farming Tips - ChashiBondhu Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        .file-input-label {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .file-input-label:hover {
            background-color: #e5e7eb;
        }
    </style>
</head>
<body class="bg-stone-100">
    <?php include 'admin_nav.php'; ?>
    <div class="p-6 ml-64">
        <h1 class="text-2xl font-bold text-green-950 mb-2">🌾 Farming Tips</h1>
        <p class="text-stone-500 text-sm mb-6">Manage educational content for farmers</p>

        <!-- Add Tip Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-stone-100 p-6 mb-8">
            <h2 class="font-bold text-green-950 mb-4 flex items-center gap-2"><i class="fa-solid fa-plus-circle text-green-700"></i> Add New Tip</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1">Title</label>
                    <input type="text" name="title" placeholder="e.g., How to prevent rice blast disease" required
                           class="w-full border border-stone-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1">Description / Guide</label>
                    <textarea name="description" rows="4" placeholder="Write detailed farming tips, fertilizer schedules, disease control methods..." required
                              class="w-full border border-stone-200 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-stone-700 mb-1">Image (optional)</label>
                    <div class="flex items-center gap-4">
                        <label for="tipImage" class="file-input-label bg-stone-100 hover:bg-stone-200 text-stone-700 font-medium px-5 py-2.5 rounded-xl transition inline-flex items-center gap-2 cursor-pointer border border-stone-200">
                            <i class="fa-solid fa-cloud-upload-alt"></i> Choose Image
                        </label>
                        <input type="file" name="image" id="tipImage" accept="image/*" class="hidden" onchange="previewImage(this)">
                        <span id="fileName" class="text-sm text-stone-500">No file chosen</span>
                    </div>
                    <div id="imagePreview" class="mt-3 hidden">
                        <img id="previewImg" class="h-24 w-auto rounded-lg border border-stone-200 object-cover">
                    </div>
                    <p class="text-xs text-stone-400 mt-1">Allowed: JPG, PNG, GIF, WEBP (max 2MB)</p>
                </div>
                <button type="submit" name="add" class="bg-green-700 hover:bg-green-600 text-white font-bold px-6 py-2.5 rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Publish Tip
                </button>
            </form>
        </div>

        <!-- Existing Tips List -->
        <h2 class="font-bold text-green-950 mb-4 flex items-center gap-2"><i class="fa-solid fa-list"></i> All Farming Tips</h2>
        <?php if (count($tips) > 0): ?>
            <div class="grid md:grid-cols-2 gap-5">
                <?php foreach ($tips as $t): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-stone-100 overflow-hidden hover:shadow-md transition">
                        <?php if ($t['image'] && file_exists($t['image'])): ?>
                            <img src="<?php echo $t['image']; ?>" class="w-full h-40 object-cover">
                        <?php else: ?>
                            <div class="w-full h-40 bg-green-100 flex items-center justify-center">
                                <i class="fa-solid fa-seedling text-4xl text-green-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="p-5">
                            <h3 class="font-bold text-lg text-green-950 mb-2"><?php echo htmlspecialchars($t['title']); ?></h3>
                            <p class="text-stone-600 text-sm leading-relaxed mb-4 line-clamp-3"><?php echo nl2br(htmlspecialchars($t['description'])); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-stone-400"><i class="fa-regular fa-calendar"></i> <?php echo date('d M Y', strtotime($t['created_at'])); ?></span>
                                <a href="?delete=<?php echo $t['id']; ?>" onclick="return confirm('Delete this tip?')" class="text-red-500 hover:text-red-700 text-sm font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl border border-stone-100 text-center py-12">
                <i class="fa-solid fa-lightbulb text-4xl text-stone-300 mb-3"></i>
                <p class="text-stone-400">No farming tips yet. Add your first tip above!</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function previewImage(input) {
            const previewDiv = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const fileNameSpan = document.getElementById('fileName');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.classList.remove('hidden');
                    fileNameSpan.textContent = input.files[0].name;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                previewDiv.classList.add('hidden');
                fileNameSpan.textContent = 'No file chosen';
            }
        }
    </script>
</body>
</html>