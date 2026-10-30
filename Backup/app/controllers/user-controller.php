<?php
require_once __DIR__ . '/../config/config.php';

// Captura a ação do tráfego
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'register':
        handle_register($conn);
        exit;
    case 'edit_admin':
        handle_edit_admin($conn);
        break;
    case 'edit_profile':
        handle_edit_profile($conn);
        break;
    case 'disable': {
        handle_disable($conn);
        break;
    }
    default:
        http_response_code(400);
        header("Location: " . BASE_URL . 'index.php');
        exit;
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
        $role = trim(filter_input(INPUT_POST, 'role', FILTER_DEFAULT));
        $status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT);

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($cpf) || empty($cep) || empty($address) || empty($address_number) || empty($role) || empty($status)) {
            header("Location: " . BASE_URL . "dashboard/user-create.php?alert=data_empty");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . BASE_URL . "dashboard/user-create.php?alert=register_error");
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            mysqli_stmt_close($check);
            header("Location: " . BASE_URL . "dashboard/user-create.php?alert=email_exist");
            exit;
        }
        mysqli_stmt_close($check);

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users(name, email, password, phone, cpf, cep, address, address_number, address_complement, role, status) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "sssssssssss", $name, $email, $password_hash, $phone, $cpf, $cep, $address, $address_number, $address_complement, $role, $status);

        try {
            mysqli_stmt_execute($stmt);
            header("Location: " . BASE_URL . "dashboard/user-create.php?alert=register_success");
        } catch (mysqli_sql_exception $e) {
            header("Location: " . BASE_URL . "dashboard/user-create.php?alert=register_error");
        }

        mysqli_stmt_close($stmt);
        exit;
        #SUBSTIUIR ESSE CÓDIGO POR ALGO SIMPLES COMO ATIVAR BADGE NO FORMULÁRIO
    }
}
function handle_edit_admin($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? 0;
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_DEFAULT));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_DEFAULT));
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS));
        $cpf = trim(filter_input(INPUT_POST, 'cpf', FILTER_DEFAULT));
        $cep = trim(filter_input(INPUT_POST, 'cep', FILTER_DEFAULT));
        $address = trim(filter_input(INPUT_POST, 'address', FILTER_DEFAULT));
        $address_number = trim(filter_input(INPUT_POST, 'address_number', FILTER_DEFAULT));
        $address_complement = $_POST['address_complement'] ?? null;
        $role = trim(filter_input(INPUT_POST, 'role', FILTER_DEFAULT));
        $status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT);

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (empty($id) || empty($name) || empty($email) || empty($phone) || empty($cpf) || empty($cep) || empty($address) || empty($address_number) || empty($role) || empty($status)) {
            header("Location: " . BASE_URL . "dashboard/user-edit.php?id=" . $id . "&alert=data_empty");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . BASE_URL . "dashboard/user-edit.php?id=" . $id . "&alert=update_error");
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $email, $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            mysqli_stmt_close($check);
            header("Location: " . BASE_URL . "dashboard/user-edit.php?id=" . $id . "&alert=email_exist");
            exit;
        }
        mysqli_stmt_close($check);

        $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, cpf = ?, phone = ?, cep = ?, address = ?, address_number = ?, address_complement = ?, role = ?, status = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssssssssi", $name, $email, $phone, $cpf, $cep, $address, $address_number, $address_complement, $role, $status, $id);

        try {
            mysqli_stmt_execute($stmt);

            if ($_SESSION['id'] == $id) {
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;
                header("Location: " . BASE_URL . "dashboard/users.php?alert=update_success");
            }

            header("Location: " . BASE_URL . "dashboard/users.php?alert=update_success");
        } catch (mysqli_sql_exception $e) {
            header("Location: " . BASE_URL . "dashboard/user-edit.php?id=" . $id . "&alert=update_error");
        }

        mysqli_stmt_close($stmt);
        exit;
    }
}

function handle_edit_profile($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? 0;
        $name = trim(filter_input(INPUT_POST, 'name', FILTER_DEFAULT));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_DEFAULT));
        $phone = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS));
        $cpf = trim(filter_input(INPUT_POST, 'cpf', FILTER_DEFAULT));
        $cep = trim(filter_input(INPUT_POST, 'cep', FILTER_DEFAULT));
        $address = trim(filter_input(INPUT_POST, 'address', FILTER_DEFAULT));
        $address_number = trim(filter_input(INPUT_POST, 'address_number', FILTER_DEFAULT));
        $address_complement = $_POST['address_complement'] ?? null;

        $phone = preg_replace('/[^0-9]/', '', $phone);
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        $cep = preg_replace('/[^0-9]/', '', $cep);

        if (empty($id) || empty($name) || empty($email) || empty($phone) || empty($cpf) || empty($cep) || empty($address) || empty($address_number)) {
            header("Location: " . BASE_URL . "dashboard/edit-user.php?alert=data_empty");
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: " . BASE_URL . "account/profile.php?alert=update_error");
            exit;
        }

        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? AND id != ?");
        mysqli_stmt_bind_param($check, "si", $email, $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            mysqli_stmt_close($check);
            header("Location: " . BASE_URL . "account/profile.php?alert=email_exist");
            exit;
        }
        mysqli_stmt_close($check);

        $stmt = mysqli_prepare($conn, "UPDATE users SET name = ?, email = ?, cpf = ?, phone = ?, cep = ?, address = ?, address_number = ?, address_complement = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssssssi", $name, $email, $phone, $cpf, $cep, $address, $address_number, $address_complement, $id);

        try {
            mysqli_stmt_execute($stmt);

            $_SESSION['name'] = $name;

            header("Location: " . BASE_URL . "account/profile.php?alert=update_success");
        } catch (mysqli_sql_exception $e) {
            mysqli_stmt_close($stmt);

            header("Location: " . BASE_URL . "account/profile.php?alert=update_error");
        }

        mysqli_stmt_close($stmt);
        exit;
    }
}

function handle_disable($conn)
{
    $id = $_SESSION['id'];
    $stmt = mysqli_prepare($conn, "UPDATE users SET status = 'Inativo' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    session_start();
    session_unset();
    session_destroy();

    header("Location: " . BASE_URL . '/index.php');

    mysqli_stmt_close($stmt);
    exit;
}
?>