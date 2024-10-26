<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<p><a href="users.php" class="logout-btn">Пользователи</a></p>
<p><a href="index.php" class="logout-btn">Главная</a></p>

<?php

require_once 'db.php';

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}
$create_chat = isset($_GET['create_chat']);
$user1 = isset($_GET['user1']) ? $_GET['user1'] : null;
$user2 = isset($_GET['user2']) ? $_GET['user2'] : null;

$chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : null;

$current_user = $_SESSION['user_id'];

$sql = "SELECT * FROM chats WHERE user1=:user1 OR user2=:user2";
$stmt = $db->prepare($sql);
$stmt->bindParam(':user1', $current_user);
$stmt->bindParam(':user2', $current_user);
$stmt->execute();
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($chats) {
    ?>
    <main>
        <section id="chat-list">
            <h2>Ваши чаты</h2>
    <ul>
        <?php foreach ($chats as $chat): ?>
            <li>
                <a href="chats.php?chat_id=<?php echo $chat['id']; ?>">
                    <?php 

                            $id = ($chat['user1'] == $current_user) ? $chat['user2'] : $chat['user1'];

                            $sql = "SELECT * FROM users WHERE id=:id";
                            $stmt = $db->prepare($sql);
                            $stmt->bindParam(':id', $id);
                            $stmt->execute();
                            $user = $stmt->fetch(PDO::FETCH_ASSOC);

                    ?>
                     <img src="<?php echo (!empty($user['avatar']) ? 'avatar/'.$user['id'].'/'.$user['avatar'] : 'avatar/default.png'); ?>" class="avatar-chat"> <?php echo "          " . $user['username']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    </section>
    <?php
} else {
    echo "У вас нет чатов.";
}


if ($create_chat) {
    $sql = "INSERT INTO chats (user1, user2) VALUES (:user1, :user2)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':user1', $user1);
    $stmt->bindParam(':user2', $user2);
    $stmt->execute();

    $chat_id = $db->lastInsertId();

    header("Location: chats.php?chat_id=$chat_id");
} elseif ($chat_id) {
    $sql = "SELECT * FROM chats WHERE id=:chat_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->execute();
    $chat = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT * FROM messages WHERE chat_id=:chat_id ORDER BY timestamp ASC";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($chat) {
        ?>
            <section id="chat-content">
                <h2>Чат с <?php echo $user['username']; ?></h2>
                <div id="chat-messages">
                    <?php foreach ($messages as $message): ?>
                        <div class="message <?php echo ($message['username'] == $_SESSION['username']) ? 'sent' : 'received'; ?>">
                            <span class="username"><?php echo $message['username']; ?></span>: <br>
                            <span class="content"><?php echo $message['content']; ?></span> <br>
                            <span class="timestamp"><?php echo date('H:i:s', $message['timestamp']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form id="chat-form" method="post">
                    <input type="hidden" name="chat_id" value="<?php echo $chat_id; ?>">
                    <input type="text" name="message" placeholder="Введите сообщение...">
                    <button type="submit">Отправить</button>
                </form>
            </section>
        <?php
    } else {
        echo "Чат не найден.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chat_id = $_POST['chat_id'];
    $message = $_POST['message'];
    $username = $_SESSION['username'];

    $sql = "INSERT INTO messages (chat_id, username, content, timestamp) VALUES (:chat_id, :username, :content, :timestamp)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':content', $message);
    $stmt->bindParam(':timestamp', time());
    $stmt->execute();

    header("Location: chats.php?chat_id=$chat_id");
}

?>
    <script src="script.js"></script>
</body>
</html>