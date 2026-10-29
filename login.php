<?php
$page_title = "Nós de Luz - Login";
require_once __DIR__ . '/app/config/config.php';
require_once BASE_PATH . '/app/middleware/guest.php';

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover limited">
        <div class="card-body">
            <form action="app/controllers/auth-controller.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="form-content mb-3">
                    <div class="form-header">
                        <div class="icon-box"><i class="ri-login-box-line"></i></div>
                        <h3 class="form-title">Entrar na Conta</h3>
                    </div>
                    <div class="form-body row g-3">
                        <div class="form-field col-12">
                            <label for="#">E-mail</label>
                            <input class="form-control" name="email" id="email" placeholder="nome@exemplo.com"
                                type="email" minlength="3" maxlength="255" required />
                        </div>
                        <div class="form-field col-12">
                            <label for="#">Senha</label>
                            <input class="form-control" name="password" id="password" placeholder="••••••••"
                                type="password" minlength="8" maxlength="100" required />
                        </div>
                        <a href="<?= BASE_URL ?>forgot-password-email.php" class="form-link">Esqueceu sua senha?</a>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn primary w-100 btnNext">
                                    Login
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center">Não é um cliente? <span><a href="register.php">Cadastrar-se</a></span></p>
                <span class="badge secondary red <?= ($alert === "data_empty") ? "d-block" : "d-none"?>">
                    Preencha todos os campos obrigatórios.
                </span>
                <span class="badge secondary red <?= ($alert === "data_invalid") ? "d-block" : "d-none"?>">
                    E-mail ou Senha inválidos.
                </span>
                <span class="badge secondary red <?= ($alert === "user_disabled") ? "d-block" : "d-none"?>">
                    Usuário está desativado. Contate o suporte.
                </span>
            </form>
        </div>
    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>