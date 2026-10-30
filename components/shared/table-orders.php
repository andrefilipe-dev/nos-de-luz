
<div class="table-responsive table-container">

    <table class="table align-middle text-nowrap">

        <thead>
            <tr>
                <th>ID</th>
                <th>Usuário</th>
                <th>Pagamento</th>
                <th>Pedido</th>
                <th>Data de registro</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?= e($order['id']) ?>
                        </td>

                        <td>
                            <?= e($order['user_id']) ?>
                        </td>

                        <td>
                            <?= e($order['payment_status']) ?>
                        </td>

                        <td>
                            <?= e($order['order_status']) ?>
                        </td>

                        <td>
                            <?= e($order['date_register']) ?>
                        </td>

                        <td>
                            <a
                                class="btn secondary"
                                href="<?= BASE_URL ?>dashboard/order-edit.php?id=<?= (int) $order['id'] ?>"
                            >
                                Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            <?php else: ?>
                <tr>
                    <td
                        colspan="6"
                        class="text-center text-muted"
                    >
                        Nenhum pedido encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>