<?php

require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($password !== $password_confirm) {
        header("Location: " . BASE_URL . "forgot-password-reset.php?token=" . urlencode($token) . "&alert=password_mismatch");

        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ? AND reset_token_expires > NOW()");
    mysqli_stmt_bind_param($stmt, "ss", $password_hash, $token);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) === 0) {
        header("Location: " . BASE_URL . "forgot-password-reset.php?token=" . urlencode($token) . "&alert=token_expired");

        exit;
    } else {
        header("Location: " . BASE_URL . "forgot-password-reset.php?token=" . urlencode($token) . "&alert=password_reseted");

        mysqli_stmt_close($stmt);
        exit;
    }
}