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



    ?>
    <!DOCTYPE html>
    <html lang="<?= $_SESSION['lang'] ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-learning</title>
        <link rel="icon" href="../images/favicon.ico">
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../css/style.css">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>

            .quiz-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                margin-bottom: 20px;
            }

            .quiz-title {
                color: #2c3e50;
                font-weight: 600;
            }

            .take-quiz-btn {
                background: #006cdb;
                border: none;
                border-radius: 8px;
                padding: 8px 20px;
                color: white;
                font-weight: bolder;
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

    <div class="container py-5">
        <h1 class="mb-4"><?= $lang['student']['available_quizzes'] ?></h1>

        <div class="row">
            <?php
            require '/var/www/config/config.php';
            $pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);

            $stmt = $pdo->query("SELECT * FROM quizzes");

            while ($quiz = $stmt->fetch()) {
                $attemptStmt = $pdo->prepare("SELECT * FROM quiz_attempts 
                                             WHERE student_id = ? AND quiz_id = ?");
                $attemptStmt->execute([$_SESSION['user_id'], $quiz['id']]);
                $attempt = $attemptStmt->fetch();

                echo '<div class="col-md-6">
                    <div class="quiz-card p-4">
                        <h3 class="quiz-title">' . htmlspecialchars($quiz['title']) . '</h3>
                        <p class="text-muted">' .$lang['student']['created'] . ' : ' . date('d.m.Y H:i', strtotime($quiz['created_at'])) . '</p>';

                if ($attempt) {
                    echo '<p class="text-success">' .$lang['student']['your_score'] . ': ' . $attempt['score'] . '/' . $attempt['max_score'] . '</p>';
                    echo '<a href="takeQuiz.php?quiz_id=' . $quiz['id'] . '" class="take-quiz-btn">
                             ' .$lang['student']['retake_quiz'] . '
                          </a>';
                } else {
                    echo '<a href="takeQuiz.php?quiz_id=' . $quiz['id'] . '" class="take-quiz-btn">
                             ' .$lang['student']['take_quiz'] . '
                          </a>';
                }

                echo '</div></div>';
            }
            ?>
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