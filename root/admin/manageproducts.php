<?php
include '../../config/dbconfig.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];

$search = $_GET['search'] ?? '';

$whereClause = '';
$params = [];

if ($search !== '') {
    $whereClause = "WHERE item_name LIKE :search OR item_category LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$orderBy = "item_name ASC";

$sql = "SELECT * FROM items $whereClause ORDER BY $orderBy";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Products - POS System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      height: 100vh;
      background-color: #343a40;
    }
    .sidebar a {
      color: #fff;
      padding: 15px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <div class="sidebar d-flex flex-column p-3">
    <h4 class="text-white text-center mb-4">Cafe Menu</h4>
    <h6 class="text-white mb-4">Welcome, <?= htmlspecialchars($user['name']) ?> </h6>
    <h6 class="text-white-50">Home</h6>
    <a href="../admin/dashboard.php">Dashboard</a>
    <h6 class="text-white-50">Control Center</h6>
    <a href="../admin/addproduct.php">Add Product</a>
    <a href="../admin/manageproducts.php">Manage Products</a>
    <a href="../admin/reports.php">Reports</a>
    <a href="../../root/logout.php">Logout</a>
  </div>
  <div class="flex-grow-1 p-4">
    <h1>Manage Products</h1>
    <form method="GET" class="mb-3 d-flex gap-2">
      <input type="text" name="search" class="form-control" placeholder="Search by name or category" value="<?= htmlspecialchars($search) ?>" />
      <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <?php if (count($items) === 0): ?>
      <p>No products available.</p>
    <?php else: ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Size</th>
            <th>Price</th>
            <th>Availability</th>
            <th colspan="2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['item_id']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= htmlspecialchars($item['item_category']) ?></td>
            <td><?= htmlspecialchars($item['item_size']) ?></td>
            <td>₱<?= number_format($item['item_price'], 2) ?></td>
            <td><?= $item['item_availability'] ? 'Available' : 'Unavailable' ?></td>
            <td><a href="editproduct.php?id=<?= htmlspecialchars($item['item_id']) ?>" class="btn btn-primary btn-sm">Edit</a></td>
            <td><a href="deleteproduct.php?id=<?= htmlspecialchars($item['item_id']) ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
