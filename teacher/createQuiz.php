<?php
session_start();
require_once '/var/www/config/config.php';

$pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'teacher') {
    die('You must be logged in as a teacher.');
}

$teacher_id = $_SESSION['user_id'];
$title = $_POST['title'];
$course_id = empty($_POST['course_id']) ? NULL : $_POST['course_id'];
$questions = $_POST['questions'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO quizzes (teacher_id, title, course_id) VALUES (?, ?, ?)");
    $stmt->execute([$teacher_id, $title, $course_id]);
    $quiz_id = $pdo->lastInsertId();

    foreach ($questions as $qIndex => $qData) {
        $type = $qData['type'] ?? 'single';

        $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, question_type) VALUES (?, ?, ?)");
        $stmt->execute([$quiz_id, $qData['text'], $type]);
        $question_id = $pdo->lastInsertId();

        foreach ($qData['answers'] as $aIndex => $aData) {
            $answer_text = $aData['text'];
            if ($type === 'single') {
                $is_correct = ($aIndex == $qData['correct']) ? 1 : 0;
            } else {
                $is_correct = isset($aData['is_correct']) ? 1 : 0;
            }

            $stmt = $pdo->prepare("INSERT INTO quiz_answers (question_id, answer_text, is_correct) VALUES (?, ?, ?)");
            $stmt->execute([$question_id, $answer_text, $is_correct]);


        }
    }
    $_SESSION['test_created'] = true;
    $pdo->commit();
    header('Location: quizzCreate.php');
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
