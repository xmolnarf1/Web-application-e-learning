<?php
session_start();
require_once '/var/www/config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../login.php");
    exit();
}

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

$pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);
$student_id = $_SESSION['user_id'];

$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_attempts,
        SUM(score) as total_score,
        SUM(max_score) as total_max_score,
        AVG(score * 100.0 / max_score) as average_percentage,
        MIN(score * 100.0 / max_score) as min_percentage,
        MAX(score * 100.0 / max_score) as max_percentage
    FROM quiz_attempts
    WHERE student_id = ?
");
$statsStmt->execute([$student_id]);
$overallStats = $statsStmt->fetch();

$historyStmt = $pdo->prepare("
    SELECT q.title, a.score, a.max_score, a.attempted_at, 
           (a.score * 100.0 / a.max_score) as percentage
    FROM quiz_attempts a
    JOIN quizzes q ON a.quiz_id = q.id
    WHERE a.student_id = ?
    ORDER BY a.attempted_at DESC
");
$historyStmt->execute([$student_id]);
$attemptHistory = $historyStmt->fetchAll();

?>

<!doctype html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-learning</title>
    <link rel="icon" href="../images/favicon.ico">

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        .stats-container {
            max-width: 1200px;
            margin: 2rem auto;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #b6b4b4;
        }

        .stat-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-title {
            color: black;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            padding: 2em;
            color: black;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .progress {
            height: 10px;
            border-radius: 5px;
        }

        .progress-bar {
            background: #138f04;
        }

        .attempt-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }


        .attempt-score {
            font-weight: bold;
        }


        h3{
            text-decoration: none;
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

<div class="py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="text-black mb-0"><i class="fas fa-chart-line me-2"></i><?= $lang['stats']['my_stats'] ?></h2>
                <p class="text-muted mb-0"><?= $lang['stats']['overview_of_tests'] ?></p>
            </div>
            <div class="role-badge">
                <span class="badge bg-dark fs-6 py-2 px-3">
                    <i class="fas fa-user-tag me-1"></i>
                    <?= htmlspecialchars($_SESSION['role']) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="stats-container py-4">
    <div class="container">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title"><i class="fas fa-tachometer-alt me-2 text-primary"></i><?= $lang['stats']['full_overview'] ?></h3>
            </div>

            <div class="stat-grid">
                <div class="text-center p-3 rounded">
                    <div><i class="fas fa-check-circle me-1"></i><?= $lang['stats']['number_of_times'] ?></div>
                    <div class="stat-value"><?= $overallStats['total_attempts'] ?></div>
                </div>

                <div class="text-center p-3 rounded">
                    <div><i class="fas fa-percentage me-1"></i><?= $lang['stats']['percentage'] ?></div>
                    <div class="stat-value"><?= round($overallStats['average_percentage']) ?>%</div>
                    <div class="progress mt-2">
                        <div class="progress-bar" style="width: <?= round($overallStats['average_percentage']) ?>%"></div>
                    </div>
                </div>

                <div class="text-center p-3 rounded">
                    <div><i class="fas fa-trophy me-1"></i><?= $lang['stats']['best'] ?></div>
                    <div class="stat-value"><?= round($overallStats['max_percentage']) ?>%</div>
                    <div class="progress mt-2">
                        <div class="progress-bar" style="width: <?= round($overallStats['max_percentage']) ?>%"></div>
                    </div>
                </div>

                <div class="text-center p-3 rounded">
                    <div><i class="fas fa-star me-1"></i><?= $lang['stats']['overall'] ?></div>
                    <div class="stat-value"><?= $overallStats['total_score'] ?>/<?= $overallStats['total_max_score'] ?></div>
                    <div class="progress mt-2">
                        <div class="progress-bar" style="width: <?= round(($overallStats['total_score'] / $overallStats['total_max_score']) * 100) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title"><i class="fas fa-history me-2 text-primary"></i><?= $lang['stats']['last_test_taken'] ?></h3>
                <p class="text-muted mb-0"><?= $lang['stats']['all_of_last_tests'] ?></p>
            </div>

            <?php foreach ($attemptHistory as $attempt):
                $percentage = round($attempt['percentage']);
                $isPass = $percentage >= 50;
                ?>
                <div class="attempt-item">
                    <div>
                        <strong><?= htmlspecialchars($attempt['title']) ?></strong>
                        <div class="text-muted small"><i class="far fa-clock me-1"></i><?= date('d.m.Y H:i', strtotime($attempt['attempted_at'])) ?></div>
                    </div>
                    <div class="text-end">
                        <div class="attempt-score"><?= $attempt['score'] ?>/<?= $attempt['max_score'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
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