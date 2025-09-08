<?php
require_once '/var/www/config/config.php';
session_start();

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


$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

if (!isset($_GET['id'])) {
    die("Prednáška neexistuje.");
}

$id = intval($_GET['id']);
$query = "SELECT * FROM courses WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Prednáška neexistuje.");
}
$filePath = htmlspecialchars($course['file_path']);
$fileExt = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
?>


<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                <button class="btn btn-secondary dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
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

<div class="container mt-5">
    <?php
    $courseTitle = ($_SESSION['lang'] === 'en') ? $course['title_en'] : $course['title'];
    $courseDescription = ($_SESSION['lang'] === 'en') ? $course['description_en'] : $course['description'];
    ?>
    <h2><?= htmlspecialchars($courseTitle) ?></h2>
    <p><?= nl2br(htmlspecialchars($courseDescription)) ?></p>

    <?php if (in_array($fileExt, ['pdf', 'txt', 'docx'])): ?>
        <div class="ratio ratio-16x9 mt-4">
            <iframe src="<?= $filePath ?>" frameborder="0"></iframe>
        </div>
    <?php elseif (in_array($fileExt, ['html', 'htm'])): ?>
        <div class="alert alert-info mt-4">
            <?php if ($_SESSION['lang'] === 'en'): ?>
                This is an HTML lecture. <a href="<?= $filePath ?>" target="_blank" class="btn btn-primary">View in a new window</a>
            <?php else: ?>
                Toto je HTML prednáška. <a href="<?= $filePath ?>" target="_blank" class="btn btn-primary">Zobraziť v novom okne</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p class="text-danger">Nepodporovaný formát súboru.</p>
    <?php endif; ?>
</div>

<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>


</body>
</html>
