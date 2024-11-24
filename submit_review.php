<?php
session_start();
require_once 'config/db.php';

// Проверка, что пользователь авторизован и является клиентом
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['executor_id']) && isset($_POST['rating']) && isset($_POST['comment'])) {
    $client_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];
    $executor_id = $_POST['executor_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $connection = getDBConnection();

    // Добавление отзыва в базу данных
    $stmt = $connection->prepare("INSERT INTO reviews (client_id, executor_id, order_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $client_id, $executor_id, $order_id, $rating, $comment);

    if ($stmt->execute()) {
        echo "Отзыв успешно добавлен!";
    } else {
        echo "Ошибка при добавлении отзыва: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
    header("Location: profile.php");
    exit;
} else {
    echo "Некорректные данные для отправки отзыва.";
}
?>
<!-- Updated layout and styles applied -->
