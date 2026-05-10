
<?php
/**
 * Add Item to Cart
 * Called via AJAX from product cards
 */
session_start();
require_once '../config/database.php';
require_once '../includes/marketplace-functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['consumer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to add items to cart']);
    exit;
}

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$consumerId = $_SESSION['consumer_id'];
$productId = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);

if ($productId <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

try {
    $conn = Database::getConnection();
    
    // Check if product exists and is active
    $stmt = $conn->prepare("SELECT id, quantity, status FROM products WHERE id = ? AND status = 'active'");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not available']);
        exit;
    }
    
    if ($product['quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
        exit;
    }
    
    // Check if already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE consumer_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $consumerId, $productId);
    $stmt->execute();
    $existing = $stmt->get_result()->fetch_assoc();
    
    if ($existing) {
        // Update quantity
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $newQty, $existing['id']);
        $stmt->execute();
    } else {
        // Insert new
        $stmt = $conn->prepare("INSERT INTO cart (consumer_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $consumerId, $productId, $quantity);
        $stmt->execute();
    }
    
    // Get updated cart count
    $count = getCartCount($consumerId);
    
    echo json_encode(['success' => true, 'message' => 'Added to cart! 🛒', 'count' => $count]);
    
} catch (Exception $e) {
    error_log("Cart error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Something went wrong']);
}