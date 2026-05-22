<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vendorReg.php');
    exit();
}

$full_name     = trim($_POST['full_name'] ?? '');
$business_name = trim($_POST['business_name'] ?? '');
$email         = trim($_POST['email'] ?? '');
$phone         = trim($_POST['phone'] ?? '');
$district      = trim($_POST['district'] ?? '');
$business_type = trim($_POST['business_type'] ?? '');
$nid           = trim($_POST['nid'] ?? '');
$password      = $_POST['password'] ?? '';
$confirm       = $_POST['confirm_password'] ?? '';

// Validation
$errors = [];

if (empty($full_name))     $errors[] = 'Full name is required.';
if (empty($business_name)) $errors[] = 'Business name is required.';
if (empty($email))         $errors[] = 'Email is required.';
if (empty($phone))         $errors[] = 'Phone number is required.';
if (empty($district))      $errors[] = 'District is required.';
if (empty($business_type)) $errors[] = 'Business type is required.';
if (empty($nid))           $errors[] = 'NID is required.';
if (empty($password))      $errors[] = 'Password is required.';
if (empty($confirm))       $errors[] = 'Confirm password is required.';

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address.";
}

if (!empty($password) && strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

if (!empty($password) && !empty($confirm) && $password !== $confirm) {
    $errors[] = "Passwords do not match.";
}

if (!empty($errors)) {
    $_SESSION['reg_error'] = implode('<br>', $errors);
    header('Location: vendorReg.php');
    exit();
}

// Check duplicate email
$check = $conn->prepare("SELECT id FROM vendors WHERE email = ? LIMIT 1");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    $_SESSION['reg_error'] = "This email is already registered. <a href='vendorLogin.php'>Login instead</a>";
    $check->close();
    header('Location: vendorReg.php');
    exit();
}
$check->close();

// Check duplicate phone
$checkPhone = $conn->prepare("SELECT id FROM vendors WHERE phone = ? LIMIT 1");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$checkPhone->store_result();
if ($checkPhone->num_rows > 0) {
    $_SESSION['reg_error'] = "This phone number is already registered.";
    $checkPhone->close();
    header('Location: vendorReg.php');
    exit();
}
$checkPhone->close();

// Check duplicate NID
$checkNid = $conn->prepare("SELECT id FROM vendors WHERE nid = ? LIMIT 1");
$checkNid->bind_param("s", $nid);
$checkNid->execute();
$checkNid->store_result();
if ($checkNid->num_rows > 0) {
    $_SESSION['reg_error'] = "This NID is already registered.";
    $checkNid->close();
    header('Location: vendorReg.php');
    exit();
}
$checkNid->close();

// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Insert vendor
$stmt = $conn->prepare("
    INSERT INTO vendors 
        (full_name, business_name, email, phone, district, business_type, nid, password_hash, account_status) 
    VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, 'active')
");
$stmt->bind_param("ssssssss", $full_name, $business_name, $email, $phone, $district, $business_type, $nid, $hashed);

if ($stmt->execute()) {
    $_SESSION['reg_success'] = "Registration successful! You can now log in.";
    $stmt->close();
    $conn->close();
    header('Location: vendorLogin.php');
    exit();
} else {
    error_log("Vendor registration error: " . $stmt->error);
    $_SESSION['reg_error'] = "Something went wrong. Please try again. Error: " . $stmt->error;
    $stmt->close();
    $conn->close();
    header('Location: vendorReg.php');
    exit();
}
?>