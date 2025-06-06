<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); 
    exit();
}

require_once 'db_connection.php';

try {
    // Запрос для получения данных пользователей
    $stmt = $pdo->prepare("
        SELECT 
            u.user_id, u.login, u.fio, u.phone, u.email, u.dob, u.gender, u.bio,
            GROUP_CONCAT(l.lang_name ORDER BY l.lang_name SEPARATOR ', ') AS langs
        FROM users u
        LEFT JOIN users_languages ul ON u.user_id = ul.user_id
        LEFT JOIN langs l ON ul.lang_id = l.lang_id
        GROUP BY u.user_id
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Ошибка при получении данных: " . htmlspecialchars($e->getMessage());
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Страница администратора</title>
    <link rel="stylesheet" href="styles.css"> 
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #c2c2e5;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        a {
            border: 2px solid #073318;
            background-color: #64ed73;
            text-decoration: none;
            color: #0e0e2d;
            padding: 1px;
        }
        button {
            padding: 0;
            border: 2px solid #51091a;
            background-color: #eb5277;
            padding: 1px;
        }
        .exit {
            position: absolute;
            top: 10px;
            left: 10px;
            border: 2px solid #3c2057;
            padding: 8px;
            border-radius: 5%;
        }
        .langbutton {
            position: absolute;
            top: 10px;
            right: 10px;
            border: 2px solid #3c2057;
            padding: 8px;
            background-color: #ccb4e4;
            border-radius: 5%;
        }
    </style>
    <script>
        function confirmDelete(userId) {
            if (confirm("Вы уверены, что хотите удалить пользователя?")) {
                window.location.href = 'delete_user.php?user_id=' + userId;
            }
        }
    </script>
</head>
<body>
<h1>Страница администратора</h1>
<a href="mainpage.html" class="exit">Выйти</a>
<a href="stats.php" class="langbutton">Статистика ЯП</a>
<table>
    <tr>
        <th>ID</th>
        <th>Логин</th>
        <th>ФИО</th>
        <th>Телефон</th>
        <th>Email</th>
        <th>Дата рождения</th>
        <th>Пол</th>
        <th>О себе</th>
        <th>Любимые ЯП</th>
        <th>Действия</th>
    </tr>
    <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['user_id']); ?></td>
            <td><?php echo htmlspecialchars($user['login']); ?></td>
            <td><?php echo htmlspecialchars($user['fio']); ?></td>
            <td><?php echo htmlspecialchars($user['phone']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['dob']); ?></td>
            <td><?php echo htmlspecialchars($user['gender']); ?></td>
            <td><?php echo htmlspecialchars($user['bio']); ?></td>
            <td><?php echo htmlspecialchars($user['langs']); ?></td>
            <td>
                <a href="edit_user.php?user_id=<?php echo htmlspecialchars($user['user_id']); ?>">Редактировать</a>
                <button onclick="confirmDelete(<?php echo htmlspecialchars($user['user_id']); ?>)">Удалить</button>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>
