<?php
require_once 'config.php';
$result = $conn->query("SELECT email, password_hash, secret_key_hash FROM admins WHERE email = 'admin@chashibondhu.com'");
if ($row = $result->fetch_assoc()) {
    echo "Admin found.<br>";
    echo "Password hash: " . $row['password_hash'] . "<br>";
    echo "Secret hash: " . $row['secret_key_hash'] . "<br>";
    echo "Test password_verify('Admin@123'): " . (password_verify('Admin@123', $row['password_hash']) ? 'OK' : 'FAIL') . "<br>";
    echo "Test password_verify('SuperSecret@456'): " . (password_verify('SuperSecret@456', $row['secret_key_hash']) ? 'OK' : 'FAIL');
} else {
    echo "No admin found with that email.";
}
?>