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

    // Обновляем заказ, назначая его текущему исполнителю и меняя статус на "in_progress"
    $stmt = $connection->prepare("UPDATE orders SET executor_id = ?, status = 'in_progress' WHERE order_id = ?");
    $stmt->bind_param("ii", $executor_id, $order_id);

    if ($stmt->execute()) {
        echo "Заказ успешно взят в работу!";
    } else {
        echo "Ошибка при попытке взять заказ: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
    header("Location: profile.php");
    exit;
}
?>
<!-- Updated layout and styles applied -->
