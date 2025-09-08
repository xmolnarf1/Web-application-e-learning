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


        input.is-invalid {
            border: 2px solid #dc3545 !important;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
            display: none;
        }

        .invalid-feedback.active {
            display: block;
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
                    <a class="nav-link" href="../student/index.php">
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
        <i class="fas fa-check-circle me-2"></i> <?= ($_SESSION['lang'] == 'sk') ? 'Prihlásenie prebehlo úspešne!' : 'Login was successful!' ?>

    </div>
</div>


<div id="toastForDeleteUserSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i> <?= ($_SESSION['lang'] == 'sk') ? 'Používateľ bol úspešne odstránený!' : 'User has been successfully deleted!' ?>

    </div>
</div>


<div id="toastForDeleteUserFail" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #dc3545;; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <?= ($_SESSION['lang'] == 'sk') ? 'Používateľa sa nedalo odstrániť!' : 'The user could not be deleted!' ?>


    </div>
</div>


<div id="toastForEditUserSuccess" class="toast position-fixed top-25 end-0 m-3 p-1" role="alert" aria-live="assertive" aria-atomic="true" style="background: #20c997; color: white; font-size: 1em; box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.5)">
    <div class="toast-body">
        <i class="fas fa-check me-2"></i> <?= ($_SESSION['lang'] == 'sk') ? 'Údaje používateľa sa úspešne aktualizovali!' : 'User data updated successfully!' ?>
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
                    <a href="addUser.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> <?= $lang['register']['add_user'] ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="container mt-5">
    <div class="table-responsive">
        <h2><?= $lang['admin']['users_edit_table_title'] ?></h2>
        <table id="usersTable" class="table table-striped table-bordered" style="width:100%">
            <thead class="mt-3" style="background: #33373d">
            <tr>
                <th>ID</th>
                <th><?= $lang['admin']['name'] ?></th>
                <th>Email</th>
                <th><?= $lang['admin']['role'] ?></th>
                <th><?= $lang['admin']['approved'] ?></th>
                <th><?= $lang['admin']['edit_table_title'] ?></th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><?= $lang['admin']['approve_remove'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?= ($_SESSION['lang'] == 'sk') ? 'Ste si istý, že chcete odstrániť tohto používateľa?' : 'Are you sure you want to delete this user?' ?>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['admin']['cancel'] ?></button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn"><?= $lang['admin']['remove_button'] ?></button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editUserModalLabel"><?= $lang['admin']['edit_user'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id"> <!-- Hidden user ID -->

                    <div class="mb-5">
                        <label for="editFullname" class="form-label"><?= $lang['admin']['name'] ?>:</label>
                        <input type="text" class="form-control" id="editFullname" maxlength="40" name="fullname" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="mb-5">
                        <label for="editEmail" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="editEmail" name="email" required>
                        <div class="invalid-feedback" id="emailError"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= $lang['admin']['cancel'] ?></button>
                <button type="button" class="btn btn-primary" id="saveUserChanges"><?= $lang['admin']['save_edits'] ?></button>
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

    function validateEditForm() {
        let isValid = true;
        const fullnameInput = document.getElementById('editFullname');
        const emailInput = document.getElementById('editEmail');
        const trimmedName = fullnameInput.value.trim();

        const nameRegex = /^[A-Za-zÁÉÍÓÚÝŔŮĽŠČŤŽŇÄÔĎŤÚÝŽáéíóúýŕůľščťžňäôďťúýž\s\-]+$/;
        if (!nameRegex.test(trimmedName)) {
            showError(fullnameInput, "<?= addslashes($lang['admin']['name_error'] ?? 'Please enter a valid name (letters, spaces, or hyphens).') ?>");
            isValid = false;
        } else if (trimmedName.length < 4 || trimmedName.length > 40) {
            showError(fullnameInput, "<?= addslashes($lang['admin']['name_length_error'] ?? 'Name must be between 2-50 characters.') ?>");
            isValid = false;
        } else {
            clearError(fullnameInput);
        }

        if (!validateEmail(emailInput.value.trim())) {
            showError(emailInput, "<?= addslashes($lang['admin']['email_error'] ?? 'Please enter a valid email address.') ?>");
            isValid = false;
        } else {
            clearError(emailInput);
        }

        return isValid;
    }

    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback) {
            feedback.textContent = message;
            feedback.classList.add('active');
        }
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        const feedback = input.nextElementSibling;
        if (feedback) {
            feedback.textContent = '';
            feedback.classList.remove('active');
        }
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email.toLowerCase());
    }


    <?php if (isset($_SESSION["succefullLogIn"]) && $_SESSION["succefullLogIn"] == true): ?>
    document.addEventListener('DOMContentLoaded', function() {
        let toast = new bootstrap.Toast(document.getElementById("toastForSuccess"));
        toast.show();
    });
    <?php unset($_SESSION["succefullLogIn"]); ?>
    <?php endif; ?>

    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            "ajax": "fetchUsers.php",
            "columns": [
                { "data": "id" },
                { "data": "fullname" },
                { "data": "email" },
                { "data": "role" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        if (row.role === 'student' || row.role === 'teacher') {
                            var approveBtn = row.is_approved ?
                                '<button class="btn btn-success btn-sm me-1" disabled><?= $lang['admin']['approved_button'] ?></button>' :
                                '<button class="btn btn-warning btn-sm me-1 approve-btn"><?= $lang['admin']['approve_button'] ?></button>';
                            return approveBtn;
                        }else{
                            return '<button class="btn btn-success btn-sm me-1" disabled><?= $lang['admin']['approved_button'] ?></button>';

                        }
                    },
                },
                {
                    "data": null,
                    "defaultContent": `
                        <div class="d-flex justify-content-center gap-2">
                            <button class='btn me-2 btn-danger btn-sm delete-btn'>
                                <i class='fas fa-trash-alt'></i> <?= $lang['teacher']['odstranit'] ?>
                            </button>
                            <button class='btn btn-primary btn-sm edit-btn'>
                                <i class='fas fa-pen'></i> <?= $lang['teacher']['upravit'] ?>
                            </button>
                        </div>
                    `,
                    "orderable": false
                }
            ]
        });

        let selectedUserId = null;

        $('#usersTable tbody').on('click', '.delete-btn', function() {
            var row = $(this).closest('tr');
            var data = table.row(row).data();
            selectedUserId = data.id;

            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(function() {
            if (selectedUserId) {
                $.ajax({
                    url: 'deleteUserAdmin.php',
                    type: 'POST',
                    data: { id: selectedUserId },
                    dataType: 'json',
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');

                        if (response.success) {
                            let toastForDeleteSuccess = document.getElementById("toastForDeleteUserSuccess");
                            toastForDeleteSuccess = new bootstrap.Toast(toastForDeleteSuccess);
                            toastForDeleteSuccess.show();

                            table.ajax.reload();
                        } else {

                        }
                    },
                    error: function() {
                        $('#confirmDeleteModal').modal('hide');
                        let toastForDeleteFail = document.getElementById("toastForDeleteUserFail");
                        toastForDeleteFail = new bootstrap.Toast(toastForDeleteFail);
                        toastForDeleteFail.show();
                    }
                });
            }
        });

        $('#usersTable tbody').on('click', '.edit-btn', function() {
            var row = $(this).closest('tr');
            var data = table.row(row).data();

            $('#editUserId').val(data.id);
            $('#editFullname').val(data.fullname);
            $('#editEmail').val(data.email);

            $('#editUserModal').modal('show');
        });

        $('#saveUserChanges').click(function() {
            if (!validateEditForm()) {
                return false;
            }

            var formData = $('#editUserForm').serialize();

            $.ajax({
                url: 'updateUser.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $('#editUserModal').modal('hide');

                    if (response.success) {
                        let toast = document.getElementById('toastForEditUserSuccess');
                        toast = new bootstrap.Toast(toast);
                        toast.show();
                        table.ajax.reload();
                    } else {
                        if (response.errors) {
                            if (response.errors.fullname) {
                                showError(document.getElementById('editFullname'), response.errors.fullname);
                            }
                            if (response.errors.email) {
                                showError(document.getElementById('editEmail'), response.errors.email);
                            }
                        }
                    }
                },
                error: function() {
                }
            });
        });
        $('#editUserModal').on('shown.bs.modal', function() {
            $('#editFullname').on('input', function() {
                const trimmed = $(this).val().trim();
                if (trimmed.length > 0) {
                    validateEditForm();
                } else {
                    clearError(this);
                }
            });

            $('#editEmail').on('input', function() {
                const trimmed = $(this).val().trim();
                if (trimmed.length > 0) {
                    validateEditForm();
                } else {
                    clearError(this);
                }
            });
        });

        $('#editUserModal').on('hidden.bs.modal', function() {
            clearError(document.getElementById('editFullname'));
            clearError(document.getElementById('editEmail'));
        });

        $('#usersTable tbody').on('click', '.approve-btn', function() {
            var row = $(this).closest('tr');
            var data = table.row(row).data();

            $.ajax({
                url: 'approveUser.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id: data.id }),
                dataType: 'json',
                success: function(response) {
                    if (response && response.success) {
                        table.ajax.reload();
                    } else {

                    }
                },
                error: function() {

                }
            });
        });
    });
</script>
</body>
</html>