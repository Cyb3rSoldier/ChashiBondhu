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

    exit();
}

/*
|--------------------------------------------------------------------------
| GET OTHER USER
|--------------------------------------------------------------------------
*/

$other_id = $_GET['user_id'] ?? 0;
$other_role = $_GET['role'] ?? '';

if (!in_array($other_role, ['farmer', 'consumer', 'vendor'])) {
    exit();
}

/*
|--------------------------------------------------------------------------
| FETCH MESSAGES
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT * FROM messages
WHERE
(
    sender_id = ?
    AND sender_role = ?
    AND receiver_id = ?
    AND receiver_role = ?
)
OR
(
    sender_id = ?
    AND sender_role = ?
    AND receiver_id = ?
    AND receiver_role = ?
)
ORDER BY created_at ASC
");
$stmt->bind_param(
    "isisisis",
    $my_id,
    $my_role,
    $other_id,
    $other_role,

    $other_id,
    $other_role,
    $my_id,
    $my_role
);

$stmt->execute();

$result = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| DISPLAY CHAT
|--------------------------------------------------------------------------
*/

while ($row = $result->fetch_assoc()) {

    $isMe = (
        $row['sender_id'] == $my_id &&
        $row['sender_role'] == $my_role
    );

    if ($isMe) {

        echo "
        <div style='text-align:right; margin:10px 0;'>

            <div style='
                background:#16a34a;
                color:white;
                padding:10px 14px;
                border-radius:16px;
                display:inline-block;
                max-width:70%;
                word-wrap:break-word;
            '>
                ".htmlspecialchars($row['message'])."
            </div>

        </div>";

    } else {

        echo "
        <div style='text-align:left; margin:10px 0;'>

            <div style='
                background:#e5e7eb;
                color:black;
                padding:10px 14px;
                border-radius:16px;
                display:inline-block;
                max-width:70%;
                word-wrap:break-word;
            '>
                ".htmlspecialchars($row['message'])."
            </div>

        </div>";
    }
}

$stmt->close();
?>