<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">

<?php

// Включаем файл с подключением к базе данных
require_once 'db.php';

// Проверка, является ли запрос на просмотр профиля пользователя
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Проверка сессии пользователя
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['username'])) {
    // Перенаправление на страницу входа
    header("Location: login.php");
}

// Если запрос на просмотр профиля пользователя
if ($id) {
    // Получение данных пользователя из базы данных
    $sql = "SELECT * FROM users WHERE id=:id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user['username'];
    // Вывод данных пользователя
    if ($user) {
        ?>

        <h1 align="center"><?php echo $user['username']; ?></h1>

        <p align="center">
            

        <img src="<?php echo (!empty($user['avatar']) ? 'avatar/'.$user['id'].'/'.$user['avatar'] : 'avatar/default.png'); ?>" class="avatar">
            
        
        </p>




        <div class="container1">
        <p>ID: <?php echo $user['id']; ?></p>
            
        </div>
        <br>
        <div class="container1">
        <p>Логин: <?php echo $user['username']; ?></p>
            
        </div>
        <br>
        <p><a href="users.php" class="logout-btn">Пользователи</a></p>
        <p><a href="index.php" class="logout-btn">Главная</a></p>

        <?php
        // Проверка, есть ли уже чат с этим пользователем
        $current_user = $_SESSION['user_id'];
        $sql = "SELECT * FROM chats WHERE (user1=:user1 AND user2=:user2) OR (user1=:user2 AND user2=:user1)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user1', $current_user);
        $stmt->bindParam(':user2', $id);
        $stmt->execute();
        $chat_exists = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($chat_exists) {
            // Чат уже существует, ссылка на него
            ?>
            <a class="logout-btn1" href="chats.php?chat_id=<?php echo $chat_exists['username']; ?>">Перейти в чат</a>
            <?php
        } else {
            // Нет чата, кнопка для создания чата
            ?>
            <a class="logout-btn1" href="chats.php?create_chat=true&user1=<?php echo $current_user; ?>&user2=<?php echo $id; ?>">Создать чат</a>
            <?php
        }
        ?>

        <?php
    } else {
        echo "Пользователь не найден.";
    }
} else {
    // Получение списка всех пользователей
    $sql = "SELECT * FROM users";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Вывод списка пользователей
    if ($users) {
        ?>
        <p><a href="index.php" class="logout-btn">Главная</a></p>

        <h2>Список пользователей</h2>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <div class="contanier">
                        <a href="users.php?id=<?php echo $user['id']; ?>">
                             <img src="<?php echo (!empty($user['avatar']) ? 'avatar/'.$user['id'].'/'.$user['avatar'] : 'avatar/default.png'); ?>" class="avatar-chat"> <?php echo "          " . $user['username']; ?>

                        </a>
                    </div>

                </li>
            <?php endforeach; ?>
        </ul>
        <?php
    } else {
        echo "Пользователей не найдено.";
    }
}

?>
</div>
</body>
</html>
