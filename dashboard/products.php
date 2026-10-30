<?php
$page_title = "Nós de Luz - Produtos";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

$filter = $_GET['filter'] ?? '';

if ($filter === '') {
    
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, quantity, price, category, image
        FROM products
        ORDER BY id DESC"
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} else {
    $param = "%{$filter}%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, quantity, price, category, image
         FROM products
         WHERE id LIKE ?
            OR name LIKE ?
            OR quantity LIKE ?
            OR price LIKE ?
            OR category LIKE ?
         ORDER BY id DESC"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssss",
        $param,
        $param,
        $param,
        $param,
        $param
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
}

$products = mysqli_fetch_all($result, MYSQLI_ASSOC);

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container section-spacer">
    <div class="row g-3">
        <div class="col-12">
            <span class="badge secondary green mb-3 <?= ($alert === "update_success") ? "d-inline-block" : "d-none" ?>">
                Alterações salvas com sucesso.
            </span>
            <div class="card no-hover">
                <div class="card-body">
                    <form name="filter-form" id="filter-form" action="<?= BASE_URL ?>dashboard/products.php"
                        method="GET">
                        <div class="form-content">
                            <div class="form-header">
                                <h3 class="card-title">Filtrar Produtos</h3>
                            </div>
                            <div class="form-body row g-3">
                                <div class="form-field col-12">
                                    <label for="filtert">Filtro</label>
                                    <input class="form-control" name="filter" id="filter"
                                        placeholder="Digite algum dado..." type="text" maxlength="100"
                                        value="<?= e($filter) ?>">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button class="btn secondary" form="filter-form">Filtrar</button>
        </div>
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">
                     <div class="d-flex justify-content-between align-items-center">
                        <div class="icon-box"><i class="ri-apps-line"></i></div>
                        <a href="<?= BASE_URL ?>dashboard/product-create.php" class="btn secondary">Adicionar</a>
                    </div>
                    <h3 class="card-title">Projetos</h3>
                    <p>Todos os produtos cadastrados.</p>
                    <hr>
                    <div>
                        <?php require_once BASE_PATH . '/components/shared/table-products.php' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>