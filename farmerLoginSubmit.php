<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: farmerLogin.php');
    exit();
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare("
    SELECT id, full_name, email, password_hash, account_status, login_attempts, locked_until 
    FROM farmers 
    WHERE email = ? 
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "No account found with this email.";
    $stmt->close();
    header('Location: farmerLogin.php');
    exit();
}

$farmer = $result->fetch_assoc();
$stmt->close();

if ($farmer['locked_until'] && strtotime($farmer['locked_until']) > time()) {
    $remaining = ceil((strtotime($farmer['locked_until']) - time()) / 60);
    $_SESSION['login_error'] = "Account locked due to too many failed attempts. Try again in {$remaining} minute(s).";
    header('Location: farmerLogin.php');
    exit();
}

if (!password_verify($password, $farmer['password_hash'])) {

    $attempts = $farmer['login_attempts'] + 1;

    if ($attempts >= 10) {
        $lockUntil = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $conn->query("UPDATE farmers SET login_attempts = $attempts, locked_until = '$lockUntil' WHERE id = {$farmer['id']}");
        $_SESSION['login_error'] = "Too many failed attempts. Account locked for 5 minutes.";
    } else {
        $conn->query("UPDATE farmers SET login_attempts = $attempts WHERE id = {$farmer['id']}");
        $remaining = 10 - $attempts;
        $_SESSION['login_error'] = "Incorrect password. {$remaining} attempt(s) remaining.";
    }

    header('Location: farmerLogin.php');
    exit();
}

if ($farmer['account_status'] === 'pending') {
    $_SESSION['login_error'] = "Your account is pending admin verification. Please wait.";
    header('Location: farmerLogin.php');
    exit();
}

if ($farmer['account_status'] === 'suspended') {
    $_SESSION['login_error'] = "Your account has been suspended. Contact support.";
    header('Location: farmerLogin.php');
    exit();
}

// Reset login attempts and update last login
$conn->query("UPDATE farmers SET login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = {$farmer['id']}");

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

$_SESSION['farmer_id']    = $farmer['id'];
$_SESSION['farmer_name']  = $farmer['full_name']; 
$_SESSION['farmer_email'] = $farmer['email'];
$_SESSION['role']         = 'farmer';

$conn->close();
header('Location: farmerDash.php');
exit();
?>