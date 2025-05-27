<?php
session_start();
require_once 'db_connection.php';

// Проверка прав доступа
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    die("У вас нет прав для выполнения этого действия.");
}

// Проверка наличия user_id и его валидация
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];

    // Подготовка запроса на удаление
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    
    try {
        $stmt->execute([$user_id]);
        // Перенаправление на страницу администратора с сообщением об успехе
        header("Location: adminpage.php?message=Пользователь успешно удален.");
    } catch (PDOException $e) {
        // Обработка ошибок базы данных
        header("Location: adminpage.php?error=Ошибка при удалении пользователя.");
    }
} else {
    header("Location: adminpage.php?error=Неверный идентификатор пользователя.");
}

exit();
?>
