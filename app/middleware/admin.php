<?php
require_once __DIR__ . '/../config/config.php';

$user_role = $_SESSION['role'] ?? '';

if ($user_role !== 'Admin') {
    header("Location: " . BASE_URL . 'index.php');
    exit;
}
?>