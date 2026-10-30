<?php
$page_title = "Nós de Luz - Carrinho";

require_once __DIR__ . '/app/config/config.php';

?>

<?php if (!empty($_SESSION['checkout_error'])): ?>
    <div class="alert alert-danger" role="alert">
        <strong>Diagnóstico do checkout:</strong>
        <?= e($_SESSION['checkout_error']) ?>
    </div>
    <?php unset($_SESSION['checkout_error']); ?>
<?php endif; ?>

<?php
/*
|--------------------------------------------------------------------------
| Carrinho
|--------------------------------------------------------------------------
|
| Exemplo:
|
| $_SESSION['cart'] = [
|     5 => 2,
|     8 => 1,
|     13 => 3
| ];
|
| ID do produto => quantidade
|
*/

$cart = $_SESSION['cart'] ?? [];

$products = [];

$subtotal_cart = 0;


/*
|--------------------------------------------------------------------------
| Busca os produtos do carrinho
|--------------------------------------------------------------------------
*/

if (!empty($cart)) {

    $ids = array_keys($cart);

    $placeholders = implode(
        ',',
        array_fill(0, count($ids), '?')
    );

    $types = str_repeat('i', count($ids));

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, name, price, description, image
         FROM products
         WHERE id IN ($placeholders)"
    );

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            $types,
            ...$ids
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        $products = mysqli_fetch_all(
            $result,
            MYSQLI_ASSOC
        );

        mysqli_stmt_close($stmt);
    }
}


/*
|--------------------------------------------------------------------------
| Calcula o subtotal geral
|--------------------------------------------------------------------------
*/

foreach ($products as $product) {

    $product_id = (int) $product['id'];

    $quantity = (int) (
        $cart[$product_id] ?? 0
    );

    $price = (float) $product['price'];

    $subtotal = $price * $quantity;

    $subtotal_cart += $subtotal;
}

$is_vip = false;

if (isset($_SESSION['id'])) {
    $stmt_vip = mysqli_prepare(
        $conn,
        "SELECT vip FROM users WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt_vip,
        "i",
        $_SESSION['id']
    );

    mysqli_stmt_execute($stmt_vip);

    $result_vip = mysqli_stmt_get_result($stmt_vip);
    $user_vip = mysqli_fetch_assoc($result_vip);

    $is_vip = $user_vip && $user_vip['vip'] === 'sim';

    mysqli_stmt_close($stmt_vip);
}

/*
|--------------------------------------------------------------------------
| Desconto VIP
|--------------------------------------------------------------------------
*/

$discount = 0;

if ($is_vip) {
    $discount = $subtotal_cart * 0.10;
}

/*
|--------------------------------------------------------------------------
| Total
|--------------------------------------------------------------------------
*/

$total = $subtotal_cart - $discount;


require_once BASE_PATH . '/components/cart/head.php';
require_once BASE_PATH . '/components/layout/header.php';


?>


