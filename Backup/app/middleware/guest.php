<?php
require_once __DIR__ . '/../config/config.php';

$user_role = $_SESSION['role'] ?? '';

if(!empty($user_role)) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}
?>