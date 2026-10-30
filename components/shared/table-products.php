<div class="table-responsive table-container">

    <table class="table align-middle text-nowrap">

        <thead>
            <tr>
                <th>ID</th>
                <th>Imagem</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Categoria</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>
                            <?= e($product['id']) ?>
                        </td>
                        <td>
                            <img src="<?= BASE_URL . e($product['image']) ?>" alt="<?= e($product['name']) ?>" style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <?= e($product['name']) ?>
                        </td>
                        <td>
                            R$ <?= number_format($product['price'], 2, ',', '.') ?>
                        </td>

                        <td>
                            <?= e($product['quantity']) ?>
                        </td>
                        <td>
                            <?= e($product['category']) ?>
                        </td>
                        <td>
                            <a
                                class="btn secondary"
                                href="<?= BASE_URL ?>dashboard/product-edit.php?id=<?= $product['id'] ?>"
                            >
                                Editar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td
                        colspan="7"
                        class="text-center text-muted"
                    >
                        Nenhum produto encontrado.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>