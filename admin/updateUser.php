<?php
session_start();
require_once '/var/www/config/config.php';

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['fullname'], $_POST['email'])) {
        $id = intval($_POST['id']);
        $fullname = trim($_POST['fullname']);
        $email = trim($_POST['email']);


        if ($_SESSION['role'] !== 'admin') {
            echo json_encode(["success" => false, "message" => "Unauthorized action."]);
            exit();
        }

        $stmt = $conn->prepare("UPDATE users1 SET fullname = :fullname, email = :email WHERE id = :id");
        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Používateľ bol úspešne aktualizovaný."]);
        } else {
            echo json_encode(["success" => false, "message" => "Chyba pri aktualizácii používateľa."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Neplatná požiadavka."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Neplatná metóda požiadavky."]);
}
?>
