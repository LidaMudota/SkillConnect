<?php
require_once 'config/db.php';

// Получаем соединение с базой данных
$db = getDBConnection();

$executor_id = $_GET['executor_id'] ?? null;

if (!$executor_id) {
    die("Не указан ID исполнителя.");
}

// Получаем данные об исполнителе
$query = $db->prepare("SELECT * FROM executors WHERE executor_id = ?");
$query->bind_param("i", $executor_id);
$query->execute();
$executor = $query->get_result()->fetch_assoc();

if (!$executor) {
    die("Исполнитель не найден.");
}

// Получаем отзывы исполнителя
$reviews_query = $db->prepare("SELECT r.*, u.username AS client_name FROM reviews r
    JOIN users u ON r.client_id = u.user_id
    WHERE r.executor_id = ?
    ORDER BY r.created_at DESC");
$reviews_query->bind_param("i", $executor_id);
$reviews_query->execute();
$reviews = $reviews_query->get_result();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль исполнителя</title>
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
        <section class="executor-profile">
            <div class="container">
                <h1>Профиль исполнителя</h1>
                <div class="profile-container">
                    <div class="profile-photo">
                        <?php echo $executor['photo'] ? "<img src='{$executor['photo']}' alt='Фото исполнителя'>" : "<div class='placeholder'>Фото отсутствует</div>"; ?>
                    </div>
                    <div class="profile-info">
                        <p><strong>Имя:</strong> <?php echo htmlspecialchars($executor['name']); ?></p>
                        <p><strong>Специализация:</strong> <?php echo htmlspecialchars($executor['specialization']); ?></p>
                        <p><strong>Рейтинг:</strong> <?php echo htmlspecialchars($executor['rating']); ?></p>
                        <p><strong>Количество отзывов:</strong> <?php echo htmlspecialchars($executor['reviews_count']); ?></p>
                        <p><strong>Опыт работы:</strong> <?php echo $executor['experience_years'] ? $executor['experience_years'] . " лет" : "Не указан"; ?></p>
                        <p><strong>О себе:</strong> <?php echo $executor['bio'] ?: "Нет информации"; ?></p>
                    </div>
                </div>

                <h2>Загрузить фото</h2>
                <form action="upload_photo.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="executor_id" value="<?php echo $executor_id; ?>">
                    <input type="file" name="photo" accept="image/*" required>
                    <button type="submit" class="button primary">Загрузить фото</button>
                </form>

                <h2>Отзывы</h2>
                <div class="reviews-section">
                    <?php if ($reviews->num_rows > 0): ?>
                        <?php while ($review = $reviews->fetch_assoc()): ?>
                            <div class="review">
                                <div class="review-header">
                                    Клиент: <?php echo htmlspecialchars($review['client_name']); ?> | Дата: <?php echo $review['created_at']; ?>
                                </div>
                                <div class="review-body">
                                    <p><strong>Отзыв:</strong> <?php echo htmlspecialchars($review['review_text']); ?></p>
                                    <p><strong>Оценка:</strong> <?php echo htmlspecialchars($review['rating']); ?>/5</p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>У этого исполнителя пока нет отзывов.</p>
                    <?php endif; ?>
                </div>
                <a href="search_executors.php" class="button secondary">Назад к поиску</a>
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