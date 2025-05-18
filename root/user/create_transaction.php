<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Worker') {
    header("location:../index.php");
    exit();
}
include '../../config/dbconfig.php';

$errors = [];
$success = false;

$fetch = $pdo->query("SELECT item_id, item_name, item_price FROM items WHERE item_availability = 1 ORDER BY item_name");
$items = $fetch->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_type = $_POST['transaction_type'] ?? 'Dine In';
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $discount_reason = $_POST['discount_reason'] ?? '';
    $items_ordered = $_POST['items'] ?? [];

    if (empty($items_ordered)) {
        $errors[] = "Please select at least one item.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $total_amount = 0;
            foreach ($items_ordered as $item_id => $quantity) {
                $quantity = intval($quantity);
                if ($quantity > 0) {
                    $fetchprice = $pdo->prepare("SELECT item_price FROM items WHERE item_id = ?");
                    $fetchprice->execute([$item_id]);
                    $item = $fetchprice->fetch();
                    if ($item) {
                        $total_amount += $item['item_price'] * $quantity;
                    }
                }
            }
            $total_amount -= $discount_amount;

            $create = $pdo->prepare("INSERT INTO transactions (account_id, transaction_date, transaction_type, discount_amount, discount_reason, total_amount, payment_method) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
            $create->execute([
                $_SESSION['user']['account_id'],
                $transaction_type,
                $discount_amount,
                $discount_reason,
                $total_amount,
                $payment_method
            ]);
            $transaction_id = $pdo->lastInsertId();

            foreach ($items_ordered as $item_id => $quantity) {
                $quantity = intval($quantity);
                if ($quantity > 0) {
                    $ticket = $pdo->prepare("INSERT INTO tickets (transaction_id, item_id, ticket_quantity, purchase_price) VALUES (?, ?, ?, ?)");
                    $ticket->execute([
                        $transaction_id,
                        $item_id,
                        $quantity,
                        $items[array_search($item_id, array_column($items, 'item_id'))]['item_price']
                    ]);
                }
            }

            $pdo->commit();
            $success = true;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Transaction failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create Transaction - POS System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">POS User</a>
        <div class="d-flex">
            <span class="navbar-text me-3">Hello, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
            <a href="../logout.php" class="btn btn-outline-light">Logout</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
    <h1>Create Transaction</h1>
    <?php if ($success): ?>
        <div class="alert alert-success">Transaction created successfully.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="transaction_type" class="form-label">Transaction Type</label>
            <select name="transaction_type" id="transaction_type" class="form-select">
                <option value="Dine In">Dine In</option>
                <option value="Take Out">Take Out</option>
                <option value="Pickup">Pickup</option>
                <option value="Delivery">Delivery</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select name="payment_method" id="payment_method" class="form-select">
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Online">Online</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="discount_amount" class="form-label">Discount Amount</label>
            <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="form-control" value="0" />
        </div>
        <div class="mb-3">
            <label for="discount_reason" class="form-label">Discount Reason</label>
            <input type="text" name="discount_reason" id="discount_reason" class="form-control" />
        </div>
        <h3>Items</h3>
        <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
                <div class="mb-3 row align-items-center">
                    <label class="col-sm-6 col-form-label"><?php echo htmlspecialchars($item['item_name']); ?> ($<?php echo number_format($item['item_price'], 2); ?>)</label>
                    <div class="col-sm-3">
                        <input type="number" min="0" name="items[<?php echo $item['item_id']; ?>]" class="form-control" value="0" />
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No items available.</p>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Submit Transaction</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
