<?php
require_once 'config/db.php'; // Подключение к базе данных

$db = getDBConnection(); // Получаем соединение с БД

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $executor_id = $_POST['executor_id'] ?? null;

    // Проверка наличия ID исполнителя
    if (!$executor_id) {
        die("ID исполнителя не указан.");
    }

    // Проверка наличия загруженного файла
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        die("Ошибка при загрузке файла.");
    }

    // Проверка типа файла
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['photo']['type'], $allowed_types)) {
        die("Недопустимый тип файла. Разрешены: JPEG, PNG, GIF.");
    }

    // Загрузка файла
    $uploads_dir = 'uploads/photos'; // Папка для загрузки фото
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // Создаем папку, если она не существует
    }

    $filename = uniqid('executor_') . '_' . basename($_FILES['photo']['name']);
    $file_path = $uploads_dir . '/' . $filename;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
        // Сохранение пути к файлу в БД
        $query = $db->prepare("UPDATE executors SET photo = ? WHERE executor_id = ?");
        $query->bind_param("si", $file_path, $executor_id);

        if ($query->execute()) {
            echo "Фото успешно загружено!";
        } else {
            die("Ошибка сохранения пути к файлу в базу данных.");
        }
    } else {
        die("Не удалось сохранить файл.");
    }
} else {
    die("Некорректный метод запроса.");
}
?>
<!-- Updated layout and styles applied -->
