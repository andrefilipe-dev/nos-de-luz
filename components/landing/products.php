<?php
$sql = "
    SELECT
        p.*,
        COALESCE(v.total_compras, 0) AS total_compras
    FROM products p
    LEFT JOIN (
        SELECT
            product_id,
            COUNT(DISTINCT order_id) AS total_compras
        FROM orders_items
        GROUP BY product_id
    ) v ON v.product_id = p.id
    ORDER BY total_compras DESC, p.name ASC
    LIMIT 4
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log('Erro ao buscar produtos da landing page: ' . mysqli_error($conn));
    $products = [];
} else {
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}
?>

<section class="products section-spacer" id="products">
    <div class="custom-main-container">

        <div class="custom-text-container"
             data-aos="fade-up"
             data-aos-duration="800"
             data-aos-delay="100">

            <span class="badge primary">Principais produtos</span>
            <h1>Conheça nosso catálogo</h1>
            <p>
                Encontre produtos selecionados para você com praticidade e facilidade.
            </p>
        </div>

        <div class="row g-3"
             data-aos="fade-up"
             data-aos-duration="800"
             data-aos-delay="300">

            <?php if (!empty($products)): ?>

                <?php 

                $cart = $_SESSION['cart'] ?? [];

                foreach ($products as $product): ?>
                    <?php
                    $stock = (int) $product['quantity'];
                    $productId = (int) $product['id'];
                    $productName = $product['name'];
                    $image = trim((string) $product['image']);
                    $isAddedToCart = isset($cart[$productId]);
                    ?>

                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card product">
                            <div class="card-body d-flex flex-column">

                                <div class="product-img mb-3">
                                    <img
                                        src="<?= BASE_URL . e($image) ?>"
                                        alt="<?= e($productName) ?>"
                                        loading="lazy"
                                    >
                                </div>

                                <hr>

                                <h3 class="card-title">
                                    <?= e($productName) ?>
                                </h3>

                                <p>
                                    <?= nl2br(e($product['description'])) ?>
                                </p>

                                <p class="product-price">
                                    R$ <?= number_format(
                                        (float) $product['price'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </p>

                                <div class="mt-auto">
                                    <?php if ($stock > 0): ?>

                                        <button
                                            type="button"
                                            class="btn w-100 mb-3 btn-add-cart
                                                <?= $isAddedToCart ? 'btn-cart-added' : 'primary' ?>"
                                            data-url="<?= BASE_URL ?>app/controllers/cart-controller.php?action=add&id=<?= $productId ?>"
                                            <?= $isAddedToCart ? 'disabled' : '' ?>
                                        >
                                            <?= $isAddedToCart
                                                ? 'Adicionado ao carrinho'
                                                : 'Adicionar ao carrinho' ?>
                                        </button>

                                    <?php else: ?>

                                        <button
                                            type="button"
                                            class="btn secondary w-100 mb-3"
                                            disabled
                                        >
                                            Fora de estoque
                                        </button>

                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-12 text-center">
                    <p>Não há produtos disponíveis para exibir no momento.</p>
                </div>

            <?php endif; ?>

            <div class="col-12 d-flex justify-content-center mt-5">
                <a
                    href="<?= BASE_URL ?>catalog.php"
                    class="btn primary d-inline-block"
                >
                    Ver mais produtos
                </a>
            </div>

        </div>
    </div>
</section>

<script>
    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', async () => {
            if (button.disabled) return;

            const originalText = 'Adicionar ao carrinho';

            button.disabled = true;
            button.textContent = 'Adicionando...';

            try {
                const response = await fetch(button.dataset.url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || 'Não foi possível adicionar o produto.'
                    );
                }

                button.textContent = 'Adicionado ao carrinho';
                button.classList.remove('primary', 'secondary');
                button.classList.add('btn-cart-added');

                // Continua desabilitado para evitar cliques duplicados
                button.disabled = true;

            } catch (error) {
                console.error('Erro ao adicionar produto:', error);

                button.disabled = false;
                button.textContent = originalText;

                alert(error.message || 'Ocorreu um erro. Tente novamente.');
            }
        });
    });
/*
    function updateCartBadge(quantity) {
        const count = Number(quantity ?? 0);

        document.querySelectorAll('.cart-badge').forEach(badge => {
            badge.textContent = count;
            badge.classList.toggle('d-none', count <= 0);
        });
    }

    document.querySelectorAll('.btn-add-cart').forEach(button => {
        button.addEventListener('click', async () => {
            if (button.disabled) return;

            const originalText = 'Adicionar ao carrinho';

            button.disabled = true;
            button.textContent = 'Adicionando...';

            try {
                const response = await fetch(button.dataset.url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(
                        data.message || 'Não foi possível adicionar o produto.'
                    );
                }

                // Atualiza a notificação do carrinho no header.
                updateCartBadge(data.cartCount);

                // Atualiza o botão do produto.
                button.textContent = 'Adicionado ao carrinho';
                button.classList.remove('primary', 'secondary');
                button.classList.add('btn-cart-added');
                button.disabled = true;

            } catch (error) {
                console.error('Erro ao adicionar produto:', error);

                button.disabled = false;
                button.textContent = originalText;

                alert(error.message || 'Ocorreu um erro. Tente novamente.');
            }
        });
    });
    */
</script>