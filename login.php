<?php
require_once '/var/www/config/config.php';

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
    header("location: student/index.php");
    exit;
}

$conn = connectDatabase($servername, $dbname, $dbusername, $dbpassword);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT id, fullname, email, password, role, is_approved FROM users1 WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $_POST["email"], PDO::PARAM_STR);
    $errors = "";
    $pendings = "";

    if ($stmt->execute()) {
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch();
            $hashed_password = $row["password"];

            if (password_verify($_POST['password'], $hashed_password)) {

                if (($row['role'] == 'teacher') && $row['is_approved'] == 0){
                    if($_SESSION['lang'] == 'sk'){
                        $pendings = "Váš účet: " . $_POST["email"] . " čaká na schválenie administrátorom.";
                    }else{
                        $pendings = "Your account: " . $_POST["email"] . " is awaiting administrator approval.";
                    }
                }else{
                    $_SESSION["loggedin"] = true;
                    $_SESSION["fullname"] = $row['fullname'];
                    $_SESSION["email"] = $row['email'];
                    $_SESSION["role"] = $row['role'];
                    $_SESSION["succefullLogIn"] = true;

                    $_SESSION["user_id"] = $row['id'];

                    if ($row['role'] == "admin"){
                        header("Location: admin/indexAdmin.php");
                    }elseif ($row['role'] == "teacher") {
                        header("Location: teacher/indexTeacher.php?loginTeacher=true");
                    }else{
                        header("Location: student/index.php?loginUser=true");
                    }
                }
            }else {
                if($_SESSION['lang'] == 'sk'){
                    $errors = "Nesprávny email alebo heslo.";
                } else{
                    $errors = "The given email or password is incorrect.";
                }
            }
        }else{
            if($_SESSION['lang'] == 'sk'){
                $errors = "Nesprávny email alebo heslo.";
            } else{
                $errors = "The given email or password is incorrect.";
            }
        }
    }else {
        if($_SESSION['lang'] == 'sk'){
            $errors = "Ups. Niečo sa pokazilo...";
        } else{
            $errors = "Oops. Something went wrong.";
        }
    }
    unset($stmt);
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

    <title>Login</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src = "js/redirectRegister.js"></script>

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

<div id="toastForSuccess" class="toast position-fixed top-25 end-0 m-3 p-2" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check-circle me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Registrácia prebehla úspešne!' : 'Registration was successful!' ?>

    </div>
</div>

<div class="container justify-content-center align-content-center" style="height: 80vh">
    <div class="container text-center">
        <?= ($_SESSION['lang'] == 'sk') ? '<h2>Prihláste sa do systému, alebo <span class="fst-italic text-warning">zaregistrujte sa</span></h2>' : '<h2>Log in to the system or <span class="fst-italic text-warning">register</span></h2>' ?>

    </div>

    <?php if (!empty($errors)) { ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($errors); ?>
        </div>
    <?php } ?>

    <?php if (!empty($pendings)) { ?>
        <div class="alert alert-warning" role="alert">
            <?php echo htmlspecialchars($pendings); ?>
        </div>
    <?php } ?>

    <div class="row my-5 ">

        <div class="col-lg-5 col-md-0 d-none d-lg-block">
            <img src="images/loginPicture.jpg" alt="Obrázok na login form" class="img-fluid rounded-2">
        </div>

        <div class="col-lg-7 col-md-12 justify-content-center align-content-center bg-light shadow">
            <form id="myForm" method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" style="width: 90%">

                <h2 class="text-center"><?= $lang['login']['log_in'] ?></h2>
                <div class="form-floating mb-5">
                    <input type="text" class="form-control " id="email" name="email" placeholder="E-mail" required>
                    <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
                    <div class="error text-danger" id="emailError"></div>
                </div>
                <div class="form-floating mb-5">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Heslo" required>
                    <label for="password"><i class="fas fa-lock"></i> <?= $lang['login']['password'] ?></label>
                    <div class="error text-danger" id="passwordError"></div>
                </div>

                <div class="d-flex justify-content-between mb-3">
                    <button type="submit" class="btn btn-primary w-50"><?= $lang['login']['log_in_button'] ?></button>
                    <button type="button" class="btn btn-light" onclick="redirectToRegister()"><?= $lang['login']['register_button'] ?></button>
                </div>

            </form>
        </div>
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
<script src="js/loginValidation.js"></script>
<script>
    if (new URLSearchParams(window.location.search).has('registered')){
        let toast = new bootstrap.Toast(document.getElementById('toastForSuccess'));
        toast.show();
    }
</script>

</body>
</html>
