<?php
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function redirectOrder(int $orderId, string $alert): void
{
    header(
        "Location: " . BASE_URL .
        "dashboard/order-edit.php?id=" . $orderId .
        "&alert=" . urlencode($alert)
    );
    exit;
}

function getEnumValues(mysqli $conn, string $column): array
{
    $allowedColumns = ['payment_status', 'order_status'];

    if (!in_array($column, $allowedColumns, true)) {
        return [];
    }

    $stmt = mysqli_prepare(
        $conn,
        "SELECT COLUMN_TYPE
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE()
           AND TABLE_NAME = 'orders'
           AND COLUMN_NAME = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $column);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$row || !preg_match(
        "/^enum\((.*)\)$/i",
        $row['COLUMN_TYPE'],
        $matches
    )) {
        return [];
    }

    return str_getcsv($matches[1], ',', "'");
}

function recalculateOrderTotal(mysqli $conn, int $orderId): void
{
    $stmt = mysqli_prepare(
        $conn,
        "SELECT COALESCE(SUM(quantity * price), 0) AS total
         FROM orders_items
         WHERE order_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $orderId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    $total = (float) $row['total'];

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE orders SET total = ? WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "di", $total, $orderId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "dashboard/orders.php");
    exit;
}

$action = $_POST['action'] ?? '';
$orderId = (int) ($_POST['order_id'] ?? 0);

if ($orderId <= 0) {
    header("Location: " . BASE_URL . "dashboard/orders.php");
    exit;
}

try {
    switch ($action) {

        // ATUALIZAR STATUS DO PEDIDO

        case 'update_status':
            
            $paymentStatus = $_POST['payment_status'] ?? '';
            $orderStatus = $_POST['order_status'] ?? '';
            
            $paymentOptions = getEnumValues($conn, 'payment_status');
            $orderOptions = getEnumValues($conn, 'order_status');
            
            if (
                !in_array($paymentStatus, $paymentOptions, true) ||
                !in_array($orderStatus, $orderOptions, true)
            ) {
                redirectOrder($orderId, 'invalid_status');
            }

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE orders
                 SET payment_status = ?, order_status = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssi",
                $paymentStatus,
                $orderStatus,
                $orderId
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            redirectOrder($orderId, 'update_success');


        // ALTERAR QUANTIDADE DE UM ITEM

        case 'update_item_quantity':

            $itemId = (int) ($_POST['item_id'] ?? 0);
            $newQuantity = filter_var(
                $_POST['quantity'] ?? null,
                FILTER_VALIDATE_INT
            );

            if ($itemId <= 0 || $newQuantity === false || $newQuantity === null || $newQuantity < 1) {
                redirectOrder($orderId, 'data_empty');
            }

            mysqli_begin_transaction($conn);

            // Confirma que o item pertence ao pedido.

            $stmt = mysqli_prepare(
                $conn,
                "SELECT product_id, quantity
                 FROM orders_items
                 WHERE id = ? AND order_id = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt, "ii", $itemId, $orderId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $item = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if (!$item) {
                mysqli_rollback($conn);
                redirectOrder($orderId, 'update_error');
            }

            $productId = (int) $item['product_id'];
            $oldQuantity = (int) $item['quantity'];
            $difference = $newQuantity - $oldQuantity;

            // Ajusta o estoque conforme a diferença de quantidade.

            $stmt = mysqli_prepare(
                $conn,
                "SELECT quantity
                 FROM products
                 WHERE id = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt, "i", $productId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if ($product && $difference > 0) {

                if ((int) $product['quantity'] < $difference) {
                    mysqli_rollback($conn);
                    redirectOrder($orderId, 'stock_error');
                }

                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE products
                     SET quantity = quantity - ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ii",
                    $difference,
                    $productId
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

            } elseif ($product && $difference < 0) {

                $stockToReturn = abs($difference);

                $stmt = mysqli_prepare(
                    $conn,
                    "UPDATE products
                     SET quantity = quantity + ?
                     WHERE id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "ii",
                    $stockToReturn,
                    $productId
                );

                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE orders_items
                 SET quantity = ?
                 WHERE id = ? AND order_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "iii",
                $newQuantity,
                $itemId,
                $orderId
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            recalculateOrderTotal($conn, $orderId);

            mysqli_commit($conn);

            redirectOrder($orderId, 'update_success');


        // REMOVER ITEM DO PEDIDO

        case 'remove_item':

            $itemId = (int) ($_POST['item_id'] ?? 0);

            if ($itemId <= 0) {
                redirectOrder($orderId, 'update_error');
            }

            mysqli_begin_transaction($conn);

            $stmt = mysqli_prepare(
                $conn,
                "SELECT product_id, quantity
                 FROM orders_items
                 WHERE id = ? AND order_id = ?
                 FOR UPDATE"
            );

            mysqli_stmt_bind_param($stmt, "ii", $itemId, $orderId);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $item = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);

            if (!$item) {
                mysqli_rollback($conn);
                redirectOrder($orderId, 'update_error');
            }

            $productId = (int) $item['product_id'];
            $quantityToReturn = (int) $item['quantity'];

            // Devolve ao estoque a quantidade removida.

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE products
                 SET quantity = quantity + ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ii",
                $quantityToReturn,
                $productId
            );

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $stmt = mysqli_prepare(
                $conn,
                "DELETE FROM orders_items
                 WHERE id = ? AND order_id = ?"
            );

            mysqli_stmt_bind_param($stmt, "ii", $itemId, $orderId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            recalculateOrderTotal($conn, $orderId);

            mysqli_commit($conn);

            redirectOrder($orderId, 'item_removed');


        default:
            redirectOrder($orderId, 'update_error');
    }

} catch (Throwable $exception) {

    try {
        mysqli_rollback($conn);
    } catch (Throwable $rollbackException) {
        // Não há transação ativa para reverter.
    }

    error_log(
        "Erro ao editar pedido #{$orderId}: " .
        $exception->getMessage()
    );

    redirectOrder($orderId, 'update_error');
}
?>