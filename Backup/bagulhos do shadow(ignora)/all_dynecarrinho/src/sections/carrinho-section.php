<?php

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;

foreach ($carrinho as $item) {
    $total +=
        $item['preco'] *
        $item['quantidade'];

}

?>

<section class="cart-section section-spacer">
    <div class="custom-main-container">

        <?php if (empty($carrinho)): ?>

            <!-- Carrinho vazio -->

            <div class="empty-cart card no-hover">
                <div class="card-body text-center">
                    <div class="icon-box xxl circle empty-cart-icon">

                        <i class="bi bi-cart3"></i>
                    </div>

                    <h2> Seu carrinho está vazio </h2>

                    <p> Você ainda não adicionou nenhum produto. </p>

                    <a
                        href="catalogo.php"
                        class="btn primary"
                    >

                        <i class="bi bi-shop"></i>
                        Ver catálogo
                    </a>
                </div>
            </div>

        <?php else: ?>

            <div class="row g-4">

                <!-- Lista de produtos -->

                <div class="col-lg-8">
                    <div class="card cart-products-card no-hover">
                        <div class="card-body">
                            <div class="cart-products-header">

                                <h2>
                                    Produtos
                                </h2>

                                <a
                                    href="catalogo.php"
                                    class="continue-shopping"
                                >
                                    <i class="bi bi-arrow-left"></i>
                                    Continuar comprando
                                </a>
                            </div>

                            <div class="products-list">

                                <?php foreach ($carrinho as $index => $item): ?>
                                    <?php

                                    $subtotal =
                                        $item['preco'] *
                                        $item['quantidade'];
                                    ?>

                                    <article class="cart-product">


                                        <div class="product-image">
                                            <?php if (!empty($item['imagem'])): ?>

                                                <img
                                                    src="<?= htmlspecialchars($item['imagem']) ?>"
                                                    alt="<?= htmlspecialchars($item['nome']) ?>"
                                                >
                                            <?php else: ?>

                                                <div class="product-image-placeholder">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>


                                        <!-- Produto -->

                                        <div class="product-info">
                                            <h3>
                                                <?= htmlspecialchars($item['nome']) ?>
                                            </h3>
                                            <span class="product-price">

                                                R$

                                                <?= number_format(
                                                    $item['preco'],
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </span>
                                        </div>

                                        <!-- Quantidade -->

                                        <div class="quantity-control">
                                            <button
                                                type="button"
                                                class="quantity-btn"
                                                onclick="alterarQuantidade(
                                                    <?= $index ?>,
                                                    -1
                                                )"
                                            >

                                                <i class="bi bi-dash"></i>
                                            </button>


                                            <span class="quantity">
                                                <?= $item['quantidade'] ?>
                                            </span>


                                            <button
                                                type="button"
                                                class="quantity-btn"
                                                onclick="alterarQuantidade(
                                                    <?= $index ?>,
                                                    1
                                                )"
                                            >

                                                <i class="bi bi-plus"></i>
                                            </button>

                                        </div>

                                        <!-- subtotal -->

                                        <div class="product-subtotal">
                                            <strong>

                                                R$

                                                <?= number_format(
                                                    $subtotal,
                                                    2,
                                                    ',',
                                                    '.'
                                                ) ?>
                                            </strong>
                                        </div>

                                        <!-- remover -->

                                        <button
                                            type="button"
                                            class="remove-product"
                                            onclick="removerProduto(
                                                <?= $index ?>
                                            )"
                                        >

                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Resumo -->

                <div class="col-lg-4">
                    <div class="card order-summary no-hover">
                        <div class="card-body">
                            <h2>
                                Resumo do pedido
                            </h2>

                            <div class="summary-line">
                                <span>
                                    Subtotal
                                </span>

                                <strong>

                                    R$

                                    <?= number_format(
                                        $total,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>
                            </div>


                            <div class="summary-line">
                                <span>
                                    Frete
                                </span>

                                <strong>
                                    A calcular
                                </strong>
                            </div>

                            <hr>

                            <div class="summary-total">
                                <span>
                                    Total
                                </span>
                                <strong>

                                    R$

                                    <?= number_format(
                                        $total,
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                                </strong>
                            </div>

                            <a
                                href="checkout.php"
                                class="btn primary checkout-btn"
                            >
                                Finalizar compra
                                <i class="bi bi-arrow-right"></i>
                            </a>

                            <div class="secure-purchase">
                                <i class="bi bi-shield-check"></i>
                                Compra segura
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
