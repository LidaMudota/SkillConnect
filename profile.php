<?php
session_start();
require_once 'config/db.php';

// Проверка, что пользователь вошел в систему
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

$connection = getDBConnection();

// Получаем информацию о пользователе
$stmt = $connection->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя</title>
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
                    <li><a href="auth/logout.php">LOGOUT</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="profile">
            <div class="container">
                <h2>Профиль пользователя: <?= htmlspecialchars($username) ?></h2>
                <p><strong>Роль:</strong> <?= htmlspecialchars($role) ?></p>

                <?php if ($role === 'client'): ?>
                    <h3>Создать новый заказ</h3>
                    <form action="create_order.php" method="post" class="form-section">
                        <label for="service_type">Тип услуги:</label>
                        <input type="text" id="service_type" name="service_type" required><br>
                        <label for="description">Описание:</label>
                        <textarea id="description" name="description" required></textarea><br>
                        <button type="submit" class="button primary">Создать заказ</button>
                    </form>

                    <h3>Завершенные заказы для оставления отзывов</h3>
                    <?php
                    $stmt = $connection->prepare("
                        SELECT * FROM orders 
                        WHERE client_id = ? 
                        AND status = 'completed' 
                        AND order_id NOT IN (SELECT order_id FROM reviews)
                    ");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $completed_orders_result = $stmt->get_result();

                    if ($completed_orders_result->num_rows > 0): ?>
                        <ul>
                            <?php while ($order = $completed_orders_result->fetch_assoc()): ?>
                                <li>
                                    <strong>Тип услуги:</strong> <?= htmlspecialchars($order['service_type']) ?><br>
                                    <strong>Описание:</strong> <?= htmlspecialchars($order['description']) ?><br>
                                    <form action="submit_review.php" method="post">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <input type="hidden" name="executor_id" value="<?= $order['executor_id'] ?>">
                                        <label for="rating">Оценка (1-5):</label>
                                        <input type="number" id="rating" name="rating" min="1" max="5" required><br>
                                        <label for="comment">Комментарий:</label>
                                        <textarea id="comment" name="comment" required></textarea><br>
                                        <button type="submit" class="button secondary">Оставить отзыв</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Нет завершенных заказов для оставления отзывов.</p>
                    <?php endif; ?>
                    <?php $stmt->close(); ?>

                <?php elseif ($role === 'executor'): ?>
                    <h3>Список доступных заказов</h3>
                    <?php
                    $stmt = $connection->prepare("SELECT * FROM orders WHERE status = 'pending'");
                    $stmt->execute();
                    $orders_result = $stmt->get_result();

                    if ($orders_result->num_rows > 0): ?>
                        <ul>
                            <?php while ($order = $orders_result->fetch_assoc()): ?>
                                <li>
                                    <strong>Тип услуги:</strong> <?= htmlspecialchars($order['service_type']) ?><br>
                                    <strong>Описание:</strong> <?= htmlspecialchars($order['description']) ?><br>
                                    <form action="take_order.php" method="post">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" class="button primary">Взять в работу</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Нет доступных заказов.</p>
                    <?php endif; ?>
                    <?php $stmt->close(); ?>

                    <h3>Ваши текущие заказы</h3>
                    <?php
                    $stmt = $connection->prepare("SELECT * FROM orders WHERE executor_id = ? AND status = 'in_progress'");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $in_progress_orders_result = $stmt->get_result();

                    if ($in_progress_orders_result->num_rows > 0): ?>
                        <ul>
                            <?php while ($order = $in_progress_orders_result->fetch_assoc()): ?>
                                <li>
                                    <strong>Тип услуги:</strong> <?= htmlspecialchars($order['service_type']) ?><br>
                                    <strong>Описание:</strong> <?= htmlspecialchars($order['description']) ?><br>
                                    <strong>Статус:</strong> В работе<br>
                                    <form action="complete_order.php" method="post">
                                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                        <button type="submit" class="button secondary">Завершить заказ</button>
                                    </form>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>У вас нет текущих заказов в работе.</p>
                    <?php endif; ?>
                    <?php $stmt->close(); ?>
                <?php endif; ?>

                <a href="index.php" class="button primary">На главную</a>
            </div>
        </section>
    </main>
</body>
</html>

<?php $connection->close(); ?>