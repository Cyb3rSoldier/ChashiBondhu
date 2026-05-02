<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, phone, role, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $phone, $role, $subject, $message);

    if ($stmt->execute()) {
        $_SESSION['message_success'] = "Message delivered successfully. Thanks for reaching out!";
    } else {
        echo "Error: " . $stmt->error;
    }


    $stmt->close();
    $conn->close();
    session_write_close();
    header("Location: contact.php#contact");
    exit();
}
?>