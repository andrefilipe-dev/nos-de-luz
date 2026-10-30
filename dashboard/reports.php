<?php
$page_title = "Nós de Luz - Relatórios";

require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

/*
|--------------------------------------------------------------------------
| Filtros
|--------------------------------------------------------------------------
*/
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';
$order = $_GET['order'] ?? 'DESC';

/*
 * Impede que valores arbitrários sejam usados no ORDER BY.
 */
$order = strtoupper($order);

if (!in_array($order, ['ASC', 'DESC'], true)) {
    $order = 'DESC';
}

/*
|--------------------------------------------------------------------------
| Valores padrão do relatório
|--------------------------------------------------------------------------
*/
$total_orders = 0;
$total_revenue = 0.00;
$recent_orders = [];

/*
|--------------------------------------------------------------------------
| Executa o relatório quando as datas são informadas
|--------------------------------------------------------------------------
*/
if ($date_start !== '' && $date_end !== '') {

    /*
     * Início deve ser anterior ou igual ao fim.
     */
    $valid_dates = (
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start) &&
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end) &&
        $date_start <= $date_end
    );

    if ($valid_dates) {

        /*
        |--------------------------------------------------------------------------
        | Quantidade de pedidos no período
        |--------------------------------------------------------------------------
        */
        $stmt_orders = mysqli_prepare(
            $conn,
            "SELECT COUNT(*) AS total_orders
             FROM orders
             WHERE date_register >= ?
               AND date_register < DATE_ADD(?, INTERVAL 1 DAY)"
        );

        mysqli_stmt_bind_param(
            $stmt_orders,
            "ss",
            $date_start,
            $date_end
        );

        mysqli_stmt_execute($stmt_orders);

        $result_orders = mysqli_stmt_get_result($stmt_orders);
        $data_orders = mysqli_fetch_assoc($result_orders);

        $total_orders = (int) ($data_orders['total_orders'] ?? 0);

        mysqli_stmt_close($stmt_orders);

        /*
        |--------------------------------------------------------------------------
        | Receita dos pedidos entregues e com pagamento aprovado
        |--------------------------------------------------------------------------
        */
        $stmt_revenue = mysqli_prepare(
            $conn,
            "SELECT COALESCE(SUM(total), 0) AS total_revenue
             FROM orders
             WHERE date_register >= ?
               AND date_register < DATE_ADD(?, INTERVAL 1 DAY)
               AND order_status = 'Entregue'
               AND payment_status = 'Aprovado'"
        );

        mysqli_stmt_bind_param(
            $stmt_revenue,
            "ss",
            $date_start,
            $date_end
        );

        mysqli_stmt_execute($stmt_revenue);

        $result_revenue = mysqli_stmt_get_result($stmt_revenue);
        $data_revenue = mysqli_fetch_assoc($result_revenue);

        $total_revenue = (float) ($data_revenue['total_revenue'] ?? 0);

        mysqli_stmt_close($stmt_revenue);

        /*
        |--------------------------------------------------------------------------
        | Pedidos registrados no período
        |--------------------------------------------------------------------------
        */
        $sql_recent_orders = "
            SELECT
                orders.id,
                orders.user_id,
                orders.total,
                orders.payment_status,
                orders.order_status,
                orders.date_register,
                users.name AS user_name
            FROM orders
            INNER JOIN users
                ON orders.user_id = users.id
            WHERE orders.date_register >= ?
              AND orders.date_register < DATE_ADD(?, INTERVAL 1 DAY)
            ORDER BY orders.date_register $order, orders.id $order
        ";

        $stmt_recent_orders = mysqli_prepare(
            $conn,
            $sql_recent_orders
        );

        mysqli_stmt_bind_param(
            $stmt_recent_orders,
            "ss",
            $date_start,
            $date_end
        );

        mysqli_stmt_execute($stmt_recent_orders);

        $result_recent_orders = mysqli_stmt_get_result($stmt_recent_orders);

        $recent_orders = mysqli_fetch_all(
            $result_recent_orders,
            MYSQLI_ASSOC
        );

        mysqli_stmt_close($stmt_recent_orders);
    }
}

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container section-spacer">
    <div class="row g-3">

        <!-- Formulário de filtros -->
        <div class="col-12">
            <div class="card no-hover mb-3">
                <div class="card-body">

                    <form
                        name="filter-form"
                        id="filter-form"
                        action="<?= BASE_URL ?>dashboard/reports.php"
                        method="GET"
                    >
                        <div class="form-content mb-3">

                            <div class="form-header">
                                <div class="icon-box">
                                    <i class="ri-search-line"></i>
                                </div>

                                <h3 class="form-title">
                                    Gerar Relatório
                                </h3>
                            </div>

                            <div class="form-body row g-3">

                                <div class="form-field col-12 col-md-4">
                                    <label for="date_start">
                                        Início
                                    </label>

                                    <input
                                        class="form-control"
                                        name="date_start"
                                        id="date_start"
                                        type="date"
                                        value="<?= e($date_start) ?>"
                                        required
                                    />
                                </div>

                                <div class="form-field col-12 col-md-4">
                                    <label for="date_end">
                                        Fim
                                    </label>

                                    <input
                                        class="form-control"
                                        name="date_end"
                                        id="date_end"
                                        type="date"
                                        value="<?= e($date_end) ?>"
                                        min="<?= e($date_start) ?>"
                                        required
                                    />
                                </div>

                                <div class="form-field col-12 col-md-4">
                                    <label for="order">
                                        Ordem
                                    </label>

                                    <select
                                        class="form-select"
                                        name="order"
                                        id="order"
                                        required
                                    >
                                        <option
                                            value="ASC"
                                            <?= $order === 'ASC' ? 'selected' : '' ?>
                                        >
                                            Crescente
                                        </option>

                                        <option
                                            value="DESC"
                                            <?= $order === 'DESC' ? 'selected' : '' ?>
                                        >
                                            Decrescente
                                        </option>
                                    </select>
                                </div>

                            </div>
                        </div>
                    </form>

                </div>
            </div>

            <div class="d-flex w-100 justify-content-end p-0">
                <button
                    type="submit"
                    class="btn secondary"
                    form="filter-form"
                >
                    Gerar Relatório
                </button>
            </div>
        </div>

        <!-- Quantidade de pedidos -->
        <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
                <div class="card-body">

                    <div class="icon-box">
                        <i class="ri-shopping-bag-line"></i>
                    </div>

                    <h3 class="card-title">
                        <?php if ($date_start !== '' && $date_end !== ''): ?>
                            <?= e($total_orders) ?>
                        <?php else: ?>
                            Selecione um período
                        <?php endif; ?>
                    </h3>

                    <p>Pedidos registrados</p>

                </div>
            </div>
        </div>

        <!-- Receita total -->
        <div class="col-12 col-md-6">
            <div class="card no-hover h-100">
                <div class="card-body">

                    <div class="icon-box">
                        <i class="ri-funds-line"></i>
                    </div>

                    <h3 class="card-title">
                        <?php if ($date_start !== '' && $date_end !== ''): ?>
                            R$ <?= e(number_format(
                                $total_revenue,
                                2,
                                ',',
                                '.'
                            )) ?>
                        <?php else: ?>
                            Selecione um período
                        <?php endif; ?>
                    </h3>

                    <p>Receita dos pedidos atendidos e pagos</p>

                </div>
            </div>
        </div>

        <!-- Tabela de pedidos -->
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">

                    <div class="icon-box">
                        <i class="ri-shopping-bag-line"></i>
                    </div>

                    <h3 class="card-title">
                        Pedidos
                    </h3>

                    <p>
                        Pedidos registrados no período selecionado.
                    </p>

                    <hr>

                    <div class="table-responsive">
                        <table class="table align-middle text-nowrap">

                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Pagamento</th>
                                    <th>Entrega</th>
                                    <th>Valor</th>
                                    <th>Data</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if ($date_start === '' || $date_end === ''): ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Selecione as datas para gerar o relatório.
                                        </td>
                                    </tr>

                                <?php elseif (
                                    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start) ||
                                    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end) ||
                                    $date_start > $date_end
                                ): ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Informe um período válido.
                                        </td>
                                    </tr>

                                <?php elseif (!empty($recent_orders)): ?>

                                    <?php foreach ($recent_orders as $order_item): ?>

                                        <tr>
                                            <td>
                                                <?= e($order_item['id']) ?>
                                            </td>

                                            <td>
                                                <?= e($order_item['user_name']) ?>
                                            </td>

                                            <td>
                                                <?= e($order_item['payment_status']) ?>
                                            </td>

                                            <td>
                                                <?= e($order_item['order_status']) ?>
                                            </td>

                                            <td>
                                                R$ <?= e(number_format(
                                                    (float) $order_item['total'],
                                                    2,
                                                    ',',
                                                    '.'
                                                )) ?>
                                            </td>

                                            <td>
                                                <?= e(date(
                                                    'd/m/Y',
                                                    strtotime($order_item['date_register'])
                                                )) ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            Nenhum pedido encontrado no período.
                                        </td>
                                    </tr>

                                <?php endif; ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>