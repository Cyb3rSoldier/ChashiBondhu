<?php
session_start();
require_once 'config.php';

$offset   = isset($_GET['offset'])   ? intval($_GET['offset'])   : 8;
$category = isset($_GET['category']) ? trim($_GET['category'])   : 'All';

if ($category === 'All') {
    $stmt = $conn->prepare("
        SELECT p.*, f.full_name AS farmer_name, f.district
        FROM products p
        LEFT JOIN farmers f ON p.farmer_id = f.id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC
        LIMIT 8 OFFSET ?
    ");
    $stmt->bind_param("i", $offset);
} else {
    $stmt = $conn->prepare("
        SELECT p.*, f.full_name AS farmer_name, f.district
        FROM products p
        LEFT JOIN farmers f ON p.farmer_id = f.id
        WHERE p.status = 'active' AND p.category = ?
        ORDER BY p.created_at DESC
        LIMIT 8 OFFSET ?
    ");
    $stmt->bind_param("si", $category, $offset);
}

$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Return empty if no more products
if (count($products) === 0) {
    exit;
}

foreach ($products as $product): ?>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-stone-300 hover:shadow-md hover:-translate-y-0.5 transition duration-200 flex flex-col cursor-pointer"
        onclick="window.location='details.php?id=<?php echo $product['id']; ?>'">

        <div class="relative">
            <img src="<?php echo $product['image_path'] ? htmlspecialchars($product['image_path']) : 'asset/img/placeholder-product.jpg'; ?>"
                alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                class="w-full h-40 object-cover"
                loading="lazy">

            <?php if ($product['badge']): ?>
                <span class="absolute top-2 left-2 text-xs font-bold px-2 py-0.5 rounded-full
                    <?php echo $product['badge'] === 'sale' ? 'bg-red-100 text-red-600' : 'bg-blue-100 text-blue-600'; ?>">
                    <?php echo ucfirst($product['badge']); ?>
                </span>
            <?php endif; ?>

            <?php if ($product['is_organic']): ?>
                <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">
                    🌿 Organic
                </span>
            <?php endif; ?>
        </div>

        <div class="p-3 flex flex-col flex-1">
            <h3 class="font-bold text-green-950 text-sm leading-snug mb-1 truncate">
                <?php echo htmlspecialchars($product['product_name']); ?>
            </h3>

            <div class="flex items-center gap-2 mb-1">
                <span class="text-green-700 font-bold text-base">
                    ৳<?php echo number_format($product['price'], 0); ?>
                </span>
                <span class="text-stone-400 text-xs">/ <?php echo $product['unit']; ?></span>
                <?php if ($product['government_price']): ?>
                    <span class="text-stone-400 text-xs line-through">
                        ৳<?php echo number_format($product['government_price'], 0); ?>
                    </span>
                <?php endif; ?>
            </div>

            <p class="text-xs text-stone-500 truncate">
                <i class="fa-solid fa-user text-stone-300 mr-1"></i>
                <?php echo htmlspecialchars($product['farmer_name'] ?? 'Unknown'); ?>
            </p>
            <p class="text-xs text-stone-400 mt-0.5 truncate">
                📍 <?php echo htmlspecialchars($product['district'] ?? ''); ?>
            </p>

            <?php if (isset($_SESSION['consumer_id'])): ?>
                <button
                    onclick="event.stopPropagation(); addToCart(<?php echo $product['id']; ?>, this)"
                    class="mt-3 w-full bg-green-700 hover:bg-green-600 text-white text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-plus text-xs"></i> Add to Cart
                </button>
            <?php else: ?>
                <a href="consumerLogin.php"
                    onclick="event.stopPropagation()"
                    class="mt-3 w-full border border-green-200 text-green-700 hover:bg-green-50 text-xs font-bold py-2 rounded-xl transition duration-200 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-right-to-bracket text-xs"></i> Login to Buy
                </a>
            <?php endif; ?>
        </div>

    </div>

<?php endforeach; ?>