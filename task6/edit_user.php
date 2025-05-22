<?php
require 'db_connection.php';

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        echo "Пользователь не найден.";
        exit;
    }
} else {
    echo "user_id не задан.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fio = $_POST['fio'] ?? '';
    $login = $_POST['login'] ?? '';

    $stmt = $pdo->prepare('UPDATE users SET fio = :fio, login = :login WHERE user_id = :user_id');
    $stmt->execute(['fio' => $fio, 'login' => $login, 'user_id' => $user_id]);

    echo "Данные пользователя успешно обновлены.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование пользователя</title>
</head>
<body>
    <h1>Редактирование пользователя</h1>
    <form method="POST">
        <label for="fio">ФИО:</label>
        <input type="text" id="fio" name="fio" value="<?= htmlspecialchars($user['fio']) ?>" required><br>

        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" value="<?= htmlspecialchars($user['login']) ?>" required><br>

        <button type="submit">Сохранить</button>
    </form>

    <a href="admin_panel.php">Назад к панели администратора</a>
</body>
</html>