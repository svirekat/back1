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
// Функция валидации
function validate_form($data) {
    $errors = []; 
    $fio = sanitize($data['fio']);
    if (!preg_match("/^[a-zA-Zа-яА-Я\s]+$/u", $fio)) {
        $errors['fio'] = "ФИО должно содержать только буквы и пробелы.";
    }
    if (strlen($fio) > 150) {
        $errors['fio'] = "ФИО не должно превышать 150 символов.";
    }
    $phone = sanitize($data['phone']);
    if (!preg_match("/^[0-9\+\-\(\)\s]+$/", $phone)) {
        $errors['phone'] = "Некорректный формат телефона.";
    }
    $email = sanitize($data['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный формат email.";
    }
    $dob = sanitize($data['dob']);
    if (empty($dob)) {
        $errors['dob'] = "Дата рождения обязательна для заполнения.";
    }
    $gender = sanitize($data['gender']);
    if (!in_array($gender, ['male', 'female'])) {
        $errors['gender'] = "Некорректное значение пола.";
    }
    $languages = isset($data['languages']) ? $data['languages'] : [];
    $allowed_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
    if (is_array($languages)) {
      foreach ($languages as $language) {
          if (!in_array($language, $allowed_languages)) {
              $errors['languages'] = "Недопустимый язык программирования.";
              break; 
          }
      }
    }
    $bio = sanitize($data['bio']);
    if (empty($bio)) {
        $errors['bio'] = "Биография обязательна для заполнения.";
    }
    if (!isset($data['agreement'])) {
        $errors['agreement'] = "Необходимо согласиться с условиями.";
    }
    setcookie('fio', $fio, time() + 365 * 24 * 60 * 60); 
    setcookie('phone', $phone, time() + 365 * 24 * 60 * 60);
    setcookie('email', $email, time() + 365 * 24 * 60 * 60);
    setcookie('dob', $dob, time() + 365 * 24 * 60 * 60);
    setcookie('gender', $gender, time() + 365 * 24 * 60 * 60);
    setcookie('bio', $bio, time() + 365 * 24 * 60 * 60);
    setcookie('languages', serialize($languages), time() + 365 * 24 * 60 * 60);
  
    return $errors;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validate_form($_POST);
    if (empty($errors)) {
        try {
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

            // Удаление cookies с ошибками (если были)
            setcookie('fio', '', time() - 3600);
            setcookie('phone', '', time() - 3600);
            setcookie('email', '', time() - 3600);
            setcookie('dob', '', time() - 3600);
            setcookie('gender', '', time() - 3600);
            setcookie('bio', '', time() - 3600);
            setcookie('languages', '', time() - 3600);
            setcookie('errors', '', time() - 3600);

            echo "<body style='
                margin-top: 20px;
                font-family: sans-serif;
                background-color: #e6e6fa;
                display: flex;
                flex-direction: column;
                align-items: center;
                background-image: url('fon.jpg');
                background-repeat: repeat;
                background-size: 40%;>";
            echo "<div id='greentext'>
                <p style='color:green; font-size: larger;'> Данные успешно обновлены!</p>
                </div>";
            echo "<div> <a style='
                text-decoration: none;
                color: #581573;
                font-size: medium;'
                href='mainpage.html'>Вернуться на главную страницу</a> </div>";
            echo "</body>";
            
            exit();
        } 
        catch (PDOException $e) {
            die("<p style='color:red;'>Ошибка сохранения данных: " . $e->getMessage() . "</p>");
        }
    }
    else {
        // Сохраняем ошибки в cookie
        setcookie('errors', serialize($errors), time() + 3600);  // Cookie на сессию
        // Перезагружаем страницу методом GET
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
} 
else {
    // Если форма не была отправлена, перенаправление на edit.php
    header("Location: edit.php");
    exit();
}
?>
