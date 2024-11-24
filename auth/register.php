<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $connection = getDBConnection();

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $connection->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password_hash, $role);

    if ($stmt->execute()) {
        echo "Регистрация успешна! Можете войти на сайт.";
    } else {
        echo "Ошибка регистрации: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <form action="register.php" method="post">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="role">Роль:</label>
        <select id="role" name="role">
            <option value="client">Клиент</option>
            <option value="executor">Исполнитель</option>
        </select><br>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <a href="../index.php"><button>На главную</button></a>
</body>
</html>