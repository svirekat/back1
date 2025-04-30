<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Код для обновления данных пользователя здесь...
$user_id = $_SESSION['user_id'];
$fio = sanitize($_POST['fio']);
$phone = sanitize($_POST['phone']);
// Обновление в базе данных
$stmt = $pdo->prepare("UPDATE users SET fio = ?, phone = ? WHERE user_id = ?");
$stmt->execute([$fio, $phone, $user_id]);
echo "<p>Данные успешно обновлены!</p>";