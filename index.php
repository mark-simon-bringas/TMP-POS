<?php
    include 'config/dbconfig.php';

    if (isset($_POST['register'])) {
        header('location:register.php');
        exit;
    }

    // add login logic
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMP-POS | Login</title>
</head>
<body>
    <form method="POST">
        <h1>Login</h1>
        <table>
            <tr>
                <td>Username: </td>
                <td>
                    <input type="text" name="username" placeholder="Enter username">
                </td>
            </tr>
            <tr>
                <td>Password: </td>
                <td>
                    <input type="password" name="password" placeholder="Enter password">
                </td>
            </tr>
            <tr>
                <td>
                    <button name="login">Log In</button>
                </td>
            </tr>
            <tr>
                <td>
                    <button name="register">Register</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>