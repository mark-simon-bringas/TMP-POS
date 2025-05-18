<?php
include '../config/dbconfig.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4 mb-4">
    <h2>Register</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="mt-3">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" required class="form-control">
        </div>

        <div class="mb-3">
            <label>Username</label>
            <input type="text" name="usern" required class="form-control">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="pass" required class="form-control">
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
        <a href="../index.php" class="btn btn-secondary">Back</a>
    </form>

    <?php
        $errors = [];
        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $usern = $_POST['usern'];
            $pass = md5($_POST['pass']);
            $role = 'Worker';
            $status = 'Active';

            try {
                $pdo->beginTransaction();

                $insertAccount = "INSERT INTO accounts (name, role, created_at) VALUES (:name, :role, NOW())";
                $account = $pdo->prepare($insertAccount);
                $account->execute([
                    ':name' => $name,
                    ':role' => $role
                ]);
                $accountId = $pdo->lastInsertId();

                $insertLogin = "INSERT INTO login (account_id, username, password, status) VALUES (:account_id, :username, :password, :status)";
                $login = $pdo->prepare($insertLogin);
                $login->execute([
                    ':account_id' => $accountId,
                    ':username' => $usern,
                    ':password' => $pass,
                    ':status' => $status
                ]);

                $pdo->commit();
                header("location:../root/index.php");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = "Registration failed: " . $e->getMessage();
            }
        }
    ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mt-3">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>
</html>
