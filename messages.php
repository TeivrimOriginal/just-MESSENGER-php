<?php

// Включаем файл с подключением к базе данных
require_once 'db.php';

// Проверка сессии пользователя
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    // Возвращаем ошибку
    echo json_encode(['error' => 'Необходимо авторизоваться']);
    exit;
}

// Проверка, отправлено ли сообщение
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получение данных из запроса
    $data = json_decode(file_get_contents('php://input'), true);
    $chat_id = $data['chat_id'];
    $message = $data['message'];
    $user_id = $_SESSION['user_id']; // Используем ID текущего пользователя

    // Сохранение сообщения в базе данных
    $sql = "INSERT INTO messages (chat_id, user_id, content, timestamp) VALUES (:chat_id, :user_id, :content, :timestamp)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':content', $message);
    $stmt->bindParam(':timestamp', time());
    $stmt->execute();

    // Найдем имя пользователя по ID
    $sql = "SELECT username FROM users WHERE id=:user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $username = $stmt->fetch(PDO::FETCH_ASSOC)['username'];

    // Подготовка данных для JSON-ответа
    $response = [
        'username' => $username, // Используем имя пользователя из базы данных
        'message' => $message,
        'timestamp' => date('H:i:s', time())
    ];

    // Вывод JSON-ответа
    echo json_encode($response);
} else {
    // Возвращаем ошибку
    echo json_encode(['error' => 'Неверный запрос']);
}

?>
