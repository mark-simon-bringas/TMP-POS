<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "
        <script>
            alert('Error Access Denied');
            window.location.href = './dashboard.php';
        </script>
    ";
}

if ($_SESSION['user']['role'] !== 'Worker') {
    echo "
        <script>
            alert('Error Access Denied');
            window.location.href = './dashboard.php';
        </script>
    ";
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "
        <script>
            alert('Error Method Not Allowed');
            window.location.href = './dashboard.php';
        </script>
    ";
}

$productId = $_POST['product_id'] ?? null;
$quantityToRemove = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId === null) {
    echo "
        <script>
            alert('Error Product Id Required');
            window.location.href = './dashboard.php';
        </script>
    ";
}

if ($quantityToRemove < 1) {
    echo "
        <script>
            alert('Error Invalid Quantity');
            window.location.href = './dashboard.php';
        </script>
    ";
}

if (!isset($_SESSION['order'])) {
    $_SESSION['order'] = [];
}

$found = false;
foreach ($_SESSION['order'] as $key => &$item) {
    if ($item['item_id'] == $productId) {
        if ($quantityToRemove >= $item['quantity']) {
            // Remove the item entirely
            unset($_SESSION['order'][$key]);
        } else {
            // Reduce the quantity
            $item['quantity'] -= $quantityToRemove;
        }
        $found = true;
        break;
    }
}
unset($item);

if ($found) {
    $_SESSION['order'] = array_values($_SESSION['order']);
    echo "
        <script>
            alert('Order/s Removed Successfully');
            window.location.href = './dashboard.php';
        </script>
    ";
} else {
    echo "
        <script>
            alert('Order/s Removed Failed');
            window.location.href = './dashboard.php';
        </script>
    ";
}
?>
