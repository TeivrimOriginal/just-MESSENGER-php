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
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db_file = 'users.db';
$conn = new PDO("sqlite:$db_file");

if (!$conn) {
    die("Ошибка подключения к базе данных: " . $conn->errorInfo());
}


$edit = isset($_GET['edit']);
$errors = isset($_GET['error']);
$upload_avatar = isset($_POST['upload_avatar']);
$id = $_SESSION['user_id'];
if ($edit) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $password = $_POST['password'];
        $login = $_POST['login'];
        $discription = $_POST['discription'];
        $username = $_POST['username'];

        if(empty($login) || empty($password)) {
            echo "ОШИБКА - Пороль или логин пустые!";
            header("Location: profile.php?error");

        } 
        else 
        {
            $sql = "UPDATE users SET password=:password, login=:login, discription=:discription, username=:username WHERE id=:id ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':login', $login);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':discription', $discription);
            $stmt->execute();

        }
        header("Location: profile.php");

        
    } else {

        ?>
        <h2>Редактирование профиля</h2>
        <?php 
        
        $sql = "SELECT * FROM users WHERE id=:id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        ?>
        <form method="post">

            <label for="username">Имя:</label>
            <input type="text" name="username" id="username" value="<?php echo $user['username']; ?>">

            <label for="login">Логин:</label>
            <input type="text" name="login" id="login" value="<?php echo $user['login']; ?>">

            <label for="password">Пароль:</label>
            <input type="password" name="password" id="password" value="<?php echo $user['password']; ?>">

            <label for="discription">Описание:</label>
            <input type="text" name="discription" id="discription" value="<?php echo $user['discription']; ?>">

            <button type="submit">Сохранить изменения</button>
        </form>
        <?php
    }
} else {

    $sql = "SELECT * FROM users WHERE id=:id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        ?>
        <h2>Ваш профиль</h2>
        <p align="center"> 
        <img src="<?php echo (!empty($user['avatar']) ? 'avatar/'.$user['id'].'/'.$user['avatar'] : 'avatar/default.png'); ?>" class="avatar">

        </p>
        <p>Имя: <?php echo $user['username']; ?></p>
        <p>Логин: <?php echo $user['login']; ?></p>
        <p>Пароль: <?php echo $user['password']; ?></p>
        <p>Описание: <?php echo $user['discription']; ?></p>
        <?php 
        
        if($errors) {?><p> Пороль или логин оказался пустым, пожалуйста повторите редактирование профиля.</p><?php } 
        
        ?>
        <p><a href="profile.php?edit" class="logout-btn">Редактировать профиль</a></p>
        <p><a href="index.php" class="logout-btn">Главная</a></p>
        <p>Айди: <?php echo $user['id']; ?></p>

        <?php 
        if ($upload_avatar) {
            $target_dir = "avatar/".$user['id']."/";
            $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["avatar"]["tmp_name"]);
            if($check !== false) {
                echo "Файл - изображение - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "Файл не является изображением.";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                echo "Извините, файл уже существует.";
                $uploadOk = 0;
            }

            if ($_FILES["avatar"]["size"] > 500000) {
                echo "Извините, ваш файл слишком большой.";
                $uploadOk = 0;
            }

            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
                echo "Извините, разрешены только JPG, JPEG, PNG & GIF файлы.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                $files = glob($target_dir . '*'); // Получение всех файлов в папке
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); // Удаление файла
                    }
                }

                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    echo "Файл ". basename( $_FILES["avatar"]["name"]). " успешно загружен.";

                    // Сжимаем изображение с помощью GD Library
                    $image = imagecreatefromstring(file_get_contents($target_file));
                    $resizedImage = imagescale($image, 1000, 1000);
                    imagejpeg($resizedImage, $target_file);
                    imagedestroy($resizedImage);

                    // Обновление аватара в базе данных
                    $sql = "UPDATE users SET avatar=:avatar WHERE id=:id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':id', $id);
                    $stmt->bindParam(':avatar', basename( $_FILES["avatar"]["name"]));
                    $stmt->execute();

                    header("Location: profile.php");
                } else {
                    echo "Ошибка загрузки файла.";
                }
            }
        }
        ?>

        <h2>Загрузка аватара</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="avatar" id="avatar">
            <button type="submit" name="upload_avatar">Загрузить</button>
        </form>
        <?php
    } else {
        echo "Пользователь не найден.";
    }
}

$conn = null;

?>
<div>
</body>
</html>
