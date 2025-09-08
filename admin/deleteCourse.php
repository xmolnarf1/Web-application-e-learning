<?php
session_start();
require_once '/var/www/config/config.php';
$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie ste prihlásený.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$courseId = $_POST['id'] ?? null;

if (!$courseId) {
    echo json_encode(['success' => false, 'message' => 'Chýbajúce ID kurzu.']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT teacher_id FROM courses WHERE id = :id");
    $stmt->bindParam(':id', $courseId, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        echo json_encode(['success' => false, 'message' => 'Kurz neexistuje.']);
        exit();
    }

    if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $course['teacher_id']) {
        echo json_encode(['success' => false, 'message' => 'Nemáte oprávnenie vymazať tento kurz.']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM courses WHERE id = :id");
    $stmt->bindParam(':id', $courseId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Kurz bol úspešne odstránený.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Chyba pri odstraňovaní kurzu.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Databázová chyba: ' . $e->getMessage()]);
}
?>