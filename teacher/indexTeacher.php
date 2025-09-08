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



if (!isset($_SESSION['fullname'])) {
    header("Location: ../login.php");
    exit();
}
if ($_SESSION['role'] == "admin") {
    header("Location: ../admin/indexAdmin.php");
    exit();
}
if ($_SESSION['role'] == "student") {
    header("Location: ../student/index.php");
    exit();
}

$pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

$studentCount = $pdo->query("SELECT COUNT(*) FROM users1 WHERE role = 'student' AND is_approved = 1")->fetchColumn();

$quizCount = $pdo->query("SELECT COUNT(*) FROM quizzes WHERE teacher_id = " . $_SESSION['user_id'])->fetchColumn();

$courseCount = $pdo->query("SELECT COUNT(*) FROM courses WHERE teacher_id = " . $_SESSION['user_id'])->fetchColumn();



$user_name = $_SESSION['fullname'];
$role = $_SESSION['role'];
?>
<!doctype html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Učiteľský panel | Krypto E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/bakalarScript.js" defer></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .teacher-navbar {
            background: #3498db;
        }
        .welcome-card {
            background: white;
            border-left: 5px solid #3498db;
        }
        .badge-teacher {
            background-color: #3498db;
        }
        .teacher-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #3498db;
        }
        .quick-stats {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 4rem;
            margin-bottom: 1.5rem;
        }
        .stat-item {
            text-align: center;
            padding: 1rem;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3498db;
        }
        .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            height: 100%;
            max-width: 250px;
            margin: 0 auto;
        }

        .quick-stats .row {
            justify-content: space-between;
        }

        .quick-stats .col-md-3 {
            display: flex;
            padding: 0 10px;
        }

        .quick-stats {
            max-width: 900px;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark teacher-navbar">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-chalkboard-teacher me-2"></i><?= $lang['teacher']['teacher_panel'] ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navHamburger">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navHamburger">
            <ul class="navbar-nav ms-auto m-2">
                <li class="nav-item me-2">
                    <a class="nav-link active" href="indexTeacher.php">
                        <i class="fas fa-home teacher-icon"></i><?= $lang['teacher']['main_page'] ?>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="coursesTable.php">
                        <i class="fas fa-book teacher-icon"></i><?= $lang['teacher']['available_material'] ?>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="testsTable.php">
                        <i class="fas fa-question-circle teacher-icon"></i><?= $lang['teacher']['available_material_t'] ?>
                    </a>
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

<div id="toastForSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Prihlásenie prebehlo úspešne!' : 'Login was successful!' ?>
    </div>
</div>

<div class="container my-4">
    <div class="welcome-card p-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                    <?= $lang['teacher']['welcome'] ?>, <span style="color: #0d6efd;"><?= htmlspecialchars($user_name) ?></span>
                </h2>
                <p class="lead text-muted mb-0">
                    <?= $lang['teacher']['logged_in_as'] ?> <span class="badge badge-teacher"><?= htmlspecialchars($role) ?></span>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <a href="coursesTable.php" class="btn btn-primary">
                        <i class="fas fa-book me-1"></i> <?= $lang['teacher']['your_courses'] ?>
                    </a>
                    <a href="quizzCreate.php" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i> <?= $lang['teacher']['new_test'] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container mt-4">
    <div class="quick-stats">
        <h4 class="mb-4 text-center"><i class="fas fa-chart-pie me-2"></i><?= $lang['teacher']['system_overview'] ?></h4>
        <div class="row">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number"><?= $studentCount ?></div>
                    <div class="stat-label"><?= $lang['teacher']['active_students'] ?></div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: <?= min(100, $studentCount * 5) ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-number"><?= $courseCount ?></div>
                    <div class="stat-label"><?= $lang['teacher']['your_courses_teacher'] ?></div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: <?= min(100, $courseCount * 20) ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-number"><?= $quizCount ?></div>
                    <div class="stat-label"><?= $lang['teacher']['created_quizzes'] ?></div>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: <?= min(100, $quizCount * 10) ?>%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container mt-5">
    <h3 class="mb-4"><i class="fas fa-rocket me-2 text-primary"></i><?= $lang['teacher']['quick_actions'] ?></h3>
    <div class="row g-6">
        <div class="col-md-6">
            <div class="feature-card">
                <div class="card-body text-center">
                    <div class="feature-icon">
                        <i class="fas fa-upload"></i>
                    </div>
                    <h5 class="card-title"><?= $lang['teacher']['add_new_material'] ?></h5>
                    <p class="card-text"><?= $lang['teacher']['load_new_study_material'] ?></p>
                    <a href="uploadLecture.php" class="btn btn-outline-primary"><?= $lang['teacher']['go_through'] ?></a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="feature-card">
                <div class="card-body text-center">
                    <div class="feature-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h5 class="card-title"><?= $lang['teacher']['create_new_quiz'] ?></h5>
                    <p class="card-text"><?= $lang['teacher']['create_new_quiz_with_option_multiple'] ?></p>
                    <a href="quizzCreate.php" class="btn btn-outline-primary"><?= $lang['teacher']['go_through'] ?></a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footerOMne mt-3 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>
<script>
    if (new URLSearchParams(window.location.search).has('loginTeacher')) {
        let toast = new bootstrap.Toast(document.getElementById('toastForSuccess'));
        toast.show();
        history.replaceState(null, null, window.location.pathname);
    }
</script>
</body>
</html>