<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <title>Задание 5</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>

    <?php
    session_start(); //Запускаем сессию для проверки авторизации
    if (!isset($_SESSION['user_id'])) { // Если не авторизован
        ?>
        <form method="post">
            <label for="login">Логин:</label>
            <input type="text" id="login" name="login"><br>
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password"><br>
            <button type="submit">Войти</button>
        </form>
        <?php
    }
    else { ?>// Если авторизован
        
        <div id="hform">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div>
                    <label for="fio">ФИО:</label>
                    <input type="text" id="fio" name="fio" value="<?php echo get_cookie_value('fio'); ?>"
                        <?php if (isset($errors['fio'])): ?>class="error-field"<?php endif; ?>><br>
                    <?php if (isset($errors['fio'])): ?>
                        <span class="error"><?php echo $errors['fio']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="phone">Телефон:</label>
                    <input type="text" id="phone" name="phone" value="<?php echo get_cookie_value('phone'); ?>"
                        <?php if (isset($errors['phone'])): ?>class="error-field"<?php endif; ?>><br>
                    <?php if (isset($errors['phone'])): ?>
                        <span class="error"><?php echo $errors['phone']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" value="<?php echo get_cookie_value('email'); ?>"
                        <?php if (isset($errors['email'])): ?>class="error-field"<?php endif; ?>><br>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="dob">Дата рождения:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo get_cookie_value('dob'); ?>"
                        <?php if (isset($errors['dob'])): ?>class="error-field"<?php endif; ?>><br>
                    <?php if (isset($errors['dob'])): ?>
                        <span class="error"><?php echo $errors['dob']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Пол:</label>
                    <input type="radio" id="male" name="gender" value="male" <?php if (get_cookie_value('gender') == 'male') echo 'checked'; ?>
                        <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                    <label for="male">Мужской</label>
                    <input type="radio" id="female" name="gender" value="female" <?php if (get_cookie_value('gender') == 'female') echo 'checked'; ?>
                        <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                    <label for="female">Женский</label><br>
                    <?php if (isset($errors['gender'])): ?>
                        <span class="error"><?php echo $errors['gender']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label>Любимые языки программирования:</label><br>
                    
                    <?php
                    $languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                    $selected_languages = get_cookie_languages(); 
                    $error_class = isset($errors['languages']) ? 'error-field' : ''; ?>
                    <select name="languages[]" multiple required class="<?php echo $error_class; ?>">
                        <?php   
                        foreach ($languages as $language): ?>
                            <option value="<?php echo $language; ?>"
                                <?php if (in_array($language, $selected_languages)) echo ' selected'; ?>>
                                <?php echo $language; ?> </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['languages'])): ?>
                        <span class="error"><?php echo $errors['languages']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="bio">Биография:</label>
                    <textarea id="bio" name="bio" <?php if (isset($errors['bio'])): ?>class="error-field"<?php endif; ?>><?php echo get_cookie_value('bio'); ?></textarea><br>
                    <?php if (isset($errors['bio'])): ?>
                        <span class="error"><?php echo $errors['bio']; ?></span><br>
                    <?php endif; ?>
                </div>
                <div>
                    <br>
                    <input type="checkbox" id="agreement" name="agreement" <?php if (isset($_POST['agreement'])) echo 'checked'; ?>
                        <?php if (isset($errors['agreement'])): ?>class="error-field"<?php endif; ?>>
                    <label for="agreement">С контрактом ознакомлен(а)</label><br>
                    <?php if (isset($errors['agreement'])): ?>
                        <span class="error"><?php echo $errors['agreement']; ?></span><br>
                    <?php endif; ?>
                </div>
                <input type="submit" value="Сохранить">

            </form>
        </div>
    <?php
    } 
    ?>            
</body>
</html>
