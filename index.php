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

$langFile = 'lang/' . $_SESSION['lang'] . '.php';
if (file_exists($langFile)) {
    $lang = require_once $langFile;
} else {
    $lang = require_once 'lang/sk.php';
}


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    if($_SESSION["role"] == "admin"){
        header("location: admin/indexAdmin.php");
    } elseif ($_SESSION["role"] == "teacher"){
        header("location: teacher/indexTeacher.php");
    } elseif ($_SESSION["role"] == "student"){
        header("location: student/index.php");
    }
    exit;
}


require_once '/var/www/config/config.php';
require_once 'utilities.php';
?>

<!doctype html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-learning</title>

    <link rel="icon" href="images/favicon.ico">

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/bakalarScript.js" defer></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media (max-width: 768px) {
            h2 {
                font-size: 1.5em;
                text-align: center;
                padding: 0 1rem;
            }

            .navbar .btn {
                display: block;
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .dropdown-menu {
                min-width: 100%;
            }

            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="index.php">E-learning</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navHamburger" aria-controls="navHamburger" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navHamburger">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="btn bg-white text-secondary m-1" href="index.php"><i class="fas fa-home"></i> <?= $lang['register']['main_page'] ?></a>
                </li>
                <li class="nav-item">
                    <a class="btn bg-primary text-white m-1" href="login.php"><i class="fas fa-user"></i> <?= $lang['register']['log_in_button'] ?></a>
                </li>
                <li class="nav-item">
                    <a class="btn bg-light text-black m-1" href="register.php"><i class="fas fa-user"></i> <?= $lang['register']['register_button'] ?></a>
                </li>
            </ul>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle m-1" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-language"></i> <?= strtoupper($_SESSION['lang']) ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="languageDropdown">
                    <li><a class="dropdown-item" href="?lang=sk">SK - Slovenčina</a></li>
                    <li><a class="dropdown-item" href="?lang=en">EN - English</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container text-center">
    <h1 class="mt-4 welcome-title"><?= $lang['main']['title'] ?></h1>
    <small class="text-dark">
        <?= $lang['main']['small_text'] ?>
    </small>
</div>

<div class="container my-5">
    <p>
        <?= $lang['main']['on_this_site'] ?>
    </p>
    <div class="">
        <h4 class="">
            <?= $lang['main']['offers_question'] ?>
        </h4>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="container list-group">
                <a href="#" class="list-group-item my-2" style="cursor: default">
                    <h5 class="my-1 text-capitalize">
                        <?= $lang['main']['blockchain_title'] ?>
                    </h5>
                    <p class="text-secondary">
                        <?= $lang['main']['blockchain_desc'] ?>

                    </p>
                    <small class="text-capitalize text-black-50"><?= $lang['main']['bl_small'] ?></small>
                </a>
            </div>
            <div class="container list-group">
                <a href="#" class="list-group-item" style="cursor: default">
                    <h5 class="my-1 text-capitalize">
                        <?= $lang['main']['dec_title'] ?>

                    </h5>
                    <p class="text-secondary">
                        <?= $lang['main']['decentralizacia_desc'] ?>


                    </p>
                    <small class="text-capitalize text-black-50">Proof of work, Proof of stake</small>
                </a>
            </div>
            <div class="container list-group">
                <a href="#" class="list-group-item my-2" style="cursor: default">
                    <h5 class="my-1 text-capitalize">
                        <?= $lang['main']['cryptography_title'] ?>
                    </h5>
                    <p class="text-secondary">
                        <?= $lang['main']['cryptography_desc'] ?>


                    </p>
                    <small class="text-capitalize text-black-50"><?= $lang['main']['cr_small'] ?></small>
                </a>
            </div>
        </div>
        <div class="col-lg-4 col-md-12 my-md-3  justify-content-center align-content-center">
            <img src="images/crypto.jpg" alt="Crypto" class="img-fluid shadow rounded-2 h-auto w-100">
        </div>
    </div>
    <div class="my-3">
    </div>
</div>


<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>


<script src="js/bootstrap.js"></script>

</body>
</html>