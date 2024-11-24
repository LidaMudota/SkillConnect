<?php
session_start();
require_once 'config/db.php';

// Проверка, что пользователь авторизован и является клиентом
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $service_type = $_POST['service_type'];
    $description = $_POST['description'];

    $connection = getDBConnection();

    // Добавление заказа в базу данных
    $stmt = $connection->prepare("INSERT INTO orders (client_id, service_type, description, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iss", $client_id, $service_type, $description);

    if ($stmt->execute()) {
        echo "Заказ успешно создан!";
    } else {
        echo "Ошибка создания заказа: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заказа</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">SkillMatch</div>
            <nav>
                <ul class="header-navigation">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="search_executors.php">EXECUTORS</a></li>
                    <li><a href="profile.php">PROFILE</a></li>
                    <li><a href="auth/logout.php">LOGOUT</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="form-section">
            <div class="container">
                <h1>Create an Order</h1>
                <form action="create_order.php" method="POST">
                    <div class="input-group">
                        <label for="service_type">Service Type:</label>
                        <input type="text" id="service_type" name="service_type" required>
                    </div>
                    <div class="input-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="button primary">Submit Order</button>
                </form>
                <div class="button-group">
                    <a href="profile.php"><button class="button secondary">Back to Profile</button></a>
                    <a href="index.php"><button class="button secondary">Home</button></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 SkillMatch. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>