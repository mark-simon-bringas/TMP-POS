<?php
session_start();
include '../../config/dbconfig.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Worker') {
    header("Location: ../index.php");
    exit;
}

$order = $_SESSION['order'] ?? [];

$total = 0;
foreach ($order as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transaction_type = $_POST['transaction_type'] ?? 'Dine In';
    $payment_method = $_POST['payment_method'] ?? 'Cash';
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $discount_reason = $_POST['discount_reason'] ?? '';

    $final_total = $total - $discount_amount;

    try {
        $pdo->beginTransaction();

        $transac = $pdo->prepare("INSERT INTO transactions (account_id, transaction_date, transaction_type, discount_amount, discount_reason, total_amount, payment_method) VALUES (?, NOW(), ?, ?, ?, ?, ?)");
        $transac->execute([
            $_SESSION['user']['account_id'],
            $transaction_type,
            $discount_amount,
            $discount_reason,
            $final_total,
            $payment_method
        ]);
        $transaction_id = $pdo->lastInsertId();

        foreach ($order as $item) {
            $tick = $pdo->prepare("INSERT INTO tickets (transaction_id, item_id, ticket_quantity, purchase_price) VALUES (?, ?, ?, ?)");
            $tick->execute([
                $transaction_id,
                $item['item_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        $pdo->commit();

        unset($_SESSION['order']);

        $success = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Transaction failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Checkout - POS System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h1>Checkout</h1>
  <?php if (!empty($success)): ?>
    <div class="alert alert-success">Transaction completed successfully.</div>
    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
  <?php else: ?>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (empty($order)): ?>
      <p>No items in the order.</p>
      <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    <?php else: ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Item</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td>₱<?= number_format($item['price'], 2) ?></td>
            <td>₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr>
            <td colspan="3" class="text-end"><strong>Total</strong></td>
            <td><strong>₱<?= number_format($total, 2) ?></strong></td>
          </tr>
        </tbody>
      </table>
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
        <button type="submit" class="btn btn-success">Complete Transaction</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
</div>
</body>
</html>
