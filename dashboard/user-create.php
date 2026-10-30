<?php
$page_title = "Nós de Luz - Usuários";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container min-vh-100 section-spacer">
    <div class="row g-3">
        <div class="col-12">
            <span class="badge secondary red mb-3 <?= ($alert === "data_empty") ? "d-inline-block" : "d-none"?>">
                Preencha todos os campos obrigatórios.
            </span>
            <span class="badge secondary red mb-3 <?= ($alert === "register_error") ? "d-inline-block" : "d-none"?>">
                Falha ao registrar dados.
            </span>
            <span class="badge secondary red mb-3 <?= ($alert === "email_exist") ? "d-inline-block" : "d-none"?>">
                E-mail já existente. 
            </span>
            <span class="badge secondary green mb-3 <?= ($alert === "register_success") ? "d-inline-block" : "d-none" ?>">
                Usuário criado com sucesso.
            </span>
            <form action="<?= BASE_URL ?>app/controllers/user-controller.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="d-flex flex-column gap-3">
                    <div class="card no-hover">
                        <div class="card-body">
                            <div class="form-content">
                                <div class="form-header">
                                    <div class="icon-box"><i class="ri-user-line"></i></div>
                                    <h3 class="form-title">Informações Pessoais</h3>
                                </div>
                                <div class="form-body row g-3">
                                    <div class="form-field col-12">
                                        <label for="name">Nome</label>
                                        <input class="form-control" name="name" id="name" placeholder="Ex: Rogério..."
                                            type="text" minlength="3" maxlength="100" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="email">E-mail</label>
                                        <input class="form-control" name="email" id="email"
                                            placeholder="nome@exemplo.com" minlength="5" maxlength="255" type="email"
                                            required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="password">Senha</label>
                                        <input class="form-control" name="password" id="password" placeholder="••••••••"
                                            type="password" minlength="8" maxlength="100" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="phone">Telefone</label>
                                        <input class="form-control" name="phone" id="phone"
                                            placeholder="(00) 00000-0000" type="text" minlength="15" maxlength="15"
                                            required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="cpf">CPF</label>
                                        <input class="form-control" name="cpf" id="cpf" placeholder="000.000.000-00"
                                            type="text" minlength="14" maxlength="14" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card no-hover">
                        <div class="card-body">
                            <div class="form-content">
                                <div class="form-header">
                                    <div class="icon-box"><i class="ri-map-pin-line"></i></div>
                                    <h3 class="form-title">Endereço</h3>
                                </div>
                                <div class="form-body row g-3">
                                    <div class="form-field col-6">
                                        <label for="cep">CEP</label>
                                        <input class="form-control" name="cep" id="cep" placeholder="00000-000"
                                            type="text" minlength="9" maxlength="9" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address">Endereço</label>
                                        <input class="form-control" name="address" id="address"
                                            placeholder="Rua, Avenida..." type="text" minlength="5" maxlength="150"
                                            required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address_number">Número</label>
                                        <input class="form-control" name="address_number" id="address_number"
                                            placeholder="123" type="text" maxlength="10" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address_complement">Complemento</label>
                                        <input class="form-control" name="address_complement" id="address_complement"
                                            placeholder="Apt, Bloco..." type="text" maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card no-hover">
                        <div class="card-body">
                            <div class="form-content">
                                <div class="form-header">
                                    <div class="icon-box"><i class="ri-safe-line"></i></div>
                                    <h3 class="form-title">Nível e Status</h3>
                                </div>
                                <div class="form-body row g-3">
                                    <div class="form-field col-6">
                                        <label for="role">Nível</label>
                                        <select class="form-select" name="role" id="role" placeholder="Nível"
                                            autocomplete="on">
                                            <option value="Cliente">Cliente</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="status">Status</label>
                                        <select class="form-select" name="status" id="status" placeholder="Status"
                                            autocomplete="on">
                                            <option value="Ativo">Ativo</option>
                                            <option value="Inativo">Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-end p-0">
                        <button type="submit" class="btn secondary">Cadastrar Usuário</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>