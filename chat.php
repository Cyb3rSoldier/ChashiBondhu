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

    header("Location: consumerLogin.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| OTHER USER
|--------------------------------------------------------------------------
*/

$other_id = $_GET['id'] ?? 0;
$other_role = $_GET['role'] ?? '';

/*
|--------------------------------------------------------------------------
| VALIDATE ROLE
|--------------------------------------------------------------------------
*/

if (!in_array($other_role, ['farmer', 'consumer', 'vendor'])) {
    die("Invalid chat user type");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Chat</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">



<div class="max-w-2xl mx-auto mt-10 bg-white shadow rounded-xl p-4">

<a href="inbox.php"
   class="inline-flex items-center gap-2 mb-4 text-green-700 font-semibold hover:underline">
   <i class="fa-solid fa-arrow-left"></i>
    Back to Inbox
</a>
    <div id="chatBox" class="h-[400px] overflow-y-auto border p-3 rounded bg-gray-50"></div>

    <div class="flex gap-2 mt-3">
        <input id="msg" class="flex-1 border rounded p-2" placeholder="Type message...">
        <button onclick="sendMsg()" class="bg-green-600 text-white px-4 rounded">
            Send
        </button>
    </div>

</div>

<script>

function loadMessages(){
    fetch("fetch_messages.php?user_id=<?php echo $other_id; ?>&role=<?php echo $other_role; ?>")
    .then(res => res.text())
    .then(data => {
        document.getElementById("chatBox").innerHTML = data;
    });
}

function sendMsg(){

    let msg = document.getElementById("msg").value;

    if(msg.trim() === ""){
        return;
    }

    let formData = new FormData();

    formData.append("receiver_id", "<?php echo $other_id; ?>");
    formData.append("receiver_role", "<?php echo $other_role; ?>");
    formData.append("message", msg);

    fetch("send_message.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {

        document.getElementById("msg").value = "";

        loadMessages();

    });
}


setInterval(loadMessages, 1500);
loadMessages();

</script>

</body>
</html>