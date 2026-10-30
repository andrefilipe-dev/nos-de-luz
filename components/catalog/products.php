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
";

$result = mysqli_query($conn, $sql);

if (!$result) {
    error_log("Erro ao buscar produtos: " . mysqli_error($conn));
    $products = [];
} else {
    $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
}

?>

<!-- Cabeçalho, pesquisa e ordenação -->
<section class="mb-4 text-center section-spacer">
    <div class="custom-main-container">
        <div class="catalogo-hero-content">

            <span class="badge primary">
                Nós de Luz
            </span>

            <h1>Nosso Catálogo</h1>

            <p>
                Encontre produtos selecionados para você,
                com qualidade, praticidade e facilidade.
            </p>

        </div>
    </div>

    <div class="custom-main-container container-fluid">
        <div class="filtros-content row g-3 align-items-center">

            <!-- Pesquisa -->
            <div class="pesquisa-produtos col-12 col-md">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="text"
                        class="form-control"
                        id="pesquisaProduto"
                        placeholder="Pesquisar produtos..."
                        autocomplete="off"
                    >
                </div>
            </div>

            <!-- Ordenação -->
            <div class="ordenar-produtos col-12 col-md-auto">
                <div class="d-flex align-items-center gap-2">

                    <label
                        for="ordenacao"
                        class="form-label mb-0 text-nowrap"
                    >
                        Ordenar:
                    </label>

                    <select
                        class="form-select"
                        id="ordenacao"
                    >
                        <option value="relevancia">
                            Mais relevantes
                        </option>

                        <option value="nome-crescente">
                            Nome: A → Z
                        </option>

                        <option value="nome-decrescente">
                            Nome: Z → A
                        </option>

                        <option value="preco-crescente">
                            Menor preço
                        </option>

                        <option value="preco-decrescente">
                            Maior preço
                        </option>
                    </select>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- Catálogo de produtos -->
<section class="catalogo-produtos" id="produtos">
    <div class="custom-main-container">

        <!-- Introdução -->
        <div
            class="custom-text-container"
            data-aos="fade-up"
            data-aos-duration="800"
            data-aos-delay="100"
        >
            <span class="badge primary">Nossos Produtos</span>

            <p>
                Encontre produtos artesanais feitos com carinho
                e atenção aos detalhes.
            </p>
        </div>

        <!-- Lista -->
        <div
            class="row g-3"
            id="listaProdutos"
            data-aos="fade-up"
            data-aos-duration="800"
            data-aos-delay="300"
        >

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

                <div
                    class="col-12 col-md-6 col-lg-3 produto-item"
                    data-nome="<?= e($product['name']) ?>"
                    data-preco="<?= e($product['price']) ?>"
                    data-compras="<?= (int) $product['total_compras'] ?>"
                >

                    <div class="card">

                        <img
                            src="<?= BASE_URL . e($image) ?>"
                            class="card-img-top"
                            alt="<?= e($product['name']) ?>"
                            style="width: 100%; height: 250px; object-fit: cover;"
                        >

                        <div class="card-body d-flex flex-column">

                            <h3 class="card-title">
                                <?= e($product['name']) ?>
                            </h3>

                            <p class="card-text">
                                <?= e($product['description']) ?>
                            </p>

                            <strong class="fs-5 mb-3 mt-auto">
                                R$ <?= number_format(
                                    (float) $product['price'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>
                            </strong>

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

            <?php endforeach; ?>

            <!-- Mensagem para pesquisa sem resultados -->
            <div
                class="col-12 text-center py-4"
                id="nenhumProduto"
                style="display: none;"
            >
                <p class="text-muted mb-0">
                    Nenhum produto encontrado.
                </p>
            </div>

        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const pesquisa = document.getElementById('pesquisaProduto');
    const ordenacao = document.getElementById('ordenacao');
    const listaProdutos = document.getElementById('listaProdutos');
    const mensagemVazia = document.getElementById('nenhumProduto');

    function atualizarCatalogo() {

        const termo = pesquisa.value
            .trim()
            .toLocaleLowerCase('pt-BR');

        const criterio = ordenacao.value;

        const produtos = Array.from(
            listaProdutos.querySelectorAll('.produto-item')
        );

        // Ordena os produtos
        produtos.sort(function (a, b) {

            const nomeA = a.dataset.nome.toLocaleLowerCase('pt-BR');
            const nomeB = b.dataset.nome.toLocaleLowerCase('pt-BR');

            const precoA = Number(a.dataset.preco);
            const precoB = Number(b.dataset.preco);

            const comprasA = Number(a.dataset.compras);
            const comprasB = Number(b.dataset.compras);

            switch (criterio) {

                case 'nome-crescente':
                    return nomeA.localeCompare(nomeB, 'pt-BR');

                case 'nome-decrescente':
                    return nomeB.localeCompare(nomeA, 'pt-BR');

                case 'preco-crescente':
                    return precoA - precoB;

                case 'preco-decrescente':
                    return precoB - precoA;

                case 'relevancia':
                default:
                    // Mais pedidos primeiro.
                    // Em caso de empate, ordena pelo nome.
                    return comprasB - comprasA ||
                        nomeA.localeCompare(nomeB, 'pt-BR');
            }
        });

        let produtosVisiveis = 0;

        produtos.forEach(function (produto) {

            const nome = produto.dataset.nome
                .toLocaleLowerCase('pt-BR');

            const descricao = produto
                .querySelector('.card-text')
                .textContent
                .trim()
                .toLocaleLowerCase('pt-BR');

            const corresponde =
                nome.includes(termo) ||
                descricao.includes(termo);

            produto.style.display = corresponde ? '' : 'none';

            if (corresponde) {
                produtosVisiveis++;
            }

            // Reposiciona o card conforme a ordenação.
            listaProdutos.insertBefore(produto, mensagemVazia);
        });

        mensagemVazia.style.display =
            produtosVisiveis === 0 ? '' : 'none';
    }

    // Pesquisa em tempo real
    pesquisa.addEventListener('input', atualizarCatalogo);

    // Ordenação
    ordenacao.addEventListener('change', atualizarCatalogo);

    // Ordenação inicial por relevância
    atualizarCatalogo();

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