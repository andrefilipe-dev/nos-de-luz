<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = trim($_POST['email'] ?? '');

    $stmt_user = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt_user, "s", $email);
    mysqli_stmt_execute($stmt_user);
    $result = mysqli_stmt_get_result($stmt_user);


    if (mysqli_num_rows($result) === 0) {
        header("Location: " . BASE_URL . "forgot-password-email.php?alert=user_dont_exist");

        mysqli_stmt_close($stmt_user);
        exit;
    }

    $user = mysqli_fetch_assoc($result);
    $id = $user["id"];

    mysqli_stmt_close($stmt_user);

    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt_token = mysqli_prepare($conn, "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt_token, "ssi", $token, $expires, $id);
    mysqli_stmt_execute($stmt_token);

    mysqli_stmt_close($stmt_token);

    $link = BASE_URL . "forgot-password-reset.php?token=" . $token;

    ob_start();

    require_once __DIR__ . "/../mail/templates/password-reset-mail.php";

    $body = ob_get_clean();

    require_once __DIR__ . "/../mail/mailer.php";

    if (send_email($email, "Recuperação de Senha", $body)) {
        header("Location: " . BASE_URL . "forgot-password-email.php?alert=email_sent");

        exit;
    } else {
        header("Location: " . BASE_URL . "forgot-password-email.php?alert=email_error");

        exit;
    }

}

?>