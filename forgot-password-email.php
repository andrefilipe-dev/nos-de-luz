<?php
$page_title = 'Nós de Luz - Redefinição de Senha';
require_once __DIR__ . '/app/config/config.php';

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover limited">
        <div class="card-body">
            <form action="app/controllers/forgot-password-controller.php" method="POST">
                <div class="form-content mb-3">
                    <div class="form-header">
                        <div class="icon-box"><i class="ri-lock-password-line"></i></div>
                        <h3 class="form-title">E-mail de Recuperação</h3>
                    </div>
                    <div class="form-body row g-3">
                        <div class="form-field col-12">
                            <label for="#">E-mail</label>
                            <input class="form-control" name="email" id="email" placeholder="nome@exemplo.com"
                                type="email" minlength="3" maxlength="255" required />
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="submit" class="btn primary w-100 btnNext">
                                    Enviar E-mail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="badge secondary red <?= ($alert === "user_dont_exist") ? "d-block" : "d-none" ?>">
                    Usuário não foi encontrado.
                </span>
                <span class="badge secondary red <?= ($alert === "email_error") ? "d-block" : "d-none" ?>">
                    Erro ao enviar E-mail.
                </span>
                <span class="badge secondary green <?= ($alert === "email_sent") ? "d-block" : "d-none" ?>">
                    E-mail enviado. Verifique seu spam.
                </span>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php' ?>