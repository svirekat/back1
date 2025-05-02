<?php
session_start();
require_once 'db_connection.php';
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
    echo "<div id='greentext'>
        <p style='color:green; font-size: larger;'> Данные успешно обновлены!</p>
        </div>";
} else {
    // Если форма не была отправлена, перенаправление на edit.php
    header("Location: edit.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div>
        <a class="linktomain" href="mainpage.html">Вернуться на главную страницу</a>
    </div>
</body>
</html>
