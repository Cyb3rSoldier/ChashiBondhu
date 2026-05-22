
<?php
require_once 'config.php';

// Reset admin credentials
$email = 'admin@chashibondhu.com';
$password = 'Admin@123';
$secretKey = 'SuperSecret@456';

// Hash the passwords
$passwordHash = password_hash($password, PASSWORD_BCRYPT);
$secretHash = password_hash($secretKey, PASSWORD_BCRYPT);

echo "<h1>Resetting Admin Credentials</h1>";
echo "<p>Email: $email</p>";
echo "<p>Password: $password</p>";
echo "<p>Secret Key: $secretKey</p>";
echo "<hr>";

// Update the admin account
$stmt = $conn->prepare("UPDATE admins SET password_hash = ?, secret_key_hash = ? WHERE email = ?");
$stmt->bind_param("sss", $passwordHash, $secretHash, $email);

if ($stmt->execute()) {
    echo "<p style='color:green;font-weight:bold;'>✅ Admin credentials updated successfully!</p>";
    echo "<p>You can now login with:</p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@chashibondhu.com</li>";
    echo "<li><strong>Password:</strong> Admin@123</li>";
    echo "<li><strong>Secret Key:</strong> SuperSecret@456</li>";
    echo "</ul>";
    
    // Verify the new hashes work
    echo "<hr><h2>Verification:</h2>";
    
    // Get the updated record
    $checkStmt = $conn->prepare("SELECT password_hash, secret_key_hash FROM admins WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $admin = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();
    
    $passValid = password_verify($password, $admin['password_hash']);
    $secretValid = password_verify($secretKey, $admin['secret_key_hash']);
    
    echo "<p>Password verification: " . ($passValid ? '✅ VALID' : '❌ INVALID') . "</p>";
    echo "<p>Secret Key verification: " . ($secretValid ? '✅ VALID' : '❌ INVALID') . "</p>";
    
    if ($passValid && $secretValid) {
        echo "<p style='color:green;font-size:16px;'>✅ Both passwords verified! You can now login.</p>";
        echo "<p><a href='admin.php'>Click here to go to Admin Login</a></p>";
    }
} else {
    echo "<p style='color:red;'>❌ Failed to update: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>