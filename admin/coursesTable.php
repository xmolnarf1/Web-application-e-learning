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
if ($_SESSION['role'] == "teacher") {
    header("Location: ../teacher/indexTeacher.php?loginTeacher=true");
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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .dataTables_paginate {
            margin-top: 1rem;
            text-align: center;
        }

        .dataTables_paginate .paginate_button {
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            color: #007bff;
            font-size: 1rem;
            padding: 0.5em 1em;
            margin: 0 0.25em;
            cursor: pointer;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
        }

        .dataTables_paginate .paginate_button:hover {
            background-color: #007bff;
            color: white;
        }

        .dataTables_paginate .paginate_button:active {
            background-color: #0056b3;
        }

        .dataTables_paginate .paginate_button.current {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }

        .dataTables_paginate .previous, .dataTables_paginate .next {
            font-weight: bold;
        }

        th{
            color: white;
        }

        .admin-navbar {
            background: #2c3e50;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .welcome-card {
            background: white;
            border-left: 5px solid #e74c3c;
        }

        .badge-admin {
            background-color: #e74c3c;
        }
        .admin-icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }
    </style>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark admin-navbar">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">
            <i class="fas fa-shield-alt me-2"></i>Admin Panel
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navHamburger">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navHamburger">
            <ul class="navbar-nav ms-auto m-2">
                <li class="nav-item me-2">
                    <a class="nav-link" href="indexAdmin.php">
                        <i class="fas fa-users admin-icon"></i><?= $lang['admin']['users_overview'] ?>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link active" href="coursesTable.php">
                        <i class="fas fa-book admin-icon"></i><?= $lang['admin']['courses_overview'] ?>
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link" href="testsTable.php">
                        <i class="fas fa-question-circle admin-icon"></i><?= $lang['teacher']['available_material_t'] ?>
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

<div id="toastForSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Úspešne uložené!' : 'Successfully saved!' ?>
    </div>
</div>

<div id="toastForDeleteSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Kurz bol úspešne odstránený!' : 'Course was successfully deleted!' ?>
    </div>
</div>

<div id="toastForDeleteFail" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #dc3545; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-exclamation-circle me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Kurz sa nepodarilo odstrániť!' : 'Failed to delete course!' ?>
    </div>
</div>



<div id="toastForEditSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Kurz bol úspešne aktualizovaný!' : 'Course was successfully edited!' ?>
    </div>
</div>


<div id="toastForEditFail" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i>
        <?= ($_SESSION['lang'] == 'sk') ? 'Kurz sa nepodarilo aktualizovať!' : 'Failed to edit course!' ?>
    </div>
</div>


<div class="container my-4">
    <div class="welcome-card p-4 shadow-sm">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="fas fa-user-cog text-primary me-2"></i>
                    <?= $lang['admin']['welcome'] ?>, <span class="text-danger"><?= htmlspecialchars($user_name) ?></span>
                </h2>
                <p class="lead text-muted mb-0">
                    <?= $lang['admin']['you_are_signed_in_as'] ?> <span class="badge badge-admin"><?= htmlspecialchars($role) ?></span>
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                    <a href="indexAdmin.php" class="btn btn-primary">
                        <i class="fas fa-users me-1"></i> <?= $lang['admin']['users_edit'] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="courses-container">
        <div class="table-responsive">
            <table id="coursesTable" class="table table-striped table-bordered" style="width:100%">
                <thead class="mt-3" style="background: #33373d">
                <tr>
                    <th>ID</th>
                    <th><?= $lang['teacher']['nazov'] ?></th>
                    <th><?= $lang['teacher']['popis'] ?></th>
                    <th><?= $lang['teacher']['vytvorene'] ?></th>
                    <th><?= $lang['teacher']['vyucujuci'] ?></th>
                    <th><?= $lang['teacher']['subor'] ?></th>
                    <th><?= $lang['teacher']['akcie'] ?></th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><?= ($_SESSION['lang'] == 'sk') ? 'Potvrdenie odstránenia' : 'Delete Confirmation' ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= ($_SESSION['lang'] == 'sk') ? 'Ste si istý, že chcete odstrániť tento kurz?' : 'Are you sure you want to delete this course?' ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= ($_SESSION['lang'] == 'sk') ? 'Zrušiť' : 'Cancel' ?></button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><?= $lang['teacher']['odstranit'] ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editCourseModalLabel"><?= $lang['teacher']['upravit'] ?> <?= $lang['teacher']['available_material_modal'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCourseForm">
                    <input type="hidden" id="editCourseId" name="id">

                    <div class="mb-3">
                        <label for="editCourseTitle" class="form-label"><?= $lang['teacher']['nazov'] ?>:</label>
                        <input type="text" class="form-control" id="editCourseTitle" maxlength="25" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="editCourseDescription" class="form-label"><?= $lang['teacher']['popis'] ?>:</label>
                        <textarea class="form-control" id="editCourseDescription" name="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editCourseFile" class="form-label"><?= ($_SESSION['lang'] == 'sk') ? 'Nový súbor (voliteľné)' : 'New file (optional)' ?>:</label>
                        <input type="file" class="form-control" id="editCourseFile" name="course_file" accept=".pdf, .docx, .txt, .html">
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= ($_SESSION['lang'] == 'sk') ? 'Zrušiť' : 'Cancel' ?></button>
                <button type="button" class="btn btn-primary" id="saveCourseChanges"><?= ($_SESSION['lang'] == 'sk') ? 'Uložiť zmeny' : 'Save changes' ?></button>
            </div>
        </div>
    </div>
