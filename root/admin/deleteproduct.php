<?php
session_start();
include '../../config/dbconfig.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Admin') {
    header("Location: ../index.php");
    exit;
}

$item_id = $_GET['id'] ?? null;
if (!$item_id) {
    header("Location: dashboard.php");
    exit;
}

$del = $pdo->prepare("DELETE FROM items WHERE item_id = ?");
$del->execute([$item_id]);

header("Location: dashboard.php");
exit;
?>
