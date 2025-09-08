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

$translations = [
    'sk' => [
        'email_invalid' => 'E-mail v zlom formáte.',
        'password_invalid' => 'Heslo musí mať minimálne 8 znakov, aspoň 1 veľké a 1 malé písmeno, 1 číslo.',
        'password_mismatch' => 'Heslá sa nezhodujú.',
        'firstname_invalid' => 'Meno v zlom formáte. (2 až 20 znakov)',
        'lastname_invalid' => 'Priezvisko v zlom formáte. (2 až 20 znakov)',
    ],
    'en' => [
        'email_invalid' => 'Email format is invalid.',
        'password_invalid' => 'Password must be at least 8 characters, with 1 uppercase, 1 lowercase letter, and 1 number.',
        'password_mismatch' => 'Passwords do not match.',
        'firstname_invalid' => 'Invalid first name format. (2–20 characters)',
        'lastname_invalid' => 'Invalid last name format. (2–20 characters)',
    ],
];

$messages = $translations[$_SESSION['lang']];


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}


require_once '/var/www/config/config.php';
require_once 'utilities.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = "";

    if (isEmpty($_POST['email']) === true) {
        $errors .= "Nevyplnený e-mail.\n";
    }

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors .= "Nesprávny formát e-mailu.\n";
    }

    $conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);
    if (userExist($conn, $_POST['email']) === true) {
        if($_SESSION['lang'] == 'sk'){
            $errors .= "Používateľ s týmto e-mailom už existuje.\n";
        } else{
            $errors .= "A user with this email already exists.\n";

        }
    }

    if (isEmpty($_POST['firstname']) === true) {
        $errors .= "Nevyplnené meno.\n";
    } elseif (isEmpty($_POST['lastname']) === true) {
        $errors .= "Nevyplnené priezvisko.\n";
    }

    $fullname = $_POST['firstname'] . ' ' . $_POST['lastname'];
    if (strlen($fullname) > 128) {
        $errors .= "Meno a priezvisko je príliš dlhé.\n";
    }

    $name = $_POST['firstname'];
    $surname = $_POST['lastname'];
    if(strlen($name) > 0 && !preg_match("/^[A-Za-zÁÉÍÓÚÝŔĽŠČŤŽáéíóúýŕľščťž]*$/", $name)) {
        $errors .= "Meno obsahuje nepovolené znaky.\n";
    }
    if(strlen($surname) > 0 && !preg_match("/^[A-Za-zÁÉÍÓÚÝŔĽŠČŤŽáéíóúýŕľščťž]*$/", $surname)) {
        $errors .= "Priezvisko obsahuje nepovolené znaky.\n";
    }

    if (isEmpty($_POST['password']) === true) {
        $errors .= "Nevyplnené heslo.\n";
    }

    if($_POST['password'] != $_POST['passwordRepeate']){
        $errors .= "Heslá sa nezhodujú.\n";
    }


    if (empty($errors)) {
        $sql = "INSERT INTO users1 (fullname, email, password, role, is_approved) VALUES (:fullname, :email, :password, :role, :is_approved)";

        $fullname = $_POST['firstname'] . ' ' . $_POST['lastname'];
        $email = $_POST['email'];
        $pw_hash = password_hash($_POST['password'], PASSWORD_ARGON2ID);
        $role = $_POST['role'];
        $is_approved = ($role == 'student') ? 1 : 0;

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":fullname", $fullname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->bindParam(":password", $pw_hash, PDO::PARAM_STR);
        $stmt->bindParam(":role", $role, PDO::PARAM_STR);
        $stmt->bindParam(":is_approved", $is_approved, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $reg_status = "Registracia prebehla uspesne.";
            header('Location: login.php?registered=true');
        } else {
            $reg_status = "Ups. Nieco sa pokazilo...";
        }

        unset($stmt);
    }
    unset($pdo);

}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="images/favicon.ico">

    <title>Registrácia</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/validator/13.7.0/validator.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .invalid-input, .valid-input {
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
            padding-right: 2.25rem;
        }

        .valid-input {
            border-color: #28a745;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        }

        .invalid-input {
            border-color: #dc3545;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        }

        .error {
            position: absolute;
            bottom: -2rem;
            margin-top: 0.313rem;
            font-size: 0.875em;
            min-height: 1.25rem;
        }

        .form-floating {
            margin-bottom: 2.5rem;
            position: relative;
        }

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

            .error {
                font-size: 0.7em;
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


<div class="container d-flex flex-column justify-content-center align-items-center py-5">
    <div>
        <h2 class="text-center mb-4">
            <?= ($_SESSION['lang'] == 'sk') ? 'Zaregistrujte sa do systému, aby ste sa učili o <span class="text-warning">kryptomenách</span>' : 'Register to learn about <span class="text-warning">cryptocurrencies</span>' ?>
        </h2>
    </div>
    <?php if (isset($reg_status)) { echo "<div class='alert alert-info'>$reg_status</div>"; } ?>


    <div class="bg-light shadow p-5" style="max-width: 800px; width: 100%">
        <form id="myForm" method="POST" action="">
            <h2 class="text-center mb-4">
                <?= $lang['register']['register'] ?>
            </h2>

            <?php if (!empty($errors)) { ?>
                <div class="alert alert-danger mt-3">
                    <strong><?= $lang['register']['error'] ?>:</strong><br>
                    <?php echo nl2br($errors); ?>
                </div>
            <?php } ?>

            <div class="form-floating my-5">
                <input type="text" class="form-control" id="firstname" name="firstname" maxlength="20" placeholder="Meno" required>
                <label for="firstname"><i class="fas fa-user"></i> <?= $lang['register']['name'] ?></label>
                <div class="error text-danger" id="nameError"></div>
            </div>

            <div class="form-floating mb-5">
                <input type="text" class="form-control" id="lastname" name="lastname" maxlength="20" placeholder="Priezvisko" required>
                <label for="lastname"><i class="fas fa-user"></i> <?= $lang['register']['surname'] ?></label>
                <div class="error text-danger" id="surnameError"></div>
            </div>

            <div class="form-floating mb-5">
                <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
                <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                <div class="error text-danger" id="emailError"></div>
            </div>

            <div class="form-floating mb-5">
                <input type="password" class="form-control" id="password" name="password" placeholder="Heslo" required>
                <label for="password"><i class="fas fa-user-lock"></i> <?= $lang['register']['password'] ?></label>
                <div class="error text-danger" id="passwordError"></div>
            </div>

            <div class="form-floating mb-5">
                <input type="password" class="form-control" id="passwordRepeate" name="passwordRepeate" placeholder="Zadajte heslo ešte raz" required>
                <label for="passwordRepeate"><i class="fas fa-user-lock"></i> <?= $lang['register']['password_repeate'] ?></label>
                <div class="error text-danger" id="passwordRepeateError"></div>
            </div>

            <div class="form-floating mb-5">
                <select class="form-select" id="role" name="role" required>
                    <option value="" disabled selected></option>
                    <option value="student"><?= $lang['register']['student'] ?></option>
                    <option value="teacher"><?= $lang['register']['teacher'] ?></option>
                </select>
                <label for="role"><i class="fas fa-user-tag"></i> <?= $lang['register']['role'] ?></label>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary w-75 mt-3"><?= $lang['register']['register_button'] ?></button>
            </div>
        </form>

        <p class="text-center mt-3"><?= $lang['register']['already_have_an_account'] ?> <a href="login.php"><?= $lang['register']['log_in_here'] ?></a></p>

    </div>
</div>

<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<script>
    const messages = <?php echo json_encode($messages); ?>;
</script>

<script src="js/bootstrap.js"></script>

<script src="js/registerValidation.js"></script>



</body>
</html>
