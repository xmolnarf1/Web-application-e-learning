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

if ($_SESSION['role'] == "teacher") {
    header("Location: ../teacher/indexTeacher.php?loginTeacher=true");
    exit();
}

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

$query = "SELECT * FROM courses";
$stmt = $conn->query($query);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border-left: 4px solid #b6b4b4;
        }

        .course-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #05243f;
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

<div class="crypto-gradient-bg text-black py-4">
    <div class="container py-3 text-center">
        <h1 class="display-5 mb-3"><?= $lang['student']['all_available_courses'] ?></h1>
        <p class="lead"><?= $lang['student']['choose_from_the_courses'] ?></p>
    </div>
</div>

<main class="py-4">
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="<?= $lang['student']['search_courses'] ?>" id="courseSearch">
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="coursesContainer">
            <?php foreach ($courses as $course): ?>
                <?php
                    $title = ($_SESSION['lang'] == 'en') ? $course['title_en'] : $course['title'];
                    $description = ($_SESSION['lang'] == 'en') ? $course['description_en'] : $course['description'];
                ?>

                <div class="col">
                    <div class="card h-100 crypto-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-book course-icon"></i>
                                <h5 class="card-title"><?= htmlspecialchars($title) ?></h5>
                            </div>
                            <p class="card-text"><?= htmlspecialchars(substr($description, 0, 120)) ?>...</p>
                            <a href="seeCourse.php?id=<?= $course['id'] ?>" class="btn btn-outline-primary w-100">
                                <i class="fas fa-play-circle me-1"></i> <?= $lang['student']['start_lection'] ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($courses)): ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
                <h4 class="text-muted">Momentálne nie sú dostupné žiadne kurzy</h4>
                <p>Skúste to prosím neskôr alebo kontaktujte administrátora.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="footerOMne mt-3 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/bootstrap.js"></script>
<script>
    $(document).ready(function() {
        $('#courseSearch').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('.col').each(function() {
                const cardText = $(this).text().toLowerCase();
                $(this).toggle(cardText.includes(searchText));
            });
        });

        $('#courseFilter').on('change', function() {
            const filterValue = $(this).val();
            $('.col').each(function() {
                if (filterValue === 'all') {
                    $(this).show();
                } else {
                    const badgeText = $(this).find('.badge').text().toLowerCase();
                    $(this).toggle(badgeText.includes(filterValue));
                }
            });
        });
    });
</script>
</body>
</html>