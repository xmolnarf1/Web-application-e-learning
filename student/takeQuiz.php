<?php
session_start();
require_once '/var/www/config/config.php';

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'sk';
}

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = ($_GET['lang'] == 'en') ? 'en' : 'sk';
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

$langFile = '../lang/' . $_SESSION['lang'] . '.php';
if (file_exists($langFile)) {
    $lang = require_once $langFile;
} else {
    $lang = require_once '../lang/sk.php';
}


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

$pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

$quiz_id = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

$quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$quizStmt->execute([$quiz_id]);
$quiz = $quizStmt->fetch();

if (!$quiz) {
    die("Quiz not found");
}

$questionsStmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ?");
$questionsStmt->execute([$quiz_id]);
$questions = $questionsStmt->fetchAll();

foreach ($questions as &$question) {
    $answersStmt = $pdo->prepare("SELECT * FROM quiz_answers WHERE question_id = ?");
    $answersStmt->execute([$question['id']]);
    $question['answers'] = $answersStmt->fetchAll();
}
unset($question);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = 0;
    $max_score = count($questions);

    foreach ($questions as $question) {
        $question_id = $question['id'];
        $correct_answers = array_filter($question['answers'], function($a) { return $a['is_correct']; });

        if ($question['question_type'] === 'single') {
            $selected_answer_id = $_POST['question_'.$question_id] ?? null;

            if ($selected_answer_id) {
                $answerStmt = $pdo->prepare("SELECT is_correct FROM quiz_answers WHERE id = ?");
                $answerStmt->execute([$selected_answer_id]);
                $is_correct = $answerStmt->fetchColumn();

                if ($is_correct) {
                    $score++;
                }
            }
        } else {
            $selected_answer_ids = $_POST['question_'.$question_id] ?? [];

            $all_correct_selected = true;
            $no_incorrect_selected = true;

            foreach ($question['answers'] as $answer) {
                if ($answer['is_correct'] && !in_array($answer['id'], $selected_answer_ids)) {
                    $all_correct_selected = false;
                }
                if (!$answer['is_correct'] && in_array($answer['id'], $selected_answer_ids)) {
                    $no_incorrect_selected = false;
                }
            }

            if ($all_correct_selected && $no_incorrect_selected) {
                $score++;
            }
        }
    }

    $attemptStmt = $pdo->prepare("INSERT INTO quiz_attempts 
                                 (student_id, quiz_id, score, max_score) 
                                 VALUES (?, ?, ?, ?)");
    $attemptStmt->execute([
        $_SESSION['user_id'],
        $quiz_id,
        $score,
        $max_score
    ]);

    header("Location: resultQuiz.php?quiz_id=$quiz_id&score=$score&max_score=$max_score");
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['title']) ?> | Krypto E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

        .quiz-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
        }

        .quiz-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .quiz-title {
            color: #2c3e50;
            font-weight: 700;
        }

        .question-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .question-text {
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .answer-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .answer-item input {
            margin-right: 1rem;
        }

        .submit-btn {
            background: #006cdb;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            box-shadow: 0 5px 15px rgba(26, 41, 128, 0.3);
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand font-weight-bold font-italic" href="#"><i class="fas fa-coins me-2"></i>Krypto E-learning</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navHamburger">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navHamburger">
            <ul class="navbar-nav ms-auto m-2">
                <li class="nav-item me-2">
                    <a class="nav-link active" href="index.php"><i class="fas fa-home"></i> <?= $lang['student']['main_page'] ?></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="courses.php"><i class="fas fa-book"></i> <?= $lang['student']['courses'] ?></a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link" href="listOfQuizzes.php"><i class="fas fa-graduation-cap"></i> <?= $lang['student']['certfication_tests'] ?></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="statisticsUser.php"><i class="fas fa-chart-bar"></i> <?= $lang['student']['statistics'] ?></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="studentDetails.php"><i class="fas fa-user"></i> <?= $lang['student']['my_account'] ?></a>
                </li>
            </ul>
        </div>
        <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> <?= $lang['teacher']['logout'] ?></a>
    </div>
</nav>

<div class="container">
    <div class="quiz-container">
        <div class="quiz-header">
            <h1 class="quiz-title">
                <?= htmlspecialchars($quiz['title']) ?>
            </h1>
        </div>

        <form method="POST">
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card">
                    <div class="question-text">
                        <?= ($index + 1) ?>. <?= htmlspecialchars($question['question_text']) ?>
                    </div>

                    <?php foreach ($question['answers'] as $answer): ?>
                        <div class="answer-item">
                            <?php if ($question['question_type'] === 'single'): ?>
                                <input type="radio"
                                       name="question_<?= $question['id'] ?>"
                                       value="<?= $answer['id'] ?>"
                                       id="answer_<?= $answer['id'] ?>"
                                       required>
                            <?php else: ?>
                                <input type="checkbox"
                                       name="question_<?= $question['id'] ?>[]"
                                       value="<?= $answer['id'] ?>"
                                       id="answer_<?= $answer['id'] ?>">
                            <?php endif; ?>

                            <label for="answer_<?= $answer['id'] ?>" style="flex-grow: 1; margin-bottom: 0;">
                                <?= htmlspecialchars($answer['answer_text']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="submit-btn">
                <i class="fas fa-paper-plane me-2"></i> <?= ($_SESSION['lang'] == 'sk') ? 'Odoslať test' : 'Submit test' ?>
            </button>
        </form>
    </div>
</div>

<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>
</body>
</html>