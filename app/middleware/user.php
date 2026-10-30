<?php
require_once __DIR__ . '/../config/config.php';

$user_role = $_SESSION['role'] ?? '';

if ($user_role !== 'Cliente') {
    header("Location: " . BASE_URL . 'register.php');
    exit;
}
?>