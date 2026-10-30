<?php
$page_title = "Nós de Luz - Dashboard";

require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

/*
|--------------------------------------------------------------------------
| Total de clientes
|--------------------------------------------------------------------------
*/
$stmt_users = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS total_users
     FROM users
     WHERE role = 'Cliente'"
);

mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
$data_users = mysqli_fetch_assoc($result_users);
$total_users = (int) ($data_users['total_users'] ?? 0);
mysqli_stmt_close($stmt_users);

/*
|--------------------------------------------------------------------------
| Receita total dos pedidos entregues e pagos
|--------------------------------------------------------------------------
*/
$stmt_revenue = mysqli_prepare(
    $conn,
    "SELECT COALESCE(SUM(total), 0) AS total_revenue
     FROM orders
     WHERE order_status = 'Entregue'
       AND payment_status = 'aprovado'"
);

mysqli_stmt_execute($stmt_revenue);
$result_revenue = mysqli_stmt_get_result($stmt_revenue);
$data_revenue = mysqli_fetch_assoc($result_revenue);
$total_revenue = (float) ($data_revenue['total_revenue'] ?? 0);
mysqli_stmt_close($stmt_revenue);

/*
|--------------------------------------------------------------------------
| Total de pedidos atendidos
|--------------------------------------------------------------------------
*/
$stmt_completed = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS completed_orders
     FROM orders
     WHERE order_status = 'Entregue'"
);

mysqli_stmt_execute($stmt_completed);
$result_completed = mysqli_stmt_get_result($stmt_completed);
$data_completed = mysqli_fetch_assoc($result_completed);
$total_orders_completed = (int) ($data_completed['completed_orders'] ?? 0);
mysqli_stmt_close($stmt_completed);

/*
|--------------------------------------------------------------------------
| Total de pedidos pendentes
|--------------------------------------------------------------------------
*/
$stmt_pending = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) AS pending_orders
     FROM orders
     WHERE order_status = 'Pendente'"
);

mysqli_stmt_execute($stmt_pending);
$result_pending = mysqli_stmt_get_result($stmt_pending);
$data_pending = mysqli_fetch_assoc($result_pending);
$total_orders_pending = (int) ($data_pending['pending_orders'] ?? 0);
mysqli_stmt_close($stmt_pending);

/*
|--------------------------------------------------------------------------
| Cinco pedidos mais recentes
|--------------------------------------------------------------------------
*/
$stmt_orders = mysqli_prepare(
    $conn,
    "SELECT id, user_id, payment_status, order_status, date_register
     FROM orders
     ORDER BY date_register DESC, id DESC
     LIMIT 5"
);

mysqli_stmt_execute($stmt_orders);
$result_orders = mysqli_stmt_get_result($stmt_orders);
$orders = mysqli_fetch_all($result_orders, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_orders);

/*
|--------------------------------------------------------------------------
| Cinco usuários mais recentes
|--------------------------------------------------------------------------
*/
$stmt_users = mysqli_prepare(
    $conn,
    "SELECT id, name, email, date_register, status
     FROM users
     ORDER BY date_register DESC, id DESC
     LIMIT 5"
);

mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
$users = mysqli_fetch_all($result_users, MYSQLI_ASSOC);
mysqli_stmt_close($stmt_users);

/*
|--------------------------------------------------------------------------
| Layout
|--------------------------------------------------------------------------
*/
require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container section-spacer">
    <div class="row g-3">

        <!-- Total de clientes -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card no-hover h-100">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-group-line"></i>
                    </div>

                    <h3 class="card-title">
                        <?= e($total_users) ?>
                    </h3>

                    <p>Clientes totais</p>
                </div>
            </div>
        </div>

        <!-- Receita total -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card no-hover h-100">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-funds-line"></i>
                    </div>

                    <h3 class="card-title">
                        R$ <?= e(number_format($total_revenue, 2, ',', '.')) ?>
                    </h3>

                    <p>Receita total</p>
                </div>
            </div>
        </div>

        <!-- Pedidos atendidos -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card no-hover h-100">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-shopping-bag-line"></i>
                    </div>

                    <h3 class="card-title">
                        <?= e($total_orders_completed) ?>
                    </h3>

                    <p>Pedidos atendidos</p>
                </div>
            </div>
        </div>

        <!-- Pedidos pendentes -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card no-hover h-100">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-alert-line"></i>
                    </div>

                    <h3 class="card-title">
                        <?= e($total_orders_pending) ?>
                    </h3>

                    <p>Pedidos pendentes</p>
                </div>
            </div>
        </div>

        <!-- Pedidos recentes -->
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="icon-box">
                            <i class="ri-shopping-bag-line"></i>
                        </div>

                        <a
                            href="<?= BASE_URL ?>dashboard/orders.php"
                            class="btn secondary"
                        >
                            Ver Todos
                        </a>
                    </div>

                    <h3 class="card-title">Pedidos Recentes</h3>
                    <p>5 pedidos mais recentes cadastrados.</p>

                    <hr>

                    <div>
                        <?php
                        require BASE_PATH . '/components/shared/table-orders.php';
                        ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- Usuários recentes -->
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="icon-box">
                            <i class="ri-group-line"></i>
                        </div>

                        <a
                            href="<?= BASE_URL ?>dashboard/users.php"
                            class="btn secondary"
                        >
                            Ver Todos
                        </a>
                    </div>

                    <h3 class="card-title">Usuários Recentes</h3>
                    <p>5 usuários mais recentes cadastrados.</p>

                    <hr>

                    <div>
                        <?php
                        require BASE_PATH . '/components/shared/table-users.php';
                        ?>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>