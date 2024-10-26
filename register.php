<?php
// register.php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Вставляем нового пользователя в базу данных
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");

    try {
        $stmt->execute([':username' => $username, ':password' => $password]);
        $_SESSION['message'] = "Регистрация прошла успешно!";
        header("Location: login.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <form method="POST" id="registration-form">
            <input type="text" name="username" placeholder="Логин" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <p><a href="login.php">Уже есть аккаунт? Войти</a></p>
    </div>
    <script src="scripts.js"></script>
</body>
</html>
