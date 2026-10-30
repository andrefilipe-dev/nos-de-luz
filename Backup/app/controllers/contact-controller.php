<?php
require_once __DIR__ . '/../config/config.php';

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $description = trim($_POST["description"]);

    ob_start();

    require_once __DIR__ . "/../mail/templates/contact-mail.php";

    $body = ob_get_clean();

    require_once __DIR__ . "/../mail/mailer.php";

    if (send_email("alldyneltd@gmail.com", "Nova Solicitação", $body, $email)) {
        header("Location: " . BASE_URL . "index.php?alert=email_sent#contact");

        exit;
    } else {
        header("Location: " . BASE_URL . "index.php?alert=email_error#contact");

        exit;
    }
}

?>