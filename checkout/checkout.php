<?php
$page_title = "Nós de Luz - Checkout";
require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/auth.php';
require_once BASE_PATH . '/app/middleware/user.php';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
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


/*
|--------------------------------------------------------------------------
| Desconto VIP
|--------------------------------------------------------------------------
*/

$discount = 0;

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

    if ($user_vip && $user_vip['vip'] === 'sim') {
        $discount = $subtotal_cart * 0.10;
    }

    mysqli_stmt_close($stmt_vip);
}

/*
|--------------------------------------------------------------------------
| Total
|--------------------------------------------------------------------------
*/

$total = $subtotal_cart - $discount;

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';

?>

<div class="custom-main-container min-vh-100 section-spacer">
    <div class="row g-3">
        <div class="col-12 col-md-8">
            <form name="checkout-form" id="checkout-form" action="app/controllers/checkout-controller.php"
                method="POST">
                <div class="d-flex flex-column gap-3">
                    <div class="card no-hover">
                        <div class="card-body">
                            <div class="form-content mb-3">
                                <div class="form-header">
                                    <div class="icon-box">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <h3 class="form-title">Informações de Pagamento</h3>
                                </div>
                                <div class="form-body row g-3">
                                    <div class="form-field col-12">
                                        <label for="">Número do Cartão</label>
                                        <input class="form-control" name="card_number" id="card_number"
                                            placeholder="0000 0000 0000 0000" type="text" minlength="19" maxlength="19"
                                            required>
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="">Validade</label>
                                        <input class="form-control" name="card_date" id="card_date" placeholder="MM/AA"
                                            type="text" minlength="5" maxlength="5" required>
                                    </div>
                                    <div class="form-field col-6">
                                        <label for="">Código (CVV)</label>
                                        <input class="form-control" name="card_cvv" id="card_cvv" placeholder="000"
                                            type="text" minlength="4" maxlength="4" required>
                                    </div>
                                    <div class="form-field col-12">
                                        <label for="">Nome do Titular</label>
                                        <input class="form-control" name="card_name" id="card_name"
                                            placeholder="Usuário" type="text" minlength="3" maxlength="100" required>
                                    </div>
                                    <div class="form-field col-12">
                                        <label for="status">Parcelas</label>
                                        <select class="form-select" name="installments" id="installments"
                                            placeholder="Parcelas" autocomplete="off">
                                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                                <option value="<?= $i ?>">
                                                    <?= $i . 'x ' . 'de ' . number_format($total / $i, 2, ',', '.') ?>
                                                </option>
                                            <?php endfor ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="card no-hover">
                        <div class="card-body">
                            <div class="form-header">
                                <div class="icon-box">
                                    <i class="ri-bank-card-line"></i>
                                </div>
                                <h3 class="form-title">Forma de Pagamento</h3>
                            </div>
                            <div class="form-field col-12">
                                <label for="status">Forma de Pagamento</label>
                                <select class="form-select" name="payment_method" id="payment_method"
                                    placeholder="Forma de Pagamento" autocomplete="off">
                                    <option value="cartao">Cartão</option>
                                    <option value="pix">PIX</option>
                                    <option value="boleto">Boleto</option>
                                </select>
                            </div>
                        </div>
                    </div>-->
                </div>
            </form>
        </div>
        <div class="col-12 col-md-4 d-flex flex-column gap-3">
            <div class="card no-hover">
                <div class="card-body">
                    <div class="icon-box">
                        <i class="ri-shopping-bag-line"></i>
                    </div>
                    <h3 class="card-title">Resumo do Pedido</h3>
                    <hr>
                    <div class="payment-list d-flex flex-column gap-3">

                        <?php foreach ($products as $product): ?>

                            <?php
                            $product_id = (int) $product['id'];
                            $quantity = (int) (
                                $cart[$product_id] ?? 0
                            );
                            $price = (float) $product['price'];
                            $subtotal = $price * $quantity;
                            ?>

                            <div class="card no-hover">

                                <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="payment-product"><img 
                                        src="<?= BASE_URL . e($product['image']) ?>"
                                        alt="<?= e($product['name']) ?>">
                                    </div>
                                    <div>
                                        <h3 class="card-title"><?= e($product['name']) ?></h3>
                                        <p><?= e($quantity) ?>x R$ <?= number_format($subtotal, 2, ',', '.') ?></p>
                                    </div>
                                </div>
                            </div>  
                        </div>

                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="span-highlight">Subtotal</span>
                        <p>R$ <?= e(number_format($subtotal_cart, 2, ',', '.')) ?></p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <span class="span-highlight">Desconto</span>
                        <p>R$ <?= e(number_format($discount, 2, ',', '.')) ?></p>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <h3 class="card-title">Total:</h3>
                        <p class="card-title">R$ <?= e(number_format($total, 2, ',', '.')) ?></p>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn primary" form="checkout-form">Finalizar Compra</button>
            </div>
        </div>
    </div>
</div>

<?php
require_once BASE_PATH . '/components/layout/scripts.php';
?>