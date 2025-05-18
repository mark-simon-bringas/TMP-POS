<?php
session_start();
include '../config/dbconfig.php';

if (isset($_SESSION['user'])){
    if ($_SESSION['user']['role'] == 'Admin'){
        header("location:../root/admin/dashboard.php");
    }else{
        header("location:../root/user/dashboard.php");
    }
}

if (isset($_POST['submit'])) {
    $usern = $_POST['usern'];
    $pass = $_POST['pass'];

    $fetch = $pdo->prepare("SELECT l.login_id, l.account_id, l.username, l.password, l.status, a.name, a.role FROM login l JOIN accounts a ON l.account_id = a.account_id WHERE l.username = :usern");
    $fetch->execute([
        ":usern" => $usern,
    ]);
    $user = $fetch->fetch();

    if ($user && $user['status'] === 'Active') {
        if (($user['role'] === 'Admin' && $pass === $user['password']) || ($user['role'] !== 'Admin' && md5($pass) === $user['password'])) {
            $_SESSION['user'] = [
                'login_id' => $user['login_id'],
                'account_id' => $user['account_id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'role' => $user['role']
            ];
            if ($user['role'] === 'Admin') {
                header("location:../root/admin/dashboard.php");
            } else {
                header("location:../root/user/dashboard.php");
            }
            exit();
        }
    }else{
        echo "
            <script>
                alert('Invalid credentials!');
                window.location.href = 'index.php';
            </script>
        ";
    }
}
?>
