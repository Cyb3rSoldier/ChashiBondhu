
<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vendorLogin.php');
    exit();
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

$stmt = $conn->prepare("
    SELECT id, full_name, email, password_hash, account_status, login_attempts, locked_until 
    FROM vendors 
    WHERE email = ? 
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['login_error'] = "No account found with this email.";
    $stmt->close();
    header('Location: vendorLogin.php');
    exit();
}

$vendor = $result->fetch_assoc();
$stmt->close();

// Check if locked
if ($vendor['locked_until'] && strtotime($vendor['locked_until']) > time()) {
    $remaining = ceil((strtotime($vendor['locked_until']) - time()) / 60);
    $_SESSION['login_error'] = "Account locked. Try again in {$remaining} minute(s).";
    header('Location: vendorLogin.php');
    exit();
}

// Verify password
if (!password_verify($password, $vendor['password_hash'])) {
    $attempts = $vendor['login_attempts'] + 1;
    
    if ($attempts >= 10) {
        $lockUntil = date('Y-m-d H:i:s', strtotime('+5 minutes'));
        $update = $conn->prepare("UPDATE vendors SET login_attempts = ?, locked_until = ? WHERE id = ?");
        $update->bind_param("isi", $attempts, $lockUntil, $vendor['id']);
        $update->execute();
        $update->close();
        $_SESSION['login_error'] = "Too many failed attempts. Account locked for 5 minutes.";
    } else {
        $update = $conn->prepare("UPDATE vendors SET login_attempts = ? WHERE id = ?");
        $update->bind_param("ii", $attempts, $vendor['id']);
        $update->execute();
        $update->close();
        $remaining = 10 - $attempts;
        $_SESSION['login_error'] = "Incorrect password. {$remaining} attempt(s) remaining.";
    }
    
    header('Location: vendorLogin.php');
    exit();
}

// Check if suspended
if ($vendor['account_status'] === 'suspended') {
    $_SESSION['login_error'] = "Your account has been suspended. Contact support.";
    header('Location: vendorLogin.php');
    exit();
}

// Reset login attempts
$reset = $conn->prepare("UPDATE vendors SET login_attempts = 0, locked_until = NULL, last_login = NOW() WHERE id = ?");
$reset->bind_param("i", $vendor['id']);
$reset->execute();
$reset->close();

session_regenerate_id(true);

$_SESSION['vendor_id']    = $vendor['id'];
$_SESSION['vendor_name']  = $vendor['full_name'];
$_SESSION['vendor_email'] = $vendor['email'];
$_SESSION['role']         = 'vendor';

$conn->close();
header('Location: vendorDash.php');
exit();
?>