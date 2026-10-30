<?php 
$page_title = "Nós de Luz - Perfil";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/auth.php';

$id = $_SESSION['id'] ?? '';

if (!empty($id)) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    if (!$user) {
        header("Location: " . BASE_URL . 'index.php');
        exit;
    }
} else {
    header("Location: " . BASE_URL . 'index.php');
    exit;
}

$name = $user['name'];
$email = $user['email'];
$phone = $user['phone'];
$cpf = $user['cpf'];
$cep = $user['cep'];
$address = $user['address'];
$address_number = $user['address_number'];
$address_complement = $user['address_complement'];
$role = $user['role'];
$status = $user['status'];

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
            <span class="badge secondary red mb-3 <?= ($alert === "update_error") ? "d-inline-block" : "d-none"?>">
                Falha ao atualizar dados.
            </span>
            <span class="badge secondary red mb-3 <?= ($alert === "email_exist") ? "d-inline-block" : "d-none"?>">
                E-mail já existente. 
            </span>
            <span class="badge secondary green mb-3 <?= ($alert === "update_success") ? "d-inline-block" : "d-none" ?>">
                Alterações salvas com sucesso.
            </span>
            <form name="filter-form" action="<?= BASE_URL ?>app/controllers/user-controller.php" method="POST">
                <input type="hidden" name="action" value="edit_profile">
                <input type="hidden" name="id" value="<?= e($_SESSION['id']) ?>">
                <div class="d-flex flex-column gap-3">
                    <div class="card no-hover">
                        <div class="card-body">
                            <div class="form-content">
                                <div class="form-header">
                                    <div class="icon-box"><i class="ri-user-line"></i></div>
                                    <h3 class="form-title">Informações Pessoais</h3>
                                </div>
                                <div class="form-body row g-3">
                                    <div class="form-field col-6">
                                        <label for="name">Nome</label>
                                        <input class="form-control" name="name" id="name"
                                            value="<?= e($name) ?>" placeholder="Ex: Rogério..."
                                            type="text" minlength="3" maxlength="100" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="email">E-mail</label>
                                        <input class="form-control" name="email" id="email"
                                            value="<?= e($email) ?>" placeholder="nome@exemplo.com"
                                            type="email" maxlength="255" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="phone">Telefone</label>
                                        <input class="form-control" name="phone" id="phone"
                                            value="<?= e($phone) ?>" placeholder="(00) 00000-0000"
                                            type="text" minlength="15" maxlength="15" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="cpf">CPF</label>
                                        <input class="form-control" name="cpf" id="cpf"
                                            value="<?= e($cpf) ?>" placeholder="000.000.000-00"
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
                                        <input class="form-control" name="cep" id="cep"
                                            value="<?= e($cep) ?>" placeholder="00000-000" type="text"
                                            minlength="9" maxlength="9" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address">Endereço</label>
                                        <input class="form-control" name="address" id="address"
                                            value="<?= e($address) ?>" placeholder="Rua, Avenida..."
                                            type="text" minlength="5" maxlength="150" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address_number">Número</label>
                                        <input class="form-control" name="address_number" id="address_number"
                                            value="<?= e($address_number) ?>" placeholder="123"
                                            type="text" maxlength="10" required />
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="address_complement">Complemento</label>
                                        <input class="form-control" name="address_complement" id="address_complement"
                                            value="<?= e($address_complement) ?>"
                                            placeholder="Apt, Bloco..." type="text" maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex w-100 justify-content-end p-0">
                        <button type="submit" class="btn secondary">Salvar Alterações</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">
                    <div class="icon-box warning"><i class="ri-lock-password-line"></i></div>
                    <h3 class="card-title">Redefinição de Senha</h3>
                    <p>Atualize a senha da sua conta para manter seus dados protegidos.</p>
                    <a href="<?= BASE_URL ?>forgot-password-email.php" class="btn secondary yellow">Redefinir Senha</a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">
                    <div class="icon-box danger"><i class="ri-alert-line"></i></div>
                    <h3 class="card-title">Zona de Perigo</h3>
                    <p>Tem certeza que deseja desativar sua conta? Seu acesso será bloqueado e seus dados ficarão
                        armazenados de forma segura.</p>
                    <a class="btn secondary red" data-bs-toggle="modal" data-bs-target="#deleteModal">Desativar
                        Conta</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered d-flex justify-content-center custom-main-container">
        <div class="modal-content card no-hover limited">
            <div class="card-body">
                <h3 class="card-title">Confirmar Desativação</h3>
                <p>Ao desativar sua conta, você não poderá realizar login.
                    Seus dados permanecerão armazenados e sua conta poderá ser reativada futuramente.</p>
                <div class="d-flex gap-1">
                    <a href="<?= BASE_URL ?>app/controllers/user-controller.php?action=disable" class="btn secondary red">Desativar</a>
                    <button type="button" class="btn secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>