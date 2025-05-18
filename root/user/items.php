<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Worker') {
    header("location:../index.php");
    exit();
}
include '../../config/dbconfig.php';

$fetch = $pdo->query("SELECT item_id, item_name, item_category, item_size, item_desc, item_price, item_availability FROM items WHERE item_availability = 1 ORDER BY item_name");
$items = $fetch->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Items - POS System</title>
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
    <h1>Available Items</h1>
    <?php if (count($items) > 0): ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($items as $item): ?>
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($item['item_category']); ?> - <?php echo htmlspecialchars($item['item_size']); ?></h6>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($item['item_desc'])); ?></p>
                    <p class="card-text"><strong>Price: </strong>₱<?php echo number_format($item['item_price'], 2); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p>No items available at the moment.</p>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
