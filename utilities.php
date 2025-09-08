<?php

function isEmpty($field) {
    if (empty(trim($field))) {
        return true;
    }
    return false;
}

function userExist($db, $email) {
    $exist = false;

    $param_email = trim($email);

    $sql = "SELECT id FROM users1 WHERE email = :email";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $exist = true;
    }

    unset($stmt);

    return $exist;
}