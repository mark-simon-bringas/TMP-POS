<?php
session_start();
include '../../config/dbconfig.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $_POST['item_name'] ?? '';
    $item_category = $_POST['item_category'] ?? '';
    $item_size = $_POST['item_size'] ?? '';
    $item_desc = $_POST['item_desc'] ?? '';
    $item_price = $_POST['item_price'] ?? 0;
    $item_availability = isset($_POST['item_availability']) ? 1 : 0;

    if (empty($item_name)) {
        $errors[] = "Item name is required.";
    }
    if (!is_numeric($item_price) || $item_price < 0) {
        $errors[] = "Item price must be a positive number.";
    }

    if (empty($errors)) {
        $add = $pdo->prepare("INSERT INTO items (item_name, item_category, item_size, item_desc, item_price, item_availability) VALUES (?, ?, ?, ?, ?, ?)");
        $add->execute([$item_name, $item_category, $item_size, $item_desc, $item_price, $item_availability]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Product - POS System</title>
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
      <h6 class="text-white mb-4">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> </h6>
      <h6 class="text-white-50">Home</h6>
      <a href="../admin/dashboard.php">Dashboard</a>
      <h6 class="text-white-50">Control Center</h6>
      <a href="../admin/addproduct.php">Add Product</a>
      <a href="../admin/manageproducts.php">Manage Products</a>
      <a href="../admin/reports.php">Reports</a>
      <a href="../../root/logout.php">Logout</a>
    </div>
  <div class="flex-grow-1 p-4">
    <h1>Add New Product</h1>
    <?php if ($success): ?>
      <div class="alert alert-success">Product added successfully.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="item_name" class="form-label">Item Name</label>
        <input type="text" name="item_name" id="item_name" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="item_category" class="form-label">Category</label>
        <select name="intem_category" id="item_category" class="form-select">
          <?php
          $categories = ['None','Food','Beverage',];
          foreach($categories as $category){
            $select = ($item['item_category'] === $category) ? 'selected' : '';
            echo "<option value=\"" . htmlspecialchars($category) . "\" $select>" . htmlspecialchars($category) . "</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="item_size" class="form-label">Size</label>
        <select name="item_size" id="item_size" class="form-select">
          <?php
          $sizes = ['None', 'Small', 'Medium', 'Large', 'Extra Large'];
          foreach ($sizes as $size) {
              $selected = ($item['item_size'] === $size) ? 'selected' : '';
              echo "<option value=\"" . htmlspecialchars($size) . "\" $selected>" . htmlspecialchars($size) . "</option>";
          }
          ?>
        </select>
      </div>
      <div class="mb-3">
        <label for="item_desc" class="form-label">Description</label>
        <textarea name="item_desc" id="item_desc" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label for="item_price" class="form-label">Price</label>
        <input type="number" step="0.01" name="item_price" id="item_price" class="form-control" required />
      </div>
      <div class="form-check mb-3">
        <input type="checkbox" name="item_availability" id="item_availability" class="form-check-input" checked />
        <label for="item_availability" class="form-check-label">Available</label>
      </div>
      <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
  </div>
</div>
</body>
