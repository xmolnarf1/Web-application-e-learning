<?php
session_start();
require_once '/var/www/config/config.php';

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

header('Content-Type: application/json');

try {
    $query = "SELECT c.id, c.title, c.description, c.file_path, c.created_at, u.fullname as teacher_name 
              FROM courses c 
              JOIN users1 u ON c.teacher_id = u.id";

    if ($_SESSION['role'] === 'teacher') {
        $query .= " WHERE c.teacher_id = :teacher_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':teacher_id', $_SESSION['user_id'], PDO::PARAM_INT);
    } else {
        $stmt = $conn->prepare($query);
    }

    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['data' => $courses]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>