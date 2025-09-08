<?php
session_start();

require_once '/var/www/config/config2.php';


$conn = connectDatabase($hostname, $database, $username, $password);

if (isset($_POST['submit'])) {
    $title_sk = $_POST['title_sk'];
    $title_en = $_POST['title_en'];
    $description_sk = $_POST['description_sk'];
    $description_en = $_POST['description_en'];

    if (isset($_FILES['course_file']) && $_FILES['course_file']['error'] == 0) {
        $file = $_FILES['course_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExts = ['pdf'];

        if (in_array($fileExt, $allowedExts)) {
            $isHtml = in_array($fileExt, ['html', 'htm']);
            $newFileName = $isHtml ? uniqid() . '.' . $fileExt : uniqid() . 'bakalarskaPraca.' . $fileExt;

            $uploadDirectory = '../uploads/';
            $fileDestination = $uploadDirectory . $newFileName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $teacherId = $_SESSION['user_id'];

                $query = "INSERT INTO courses (teacher_id, title, title_en, description, description_en, file_path) VALUES (:teacher_id, :title, :title_en, :description, :description_en, :file_path)";
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':teacher_id', $teacherId, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title_sk, PDO::PARAM_STR);
                $stmt->bindParam(':title_en', $title_en, PDO::PARAM_STR);
                $stmt->bindParam(':description', $description_sk, PDO::PARAM_STR);
                $stmt->bindParam(':description_en', $description_en, PDO::PARAM_STR);
                $stmt->bindParam(':file_path', $fileDestination, PDO::PARAM_STR);

                if ($stmt->execute()) {
                    header("Location: uploadLecture.php?uploadSuccess=true");
                } else {
                    echo "Error uploading course. Please try again.";
                }
            } else {
                echo "Error moving uploaded file.";
            }
        } else {
            echo "Invalid file type! Only PDF is allowed.";
        }
    } else {
        echo "No file uploaded or there was an error with the file upload.";
    }
}
?>
