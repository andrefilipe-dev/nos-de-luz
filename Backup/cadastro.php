<?php
$page_title = 'Nós de Luz - Cadastro';
require_once __DIR__ . '/app/config/config.php';

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
?>

<div class="custom-main-container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card no-hover limited">
        <div class="card-body">
            <form action="app/controllers/auth-controller.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="form-content step active mb-3">
                    <div class="form-header">
                        <div class="icon-box"><i class="ri-user-line"></i></div>
                        <h3 class="form-title">Informações Pessoais</h3>
                    </div>
                    <div class="form-body row g-3">
                        <div class="form-field col-12">
                            <label for="#">Nome e Sobrenome</label>
                            <input class="form-control" name="name" id="name" placeholder="Ex: Rogério..." type="text"
                                minlength="3" maxlength="100" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">E-mail</label>
                            <input class="form-control" name="email" id="email" placeholder="nome@exemplo.com"
                                type="email" maxlength="255" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">Senha</label>
                            <input class="form-control" name="password" id="password" placeholder="••••••••"
                                type="password" minlength="8" maxlength="100" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">Telefone</label>
                            <input class="form-control" name="phone" id="phone" placeholder="(00) 00000-0000"
                                type="text" minlength="15" maxlength="15" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">CPF</label>
                            <input class="form-control" name="cpf" id="cpf" placeholder="000.000.000-00" type="text"
                                minlength="14" maxlength="14" required />
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn primary w-100 btnNext">
                                    Próximo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="ghost-number">
                        <h1>01</h1>
                    </div>
                </div>
                <div class="form-content step mb-3 h-100">
                    <div class="form-header">
                        <div class="icon-box"><i class="ri-map-pin-line"></i></div>
                        <h3 class="form-title">Endereço</h3>
                    </div>
                    <div class="form-body row g-3">
                        <div class="form-field col-12">
                            <label for="#">Endereço</label>
                            <input class="form-control" name="address" id="address" placeholder="Rua, Avenida..."
                                type="text" minlength="5" maxlength="150" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">CEP</label>
                            <input class="form-control" name="cep" id="cep" placeholder="00000-000" type="text"
                                minlength="9" maxlength="9" required />
                        </div>
                        <div class="form-field col-6">
                            <label for="#">Número</label>
                            <input class="form-control" name="address_number" id="address_number" placeholder="123"
                                type="text" maxlength="10" required />
                        </div>
                        <div class="form-field col-12">
                            <label for="#">Complemento</label>
                            <input class="form-control" name="address_complement" id="address_complement"
                                placeholder="Apt, Bloco..." type="text" maxlength="100" />
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn secondary w-100 btnPrev">
                                    Anterior
                                </button>
                                <button type="submit" class="btn primary w-100">
                                    Cadastrar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="ghost-number">
                        <h1>02</h1>
                    </div>
                </div>
                <p class="text-center">Já é um cliente? <span><a href="login.php">Fazer Login</a></span></p>
                <span class="badge secondary red <?= ($alert === "data_empty") ? "d-block" : "d-none" ?>">
                    Preencha todos os campos obrigatórios.
                </span>
                <span class="badge secondary red <?= ($alert === "email_exist") ? "d-block" : "d-none" ?>">
                    E-mail já existente.
                </span>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php' ?>