<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: farmerReg.php');
    exit();
}

$name      = trim($_POST['name']);
$email     = trim($_POST['email']);
$phone     = trim($_POST['phone']);
$district  = trim($_POST['district']);
$land_size = trim($_POST['land_size']);
$crops     = trim($_POST['crops']);
$nid       = trim($_POST['nid']);
$password  = $_POST['password'];
$confirm   = $_POST['confirm_password'];

// Validate passwords match
if ($password !== $confirm) {
    $_SESSION['reg_error'] = "Passwords do not match.";
    header('Location: farmerReg.php');
    exit();
}

// Password strength — min 6 chars
if (strlen($password) < 6) {
    $_SESSION['reg_error'] = "Password must be at least 6 characters.";
    header('Location: farmerReg.php');
    exit();
}

// Check if email already exists
$check = $conn->prepare("SELECT id FROM farmers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['reg_error'] = "This email is already registered. Please ";
    $check->close();
    header('Location: farmerReg.php');
    exit();
}
$check->close();

// Check if NID already exists
$checkNid = $conn->prepare("SELECT id FROM farmers WHERE nid = ?");
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

// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO farmers (name, email, phone, district, land_size, crops, nid, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $name, $email, $phone, $district, $land_size, $crops, $nid, $hashed);

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