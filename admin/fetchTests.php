<?php
session_start();
require_once '/var/www/config/config.php';

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

header('Content-Type: application/json');

try {
    $query = "SELECT 
                q.id, 
                u.fullname AS teacher_name, 
                q.title, 
                COALESCE(c.title, 'N/A') AS course_name, 
                q.created_at
            FROM quizzes q
            LEFT JOIN courses c ON c.id = q.course_id
            JOIN users1 u ON q.teacher_id = u.id";

    if ($_SESSION['role'] === 'teacher') {
        $query .= " WHERE q.teacher_id = :teacher_id";
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