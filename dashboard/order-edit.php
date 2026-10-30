<?php
$page_title = "Nós de Luz - Editar Pedido";

require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: " . BASE_URL . "dashboard/orders.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, user_id, total, payment_status, order_status, date_register
     FROM orders
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$order = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$order) {
    header("Location: " . BASE_URL . "dashboard/orders.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        oi.id,
        oi.order_id,
        oi.product_id,
        oi.quantity,
        oi.price,
        p.name AS product_name,
        p.image AS product_image
     FROM orders_items oi
     LEFT JOIN products p ON p.id = oi.product_id
     WHERE oi.order_id = ?
     ORDER BY oi.id ASC"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$items = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);

/*
 * Lê as opções permitidas pelos ENUMs do banco,
 * evitando presumir os nomes dos status.
 */
function getOrderEnumValues(mysqli $conn, string $column): array
{
    $allowedColumns = ['payment_status', 'order_status'];

    if (!in_array($column, $allowedColumns, true)) {
        return [];
    }

    $result = mysqli_query($conn, "SHOW COLUMNS FROM `orders`");

    if (!$result) {
        error_log("Erro ao consultar colunas da tabela orders: " . mysqli_error($conn));
        return [];
    }

    $columnInfo = null;

    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['Field'] === $column) {
            $columnInfo = $row;
            break;
        }
    }

    mysqli_free_result($result);

    if (
        !$columnInfo ||
        !preg_match("/^enum\\((.*)\\)$/i", $columnInfo['Type'], $matches)
    ) {
        return [];
    }

    return str_getcsv($matches[1], ',', "'");
}

