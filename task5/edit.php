<?php
session_start(); // Запускаем сессию
// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Если не авторизован, перенаправляем на вход
    exit();
}
// Здесь код для получения данных пользователя из базы данных и их отображения
// Например:
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// Отображение данных пользователя и формы для их редактирования
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<form id="form" action="update.php" method="POST">
            <div>
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" required>
                <span class="error" id="fio_error"></span>
            </div>
        
            <div>
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" required>
                <span class="error" id="phone_error"></span>
            </div>
        
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
                <span class="error" id="email_error"></span>
            </div>
        
            <div>
                <label for="dob">Дата рождения:</label>
                <input type="date" id="dob" name="dob" required>
                <span class="error" id="dob_error"></span>
            </div>
        
            <div>
                <label>Пол:</label>
                <input type="radio" id="male" name="gender" value="male" required>
                <label for="male">Мужской</label>
                <input type="radio" id="female" name="gender" value="female" required>
                <label for="female">Женский</label>
                <span class="error" id="gender_error"></span>
            </div>
        
            <div>
                <label>Любимые языки программирования:</label><br>
                <select name="languages[]" multiple required>
                    <option value="Pascal">Pascal</option>
                    <option value="C">C</option>
                    <option value="C++">C++</option>
                    <option value="JavaScript">JavaScript</option>
                    <option value="PHP">PHP</option>
                    <option value="Python">Python</option>
                    <option value="Java">Java</option>
                    <option value="Haskell">Haskell</option>
                    <option value="Clojure">Clojure</option>
                    <option value="Prolog">Prolog</option>
                    <option value="Scala">Scala</option>
                    <option value="Go">Go</option>
                </select>
                <span class="error" id="languages_error"></span>
            </div>
        
            <div>
                <label for="bio">Биография:</label>
                <textarea id="bio" name="bio" rows="4" cols="50" required></textarea>
                <span class="error" id="bio_error"></span>
            </div>
        
            <div>
                <input type="checkbox" id="agreement" name="agreement" required>
                <label for="agreement">С контрактом ознакомлен(а)</label>
                <span class="error" id="agreement_error"></span>
            </div>
        
            <button type="submit">Сохранить</button>
        </form>
</body>
</html>