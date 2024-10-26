<?php
// db.php

$databaseFile = 'users.db'; // Файл базы данных пользователей

try {
    // Создаем подключение к базе данных SQLite
    $db = new PDO("sqlite:$databaseFile");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создаем таблицу пользователей, если она не существует
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            login TEXT,
            discription TEXT,
            avatar TEXT
        )
    ");

    // Создаем таблицу чатов, если она не существует
    $db->exec("
        CREATE TABLE IF NOT EXISTS chats (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user1 TEXT NOT NULL,
            user2 TEXT NOT NULL
        )
    ");

    // Создаем таблицу сообщений, если она не существует
    $db->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            chat_id INTEGER NOT NULL,
            username TEXT NOT NULL,
            content TEXT NOT NULL,
            timestamp INTEGER NOT NULL
        )
    ");
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
    die();
}

?>
