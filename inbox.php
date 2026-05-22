<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['farmer_id'])) {

    $my_id = $_SESSION['farmer_id'];
    $my_role = 'farmer';

} elseif (isset($_SESSION['consumer_id'])) {

    $my_id = $_SESSION['consumer_id'];
    $my_role = 'consumer';

} elseif (isset($_SESSION['vendor_id'])) {

    $my_id = $_SESSION['vendor_id'];
    $my_role = 'vendor';

} else {

    header("Location: login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| GET ALL CONVERSATIONS
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT *
FROM messages
WHERE 
(
    (receiver_id = ? AND receiver_role = ?)
    OR
    (sender_id = ? AND sender_role = ?)
)
ORDER BY created_at DESC
");

$stmt->bind_param("isis", $my_id, $my_role, $my_id, $my_role);

$stmt->execute();

$result = $stmt->get_result();

$conversations = [];

while ($row = $result->fetch_assoc()) {

    // SKIP SELF MESSAGES (important safety fix)
    if (
        $row['sender_id'] == $my_id &&
        $row['sender_role'] == $my_role &&
        $row['receiver_id'] == $my_id &&
        $row['receiver_role'] == $my_role
    ) {
        continue;
    }

    // FIND OTHER USER
    if ($row['sender_id'] == $my_id && $row['sender_role'] == $my_role) {
        $other_id = $row['receiver_id'];
        $other_role = $row['receiver_role'];
    } else {
        $other_id = $row['sender_id'];
        $other_role = $row['sender_role'];
    }

    // EXTRA SAFETY: ignore invalid grouping
    if ($other_id == $my_id && $other_role == $my_role) {
        continue;
    }

    $key = $other_role . "_" . $other_id;

    if (!isset($conversations[$key])) {

        /*
        |--------------------------------------------------------------------------
        | GET USER NAME
        |--------------------------------------------------------------------------
        */
    
        if ($other_role == 'farmer') {
    
            $q = $conn->prepare("SELECT full_name FROM farmers WHERE id=?");
    
        } elseif ($other_role == 'vendor') {
    
            $q = $conn->prepare("SELECT full_name FROM vendors WHERE id=?");
    
        } else {
    
            $q = $conn->prepare("SELECT full_name FROM consumers WHERE id=?");
        }
    
        $q->bind_param("i", $other_id);
        $q->execute();
    
        $res = $q->get_result()->fetch_assoc();
    
        $user_name = $res[array_key_first($res)] ?? 'User';
    
        $q->close();
    
        /*
        |--------------------------------------------------------------------------
        | SAVE CONVERSATION
        |--------------------------------------------------------------------------
        */
    
        $conversations[$key] = [
            'id' => $other_id,
            'role' => $other_role,
            'name' => $user_name,
            'message' => $row['message'],
            'time' => $row['created_at']
        ];
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inbox</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body class="bg-green-50 min-h-screen">

<div class="max-w-3xl mx-auto p-6">

<h1 class="text-3xl font-bold text-green-800 mb-6">
    Inbox
</h1>

<?php
if (isset($_SESSION['farmer_id'])) {
    $dashboardLink = 'farmerDash.php';
} elseif (isset($_SESSION['vendor_id'])) {
    $dashboardLink = 'vendorDash.php';
} else {
    $dashboardLink = 'consumerDash.php';
}
?>

<a href="<?php echo $dashboardLink; ?>"
   class="inline-flex items-center gap-2 mb-4 text-green-700 font-semibold hover:underline">
    <i class="fa-solid fa-arrow-left"></i>
    Back to Dashboard
</a>

<div class="space-y-3">

<?php foreach($conversations as $chat): ?>

<a href="chat.php?id=<?php echo $chat['id']; ?>&role=<?php echo $chat['role']; ?>"
class="block bg-white p-4 rounded-2xl shadow hover:bg-green-50 transition">

    <div class="flex items-center justify-between">

        <div>
            <h2 class="font-bold text-green-800">
            <?php echo htmlspecialchars($chat['name']); ?>
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                <?php echo htmlspecialchars($chat['message']); ?>
            </p>
        </div>

        <i class="fa-solid fa-chevron-right text-gray-400"></i>

    </div>

</a>

<?php endforeach; ?>

</div>

</div>

</body>
</html>