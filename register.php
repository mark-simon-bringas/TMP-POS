<?php
    include 'config/dbconfig.php';

    if (isset($_POST['cancel'])) {
        header('location:index.php');
        exit;
    }

    if (isset($_POST['register'])) {
        $name = trim($_POST['name']);
        $role = "Worker";
        $username = trim($_POST['username']);
        $password = md5(trim($_POST['password']));

        if (empty($name) || empty($username) || empty($password)) {
            // NOTE: change to modal
            echo "
                <script>
                    alert('All fields are required');
                    window.location.href = 'register.php';
                </script>
            ";
        } else {
            try {
                $pdo->beginTransaction();
    
                $accountReg = "INSERT INTO accounts(name, role) VALUES(:name, :role)";
                $stmt1 = $pdo->prepare($accountReg);
                $stmt1->execute([
                    ":name" => $name,
                    ":role" => $role
                ]);
                
                $account_id = $pdo->lastInsertId();
                $loginReg = "INSERT INTO login(account_id, username, password) VALUES(:account_id, :username, :password)";
                $stmt2 = $pdo->prepare($loginReg);
                $stmt2->execute([
                    ":account_id" => $account_id,
                    ":username" => $username,
                    ":password" => $password
                ]);
    
                $commit = $pdo->commit();
                if ($commit) {
                    echo "
                        <script>
                            alert('User registered.');
                            window.location.href = 'index.php';
                        </script>
                    ";
                } else {
                    $pdo->rollBack();
                    echo "
                        <script>
                            alert('Failed to register user.');
                            window.location.href = 'register.php';
                        </script>
                    ";
                }
            } catch (Exception $e) {
                $pdo->rollBack();
                echo "
                    <script>
                        alert('Error: " . $e . "');
                        window.location.href = 'register.php';
                    </script>
                ";
            }
        }
    }    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMP-POS | Register</title>
</head>
<body>
    <form method="POST">
        <h1>Register</h1>
        <table>
            <tr>
                <td>Name: </td>
                <td>
                    <input type="text" name="name" placeholder="Enter your name">
                </td>
            </tr>
            <tr>
                <td>Username: </td>
                <td>
                    <input type="text" name="username" placeholder="Enter a username">
                </td>
            </tr>
            <tr>
                <td>Password: </td>
                <td>
                    <input type="password" name="password" placeholder="Enter a password">
                </td>
            </tr>
            <tr>
                <td>
                    <button name="register">Register Account</button>
                </td>
            </tr>
            <tr>
                <td>
                    <button name="cancel">Cancel</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>