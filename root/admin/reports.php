<?php
session_start();
include '../../config/dbconfig.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$reports = $pdo->query("SELECT r.report_id, a.name as account_name, r.from_date, r.to_date, r.generated_at, r.file_path FROM reports r LEFT JOIN accounts a ON r.account_id = a.account_id ORDER BY r.generated_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sales Reports - POS System</title>
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
    <h1>Sales Reports</h1>
    <?php if (count($reports) === 0): ?>
      <p>No reports available.</p>
    <?php else: ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>Report ID</th>
            <th>Account</th>
            <th>From Date</th>
            <th>To Date</th>
            <th>Generated At</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reports as $report): ?>
          <tr>
            <td><?= htmlspecialchars($report['report_id']) ?></td>
            <td><?= htmlspecialchars($report['account_name']) ?></td>
            <td><?= htmlspecialchars($report['from_date']) ?></td>
            <td><?= htmlspecialchars($report['to_date']) ?></td>
            <td><?= htmlspecialchars($report['generated_at']) ?></td>
            <td>
              <?php if ($report['file_path']): ?>
                <a href="../../uploads/<?= htmlspecialchars($report['file_path']) ?>" target="_blank">Download</a>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
