<?php 
$page_title = "abu sobre";
session_start();

// 1. LIGA O MOTOR: Carrega o banco de dados, o BASE_URL e o BASE_URL automáticos
require_once __DIR__ . '/app/config/config.php';

// 2. CARREGA O VISUAL: Usando o BASE_PATH para o PHP nunca se perder nas pastas

require_once BASE_PATH . '/components/layout/head.php';

require_once BASE_PATH . '/components/layout/navbar.php';

require_once BASE_PATH . '/components/sobre/hero.php';

require_once BASE_PATH . '/components/layout/footer.php';