<?php
$page_title = "Nós de Luz - Pagamento Confirmado";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$confirm = $_GET['confirm'] ?? '';

if ($confirm !== "true") {
    header("Location: " . BASE_URL . "checkout/checkout-error.php?confirm=false");
}

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover w-100 limited">
        <div class="card-body">
            <div class="d-flex flex-column justify-content-center align-items-center text-center">
                <div class="icon-box xxl circle">
                    <i class="ri-check-line display-5"></i>
                </div>
                <h3 class="card-title">Pagamento efetuado com sucesso!</h3>
                <p>Clique no botão abaixo para ver seus pedidos.</p>
                <a href="<?= BASE_URL ?>account/orders.php" class="btn primary">Ver pedidos</a>
            </div>
        </div>
    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>