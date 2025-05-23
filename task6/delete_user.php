<?php
session_start();
require_once 'db_connection.php';

$user_id = $_GET['user_id']; 

$stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);

header("Location: adminpage.php");
exit();
?>
