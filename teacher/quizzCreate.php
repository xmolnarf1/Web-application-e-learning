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
if ($_SESSION['role'] == "student") {
    header("Location: ../student/index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Quiz | Krypto E-learning</title>
    <link rel="icon" href="../images/favicon.ico">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .teacher-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
        .teacher-navbar {
            background: #3498db;
        }

        body {
            background-color: white;
        }

        .quiz-container {
            max-width: 56.25rem;
            margin: 2rem auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 2rem;
        }

        .quiz-header {
            padding-bottom: 1rem;
            margin-bottom: 2rem;
        }

        .quiz-title {
            color: #3498db;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .question-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #3498db;
            /*transition: all 0.3s ease;*/
        }

        .question-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .answer-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #eee;
        }

        .btn-primary {
            background: #198754;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #146d44;
            box-shadow: 0 5px 15px rgba(26, 41, 128, 0.3);
        }

        .correct-toggle {
            display: flex;
            align-items: center;
            margin-left: 1rem;
            color: #1a2980;
            font-weight: 600;
        }

        .correct-toggle input {
            margin-right: 0.5rem;
        }

        .add-question-btn {
            background: #f7931a;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-question-btn:hover {
            background: #e07e0f;
            transform: translateY(-2px);
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            font-size: 1.1rem;
            margin-top: 1.5rem;
        }

        .course-select {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid #ddd;
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
        <i class="fas fa-check me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Test bol úspešne pridaný!' : 'The test was uploaded successfully!' ?>
    </div>
</div>


<div class="container">
    <div class="quiz-container">
        <div class="quiz-header">
            <h1 class="quiz-title">
                <i class="fas fa-plus-circle me-2"></i><?= $lang['teacher']['create_new_test'] ?>
            </h1>
        </div>

        <form action="createQuiz.php" method="POST">
            <div class="mb-4">
                <label for="quizTitle" class="form-label fw-bold"><?= $lang['teacher']['title_test'] ?></label>
                <input type="text" class="form-control" id="quizTitle" name="title" required>
            </div>

            <div class="mb-4">
                <label for="courseSelect" class="form-label fw-bold"><?= $lang['teacher']['course'] ?></label>
                <select class="form-select" id="courseSelect" name="course_id">
                    <option value="" selected><?= $lang['teacher']['select_course'] ?></option>
                    <?php
                    require '/var/www/config/aconfig.php';
                    $pdo = connectDatabase($servername, $dbname, $dbusername, $dbpassword);
                    $stmt = $pdo->query("SELECT * FROM courses WHERE teacher_id = '" . $_SESSION['user_id'] . "'");
                    while ($row = $stmt->fetch()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['title']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="questions"></div>

            <div class="d-flex justify-content-center mt-4">
                <button type="button" class="add-question-btn" onclick="addQuestion()">
                    <i class="fas fa-plus"></i> <?= $lang['teacher']['add_question'] ?>
                </button>
            </div>

            <button type="submit" class="btn btn-primary submit-btn">
                <i class="fas fa-save me-2"></i> <?= $lang['teacher']['save_test'] ?>
            </button>
        </form>
    </div>
</div>


<footer class="footerOMne mt-5 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>

<?php if (isset($_SESSION['test_created']) && $_SESSION['test_created']): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('toastForSuccessUpload');
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    </script>
    <?php
    unset($_SESSION['test_created']);
endif;
?>

<script>
    function addQuestion() {
        const container = document.getElementById('questions');
        const index = container.children.length;
        const qDiv = document.createElement('div');
        qDiv.className = 'question-card';
        qDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><?= $lang['teacher']['question'] ?> ${index + 1}</h5>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentNode.parentNode.remove()">
                    <i class="fas fa-trash"></i> <?= $lang['teacher']['odstranit'] ?>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= $lang['teacher']['test_of_the_question'] ?></label>
                <input type="text" class="form-control" name="questions[${index}][text]" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><?= $lang['teacher']['type_of_the_question'] ?></label>
                <select class="form-select" name="questions[${index}][type]" onchange="updateAnswerInputs(this, ${index})">
                    <option value="single"><?= $lang['teacher']['one_good_answer'] ?></option>
                    <option value="multiple"><?= $lang['teacher']['multiple_good_answers'] ?></option>
                </select>
            </div>
            <div class="answers-container" id="answers-${index}">
                ${generateAnswerInputs(index, 'single')}
            </div>
        `;
        container.appendChild(qDiv);
    }

    function generateAnswerInputs(index, type) {
        let inputs = '';
        for (let i = 0; i < 4; i++) {
            let inputType;
            if (type === 'single') {
                inputType = 'radio';
            } else {
                inputType = 'checkbox';
            }


            let correctnessName;
            if (type === 'single') {
                correctnessName = `questions[${index}][correct]`;
            } else {
                correctnessName = `questions[${index}][answers][${i}][is_correct]`;
            }

            inputs += `
                <div class="answer-item">
                    <input type="text" class="form-control" name="questions[${index}][answers][${i}][text]"
                           placeholder="<?= $lang['teacher']['answer'] ?> ${i + 1}" required>
                    <div class="correct-toggle">
                        <input type="${inputType}" name="${correctnessName}" value="${i}">
                        <span><?= $lang['teacher']['good_answer'] ?></span>
                    </div>
                </div>
            `;
        }
        return inputs;
    }

    function updateAnswerInputs(selectElement, index) {
        const type = selectElement.value;
        const questionCard = selectElement.closest('.question-card');
        const answersDiv = questionCard.querySelector('.answers-container');
        answersDiv.innerHTML = generateAnswerInputs(index, type);
    }

    document.addEventListener('DOMContentLoaded', function() {
        addQuestion();
    });



</script>

<script src="../js/bootstrap.js"></script>
</body>
</html>