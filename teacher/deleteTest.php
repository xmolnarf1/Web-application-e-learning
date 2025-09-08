<?php
session_start();
require_once '/var/www/config/config.php';

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie ste prihlásený.']);
    exit();
}

$testId = $_POST['id'] ?? null;

if (!$testId) {
    echo json_encode(['success' => false, 'message' => 'Chýbajúce ID testu.']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT teacher_id FROM quizzes WHERE id = :id");
    $stmt->bindParam(':id', $testId, PDO::PARAM_INT);
    $stmt->execute();
    $test = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$test) {
        echo json_encode(['success' => false, 'message' => 'Test neexistuje.']);
        exit();
    }

    if ($_SESSION['role'] !== 'admin' && $_SESSION['user_id'] != $test['teacher_id']) {
        echo json_encode(['success' => false, 'message' => 'Nemáte oprávnenie vymazať tento test.']);
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = :id");
    $stmt->bindParam(':id', $testId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Test bol úspešne odstránený.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Chyba pri odstraňovaní testu.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Databázová chyba: ' . $e->getMessage()]);
}
?>