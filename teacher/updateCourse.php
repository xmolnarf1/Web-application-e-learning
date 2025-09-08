<?php
session_start();
require_once '/var/www/config/config.php';

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['title'], $_POST['description'])) {
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);

        if (!in_array($_SESSION['role'], ['admin', 'teacher'])) {
            echo json_encode(["success" => false, "message" => "Unauthorized action."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE courses SET title = :title, description = :description WHERE id = :id");
        $stmt->bindParam(":title", $title, PDO::PARAM_STR);
        $stmt->bindParam(":description", $description, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Kurz bol úspešne aktualizovaný."]);
        } else {
            echo json_encode(["success" => false, "message" => "Chyba pri aktualizácii kurzu."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Neplatná požiadavka."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Neplatná metóda požiadavky."]);
}
