<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: consumerReg.php');
    exit();
}

$full_name = trim($_POST['full_name']);
$email     = trim($_POST['email']);
$phone     = trim($_POST['phone']);
$address   = trim($_POST['address']);
$password  = $_POST['password_hash'];
$confirm   = $_POST['confirm_password'];


if (
    empty($full_name) ||
    empty($email) ||
    empty($phone) ||
    empty($address) ||
    empty($password) ||
    empty($confirm)
) {
    $_SESSION['reg_error'] = "All fields are required.";
    header('Location: consumerReg.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['reg_error'] = "Please enter a valid email address.";
    header('Location: consumerReg.php');
    exit();
}

if ($password !== $confirm) {
    $_SESSION['reg_error'] = "Passwords do not match.";
    header('Location: consumerReg.php');
    exit();
}

if (strlen($password) < 6) {
    $_SESSION['reg_error'] = "Password must be at least 6 characters long.";
    header('Location: consumerReg.php');
    exit();
}


$checkEmail = $conn->prepare("SELECT id FROM consumers WHERE email = ? LIMIT 1");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();

if ($checkEmail->num_rows > 0) {
    $_SESSION['reg_error'] = "This email is already registered. <a href='consumerLogin.php' class='underline font-bold'>Login instead</a>";
    $checkEmail->close();
    header('Location: consumerReg.php');
    exit();
}

$checkEmail->close();


$checkPhone = $conn->prepare("SELECT id FROM consumers WHERE phone = ? LIMIT 1");
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();
$checkPhone->store_result();

if ($checkPhone->num_rows > 0) {
    $_SESSION['reg_error'] = "This phone number is already registered.";
    $checkPhone->close();
    header('Location: consumerReg.php');
    exit();
}

$checkPhone->close();


$hashedPassword = password_hash($password, PASSWORD_BCRYPT);


$stmt = $conn->prepare("
    INSERT INTO consumers 
    (full_name, email, password_hash, phone, address, account_status)
    VALUES
    (?, ?, ?, ?, ?, 'active')
");

$stmt->bind_param(
    "sssss",
    $full_name,
    $email,
    $hashedPassword,
    $phone,
    $address
);

if ($stmt->execute()) {

    $_SESSION['success_message'] = "Registration successful! Please wait for admin verification before logging in.";

    $stmt->close();
    $conn->close();

    header('Location: consumerReg.php');
    exit();
} else {

    $_SESSION['reg_error'] = "Something went wrong. Please try again.";

    $stmt->close();
    $conn->close();

    header('Location: consumerReg.php');
    exit();
}