</div>


<footer class="footerOMne mt-3 text-white p-3">
    <div class="container text-center">
        <p>&copy; 2025 Krypto E-learning | Autor: <i>Fridrich Molnár</i> <br> <strong>xmolnarf1</strong></p>
    </div>
</footer>


<script src="../js/bootstrap.js"></script>
<script>
    $(document).ready(function() {
        var coursesTable = $('#coursesTable').DataTable({
            "ajax": "fetchCourses.php",
            "columns": [
                { "data": "id" },
                { "data": "title" },
                {
                    "data": "description",
                    "render": function(data, type, row) {
                        return type === 'display' && data.length > 50 ?
                            data.substr(0, 50) + '...' : data;
                    }
                },
                {
                    "data": "created_at",
                    "render": function(data) {
                        return new Date(data).toLocaleDateString();
                    }
                },
                { "data": "teacher_name" },
                {
                    "data": "file_path",
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            var filename = data.split('/').pop();
                            return '<a href="' + data + '" download class="btn btn-sm btn-info"><?= $lang['teacher']['stiahnut'] ?></a>';
                        }
                        return data;
                    }
                },
                {
                    "data": null,
                    "defaultContent": `
                        <div class="d-flex justify-content-center gap-2">
                            <button class='btn btn-danger btn-sm delete-course-btn shadow-sm'>
                                <i class='fas fa-trash-alt'></i> <?= $lang['teacher']['odstranit'] ?>
                            </button>
                            <button class='btn btn-primary btn-sm edit-course-btn shadow-sm'>
                                <i class='fas fa-pen'></i> <?= $lang['teacher']['upravit'] ?>
                            </button>
                        </div>
                    `
                    ,
                    "orderable": false
                }
            ]
        });

        $('#coursesTable tbody').on('click', '.delete-course-btn', function() {
            var row = $(this).closest('tr');
            var data = coursesTable.row(row).data();
            selectedCourseId = data.id;

            $('#confirmDeleteModal').modal('show');
        });


        $('#confirmDeleteBtn').click(function() {
            if (selectedCourseId) {
                $.ajax({
                    url: 'deleteCourse.php',
                    type: 'POST',
                    data: { id: selectedCourseId },
                    dataType: 'json',
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');

                        if (response.success) {
                            let toastForDeleteSuccess = document.getElementById("toastForDeleteSuccess");
                            toastForDeleteSuccess = new bootstrap.Toast(toastForDeleteSuccess);
                            toastForDeleteSuccess.show();

                            selectedCourseId = null;

                            coursesTable.ajax.reload();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        let toastForDeleteFail = document.getElementById("toastForDeleteFail");
                        toastForDeleteFail = new bootstrap.Toast(toastForDeleteFail);
                        toastForDeleteFail.show();
                    }
                });
            }
        });

        $('#coursesTable tbody').on('click', '.edit-course-btn', function () {
            var row = $(this).closest('tr');
            var data = coursesTable.row(row).data();

            $('#editCourseId').val(data.id);
            $('#editCourseTitle').val(data.title);
            $('#editCourseDescription').val(data.description);

            $('#editCourseModal').modal('show');
        });

        $('#saveCourseChanges').click(function () {
            if (!validateEditCourseForm()) {
                return false;
            }

            var formData = $('#editCourseForm').serialize();

            $.ajax({
                url: 'updateCourse.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function (response) {
                    $('#editCourseModal').modal('hide');

                    if (response.success) {
                        let toast = document.getElementById('toastForEditSuccess');
                        toast = new bootstrap.Toast(toast);
                        toast.show();
                        coursesTable.ajax.reload();
                    } else if (response.errors) {
                        if (response.errors.title) {
                            showError(document.getElementById('editCourseTitle'), response.errors.title);
                        }
                        if (response.errors.description) {
                            showError(document.getElementById('editCourseDescription'), response.errors.description);
                        }
                    }
                },
                error: function () {
                    console.error('Error while updating course.');
                }
            });
        });

        $('#editCourseModal').on('shown.bs.modal', function () {
            $('#editCourseTitle, #editCourseDescription').on('input', function () {
                if ($(this).val().trim().length > 0) {
                    validateEditCourseForm();
                } else {
                    clearError(this);
                }
            });
        });

        $('#editCourseModal').on('hidden.bs.modal', function () {
            clearError(document.getElementById('editCourseTitle'));
            clearError(document.getElementById('editCourseDescription'));
        });

    });


    function validateEditCourseForm() {
        let valid = true;

        const titleInput = document.getElementById('editCourseTitle');
        const descInput = document.getElementById('editCourseDescription');

        if (titleInput.value.trim() === '') {
            showError(titleInput, 'Title is required.');
            valid = false;
        } else {
            clearError(titleInput);
        }

        if (descInput.value.trim() === '') {
            showError(descInput, 'Description is required.');
            valid = false;
        } else {
            clearError(descInput);
        }

        return valid;
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.classList.add('invalid-feedback');
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.innerText = message;
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.remove();
        }
    }
</script>
</body>
</html>