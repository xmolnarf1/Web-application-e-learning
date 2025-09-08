<?php
require_once '/var/www/config//config.php';

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

$db = connectDatabase($servername, $dbname, $dbusername, $dbpassword);



$meno = "";
$priezvisko = "";

if (isset($_SESSION['fullname'])) {
    $meno = $_SESSION['fullname'];
    $meno = explode(" ", $meno, 2);
    $priezvisko = $meno[1];
    $meno = $meno[0];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_SESSION['email'];
    $meno = trim($_POST["firstName"]);
    $priezvisko = trim($_POST["priezvisko"]);
    $fullname = $meno . ' ' . $priezvisko;

    $errors = "";
    $success = "";

    if (empty($meno) || empty($priezvisko)) {
        $errors = "Meno a priezvisko nesmú byť prázdne.";
    } elseif (!preg_match("/^[\p{L} '-]+$/u", $meno) || !preg_match("/^[\p{L} '-]+$/u", $priezvisko)) {
        $errors = "Meno a priezvisko môžu obsahovať len písmená, medzery, pomlčky alebo apostrofy.";
    } else {
        try {
            $sql = "SELECT id FROM users1 WHERE email = :email";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $update_sql = "UPDATE users1 SET fullname = :fullname WHERE email = :email";
                $update_stmt = $db->prepare($update_sql);
                $update_stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
                $update_stmt->bindParam(":email", $email, PDO::PARAM_STR);
                $update_stmt->execute();

                $success = "Údaje boli úspešne aktualizované!";
            } else {
                $errors = "Používateľ s týmto e-mailom neexistuje.";
            }
        } catch (PDOException $e) {
            $errors = "Chyba: " . $e->getMessage();
        }
    }
}

?>




<!DOCTYPE html>
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
        .valid-input {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: 2.25rem;
        }

        .invalid-input {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: 2.25rem;
        }

        .error {
            position: absolute;
            bottom: -1.5rem;
            margin-top: 5px;
            font-size: 0.875em;
            min-height: 20px;
            color: #dc3545;
        }


        .card-custom {
            max-width: 650px;
            margin: 3rem auto;
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .form-floating label {
            left: 12px;
        }

        .btn-primary {
            width: 100%;
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

<div class="container mt-4">
    <a href="index.php" class="btn btn-outline-primary mb-4">
        <i class="fas fa-arrow-left"></i> <?= $lang['student']['back_to_main_page'] ?>
    </a>

    <div class="card card-custom">
        <div class="card-body">
            <h4 class="card-title text-center mb-4"><i class="fas fa-user-circle"></i> <?= $lang['student']['renew_data'] ?></h4>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors) ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success-custom"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" action="studentDetails.php">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" required disabled>
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                </div>

                <div class="form-floating mb-5 ">
                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($meno) ?>" required>
                    <label for="firstName"><i class="fas fa-user"></i> <?= $lang['student']['name'] ?></label>
                    <div class="error text-danger" id="firstNameError" class="error"></div>
                </div>

                <div class="form-floating mb-5">
                    <input type="text" class="form-control" id="priezvisko" name="priezvisko" value="<?= htmlspecialchars($priezvisko) ?>" required>
                    <label for="priezvisko"><i class="fas fa-user"></i> <?= $lang['student']['surname'] ?></label>
                    <div class="error text-danger" id="priezviskoError" class="error"></div>
                </div>

                <button id="totoButton" type="submit" class="btn btn-primary"><?= $lang['student']['renew_data_button'] ?></button>
            </form>
        </div>
    </div>
</div>

<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script src="../js/bootstrap.js"></script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const firstNameInput = document.getElementById("firstName");
        const priezviskoInput = document.getElementById("priezvisko");
        const firstNameError = document.getElementById("firstNameError");
        const priezviskoError = document.getElementById("priezviskoError");

        function validateField(input, errorElement) {
            const value = input.value.trim();
            if (value.length < 2 || !/^[A-Za-zÁ-Žá-ž\s]+$/.test(value)) {
                input.classList.remove("valid-input");
                input.classList.add("invalid-input");
                errorElement.textContent = "Zadaj aspoň 2 písmená, len text.";
                return false;
            } else {
                input.classList.remove("invalid-input");
                input.classList.add("valid-input");
                errorElement.textContent = "";
                return true;
            }
        }

        firstNameInput.addEventListener("input", () => validateField(firstNameInput, firstNameError));
        priezviskoInput.addEventListener("input", () => validateField(priezviskoInput, priezviskoError));

        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            const validFirst = validateField(firstNameInput, firstNameError);
            const validLast = validateField(priezviskoInput, priezviskoError);
            if (!validFirst || !validLast) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>
