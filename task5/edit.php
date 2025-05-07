<?php
session_start(); 
require_once 'db_connection.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Если не авторизован, перенаправляем на вход
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt_user_langs = $pdo->prepare("SELECT lang_id FROM users_languages WHERE user_id = ?");
$stmt_user_langs->execute([$user_id]);
$user_langs_ids = $stmt_user_langs->fetchAll(PDO::FETCH_COLUMN); // Получаем массив lang_id

// Получаем соответствие lang_id => lang_name из таблицы langs
$stmt_langs = $pdo->query("SELECT lang_id, lang_name FROM langs");
$available_languages = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
$lang_map = []; // Ассоциативный массив: lang_id => lang_name
while ($lang = $stmt_langs->fetch(PDO::FETCH_ASSOC)) {
    $available_languages[] = $lang['lang_name'];
    $lang_map[$lang['lang_id']] = $lang['lang_name'];
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
<h2>Редактирование данных</h2>
<div id="hform">
<form id="form" action="update.php" method="POST">
            <div>
                <label for="fio">ФИО:</label>
                <input type="text" id="fio" name="fio" value="<?php echo htmlspecialchars($user['fio']); ?>"
                    <?php if (isset($errors['fio'])): ?>class="error-field"<?php endif; ?>><br>
                <?php if (isset($errors['fio'])): ?>
                    <span class="error"><?php echo $errors['fio']; ?></span><br>
                <?php endif; ?>
            </div>
            <div>
                <label for="phone">Телефон:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"
                    <?php if (isset($errors['phone'])): ?>class="error-field"<?php endif; ?>><br>
                <?php if (isset($errors['phone'])): ?>
                    <span class="error"><?php echo $errors['phone']; ?></span><br>
                <?php endif; ?>
            </div>
            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                    <?php if (isset($errors['email'])): ?>class="error-field"<?php endif; ?>><br>
                <?php if (isset($errors['email'])): ?>
                    <span class="error"><?php echo $errors['email']; ?></span><br>
                <?php endif; ?>
            </div>
            <div>
                <label for="dob">Дата рождения:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>"
                    <?php if (isset($errors['dob'])): ?>class="error-field"<?php endif; ?>><br>
                <?php if (isset($errors['dob'])): ?>
                    <span class="error"><?php echo $errors['dob']; ?></span><br>
                <?php endif; ?>
            </div>
            <div>
                <label>Пол:</label>
                <input type="radio" id="male" name="gender" value="male" <?php if (htmlspecialchars($user['gender']) == 'male') echo 'checked'; ?>
                    <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="male">Мужской</label>
                <input type="radio" id="female" name="gender" value="female" <?php if (htmlspecialchars($user['gender']) == 'female') echo 'checked'; ?>
                    <?php if (isset($errors['gender'])): ?>class="error-field"<?php endif; ?>>
                <label for="female">Женский</label><br>
                <?php if (isset($errors['gender'])): ?>
                    <span class="error"><?php echo $errors['gender']; ?></span><br>
                <?php endif; ?>
            </div>

            <div>
                <label for="bio">Биография:</label>
                <textarea id="bio" name="bio" <?php if (isset($errors['bio'])): ?>class="error-field"<?php endif; ?>><?php echo htmlspecialchars($user['bio']); ?></textarea><br>
                <?php if (isset($errors['bio'])): ?>
                    <span class="error"><?php echo $errors['bio']; ?></span><br>
                <?php endif; ?>
            </div>
            
            <div>
                <label>Языки программирования:</label><br>
                <select name="languages[]" id="languages" multiple required>
                    <?php
                    $stmt_user_langs = $pdo->prepare("SELECT lang_id FROM users_languages WHERE user_id = ?");
                    $stmt_user_langs->execute([$user_id]);
                    foreach ($lang_map as $lang_id => $lang_name):
                        $user_langs_ids = $stmt_user_langs->fetchAll(PDO::FETCH_COLUMN);
                        $selected = in_array($lang_id, $user_langs_ids) ? 'selected' : '';
                        ?>
                        <option value="<?php echo htmlspecialchars($lang_name); ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($lang_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
            <input id="sendbutton" type="submit" value="Сохранить">

        </form>
</div>
</body>
</html>
