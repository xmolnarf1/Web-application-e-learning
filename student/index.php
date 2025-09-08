<?php
session_start();

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

if ($_SESSION['role'] == "teacher") {
    header("Location: ../teacher/indexTeacher.php?loginTeacher=true");
    exit();
}

$user_name = $_SESSION['fullname'];
$role = $_SESSION['role'];
?>

<!doctype html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../js/bakalarScript.js" defer></script>
    <style>

        .crypto-card {
            transition: transform 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .crypto-card:hover {
            transform: translateY(-5px);
        }
        .crypto-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .welcome-section {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .welcome-card {
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .welcome-icon {
            color: #1a2980;
            opacity: 0.9;
        }
        .welcome-divider {
            color: #6c757d;
            font-size: 1.2rem;
        }

        .mainCardForStudent@media (max-width: 1218px) {
            max-width: 90vw;
        }

        h3{
            text-decoration: none;
            font-size: 1.9em;
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

<?php if (isset($_GET['loginUser'])): ?>
    <div id="toastForSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
        <div class="toast-body">
            <i class="fas fa-check-circle me-2"></i>
            <?= ($_SESSION['lang'] == 'sk') ? 'Úspešne prihlásený do krypto akademie!' : 'Successfully logged in to the crypto academy!' ?>

        </div>
    </div>
<?php endif; ?>

<div class="welcome-section py-5">
    <div class="container text-center py-4">
        <div class="mainCardForStudent welcome-card bg-white p-4 rounded-3 shadow-sm" style="max-width: 50vw; margin: 0 auto; border-left: 4px solid #3498db;">
            <div class="welcome-icon mb-3">

            </div>
            <h1 class="display-5 fw-bold mb-3">
                <i class="fas fa-user-tag text-primary"></i>
                <?= $lang['student']['welcome_back'] ?> <span class="text-warning"><?= htmlspecialchars($user_name) ?></span>
            </h1>
            <div class="role-badge mb-3">
                <span class="badge bg-dark fs-6 py-2 px-3">
                    <i class="fas fa-user-tag me-1"></i>
                    <?= htmlspecialchars($role) ?>
                </span>
            </div>
            <p class="lead text-muted mb-4">
                <?= $lang['student']['you_are_logged_in_to_crypto_academy'] ?>
            </p>
            <div class="welcome-divider mb-4">
                <i class="fas fa-network-wired text-success"></i>
                <span class="mx-2 text-muted">•</span>
                <i class="fas fa-link text-primary"></i>
                <span class="mx-2 text-lock">•</span>
                <i class="fas fa-lock text-warning"></i>
            </div>
            <a href="courses.php" class="btn btn-lg btn-primary px-4">
                <i class="fas fa-rocket me-2"></i> <?= $lang['student']['start_education'] ?>
            </a>
        </div>
    </div>
</div>


<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4"><i class="fas fa-bolt text-warning me-2"></i><?= $lang['student']['quick_actions'] ?></h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="courses.php" class="btn btn-success btn-lg"><i class="fas fa-graduation-cap me-2"></i><?= $lang['student']['go_to_the_courses'] ?></a>
                <a href="studentDetails.php" class="btn btn-info btn-lg"><i class="fas fa-user-edit me-2"></i><?= $lang['student']['edit_profile'] ?></a>
                <a href="statisticsUser.php" class="btn btn-warning btn-lg"><i class="fas fa-chart-line me-2"></i><?= $lang['student']['my_statistics'] ?></a>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <h3 class="mb-4"><i class="fas fa-cubes text-primary me-2"></i><?= $lang['student']['main_available_courses'] ?></h3>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="crypto-card h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="crypto-icon text-primary">
                            <i class="fas fa-link"></i>
                        </div>
                        <h4 class="card-title"><?= $lang['student']['blockchain'] ?></h4>
                    </div>
                    <p class="card-text"><?= $lang['student']['blockchain_description'] ?></p>
                    <a href="courses.php" class="btn btn-outline-primary w-100 mt-3"><?= $lang['student']['look_at_the_available_courses'] ?></a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="crypto-card h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="crypto-icon text-success">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <h4 class="card-title"><?= $lang['student']['decentralizacia'] ?></h4>
                    </div>
                    <p class="card-text"><?= $lang['student']['decentralizacia_description'] ?></p>
                    <a href="courses.php" class="btn btn-outline-success w-100 mt-3"><?= $lang['student']['look_at_the_available_courses'] ?></a>
                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="crypto-card h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="crypto-icon text-warning">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4 class="card-title"><?= $lang['student']['kryptografia'] ?></h4>
                    </div>
                    <p class="card-text"><?= $lang['student']['kryptografia_description'] ?></p>
                    <a href="courses.php" class="btn btn-outline-warning w-100 mt-3"><?= $lang['student']['look_at_the_available_courses'] ?></a>
                </div>
            </div>
        </div>
    </div>
</div>


<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    if (new URLSearchParams(window.location.search).has('loginUser')) {
        let toast = new bootstrap.Toast(document.getElementById('toastForSuccess'));
        toast.show();

        history.replaceState(null, null, window.location.pathname);
    }
</script>
</body>
</html>