$paymentOptions = getOrderEnumValues($conn, 'payment_status');
$orderOptions = getOrderEnumValues($conn, 'order_status');

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container min-vh-100 section-spacer">
    <div class="row g-3">
        <div class="col-12">

            <!-- ALERTAS -->

            <span class="badge secondary red mb-3 <?= ($alert === 'update_error') ? 'd-inline-block' : 'd-none' ?>">
                Falha ao atualizar o pedido.
            </span>

            <span class="badge secondary red mb-3 <?= ($alert === 'stock_error') ? 'd-inline-block' : 'd-none' ?>">
                Estoque insuficiente para aumentar a quantidade.
            </span>

            <span class="badge secondary red mb-3 <?= ($alert === 'invalid_status') ? 'd-inline-block' : 'd-none' ?>">
                Um dos status selecionados é inválido.
            </span>

            <span class="badge secondary red mb-3 <?= ($alert === 'data_empty') ? 'd-inline-block' : 'd-none' ?>">
                Informe uma quantidade válida.
            </span>

            <span class="badge secondary green mb-3 <?= ($alert === 'update_success') ? 'd-inline-block' : 'd-none' ?>">
                Pedido atualizado com sucesso.
            </span>

            <span class="badge secondary green mb-3 <?= ($alert === 'item_removed') ? 'd-inline-block' : 'd-none' ?>">
                Item removido do pedido.
            </span>

            <!-- INFORMAÇÕES DO PEDIDO -->

            <div class="card no-hover mb-3">
                <div class="card-body">
                    <div class="form-content">
                        <div class="form-header">
                            <div class="icon-box">
                                <i class="ri-file-list-3-line"></i>
                            </div>

                            <h3 class="form-title">
                                Informações do Pedido #<?= (int) $order['id'] ?>
                            </h3>
                        </div>

                        <div class="form-body row g-3">
                            <div class="form-field col-6">
                                <label>ID do pedido</label>
                                <input
                                    class="form-control"
                                    value="<?= (int) $order['id'] ?>"
                                    readonly
                                >
                            </div>

                            <div class="form-field col-6">
                                <label>ID do usuário</label>
                                <input
                                    class="form-control"
                                    value="<?= (int) $order['user_id'] ?>"
                                    readonly
                                >
                            </div>

                            <div class="form-field col-6">
                                <label>Total do pedido</label>
                                <input
                                    class="form-control"
                                    value="R$ <?= number_format((float) $order['total'], 2, ',', '.') ?>"
                                    readonly
                                >
                            </div>

                            <div class="form-field col-6">
                                <label>Data de registro</label>
                                <input
                                    class="form-control"
                                    value="<?= e($order['date_register']) ?>"
                                    readonly
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATUS -->

            <form
                action="<?= BASE_URL ?>app/controllers/order-controller.php"
                method="POST"
                class="card no-hover mb-3"
            >
                <div class="card-body">
                    <div class="form-content">
                        <div class="form-header">
                            <div class="icon-box">
                                <i class="ri-settings-3-line"></i>
                            </div>

                            <h3 class="form-title">Status do Pedido</h3>
                        </div>

                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">

                        <div class="form-body row g-3">
                            <div class="form-field col-12 col-md-6">
                                <label for="payment_status">Status do pagamento</label>

                                <select
                                    class="form-select"
                                    name="payment_status"
                                    id="payment_status"
                                    required
                                >
                                    <?php foreach ($paymentOptions as $status): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= ($order['payment_status'] === $status) ? 'selected' : '' ?>
                                        >
                                            <?= e(ucfirst($status)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-field col-12 col-md-6">
                                <label for="order_status">Status da entrega</label>

                                <select
                                    class="form-select"
                                    name="order_status"
                                    id="order_status"
                                    required
                                >
                                    <?php foreach ($orderOptions as $status): ?>
                                        <option
                                            value="<?= e($status) ?>"
                                            <?= ($order['order_status'] === $status) ? 'selected' : '' ?>
                                        >
                                            <?= e(ucfirst($status)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn secondary">
                                    Salvar Status
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ITENS DO PEDIDO -->

            <div class="card no-hover">
                <div class="card-body">
                    <div class="form-header">
                        <div class="icon-box">
                            <i class="ri-shopping-bag-line"></i>
                        </div>

                        <h3 class="form-title">Itens do Pedido</h3>
                    </div>

                    <p>Altere as quantidades ou remova itens deste pedido.</p>

                    <hr>

                    <div class="table-responsive table-container">
                        <table class="table align-middle text-nowrap">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço unitário</th>
                                    <th>Quantidade</th>
                                    <th>Subtotal</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($item['product_image'])): ?>
                                                        <img
                                                            src="<?= BASE_URL . e($item['product_image']) ?>"
                                                            alt="<?= e($item['product_name'] ?? 'Produto') ?>"
                                                            style="width: 50px; height: 50px; object-fit: cover;"
                                                        >
                                                    <?php endif; ?>

                                                    <div>
                                                        <div>
                                                            <?= e($item['product_name'] ?? 'Produto não encontrado') ?>
                                                        </div>

                                                        <small class="text-muted">
                                                            ID: <?= (int) $item['product_id'] ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                R$ <?= number_format((float) $item['price'], 2, ',', '.') ?>
                                            </td>

                                            <td>
                                                <form
                                                    action="<?= BASE_URL ?>app/controllers/order-controller.php"
                                                    method="POST"
                                                    class="d-flex gap-2 align-items-center"
                                                >
                                                    <input type="hidden" name="action" value="update_item_quantity">
                                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">

                                                    <input
                                                        class="form-control"
                                                        type="number"
                                                        name="quantity"
                                                        min="1"
                                                        step="1"
                                                        value="<?= (int) $item['quantity'] ?>"
                                                        style="width: 90px;"
                                                        required
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn secondary"
                                                    >
                                                        Salvar
                                                    </button>
                                                </form>
                                            </td>

                                            <td>
                                                R$ <?= number_format(
                                                    (float) $item['price'] * (int) $item['quantity'],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </td>

                                            <td>
                                                <form
                                                    action="<?= BASE_URL ?>app/controllers/order-controller.php"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja remover este item do pedido?');"
                                                >
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                                    <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">

                                                    <button
                                                        type="submit"
                                                        class="btn secondary"
                                                    >
                                                        Remover
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            Este pedido não possui itens.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        <a
                            href="<?= BASE_URL ?>dashboard/orders.php"
                            class="btn secondary"
                        >
                            Voltar aos Pedidos
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>