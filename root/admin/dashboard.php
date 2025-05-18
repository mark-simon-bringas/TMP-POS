<?php
include '../../config/dbconfig.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];

$fetch = $pdo->prepare("SELECT a.account_id, a.name, l.username, a.role FROM accounts a JOIN login l ON a.account_id = l.account_id WHERE a.account_id != :id");
$fetch->execute([
    ":id" => $user['account_id']
]);
$users = $fetch->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cafe Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
    .menu-card img {
      height: 150px;
      object-fit: cover;
    }
    @font-face {
      font-family: 'FeelingPassionate';
      src: url(../../FeelingPassionateRegular-gxp34.ttf);
    }
    .feeling-passionate {
      font-family: 'FeelingPassionate', cursive;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <div class="sidebar d-flex flex-column p-3">
    <h4 class="text-white text-center mt-3 mb-5 ms-2 me-2 feeling-passionate">the meeting place</h4>
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
    <h2>Manage Accounts</h2>
    <form action="" method="POST">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th><input type="checkbox" id="select_all"></th>
            <th>ID</th>
            <th>Name</th>
            <th>Username</th>
            <th>Role</th>
            <th colspan="2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $data): ?>
          <tr>
            <td><input type="checkbox" name="ids[]" value="<?= htmlspecialchars($data['account_id']) ?>" class="delete-checkbox"></td>
            <td><?= htmlspecialchars($data['account_id']) ?></td>
            <td><?= htmlspecialchars($data['name']) ?></td>
            <td><?= htmlspecialchars($data['username']) ?></td>
            <td><?= htmlspecialchars($data['role']) ?></td>
            <td>
              <a href="#edit.php?id=<?= htmlspecialchars($data['account_id']) ?>" class="btn btn-primary btn-sm">Edit</a>
              <a href="#delete.php?id=<?= htmlspecialchars($data['account_id']) ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</a>
            </td>
          </tr>
          <?php endforeach ?>
        </tbody>
      </table>
      <div class="text-end">
        <button type="submit" name="s_delete" onclick="return confirm('Delete all selected?')" class="btn btn-danger btn-sm" id="select_delete_button" style="display: none;">Delete Selected</button>
      </div>
    </form>
  </div>
</div>
<script>
  document.getElementById('select_all').onclick = function () {
      const checkboxes = document.getElementsByName('ids[]');
      for (let i = 0; i < checkboxes.length; i++) {
          checkboxes[i].checked = this.checked;
      }
      document.getElementById('select_delete_button').style.display = this.checked ? 'inline-block' : 'none';
  };
  const deleteCheckboxes = document.querySelectorAll('.delete-checkbox');
  deleteCheckboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
          const anyChecked = Array.from(deleteCheckboxes).some(cb => cb.checked);
          document.getElementById('select_delete_button').style.display = anyChecked ? 'inline-block' : 'none';
      });
  });
</script>
</body>
</html>
