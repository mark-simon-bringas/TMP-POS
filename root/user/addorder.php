<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

if ($_SESSION['user']['role'] !== 'Worker') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

$productId = $_POST['product_id'] ?? null;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

if ($productId === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit();
}

if ($quantity < 1) {
    $quantity = 1;
}

if (!isset($_SESSION['order'])) {
    $_SESSION['order'] = [];
}

// Check if product already in order, then increase quantity
$found = false;
foreach ($_SESSION['order'] as &$item) {
    if ($item['item_id'] == $productId) {
        $item['quantity'] += $quantity;
        $found = true;
        break;
    }
}
unset($item);

if (!$found) {
    include '../../config/dbconfig.php';
    $fetch = $pdo->prepare("SELECT item_name, item_price FROM items WHERE item_id = ?");
    $fetch->execute([$productId]);
    $product = $fetch->fetch();

    if ($product) {
        $name = $product['item_name'];
        $price = floatval($product['item_price']);
    } else {
        $name = 'Unknown';
        $price = 0.0;
    }

    $_SESSION['order'][] = [
        'item_id' => $productId,
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity
    ];
}

echo json_encode([
    'success' => true,
    'message' => 'Product added to order',
    'user_role' => $_SESSION['user']['role'] ?? null,
    'post_data' => $_POST
]);
?>
