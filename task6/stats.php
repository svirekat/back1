<?php
session_start();
require_once 'db_connection.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Если не авторизован, перенаправляем на вход
    exit();
}

$stmt = $pdo->query("
    SELECT l.lang_name, COUNT(ul.user_id) as user_count
    FROM langs l
    LEFT JOIN users_languages ul ON l.lang_id = ul.lang_id
    GROUP BY l.lang_id
");
$lang_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];    //Массив для названий ЯП
$data = [];      //Массив для количества пользователей
foreach ($lang_stats as $row) {
    $labels[] = $row['lang_name'];
    $data[] = $row['user_count'];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика ЯП</title>
    //Подключение библиотеки Chart.js для построения диаграммы
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>   
    <style>
        canvas {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
    <h2>Статистика по языкам программирования</h2>
    <canvas id="langChart"></canvas>

    <script>
        const ctx = document.getElementById('langChart').getContext('2d');
        const langChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>, // Подписи языков
                datasets: [{
                    label: 'Количество пользователей',
                    data: <?= json_encode($data) ?>, // Данные
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <a href="adminpage.php">Назад к странице администратора</a>
</body>
</html>
