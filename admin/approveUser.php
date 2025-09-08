<?php
require_once '/var/www/config/config2.php';
require '/var/www/config/vendor/autoload.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Unauthorized access']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['id'] ?? null;

    try {
        $conn = connectDatabase($hostname, $database, $username, $password);

        $stmtEmail = $conn->prepare("SELECT email FROM users1 WHERE id = :id");
        $stmtEmail->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmtEmail->execute();
        $teacher = $stmtEmail->fetch(PDO::FETCH_ASSOC);

        if (!$teacher) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE users1 SET is_approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $success = $stmt->execute();

        if ($success) {
            $mail = new PHPMailer(true);
            try {
                $mail->setFrom('molnarfrigyes7@gmail.com', 'E-learning xmolnarf1');
                $mail->addAddress($teacher['email']); // send to teacher email
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Your account has been approved!';
                $mail->Body = '<h2>Hello!</h2>
                                <p>Your teacher account has been approved by the admin.</p>
                                <p>You can now access all features in our E-learning platform.</p>
                            
                                <hr>
                            
                                <h2>Ahoj!</h2>
                                <p>Váš učiteľský účet bol schválený administrátorom.</p>
                                <p>Teraz máte prístup ku všetkým funkciám na našej E-learningovej platforme.</p>
                            
                                <hr>
                                <p>Thank you / Ďakujeme,<br>E-learning xmolnarf1 team</p>
                                ';
                $mail->AltBody = "Hello!\nYour teacher account has been approved by the admin.\nYou can now access all features in our E-learning platform.\n\n
                Ahoj!\nVáš učiteľský účet bol schválený administrátorom.\nTeraz máte prístup ku všetkým funkciám na našej E-learningovej platforme.\n\n
                Thank you / Ďakujeme,\nE-learning xmolnarf1 team";

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'molnarfrigyes7@gmail.com';
                $mail->Password = 'zbnx ukho xkhc qgjv';
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->send();
            } catch (Exception $e) {
                error_log("Email could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
            }

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?>