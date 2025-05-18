<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Worker') {
    http_response_code(403);
    echo "Access denied.";
    exit();
}
include '../../config/dbconfig.php';

$fetch = $pdo->query("SELECT item_id, item_name, item_category, item_size, item_desc, item_price, item_availability FROM items WHERE item_availability = 1 ORDER BY item_name");
$items = $fetch->fetchAll();

if (count($items) > 0):
    foreach ($items as $item):
?>
<div class="col-md-4 mb-3">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($item['item_category']); ?> - <?php echo htmlspecialchars($item['item_size']); ?></h6>
            <p class="card-text"><?php echo nl2br(htmlspecialchars($item['item_desc'])); ?></p>
            <p class="card-text"><strong>Price: </strong>₱<?php echo number_format($item['item_price'], 2); ?></p>
            <input type="number" class="form-control mb-2 quantity-input" value="1" min="1" style="width: 80px;">
            <button class="btn btn-primary add-to-cart" data-id="<?php echo $item['item_id']; ?>">Add to Cart</button>
        </div>
    </div>
</div>
<?php
    endforeach;
else:
?>
<p>No items available at the moment.</p>
<?php
endif;
?>
