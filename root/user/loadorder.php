<?php
session_start();

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Access denied.";
    exit();
}

if ($_SESSION['user']['role'] !== 'Worker') {
    http_response_code(403);
    echo "Access denied.";
    exit();
}

$order = $_SESSION['order'] ?? [];

if (empty($order)) {
    echo '<p class="text-muted">No items yet.</p>';
    exit();
}

$total = 0;
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($order as $item): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
            <td>
                <form method="POST" action="removeorder.php" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                    <input type="number" name="quantity" value="1" min="1" max="<?= htmlspecialchars($item['quantity']) ?>" style="width: 60px; display: inline-block; margin-right: 5px;" required>
                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="text-end"><strong>Total</strong></td>
            <td colspan="2"><strong>₱<?= number_format($total, 2) ?></strong></td>
        </tr>
    </tbody>
</table>
