<?php
$page_title = 'Nós de Luz - Redefinição de Senha';
require_once __DIR__ . '/app/config/config.php';

$token = $_GET['token'] ?? '';

if(empty($token)) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover limited">
        <div class="card-body">
            <form action="app/controllers/reset-password-controller.php" method="POST">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <div class="form-content mb-3">
                    <div class="form-header">
                        <div class="icon-box"><i class="ri-lock-password-line"></i></div>
                        <h3 class="form-title">Redefinir Senha</h3>
                    </div>
                    <div class="form-body row g-3">
                        <div class="form-field col-12">
                            <label for="password">Nova Senha</label>
                            <input class="form-control" name="password" id="password" placeholder="••••••••"
                                type="password" minlength="8" maxlength="100" required />
                        </div>
                        <div class="form-field col-12">
                            <label for="password_confirm">Confirmar nova Senha</label>
                            <input class="form-control" name="password_confirm" id="password_confirm" placeholder="••••••••"
                                type="password" minlength="8" maxlength="100" required />
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn primary w-100 btnNext">
                                    Redefinir Senha
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="badge secondary red <?= ($alert === "password_mismatch") ? "d-block" : "d-none" ?>">
                    As Senhas não condizem.
                </span>
                <span class="badge secondary red <?= ($alert === "token_expired") ? "d-block" : "d-none" ?>">
                    Token inválido ou expirado.
                </span>
                <span class="badge secondary green <?= ($alert === "password_reseted") ? "d-block" : "d-none" ?>">
                    Senha alterada com sucesso.
                </span>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php' ?>