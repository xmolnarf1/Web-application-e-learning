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
    <script src="../js/bakalarScript.js" defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .teacher-navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .teacher-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .upload-container {
            margin: 2rem auto;
            padding: 2rem;
        }
        .upload-header {
            padding-bottom: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .upload-title {
            color: #3498db;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .file-upload-box {
            border: 2px dashed #3498db;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .file-upload-box:hover {
            background-color: #f8f9fa;
        }

        .submit-btn {
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            margin-top: 1rem;
        }
        .submit-btn:hover {
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
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

<div id="toastForSuccessUpload" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Učivo bolo úspešne pridané!' : 'Lecture  was uploaded successfully!' ?>
    </div>
</div>

<main class="container">
    <div class="upload-container">
        <div class="upload-header">
            <h2 class="upload-title">
                <?= $lang['teacher']['upload_new_material'] ?>
            </h2>
            <p class="text-muted"><?= $lang['teacher']['fill_out_the_form'] ?></p>
        </div>

        <form action="uploadCourse.php" method="POST" enctype="multipart/form-data">

            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="title_sk" required>
                <label for="title_sk">🇸🇰 Názov kurzu (slovensky)</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" name="title_en" required>
                <label for="title_en">🇬🇧 Course Title (English)</label>
            </div>

            <div class="form-floating mb-3">
                <textarea class="form-control" name="description_sk" required></textarea>
                <label for="description_sk">🇸🇰 Popis kurzu</label>
            </div>
            <div class="form-floating mb-3">
                <textarea class="form-control" name="description_en" required></textarea>
                <label for="description_en">🇬🇧 Course Description</label>
            </div>

            <div class="mb-4">
                <div class="file-upload-box">
                    <h5><?= $lang['teacher']['move_file_here_or'] ?></h5>
                    <div class="mt-3">
                        <label for="course_file" class="btn btn-primary">
                            <i class="fas fa-folder-open me-2"></i><?= $lang['teacher']['choose_file'] ?>
                        </label>
                        <input class="d-none" type="file" name="course_file" id="course_file" accept=".pdf, .docx, .txt" required>
                        <p class="small text-muted mt-2"><?= $lang['teacher']['supported_formats'] ?></p>
                    </div>
                    <div id="file-name" class="mt-2 text-primary fw-bold"></div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-success submit-btn">
                <i class="fas fa-upload me-2"></i><?= $lang['teacher']['upload_material'] ?>
            </button>
        </form>
    </div>
</main>

<footer class="footerOMne mt-3 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>
<script>
    document.getElementById('course_file').addEventListener('change', function(e) {
        const fileName = document.getElementById('file-name');
        if (this.files.length > 0) {
            fileName.textContent = 'Vybraný súbor: ' + this.files[0].name;
        } else {
            fileName.textContent = '';
        }
    });

    if (new URLSearchParams(window.location.search).has('uploadSuccess')) {
        let toast = new bootstrap.Toast(document.getElementById('toastForSuccessUpload'));
        toast.show();
        history.replaceState(null, null, window.location.pathname);
    }
</script>
</body>
</html>