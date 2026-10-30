<?php
require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'login':
        handle_login($conn);
        break;
    case 'register':
        handle_register($conn);
        break;
    case 'logout':
        handle_logout();
        break;
    default:
        http_response_code(400);
        header("Location: " . BASE_URL . "index.php?error=invalid_action");
        exit;
}

function handle_login($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        if (empty($email) || empty($password)) {
            header("Location: " . BASE_URL . "register.php?alert=data_empty");
            exit;
        }

        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if (!$user || !password_verify($password, $user['password'])) {
            header("Location: " . BASE_URL . "login.php?alert=data_invalid");
            exit;
        }

        if ($user['status'] === 'Inativo') {
            header("Location: " . BASE_URL . "login.php?alert=user_disabled");
            exit;
        }

        $_SESSION['id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['status'] = $user['status'];

        if ($_SESSION['role'] === 'Admin') {
            header("Location: " . BASE_URL . 'dashboard/index.php');
        } else {
            header("Location: " . BASE_URL . 'account/profile.php');
        }

        mysqli_stmt_close($stmt);
        exit;
    }
}

function handle_register($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = $_POST['password'] ?? '';
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS));
        $cpf = trim(filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS));
        $cep = trim(filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS));
        $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS));
        $address_number = trim(filter_input(INPUT_POST, 'address_number', FILTER_SANITIZE_SPECIAL_CHARS));
        $address_complement = $_POST['address_complement'] ?? null;

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($cpf) || empty($cep) || empty($address) || empty($address_number)) {
            header("Location: " . BASE_URL . "register.php?alert=data_empty");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . BASE_URL . "register.php?alert=email_invalid");
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            mysqli_stmt_close($check);
            header("Location: " . BASE_URL . "register.php?alert=email_exist");
            exit;
        }
        mysqli_stmt_close($check);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users(name, email, password, phone, cpf, cep, address, address_number, address_complement) VALUES(?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssssssss", $name, $email, $password_hash, $phone, $cpf, $cep, $address, $address_number, $address_complement);
        mysqli_stmt_execute($stmt);

        header("Location: " . BASE_URL . "login.php");

        mysqli_stmt_close($stmt);
        exit;

    }
}

function handle_logout()
{
    if(session_status() === PHP_SESSION_NONE) {
        session_start();
    };
    session_unset();
    session_destroy();

    header("Location: " . BASE_URL . "index.php");
    exit;
}
?>