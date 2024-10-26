<?php
// index.php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p><a href="logout.php" class="logout-btn">Выйти</a></p>
        <p><a href="profile.php" class="logout-btn">Профиль</a></p>
        <p><a href="users.php" class="logout-btn">Пользователи</a></p>
        <p><a href="chats.php" class="logout-btn">Чаты</a></p>

    </div>
    <script src="scripts.js"></script>
</body>
</html>
