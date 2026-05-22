<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $secret = trim($_POST['secret_key']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        // Direct comparison (NO HASH)
        if (
            $password === $admin['password_hash'] &&
            $secret === $admin['secret_key_hash']
        ) {

            session_regenerate_id(true);

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];

            header('Location: admin_dashboard.php');
            exit();
        }
    }

    $_SESSION['login_error'] = 'Invalid credentials';
    header('Location: admin.php');
    exit();
}
?>