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
$score = isset($_GET['score']) ? (int)$_GET['score'] : 0;
$max_score = isset($_GET['max_score']) ? (int)$_GET['max_score'] : 1;

$quizStmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$quizStmt->execute([$quiz_id]);
$quiz = $quizStmt->fetch();

if (!$quiz) {
    die("Quiz not found");
}

$percentage = round(($score / $max_score) * 100);
?>

<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results | Krypto E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f5f7fa;
        }

        .result-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
            text-align: center;
        }

        .result-header {
            margin-bottom: 2rem;
        }

        .quiz-title {
            color: #2c3e50;
            font-weight: 700;
        }

        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: <?= $percentage >= 70 ? '#2ecc71' : ($percentage >= 50 ? '#f39c12' : '#e74c3c') ?>;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0 auto 2rem;
            font-weight: 700;
        }

        .score-percentage {
            font-size: 2.5rem;
            line-height: 1;
        }

        .score-fraction {
            font-size: 1.2rem;
        }

        .result-message {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background: #006cdb;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
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
        <div class="d-flex">
            <div class="dropdown me-2">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-language"></i> <?= strtoupper($_SESSION['lang']) ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="?lang=sk">SK - Slovenčina</a></li>
                    <li><a class="dropdown-item" href="?lang=en">EN - English</a></li>
                </ul>
            </div>
            <a href="../logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> <?= $lang['teacher']['logout'] ?></a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="result-container">
        <div class="result-header">
            <h1 class="quiz-title"><?= htmlspecialchars($quiz['title']) ?></h1>
            <p class="text-muted"><?= $lang['quiz']['quiz_result'] ?></p>
        </div>

        <div class="score-circle">
            <span class="score-percentage"><?= $percentage ?>%</span>
            <span class="score-fraction"><?= $score ?>/<?= $max_score ?></span>
        </div>

        <div class="d-flex justify-content-center gap-3">
            <a href="listOfQuizzes.php" class="btn btn-outline-primary">
                <i class="fas fa-list me-2"></i> <?= $lang['quiz']['back_to_quizzes_button'] ?>
            </a>
            <a href="takeQuiz.php?quiz_id=<?= $quiz_id ?>" class="btn btn-primary">
                <i class="fas fa-redo me-2"></i> <?= $lang['quiz']['try_again'] ?>
            </a>
        </div>
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