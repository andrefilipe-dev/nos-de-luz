<?php
$page_title = "Nós de Luz - Pedidos";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

$filter = $_GET['filter'] ?? '';

if ($filter === '') {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, user_id, payment_status, order_status, date_register
         FROM orders
         ORDER BY id DESC"
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

} else {

    $param = "%{$filter}%";

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, user_id, payment_status, order_status, date_register
         FROM orders
         WHERE CAST(id AS CHAR) LIKE ?
            OR CAST(user_id AS CHAR) LIKE ?
            OR payment_status LIKE ?
            OR order_status LIKE ?
            OR CAST(date_register AS CHAR) LIKE ?
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

$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container section-spacer">
    <div class="row g-3">

        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">

                    <form
                        name="filter-form"
                        id="filter-form"
                        action="<?= BASE_URL ?>dashboard/orders.php"
                        method="GET"
                    >
                        <div class="form-content">
                            <div class="form-header">
                                <h3 class="card-title">Filtrar Pedidos</h3>
                            </div>

                            <div class="form-body row g-3">
                                <div class="form-field col-12">
                                    <label for="filter">Filtro</label>
                                    <input
                                        class="form-control"
                                        name="filter"
                                        id="filter"
                                        placeholder="ID, usuário, status ou data..."
                                        type="text"
                                        maxlength="100"
                                        value="<?= e($filter) ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn secondary" form="filter-form">
                Filtrar
            </button>
        </div>

        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="icon-box">
                            <i class="ri-shopping-bag-3-line"></i>
                        </div>
                    </div>

                    <h3 class="card-title">Pedidos</h3>
                    <p>Todos os pedidos registrados.</p>

                    <hr>

                    <div>
                        <?php
                        require BASE_PATH . '/components/shared/table-orders.php';
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>