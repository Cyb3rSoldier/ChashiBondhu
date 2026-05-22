
<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['vendor_id'])) {
    header('Location: vendor/vendorLogin.php');
    exit();
}

$vendorId = $_SESSION['vendor_id'];
$toolId   = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($toolId > 0) {
    // Get image path before deleting
    $stmt = $conn->prepare("SELECT image_path FROM tool_products WHERE id = ? AND vendor_id = ?");
    $stmt->bind_param("ii", $toolId, $vendorId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        // Delete from database
        $stmt = $conn->prepare("DELETE FROM tool_products WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param("ii", $toolId, $vendorId);
        $stmt->execute();
        $stmt->close();

        // Delete image file
        if (!empty($row['image_path'])) {
            $fullPath = __DIR__ . '/' . $row['image_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }

        $_SESSION['tool_success'] = 'Tool deleted successfully.';
    } else {
        $_SESSION['tool_error'] = 'Tool not found or access denied.';
    }
} else {
    $_SESSION['tool_error'] = 'Invalid tool.';
}

$conn->close();
header('Location: vendor-tools.php');
exit();
?>