<?php
require_once 'config/db.php';
$connection = getDBConnection();

// Инициализация переменных для формы поиска
$specialization = '';
$min_rating = 0;
$min_reviews = 0;
$executors = [];

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $specialization = $_GET['specialization'] ?? '';
    $min_rating = $_GET['min_rating'] ?? 0;
    $min_reviews = $_GET['min_reviews'] ?? 0;

    // Объединяем таблицы executors и users для получения имени исполнителя
    $stmt = $connection->prepare("
        SELECT executors.*, users.username AS name FROM executors 
        JOIN users ON executors.user_id = users.user_id
        WHERE executors.specialization LIKE ? 
        AND executors.rating >= ?
        AND executors.reviews_count >= ?
        ORDER BY executors.rating DESC
    ");
    $like_specialization = "%$specialization%";
    $stmt->bind_param("sii", $like_specialization, $min_rating, $min_reviews);
    $stmt->execute();
    $result = $stmt->get_result();

    // Сохраняем результаты в массив
    while ($row = $result->fetch_assoc()) {
        $executors[] = $row;
    }

    $stmt->close();
}
$connection->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Поиск исполнителей</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <div class="logo">SkillMatch</div>
            <nav>
                <ul class="header-navigation">
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="profile.php">PROFILE</a></li>
                    <li><a href="auth/logout.php">LOGOUT</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="search-section">
            <div class="container">
                <h1>Поиск исполнителей</h1>
                <form action="search_executors.php" method="get" class="form-section">
                    <label for="specialization">Специализация:</label>
                    <input type="text" id="specialization" name="specialization" value="<?= htmlspecialchars($specialization) ?>" class="form-input"><br>

                    <label for="min_rating">Минимальный рейтинг:</label>
                    <input type="number" id="min_rating" name="min_rating" step="0.1" min="0" max="5" value="<?= htmlspecialchars($min_rating) ?>" class="form-input"><br>

                    <label for="min_reviews">Минимальное количество отзывов:</label>
                    <input type="number" id="min_reviews" name="min_reviews" min="0" value="<?= htmlspecialchars($min_reviews) ?>" class="form-input"><br>

                    <button type="submit" name="search" class="button primary">Найти исполнителей</button>
                </form>
            </div>
        </section>

        <section class="results-section">
            <div class="container">
                <h2>Результаты поиска</h2>
                <?php if (count($executors) > 0): ?>
                    <div class="executors-list">
                        <?php foreach ($executors as $executor): ?>
                            <div class="executor-card">
                                <div class="executor-photo">
                                    <?php if (!empty($executor['photo_url'])): ?>
                                        <img src="<?= htmlspecialchars($executor['photo_url']) ?>" alt="Фото исполнителя">
                                    <?php else: ?>
                                        <img src="default-avatar.png" alt="Фото исполнителя">
                                    <?php endif; ?>
                                </div>
                                <div class="executor-details">
                                    <strong>Имя:</strong> 
                                    <a href="executor_profile.php?executor_id=<?= $executor['executor_id'] ?>" class="executor-link">
                                        <?= htmlspecialchars($executor['name']) ?>
                                    </a><br>
                                    <strong>Специализация:</strong> <?= htmlspecialchars($executor['specialization']) ?><br>
                                    <strong>Рейтинг:</strong> <?= htmlspecialchars($executor['rating']) ?><br>
                                    <strong>Количество отзывов:</strong> <?= htmlspecialchars($executor['reviews_count']) ?><br>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>Нет исполнителей, соответствующих критериям поиска.</p>
                <?php endif; ?>
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