<?php
session_start();
require_once 'config/db.php';

// Проверка, что пользователь авторизован и является исполнителем
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'executor') {
    header("Location: auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $executor_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];

    $connection = getDBConnection();

    // Обновляем заказ, меняя статус на "completed"
    $stmt = $connection->prepare("UPDATE orders SET status = 'completed' WHERE order_id = ? AND executor_id = ?");
    $stmt->bind_param("ii", $order_id, $executor_id);

    if ($stmt->execute()) {
        echo "Заказ успешно завершен!";
    } else {
        echo "Ошибка при завершении заказа: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
    header("Location: profile.php");
    exit;
}
?>
<!-- Updated layout and styles applied -->
