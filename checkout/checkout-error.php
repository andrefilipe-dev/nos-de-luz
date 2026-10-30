<?php
$page_title = "Nós de Luz - Erro no Pagamento";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$confirm = $_GET['confirm'] ?? '';

if (empty($confirm) || $confirm !== "false") {
    header("Location: " . BASE_URL . "index.php");
}

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover w-100 limited">
        <div class="card-body">
            <div class="d-flex flex-column justify-content-center align-items-center text-center">
                <div class="icon-box danger xxl circle">
                    <i class="ri-close-line display-5"></i>
                </div>
                <h3 class="card-title">Erro ao realizar pagamento!</h3>
                <p>O pagamento não pode ser concluído tente novamente mais tarde.</p>
                <a href="<?= BASE_URL ?>index.php" class="btn secondary red">Voltar</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>