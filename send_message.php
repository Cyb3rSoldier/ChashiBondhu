<?php
session_start();
require_once 'config.php';

/*
|--------------------------------------------------------------------------
| CHECK LOGIN
|--------------------------------------------------------------------------
*/

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

    echo "not_logged_in";
    exit();
}

/*
|--------------------------------------------------------------------------
| GET DATA (FORMDATA FIX)
|--------------------------------------------------------------------------
*/

$receiver_id = $_POST['receiver_id'] ?? 0;
$receiver_role = $_POST['receiver_role'] ?? '';
$message = trim($_POST['message'] ?? '');

/*
|--------------------------------------------------------------------------
| VALIDATE
|--------------------------------------------------------------------------
*/

if (!$receiver_id || !$receiver_role || $message == '') {
    echo "invalid";
    exit();
}

/*
|--------------------------------------------------------------------------
| INSERT MESSAGE (IMPORTANT PART)
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
INSERT INTO messages
(sender_id, sender_role, receiver_id, receiver_role, message)
VALUES (?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isiss",
    $my_id,
    $my_role,
    $receiver_id,
    $receiver_role,
    $message
);

$stmt->execute();

$stmt->close();

echo "success";
?>