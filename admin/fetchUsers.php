<?php
require_once '/var/www/config/config2.php';

try {
    $conn = connectDatabase($hostname, $database, $username, $password);
    $sql = "SELECT id, fullname, email, role, is_approved FROM users1";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as &$user) {
        $user['is_approved'] = (bool)$user['is_approved'];
    }
    unset($user);

    header('Content-Type: application/json');
    echo json_encode(['data' => $users]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>