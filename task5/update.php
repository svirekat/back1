<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
function sanitize($data) {
  $data = trim($data);    
  $data = stripslashes($data);    
  $data = htmlspecialchars($data);    
  return $data;
}
// Проверка, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $fio = sanitize($_POST['fio']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $dob = sanitize($_POST['dob']);
    $gender = sanitize($_POST['gender']);
    $bio = sanitize($_POST['bio']);
    // Подготовка и выполнение SQL-запроса для обновления данных
    $stmt = $pdo->prepare("UPDATE users SET fio = ?, phone = ?, email = ?, dob = ?, gender = ?, bio = ? WHERE user_id = ?");
    $stmt->execute([$fio, $phone, $email, $dob, $gender, $bio, $user_id]);
    echo "<p>style='color:green;' Данные успешно обновлены!</p>";
} else {
    // Если форма не была отправлена, перенаправление на edit.php
    header("Location: edit.php");
    exit();
}
