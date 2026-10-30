<?php
require_once __DIR__ . '/../config/config.php';

// Captura a ação do tráfego
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'register':
        handle_register($conn);
        exit;
    case 'edit':
        handle_edit($conn);
        break;
    default:
        http_response_code(400);
        header("Location: " . BASE_URL . 'index.php');
        exit;
}

function handle_register($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = $_SESSION['id'];
        $package_id = filter_input(INPUT_POST, 'package_id', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = "Projeto de " . $_SESSION['name'];

        $stmt_package = mysqli_prepare($conn, "SELECT price FROM packages WHERE id = ?");
        mysqli_stmt_bind_param($stmt_package, 's', $package_id);
        mysqli_stmt_execute($stmt_package);

        $result = mysqli_stmt_get_result($stmt_package);
        $package = mysqli_fetch_assoc($result);
        $price = $package['price'];

        $stmt = mysqli_prepare($conn, "INSERT INTO projects(user_id, package_id, name, price) VALUES(?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssss', $user_id, $package_id, $name, $price);
        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        exit;
    }
}

function handle_edit($conn)
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? 0;
        $name = trim(filter_input(INPUT_POST, 'name_project', FILTER_DEFAULT));
        $status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT);

        if (empty($id) || empty($name) || empty($status)) {
            header("Location: " . BASE_URL . "dashboard/project-edit.php?id= " . $id . "&alert=empty_data");
            exit;
        }

        $stmt = mysqli_prepare($conn, "UPDATE projects SET name = ?, status = ? WHERE project_id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $name, $status, $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: " . BASE_URL . "dashboard/projects.php?alert=update_success");
        } else {
            header("Location: " . BASE_URL . "dashboard/projects-edit.php?alert=update_error");
        }
        exit;
    }
}
?>