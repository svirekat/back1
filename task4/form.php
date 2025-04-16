<?
header('Content-Type: text/html; charset=UTF-8');

// Функция очистки данных
function sanitize($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Функция валидации формы
function validate_form($data) {
    $errors = [];
    // Валидация ФИО
    $fio = sanitize($data['fio']);
    if (!preg_match("/^[a-zA-Zа-яА-Я\s]+$/u", $fio)) {
        $errors['fio'] = "ФИО должно содержать только буквы и пробелы.";
    }
    if (strlen($fio) > 150) {
        $errors['fio'] = "ФИО не должно превышать 150 символов.";
    }
    // Валидация телефона
    $phone = sanitize($data['phone']);
    if (!preg_match("/^[0-9\+\-\(\)\s]+$/", $phone)) {
        $errors['phone'] = "Некорректный формат телефона.";
    }
    // Валидация email
    $email = sanitize($data['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный формат email.";
    }
    // Валидация даты
    $dob = sanitize($data['dob']);
    if (empty($dob)) {
        $errors['dob'] = "Дата рождения обязательна для заполнения.";
    }
    // Validate пола
    $gender = sanitize($data['gender']);
    if (!in_array($gender, ['male', 'female'])) {
        $errors['gender'] = "Некорректное значение пола.";
    }
    // Валидация ЯП
    $languages = isset($data['languages']) ? $data['languages'] : [];
    $allowed_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
    if (!empty($languages)) { // Проверяем, что массив $languages не пустой
        foreach ($languages as $language) {
            if (!in_array($language, $allowed_languages)) {
                $errors['languages'] = "Недопустимый язык программирования.";
                break;
            }
        }
    }
    // Валидация биографии
    $bio = sanitize($data['bio']);
    if (empty($bio)) {
        $errors['bio'] = "Биография обязательна для заполнения.";
    }
    // Валидация чекбокса
    if (!isset($data['agreement'])) {
        $errors['agreement'] = "Необходимо согласиться с условиями.";
    }

    // Сохраняем данные в cookies для отображения при ошибках
    setcookie('fio', $fio, time() + 365 * 24 * 60 * 60); // На год
    setcookie('phone', $phone, time() + 365 * 24 * 60 * 60);
    setcookie('email', $email, time() + 365 * 24 * 60 * 60);
    setcookie('dob', $dob, time() + 365 * 24 * 60 * 60);
    setcookie('gender', $gender, time() + 365 * 24 * 60 * 60);
    setcookie('bio', $bio, time() + 365 * 24 * 60 * 60);
    setcookie('languages', serialize($languages), time() + 365 * 24 * 60 * 60); //сериализация массива для хранения в cookie

    return $errors;
}

$user = 'u68857';
$password = '9940611';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u68857', $user, $password,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("<p style='color:red;'>Ошибка подключения к базе данных: " . $e->getMessage() . "</p>");
}

// Обработка POST-запроса
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validate_form($_POST);

    if (empty($errors)) {
        try {
            $fio = sanitize($_POST['fio']);
            $phone = sanitize($_POST['phone']);
            $email = sanitize($_POST['email']);
            $dob = sanitize($_POST['dob']);
            $gender = sanitize($_POST['gender']);
            $bio = sanitize($_POST['bio']);

            $stmt = $pdo->prepare("INSERT INTO users (fio, phone, email, dob, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fio, $phone, $email, $dob, $gender, $bio]);
            $user_id = $pdo->lastInsertId();

            $languages = $_POST['languages'];
            foreach ($languages as $language) {
                $stmt_lang = $pdo->prepare("SELECT lang_id FROM langs WHERE lang_name = ?");
                $stmt_lang->execute([$language]);
                $lang_result = $stmt_lang->fetch(PDO::FETCH_ASSOC);
                $lang_id = $lang_result['lang_id'];
                $stmt_user_lang = $pdo->prepare("INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)");
                $stmt_user_lang->execute([$user_id, $lang_id]);
            }

            // Удаление cookies с ошибками (если были)
            setcookie('fio', '', time() - 3600);
            setcookie('phone', '', time() - 3600);
            setcookie('email', '', time() - 3600);
            setcookie('dob', '', time() - 3600);
            setcookie('gender', '', time() - 3600);
            setcookie('bio', '', time() - 3600);
            setcookie('languages', '', time() - 3600);
            setcookie('errors', '', time() - 3600);

            // Перенаправление на GET с параметром success
            header("Location: ".$_SERVER['PHP_SELF']."?success=1");
            exit();

        } catch (PDOException $e) {
            die("<p style='color:red;'>Ошибка сохранения данных: " . $e->getMessage() . "</p>");
        }
    } else {
        // Сохраняем ошибки в cookie
        setcookie('errors', serialize($errors), time() + 3600);  // Cookie на сессию
        // Перезагружаем страницу методом GET
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Функция для получения значения из Cookie (с проверкой на существование)
function get_cookie_value($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
}

// Функция для получения значения из Cookie для языков (с десериализацией)
function get_cookie_languages() {
    return isset($_COOKIE['languages']) ? unserialize($_COOKIE['languages']) : [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Задание 3</title>
  <link rel="stylesheet" href="styles.css">
  <style>
        .error {
            color: red;
        }
        .error-field {
            border: 1px solid red;
        }
    </style>
  </head>
  <body>
    <div id="hform">
        <?php
            // Вывод сообщений об успехе
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo "<p style='color:green;'>Данные успешно сохранены!</p>";
            }

            // Вывод сообщений об ошибках
            if (isset($_COOKIE['errors'])) {
                $errors = unserialize($_COOKIE['errors']);
                echo "<div class='error'>";
                foreach ($errors as $key => $value) {
                    echo "<p>$value</p>";
                }
                echo "</div>";
                // Удаляем cookie с ошибками сразу после отображения
                setcookie('errors', '', time() - 3600);
            } else {
                $errors = [];
            }
        ?>
        <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div>
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" value="<?php echo get_cookie_value('fio'); ?>" required
                <?php if (isset($errors['fio'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="fio_error"><?php if (isset($errors['fio'])): ?><?php echo $errors['fio']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo get_cookie_value('phone'); ?>" required
                <?php if (isset($errors['phone'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="phone_error"><?php if (isset($errors['phone'])): ?><?php echo $errors['phone']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo get_cookie_value('email'); ?>" required
                <?php if (isset($errors['email'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="email_error"><?php if (isset($errors['email'])): ?><?php echo $errors['email']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="dob">Дата рождения:</label>
                <input type="date" id="dob" name="dob" value="<?php echo get_cookie_value('dob'); ?>" required
                <?php if (isset($errors['dob'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="dob_error"><?php if (isset($errors['dob'])): ?><?php echo $errors['dob']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label>Пол:</label>
                <input type="radio" id="male" name="gender" value="male" required <?php if (get_cookie_value('gender') == 'male') echo 'checked'; ?>
                <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="male">Мужской</label>
                <input type="radio" id="female" name="gender" value="female" required <?php if (get_cookie_value('gender') == 'female') echo 'checked'; ?>
                <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="female">Женский</label>
                <span class="error" id="gender_error"><?php if (isset($errors['gender'])): ?><?php echo $errors['gender']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label>Любимые языки программирования:</label><br>
                <select name="languages[]" multiple required
                <?php if (isset($errors['languages'])): ?>class="error-field"<?php endif; ?>>
                <?php
                $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                $selected_languages = get_cookie_languages();
                foreach ($languages as $language): ?>
                    <option value="<?php echo $language; ?>" <?php if (in_array($language, $selected_languages)) echo 'selected'; ?>><?php echo $language; ?></option>
                <?php endforeach; ?>
                </select>
                <span class="error" id="languages_error"><?php if (isset($errors['languages'])): ?><?php echo $errors['languages']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="bio">Биография:</label>
                <textarea id="bio" name="bio" rows="4" cols="50" required
                <?php if (isset($errors['bio'])): ?>class="error-field"<?php endif; ?>><?php echo get_cookie_value('bio'); ?></textarea>
                <span class="error" id="bio_error"><?php if (isset($errors['bio'])): ?><?php echo $errors['bio']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <input type="checkbox" id="agreement" name="agreement" required <?php if (isset($_POST['agreement'])) echo 'checked'; ?>
                <?php if (isset($errors['agreement'])): ?>class="error-field"<?php endif; ?>>
                <label for="agreement">С контрактом ознакомлен(а)</label>
                <span class="error" id="agreement_error"><?php if (isset($errors['agreement'])): ?><?php echo $errors['agreement']; ?><?php endif; ?></span>
            </div>
        
            <button type="submit">Сохранить</button>
        </form>
      </div>
  </body>
</html>
<?
header('Content-Type: text/html; charset=UTF-8');

// Функция очистки данных
function sanitize($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Функция валидации формы
function validate_form($data) {
    $errors = [];
    // Валидация ФИО
    $fio = sanitize($data['fio']);
    if (!preg_match("/^[a-zA-Zа-яА-Я\s]+$/u", $fio)) {
        $errors['fio'] = "ФИО должно содержать только буквы и пробелы.";
    }
    if (strlen($fio) > 150) {
        $errors['fio'] = "ФИО не должно превышать 150 символов.";
    }
    // Валидация телефона
    $phone = sanitize($data['phone']);
    if (!preg_match("/^[0-9\+\-\(\)\s]+$/", $phone)) {
        $errors['phone'] = "Некорректный формат телефона.";
    }
    // Валидация email
    $email = sanitize($data['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Некорректный формат email.";
    }
    // Валидация даты
    $dob = sanitize($data['dob']);
    if (empty($dob)) {
        $errors['dob'] = "Дата рождения обязательна для заполнения.";
    }
    // Validate пола
    $gender = sanitize($data['gender']);
    if (!in_array($gender, ['male', 'female'])) {
        $errors['gender'] = "Некорректное значение пола.";
    }
    // Валидация ЯП
    $languages = isset($data['languages']) ? $data['languages'] : [];
    $allowed_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
    if (!empty($languages)) { // Проверяем, что массив $languages не пустой
        foreach ($languages as $language) {
            if (!in_array($language, $allowed_languages)) {
                $errors['languages'] = "Недопустимый язык программирования.";
                break;
            }
        }
    }
    // Валидация биографии
    $bio = sanitize($data['bio']);
    if (empty($bio)) {
        $errors['bio'] = "Биография обязательна для заполнения.";
    }
    // Валидация чекбокса
    if (!isset($data['agreement'])) {
        $errors['agreement'] = "Необходимо согласиться с условиями.";
    }

    // Сохраняем данные в cookies для отображения при ошибках
    setcookie('fio', $fio, time() + 365 * 24 * 60 * 60); // На год
    setcookie('phone', $phone, time() + 365 * 24 * 60 * 60);
    setcookie('email', $email, time() + 365 * 24 * 60 * 60);
    setcookie('dob', $dob, time() + 365 * 24 * 60 * 60);
    setcookie('gender', $gender, time() + 365 * 24 * 60 * 60);
    setcookie('bio', $bio, time() + 365 * 24 * 60 * 60);
    setcookie('languages', serialize($languages), time() + 365 * 24 * 60 * 60); //сериализация массива для хранения в cookie

    return $errors;
}

$user = 'u68857';
$password = '9940611';

try {
    $pdo = new PDO('mysql:host=localhost;dbname=u68857', $user, $password,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("<p style='color:red;'>Ошибка подключения к базе данных: " . $e->getMessage() . "</p>");
}

// Обработка POST-запроса
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = validate_form($_POST);

    if (empty($errors)) {
        try {
            $fio = sanitize($_POST['fio']);
            $phone = sanitize($_POST['phone']);
            $email = sanitize($_POST['email']);
            $dob = sanitize($_POST['dob']);
            $gender = sanitize($_POST['gender']);
            $bio = sanitize($_POST['bio']);

            $stmt = $pdo->prepare("INSERT INTO users (fio, phone, email, dob, gender, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fio, $phone, $email, $dob, $gender, $bio]);
            $user_id = $pdo->lastInsertId();

            $languages = $_POST['languages'];
            foreach ($languages as $language) {
                $stmt_lang = $pdo->prepare("SELECT lang_id FROM langs WHERE lang_name = ?");
                $stmt_lang->execute([$language]);
                $lang_result = $stmt_lang->fetch(PDO::FETCH_ASSOC);
                $lang_id = $lang_result['lang_id'];
                $stmt_user_lang = $pdo->prepare("INSERT INTO users_languages (user_id, lang_id) VALUES (?, ?)");
                $stmt_user_lang->execute([$user_id, $lang_id]);
            }

            // Удаление cookies с ошибками (если были)
            setcookie('fio', '', time() - 3600);
            setcookie('phone', '', time() - 3600);
            setcookie('email', '', time() - 3600);
            setcookie('dob', '', time() - 3600);
            setcookie('gender', '', time() - 3600);
            setcookie('bio', '', time() - 3600);
            setcookie('languages', '', time() - 3600);
            setcookie('errors', '', time() - 3600);

            // Перенаправление на GET с параметром success
            header("Location: ".$_SERVER['PHP_SELF']."?success=1");
            exit();

        } catch (PDOException $e) {
            die("<p style='color:red;'>Ошибка сохранения данных: " . $e->getMessage() . "</p>");
        }
    } else {
        // Сохраняем ошибки в cookie
        setcookie('errors', serialize($errors), time() + 3600);  // Cookie на сессию
        // Перезагружаем страницу методом GET
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Функция для получения значения из Cookie (с проверкой на существование)
function get_cookie_value($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
}

// Функция для получения значения из Cookie для языков (с десериализацией)
function get_cookie_languages() {
    return isset($_COOKIE['languages']) ? unserialize($_COOKIE['languages']) : [];
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Задание 3</title>
  <link rel="stylesheet" href="styles.css">
  <style>
        .error {
            color: red;
        }
        .error-field {
            border: 1px solid red;
        }
    </style>
  </head>
  <body>
    <div id="hform">
        <?php
            // Вывод сообщений об успехе
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo "<p style='color:green;'>Данные успешно сохранены!</p>";
            }

            // Вывод сообщений об ошибках
            if (isset($_COOKIE['errors'])) {
                $errors = unserialize($_COOKIE['errors']);
                echo "<div class='error'>";
                foreach ($errors as $key => $value) {
                    echo "<p>$value</p>";
                }
                echo "</div>";
                // Удаляем cookie с ошибками сразу после отображения
                setcookie('errors', '', time() - 3600);
            } else {
                $errors = [];
            }
        ?>
        <form id="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div>
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" value="<?php echo get_cookie_value('fio'); ?>" required
                <?php if (isset($errors['fio'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="fio_error"><?php if (isset($errors['fio'])): ?><?php echo $errors['fio']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo get_cookie_value('phone'); ?>" required
                <?php if (isset($errors['phone'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="phone_error"><?php if (isset($errors['phone'])): ?><?php echo $errors['phone']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo get_cookie_value('email'); ?>" required
                <?php if (isset($errors['email'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="email_error"><?php if (isset($errors['email'])): ?><?php echo $errors['email']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="dob">Дата рождения:</label>
                <input type="date" id="dob" name="dob" value="<?php echo get_cookie_value('dob'); ?>" required
                <?php if (isset($errors['dob'])): ?>class="error-field"<?php endif; ?>>
                <span class="error" id="dob_error"><?php if (isset($errors['dob'])): ?><?php echo $errors['dob']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label>Пол:</label>
                <input type="radio" id="male" name="gender" value="male" required <?php if (get_cookie_value('gender') == 'male') echo 'checked'; ?>
                <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="male">Мужской</label>
                <input type="radio" id="female" name="gender" value="female" required <?php if (get_cookie_value('gender') == 'female') echo 'checked'; ?>
                <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="female">Женский</label>
                <span class="error" id="gender_error"><?php if (isset($errors['gender'])): ?><?php echo $errors['gender']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label>Любимые языки программирования:</label><br>
                <select name="languages[]" multiple required
                <?php if (isset($errors['languages'])): ?>class="error-field"<?php endif; ?>>
                <?php
                $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                $selected_languages = get_cookie_languages();
                foreach ($languages as $language): ?>
                    <option value="<?php echo $language; ?>" <?php if (in_array($language, $selected_languages)) echo 'selected'; ?>><?php echo $language; ?></option>
                <?php endforeach; ?>
                </select>
                <span class="error" id="languages_error"><?php if (isset($errors['languages'])): ?><?php echo $errors['languages']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <label for="bio">Биография:</label>
                <textarea id="bio" name="bio" rows="4" cols="50" required
                <?php if (isset($errors['bio'])): ?>class="error-field"<?php endif; ?>><?php echo get_cookie_value('bio'); ?></textarea>
                <span class="error" id="bio_error"><?php if (isset($errors['bio'])): ?><?php echo $errors['bio']; ?><?php endif; ?></span>
            </div>
        
            <div>
                <input type="checkbox" id="agreement" name="agreement" required <?php if (isset($_POST['agreement'])) echo 'checked'; ?>
                <?php if (isset($errors['agreement'])): ?>class="error-field"<?php endif; ?>>
                <label for="agreement">С контрактом ознакомлен(а)</label>
                <span class="error" id="agreement_error"><?php if (isset($errors['agreement'])): ?><?php echo $errors['agreement']; ?><?php endif; ?></span>
            </div>
        
            <button type="submit">Сохранить</button>
        </form>
      </div>
  </body>
</html>
