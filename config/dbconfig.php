<?php

    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "pos";
    $charset = "utf8mb4";

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

    try {
        $settings = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ];
        $pdo = new PDO($dsn, $user, $pass, $settings);
    }catch (PDOException $e) {
        echo " ". $e->getMessage();
    }
?>
