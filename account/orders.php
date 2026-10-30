<?php
$page_title = "Nós de Luz - Projetos";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/auth.php';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';

$sql = "SELECT * FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $_SESSION['id']);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>



<div class="custom-main-container section-spacer">
    <div class="row g-3">
        <div class="col-12">
            <div class="card no-hover">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-apps-line"></i>
                    </div>
                    <h3 class="card-title">Meus Pedidos</h3>
                    <p>Veja todos os seus pedidos realizados e acompanhe o andamento das suas compras.</p>
                    <hr>
                    <div class="projects-list overflow-x-hidden">
                        <div class="row g-3">

                            <?php if (mysqli_num_rows($result) > 0): ?>

                                <?php while ($order = mysqli_fetch_assoc($result)): ?>

                                    <?php
                                    // Buscar os produtos deste pedido
                                    $sql_items = "
                                        SELECT oi.product_id, oi.quantity, oi.price, p.name
                                        FROM orders_items oi
                                        INNER JOIN products p ON p.id = oi.product_id
                                        WHERE oi.order_id = ?
                                    ";

                                    $stmt_items = mysqli_prepare($conn, $sql_items);

                                    mysqli_stmt_bind_param($stmt_items, "i", $order['id']);
                                    mysqli_stmt_execute($stmt_items);

                                    $items = mysqli_stmt_get_result($stmt_items);
                                    ?>

                                    <div class="col-12 col-md-6">
                                        <div class="card no-hover h-100">
                                            <div class="card-body d-flex flex-column">

                                                <div>
                                                    <h3 class="card-title">
                                                        Pedido #<?= (int) $order['id'] ?>
                                                    </h3>

                                                    <p>
                                                        Realizado em
                                                        <?= date(
                                                            'd/m/Y',
                                                            strtotime($order['date_register'])
                                                        ) ?>
                                                    </p>

                                                    <hr>
                                                </div>

                                                <div>
                                                    <span class="span-highlight">Produtos</span>

                                                    <?php if (mysqli_num_rows($items) > 0): ?>

                                                        <?php while ($item = mysqli_fetch_assoc($items)): ?>

                                                            <p>
                                                                <?= htmlspecialchars($item['name']) ?>
                                                                × <?= (int) $item['quantity'] ?>
                                                            </p>

                                                        <?php endwhile; ?>

                                                    <?php else: ?>

                                                        <p>Nenhum produto encontrado.</p>

                                                    <?php endif; ?>
                                                </div>

                                                    <div class="d-flex flex-wrap gap-3 justify-content-between mt-auto">

                                                        <div>
                                                            <span class="span-highlight">Total</span>
                                                            <p>
                                                                R$ <?= number_format(
                                                                    (float) $order['total'],
                                                                    2,
                                                                    ',',
                                                                    '.'
                                                                ) ?>
                                                            </p>
                                                        </div>

                                                        <div>
                                                            <span class="span-highlight">Pagamento</span>
                                                            <p>
                                                                <?= htmlspecialchars($order['payment_status']) ?>
                                                            </p>
                                                        </div>

                                                        <div>
                                                            <span class="span-highlight">Entrega</span>
                                                            <p>
                                                                <?= htmlspecialchars($order['order_status']) ?>
                                                            </p>
                                                        </div>

                                                    </div>


                                                    <?php
                                                    $status = $order['order_status'];

                                                    $badgeClass = match ($status) {
                                                        'Entregue'   => 'green',
                                                        'Preparando' => 'yellow',
                                                        'Pendente'   => 'yellow',
                                                        'Cancelado'  => 'red',
                                                        default      => 'secondary'
                                                    };
                                                    ?>

                                                    <span class="badge secondary top <?= $badgeClass ?>">
                                                        <?= htmlspecialchars($status) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <?php mysqli_stmt_close($stmt_items); ?>

                                    <?php endwhile; ?>

                                <?php else: ?>

                                    <div class="col-12">
                                        <p>Você ainda não realizou nenhum pedido.</p>
                                    </div>

                                <?php endif; ?>
                            <!--
                            <div class="col-12 col-md-6">
                                <div class="card no-hover h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div>
                                            <h3 class="card-title">Pedido 001</h3>
                                            <p>Realizado em 27/09/2026</p>
                                            <hr>
                                        </div>
                                        <div>
                                            <span class="span-highlight">Produtos</span>
                                            <p>Conjunto pulseira × 2</p>
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 justify-content-between mt-auto">
                                            <div>
                                                <span class="span-highlight">Total</span>
                                                <p>R$ 60,00</p>
                                            </div>
                                            <div>
                                                <span class="span-highlight">Pagamento</span>
                                                <p>Aprovado</p>
                                            </div>
                                            <div>
                                                <span class="span-highlight">Entrega</span>
                                                <p>Preparando</p>
                                            </div>
                                        </div>
                                        <span class="badge secondary top yellow">
                                            Preparando
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card no-hover h-100">
                                    <div class="card-body d-flex flex-column">
                                        <div>
                                            <h3 class="card-title">Pedido 002</h3>
                                            <p>Realizado em 25/09/2026</p>
                                            <hr>
                                        </div>
                                        <div>
                                            <span class="span-highlight">Produtos</span>
                                            <p>Conjunto pulseira × 1</p>
                                        </div>
                                        <div class="d-flex flex-wrap gap-3 justify-content-between mt-auto">
                                            <div>
                                                <span class="span-highlight">Total</span>
                                                <p>R$ 30,00</p>
                                            </div>
                                            <div>
                                                <span class="span-highlight">Pagamento</span>
                                                <p>Aprovado</p>
                                            </div>
                                            <div>
                                                <span class="span-highlight">Entrega</span>
                                                <p>Entregue</p>
                                            </div>
                                        </div>
                                        <span class="badge secondary top green">
                                            Entregue
                                        </span>
                                    </div>
                                </div>
                            </div>
                            -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>