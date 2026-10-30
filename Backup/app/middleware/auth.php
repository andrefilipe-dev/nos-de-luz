<?php
require_once __DIR__ . '/../config/config.php';

$id = $_SESSION['id'] ?? '';

if (!empty($id)) {
    $session_timeout = 1800;

    if(isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
        session_unset();
        session_destroy();

        header("Location: " . BASE_URL . "login.php");
        exit;
    }

    $stmt = mysqli_prepare($conn, "SELECT status FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if (empty($user) || $user['status'] === 'Inativo') {
        header("Location: " . BASE_URL . "app/controllers/user-controller.php?action=disable");
        exit;
    }

    $_SESSION['last_activity'] = time();
}
?>