<div class="custom-main-container section-spacer">

    <div class="row g-3">


        <!-- =========================================================
             PRODUTOS
        ========================================================== -->

        <div class="col-12 col-md-8">

            <div class="card no-hover">

                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-shopping-cart-line"></i>
                    </div>
                    <h3 class="card-title">
                        Meu carrinho
                    </h3>
                    <p>
                        Confira os produtos selecionados
                        antes de finalizar sua compra.
                    </p>
                    <hr>
                    <div class="projects-list overflow-x-hidden">

                        <div class="row g-3">
                            <?php if (!empty($products)): ?>

                                <?php foreach ($products as $product): ?>

                                    <?php
                                    $product_id = (int) $product['id'];
                                    $quantity = (int) (
                                        $cart[$product_id] ?? 0
                                    );
                                    $price = (float) $product['price'];
                                    $subtotal = $price * $quantity;
                                    ?>


                                    <div class="col-12">

                                        <div class="card no-hover product h-100">

                                            <div class="card-body">

                                                <div
                                                    class="d-flex flex-wrap gap-3 justify-content-between"
                                                >


                                                    <!-- IMAGEM -->

                                                    <div class="product-img sm">

                                                        <img
                                                            src="<?= BASE_URL . e($product['image']) ?>"
                                                            alt="<?= e($product['name']) ?>"
                                                        >

                                                    </div>


                                                    <!-- INFORMAÇÕES -->

                                                    <div class="flex-grow-1">


                                                        <h3 class="card-title">

                                                            <?= e($product['name']) ?>

                                                        </h3>


                                                        <p>

                                                            <?= e($product['description']) ?>

                                                        </p>


                                                        <!-- PREÇO -->

                                                        <p class="product-price">

                                                            R$

                                                            <?= number_format(
                                                                $price,
                                                                2,
                                                                ',',
                                                                '.'
                                                            ) ?>

                                                        </p>


                                                        <div
                                                            class="d-flex justify-content-between align-items-end gap-3"
                                                        >


                                                            <!-- QUANTIDADE -->

                                                            <form
                                                                action="<?= BASE_URL ?>app/controllers/cart-controller.php"
                                                                method="POST"
                                                            >
                                                                <input
                                                                    type="hidden"
                                                                    name="action"
                                                                    value="update"
                                                                >
                                                                <input
                                                                    type="hidden"
                                                                    name="id"
                                                                    value="<?= $product_id ?>"
                                                                >

                                                                <div class="form-field">
                                                                    <label for="quantity-<?= $product_id ?>">
                                                                        Quantidade
                                                                    </label>

                                                                    <input
                                                                        class="form-control cart-quantity"
                                                                        type="number"
                                                                        min="1"
                                                                        max="100"
                                                                        value="<?= $quantity ?>"
                                                                        id="quantity-<?= $product_id ?>"
                                                                        data-id="<?= $product_id ?>"
                                                                        data-price="<?= $price ?>"
                                                                    >
                                                                </div>

                                                            </form>


                                                            <!-- SUBTOTAL -->

                                                            <div class="text-end">

                                                                <span class="span-highlight">
                                                                    Subtotal
                                                                </span>

                                                                <p
                                                                    class="mb-0 product-subtotal"
                                                                    id="subtotal-<?= $product_id ?>"
                                                                >
                                                                    R$ <?= number_format($subtotal, 2, ',', '.') ?>
                                                                </p>

                                                            </div>

                                                        </div>

                                                        <!-- REMOVER -->

                                                        <div
                                                            class="d-flex justify-content-end mt-3"
                                                        >

                                                            <a
                                                                href="<?= BASE_URL ?>app/controllers/cart-controller.php?action=remove&id=<?= $product_id ?>"
                                                                class="btn secondary red"
                                                            >
                                                                Remover
                                                            </a>

                                                        </div>


                                                    </div>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <!-- CARRINHO VAZIO -->

                                <div class="col-12">

                                    <div class="text-center py-4">

                                        <i class="ri-shopping-cart-line fs-1"></i>


                                        <h3>
                                            Seu carrinho está vazio.
                                        </h3>


                                        <p>
                                            Adicione alguns produtos
                                            para começar sua compra.
                                        </p>


                                        <a
                                            href="<?= BASE_URL ?>catalog.php"
                                            class="btn primary"
                                        >
                                            Ver produtos
                                        </a>

                                    </div>

                                </div>


                            <?php endif; ?>


                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             RESUMO
        ========================================================== -->

        <div class="col-12 col-md-4">


            <div class="card no-hover mb-3">

                <div class="card-body">


                    <div class="icon-box">
                        <i class="ri-file-list-3-line"></i>
                    </div>


                    <h3 class="card-title">
                        Resumo do pedido
                    </h3>


                    <p>
                        Confira os valores e o total do seu pedido.
                    </p>


                    <!-- SUBTOTAL -->

                    <div
                        class="d-flex justify-content-between align-items-center w-100"
                    >

                        <span class="span-highlight">
                            Subtotal
                        </span>

                        <p id="cart-subtotal">
                            R$ <?= number_format($subtotal_cart, 2, ',', '.') ?>
                        </p>

                    </div>


                    <!-- DESCONTO -->

                    <div
                        class="d-flex justify-content-between align-items-center w-100"
                    >

                        <span class="span-highlight">
                            Desconto
                        </span>


                        <p id="cart-discount">

                            R$

                            <?= number_format(
                                $discount,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </p>

                    </div>

                    <hr>


                    <!-- TOTAL -->

                    <div
                        class="d-flex justify-content-between align-items-center w-100"
                    >

                        <h3 class="card-title">
                            Total:
                        </h3>

                        <p class="card-title" id="cart-total">
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </p>

                    </div>

                </div>
            </div>

            
            <!-- PAGAMENTO E BENEFÍCIO VIP -->

            <?php if (!empty($products)): ?>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">


                    <?php if (!$is_vip): ?>

                    <!-- INFORMAÇÕES VIP -->
                    <div class="vip-info">
                        <p class="mb-2">
                            <i class="bi bi-star-fill"></i>
                            <strong>Usuário VIP possui 10% de desconto</strong>
                        </p>

                        <button
                            type="button"
                            class="btn secondary"
                            id="btn-vip-info"
                        >
                            Mais informações
                        </button>
                    </div>
                    
                    <?php endif; ?>
                    
                    <!-- CONTINUAR PARA PAGAMENTO -->
                    <form
                        action="<?= BASE_URL ?>checkout/checkout.php"
                        method="GET"
                    >
                        <button type="submit" class="btn primary">
                            Continuar para pagamento
                        </button>
                    </form>
                    
                    
                </div>
            <?php endif; ?>

            <?php if (!$is_vip): ?>

            <!-- POP-UP DE INFORMAÇÕES VIP -->
            <div
                id="vip-modal"
                class="vip-modal"
                aria-hidden="true"
            >
                <div
                    class="vip-modal-content"
                    role="dialog"
                    aria-modal="true"
                    aria-labelledby="vip-modal-title"
                >
                    <button
                        type="button"
                        class="vip-modal-close"
                        id="btn-vip-close"
                        aria-label="Fechar"
                    >
                        &times;
                    </button>

                    <div class="icon-box">
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <h3 id="vip-modal-title">Benefício para usuários VIP</h3>

                    <p>
                        Clientes VIP do Nós de Luz recebem <strong>10% de desconto</strong>
                        no valor dos produtos do carrinho.
                    </p>

                    <p>
                        O benefício é aplicado automaticamente para contas
                        cadastradas como VIP. Se você deseja saber como obter
                        esse benefício ou tem dúvidas sobre sua conta, entre em contato.
                    </p>

                    <div class="vip-contact">
                        <strong>Contato para mais informações</strong>
                        <p class="mb-0">
                            <i class="bi bi-whatsapp"></i>
                            <a href="https://wa.me/55DDDNUMERO" target="_blank" rel="noopener noreferrer">
                                (DD) 99999-9999
                            </a>
                        </p>
                    </div>

                    <button
                        type="button"
                        class="btn primary mt-3"
                        id="btn-vip-understood"
                    >
                        Entendi
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>

const descontoVIP = <?= json_encode(
    ($discount > 0 && $subtotal_cart > 0)
        ? $discount / $subtotal_cart
        : 0
) ?>;

document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('.cart-quantity');
    const subtotalElement = document.getElementById('cart-subtotal');
    const totalElement = document.getElementById('cart-total');

    function formatPrice(value) {
        return value.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function updateDisplay() {
        let subtotalCart = 0;

        inputs.forEach(input => {
            const quantity = Math.max(
                1,
                Math.min(100, parseInt(input.value, 10) || 1)
            );

            const price = Number(input.dataset.price);
            const subtotal = price * quantity;

            subtotalCart += subtotal;

            const productSubtotal = document.getElementById(
                `subtotal-${input.dataset.id}`
            );

            if (productSubtotal) {
                productSubtotal.textContent =
                    `R$ ${formatPrice(subtotal)}`;
            }
        });

        const desconto = subtotalCart * descontoVIP;
        const total = subtotalCart - desconto;

        subtotalElement.textContent =
            `R$ ${formatPrice(subtotalCart)}`;

        document.getElementById('cart-discount').textContent =
            `R$ ${formatPrice(desconto)}`;

        totalElement.textContent =
            `R$ ${formatPrice(total)}`;
    }

    async function saveQuantity(id, quantity) {
        const body = new URLSearchParams({
            action: 'update',
            id: id,
            quantity: quantity
        });

        try {
            const response = await fetch(
                '<?= BASE_URL ?>app/controllers/cart-controller.php',
                {
                    method: 'POST',
                    headers: {
                        'Content-Type':
                            'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: body.toString()
                }
            );

            if (!response.ok) {
                throw new Error('Falha ao salvar a quantidade.');
            }
        } catch (error) {
            console.error('Erro ao atualizar o carrinho:', error);
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', updateDisplay);

        input.addEventListener('change', () => {
            const quantity = Math.max(
                1,
                Math.min(100, parseInt(input.value, 10) || 1)
            );

            input.value = quantity;
            updateDisplay();
            saveQuantity(input.dataset.id, quantity);
        });
    });

    updateDisplay();
});

const vipModal = document.getElementById('vip-modal');
const btnVipInfo = document.getElementById('btn-vip-info');
const btnVipClose = document.getElementById('btn-vip-close');
const btnVipUnderstood = document.getElementById('btn-vip-understood');

if (vipModal && btnVipInfo && btnVipClose && btnVipUnderstood) {
    function openVipModal() {
        vipModal.classList.add('is-open');
        vipModal.setAttribute('aria-hidden', 'false');
        btnVipClose.focus();
    }

    function closeVipModal() {
        vipModal.classList.remove('is-open');
        vipModal.setAttribute('aria-hidden', 'true');
        btnVipInfo.focus();
    }

    btnVipInfo.addEventListener('click', openVipModal);
    btnVipClose.addEventListener('click', closeVipModal);
    btnVipUnderstood.addEventListener('click', closeVipModal);

    vipModal.addEventListener('click', (event) => {
        if (event.target === vipModal) {
            closeVipModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (
            event.key === 'Escape' &&
            vipModal.classList.contains('is-open')
        ) {
            closeVipModal();
        }
    });
}

</script>

<?php

require_once BASE_PATH . '/components/layout/scripts.php';

?>