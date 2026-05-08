<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: consumerLogin.php');
    exit();
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare("
    SELECT 
        id,
        full_name,
        email,
        password_hash,
        account_status,
        login_attempts,
        locked_until
    FROM consumers
    WHERE email = ?
    LIMIT 1
");

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $_SESSION['login_error'] = "No account found with this email.";

    $stmt->close();

    header('Location: consumerLogin.php');
    exit();
}

$consumer = $result->fetch_assoc();

$stmt->close();


if (
    $consumer['locked_until'] &&
    strtotime($consumer['locked_until']) > time()
) {

    $remaining = ceil(
        (strtotime($consumer['locked_until']) - time()) / 60
    );

    $_SESSION['login_error'] =
        "Account locked. Try again in {$remaining} minute(s).";

    header('Location: consumerLogin.php');
    exit();
}


if (!password_verify($password, $consumer['password_hash'])) {

    $attempts = $consumer['login_attempts'] + 1;

    if ($attempts >= 10) {

        $lockUntil = date(
            'Y-m-d H:i:s',
            strtotime('+5 minutes')
        );

        $update = $conn->prepare("
            UPDATE consumers
            SET login_attempts = ?, locked_until = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "isi",
            $attempts,
            $lockUntil,
            $consumer['id']
        );

        $update->execute();
        $update->close();

        $_SESSION['login_error'] =
            "Too many failed attempts. Account locked for 5 minutes.";

    } else {

        $update = $conn->prepare("
            UPDATE consumers
            SET login_attempts = ?
            WHERE id = ?
        ");

        $update->bind_param(
            "ii",
            $attempts,
            $consumer['id']
        );

        $update->execute();
        $update->close();

        $remaining = 10 - $attempts;

        $_SESSION['login_error'] =
            "Incorrect password. {$remaining} attempt(s) remaining.";
    }

    header('Location: consumerLogin.php');
    exit();
}


if ($consumer['account_status'] === 'suspended') {

    $_SESSION['login_error'] =
        "Your account has been suspended.";

    header('Location: consumerLogin.php');
    exit();
}


$reset = $conn->prepare("
    UPDATE consumers
    SET
        login_attempts = 0,
        locked_until = NULL,
        last_login = NOW()
    WHERE id = ?
");

$reset->bind_param("i", $consumer['id']);
$reset->execute();
$reset->close();


session_regenerate_id(true);

$_SESSION['consumer_id']    = $consumer['id'];
$_SESSION['consumer_name']  = $consumer['full_name'];
$_SESSION['consumer_email'] = $consumer['email'];
$_SESSION['role']           = 'consumer';

$conn->close();

header('Location: consumerDash.php');
exit();
?>