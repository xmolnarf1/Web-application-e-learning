<?php
session_start();
require_once '/var/www/config/config2.php';

try {
    $conn = connectDatabase($hostname, $database, $username, $password);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['id'])) {
            $user_id = intval($_POST['id']);

            if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                echo json_encode(["success" => false, "message" => "Unauthorized action."]);
                exit();
            }

            $stmt = $conn->prepare("DELETE FROM users1 WHERE id = :id");
            $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "User deleted successfully."]);
            } else {
                echo json_encode(["success" => false, "message" => "Error deleting user."]);
            }

            $stmt = null;
        } else {
            echo json_encode(["success" => false, "message" => "Invalid request."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method."]);
    }

    $conn = null;
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
