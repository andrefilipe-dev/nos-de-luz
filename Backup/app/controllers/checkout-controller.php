<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $user_name = $_SESSION[ 'name'];
    $email = $_SESSION['email'];
    $package_id = filter_input(INPUT_POST, 'package_id', FILTER_SANITIZE_SPECIAL_CHARS);
    $name = "Projeto de " . $_SESSION['name'];

    $stmt_package = mysqli_prepare($conn, "SELECT name, price, description FROM packages WHERE id = ?"); // Busquei o 'name' também para usar no e-mail!
    mysqli_stmt_bind_param($stmt_package, 's', $package_id);
    mysqli_stmt_execute($stmt_package);

    $result = mysqli_stmt_get_result($stmt_package);
    $package = mysqli_fetch_assoc($result);
    $package_name = $package['name'];
    $price = $package['price'];
    $description = $package['description'];

    $stmt = mysqli_prepare($conn, "INSERT INTO projects(user_id, package_id, name, price) VALUES(?,?,?,?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $user_id, $package_id, $name, $price);
    $success = mysqli_stmt_execute($stmt);

    if ($success) {
        ob_start();
        require __DIR__ . "/../mail/templates/checkout-user-mail.php";
        $body_user = ob_get_clean();
        require_once __DIR__ . "/../mail/mailer.php";
        send_email($email, "Pagamento Confirmado", $body_user);


        ob_start();
        require __DIR__ . "/../mail/templates/checkout-admin-mail.php";
        $body_admin = ob_get_clean();
        send_email("alldyneltd@gmail.com", "Novo Projeto", $body_admin);

        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt_package);

        header("Location: " . BASE_URL . "checkout/checkout-success.php?confirm=true");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        mysqli_stmt_close($stmt_package);

        header("Location: " . BASE_URL  . "checkout/checkout-error.php?confirm=false");
        exit;
    }
}

?>