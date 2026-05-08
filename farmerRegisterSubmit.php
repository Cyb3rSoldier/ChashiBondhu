<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: farmerReg.php');
    exit();
}

// Collect and trim inputs
$name      = trim($_POST['name']);
$email     = trim($_POST['email']);
$phone     = trim($_POST['phone']);
$district  = trim($_POST['district']);
$land_size = trim($_POST['land_size']);
$crops     = trim($_POST['crops']);
$nid       = trim($_POST['nid']);
$password  = $_POST['password'];
$confirm   = $_POST['confirm_password'];

// ── Validation ──────────────────────────────────────────

if (empty($name) || empty($email) || empty($phone) || empty($district) || empty($land_size) || empty($crops) || empty($nid)) {
    $_SESSION['reg_error'] = "All fields are required.";
    header('Location: farmerReg.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reg_error'] = "Please enter a valid email address.";
    header('Location: farmerReg.php');
    exit();
}

if ($password !== $confirm) {
    $_SESSION['reg_error'] = "Passwords do not match.";
    header('Location: farmerReg.php');
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['reg_error'] = "Password must be at least 6 characters.";
    header('Location: farmerReg.php');
    exit();
}

// ── Check duplicate email ────────────────────────────────

$check = $conn->prepare("SELECT id FROM farmers WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['reg_error'] = "This email is already registered. <a href='farmerLogin.php' class='underline font-bold'>Login instead</a>";
    $check->close();
    header('Location: farmerReg.php');
    exit();
}
$check->close();

// ── Check duplicate phone ────────────────────────────────

$checkPhone = $conn->prepare("SELECT id FROM farmers WHERE phone = ? LIMIT 1");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$checkPhone->store_result();

if ($checkPhone->num_rows > 0) {
    $_SESSION['reg_error'] = "This phone number is already registered.";
    $checkPhone->close();
    header('Location: farmerReg.php');
    exit();
}
$checkPhone->close();

// ── Check duplicate NID ──────────────────────────────────

$checkNid = $conn->prepare("SELECT id FROM farmers WHERE nid = ? LIMIT 1");
$checkNid->bind_param("s", $nid);
$checkNid->execute();
$checkNid->store_result();

if ($checkNid->num_rows > 0) {
    $_SESSION['reg_error'] = "This NID is already registered.";
    $checkNid->close();
    header('Location: farmerReg.php');
    exit();
}
$checkNid->close();

// ── Hash password ────────────────────────────────────────

$hashed = password_hash($password, PASSWORD_BCRYPT);

// ── Insert into DB ───────────────────────────────────────
// Column names match the new table:
// full_name, password_hash, account_status

$stmt = $conn->prepare("
    INSERT INTO farmers 
        (full_name, email, password_hash, phone, district, land_size, crops, nid, account_status) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param(
    "ssssssss",
    $name, $email, $hashed, $phone,
    $district, $land_size, $crops, $nid
);

if ($stmt->execute()) {
    $_SESSION['reg_success'] = "Registration successful! Please wait for admin verification before logging in.";
    $stmt->close();
    $conn->close();
    header('Location: farmerReg.php');
    exit();
} else {
    $_SESSION['reg_error'] = "Something went wrong. Please try again.";
    $stmt->close();
    $conn->close();
    header('Location: farmerReg.php');
    exit();
}
?>