
<?php
session_start();
if (!isset($_SESSION['consumer_id'])) {
    header('Location: ../consumerLogin.php');
    exit();
}

$consumerName = $_SESSION['consumer_name'];
$consumerEmail = $_SESSION['consumer_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ChashiBondhu</title>
    <link rel="stylesheet" href="../design.css">
    <link rel="website icon" type="png" href="../asset/img/ChashiBondhu logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-green-50 min-h-screen">

    <?php include('../navbar2.php'); ?>

    <main class="max-w-2xl mx-auto px-5 pt-24 pb-16">
        <div class="bg-white rounded-2xl p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-green-950 mb-6">My Profile</h1>
            
            <div class="space-y-4">
                <div class="border-b border-stone-100 pb-3">
                    <p class="text-xs text-stone-400 uppercase tracking-wider">Full Name</p>
                    <p class="text-lg font-semibold text-green-950"><?php echo htmlspecialchars($consumerName); ?></p>
                </div>
                
                <div class="border-b border-stone-100 pb-3">
                    <p class="text-xs text-stone-400 uppercase tracking-wider">Email Address</p>
                    <p class="text-lg font-semibold text-green-950"><?php echo htmlspecialchars($consumerEmail); ?></p>
                </div>
            </div>
            
            <div class="mt-8">
                <a href="../consumerDash.php" class="inline-flex items-center gap-2 text-green-600 hover:text-green-800 transition">
                    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </main>

    <?php include('../footer.php'); ?>
</body>
</html>