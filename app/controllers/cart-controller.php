<?php

require_once __DIR__ . '/../config/config.php';





/*
|--------------------------------------------------------------------------
| Descobre a ação
|--------------------------------------------------------------------------
*/

$action = $_GET['action'] ?? $_POST['action'] ?? '';

$isAjax = strtolower(
    $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''
) === 'xmlhttprequest';


// Verifica autenticação

if (!isset($_SESSION['id'])) {

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'redirect' => BASE_URL . 'register.php',
            'message' => 'Você precisa estar logado para adicionar produtos ao carrinho.'
        ]);

        exit;
    }

    header('Location: ' . BASE_URL . 'register.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| Executa a ação
|--------------------------------------------------------------------------
*/

switch ($action) {

    case 'add':
        handle_add($conn);
        break;


    case 'update':
        handle_update();
        break;


    case 'remove':
        handle_remove();
        break;


    default:

        header(
            "Location: " . BASE_URL . "cart.php"
        );

        exit;
}


/*
|--------------------------------------------------------------------------
| ADICIONAR PRODUTO
|--------------------------------------------------------------------------
*/



function handle_add($conn)
{
    global $isAjax;

    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

    // Resposta para requisições AJAX ou navegação normal
    $respond = function (
        bool $success,
        string $message,
        int $status = 200
    ) use ($isAjax) {
        if ($isAjax) {
            http_response_code($status);
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => $success,
                'message' => $message,
                'cartCount' => array_sum($_SESSION['cart'] ?? [])
            ]);

            exit;
        }

        if (!$success) {
            $_SESSION['cart_error'] = $message;
        }

        header('Location: ' . BASE_URL . 'cart.php');
        exit;
    };

    // Valida o ID
    if ($id <= 0) {
        $respond(false, 'Produto inválido.', 400);
    }

    // Busca produto e estoque
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, quantity FROM products WHERE id = ?"
    );

    if (!$stmt) {
        $respond(false, 'Não foi possível consultar o produto.', 500);
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$product) {
        $respond(false, 'Produto não encontrado.', 404);
    }

    $stock = (int) $product['quantity'];

    if ($stock <= 0) {
        $respond(false, 'Produto fora de estoque.', 409);
    }

    // Inicializa o carrinho
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $currentQuantity = (int) ($_SESSION['cart'][$id] ?? 0);

    // Impede ultrapassar o estoque
    if ($currentQuantity >= $stock) {
        $respond(false, 'Você já adicionou todo o estoque disponível.', 409);
    }

    // Adiciona uma unidade
    $_SESSION['cart'][$id] = $currentQuantity + 1;

    // Retorna a quantidade atualizada ao JavaScript
    $respond(true, 'Produto adicionado ao carrinho.');
}


/*
|--------------------------------------------------------------------------
| ATUALIZAR QUANTIDADE
|--------------------------------------------------------------------------
*/

function handle_update()
{
    $id = (int) ($_POST['id'] ?? 0);

    $quantity = (int) (
        $_POST['quantity'] ?? 0
    );


    /*
    | Dados inválidos
    */

    if ($id <= 0 || $quantity < 1) {

        header(
            "Location: " . BASE_URL . "cart.php"
        );

        exit;
    }


    /*
    | Produto não está no carrinho
    */

    if (!isset($_SESSION['cart'][$id])) {

        header(
            "Location: " . BASE_URL . "cart.php"
        );

        exit;
    }


    /*
    | Limita a quantidade máxima
    */

    if ($quantity > 100) {

        $quantity = 100;

    }


    /*
    | Atualiza a sessão
    */

    $_SESSION['cart'][$id] = $quantity;

    
    /*
    | Volta para o carrinho
    */

    if (
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')
        === 'xmlhttprequest'
    ) {
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'id' => $id,
            'quantity' => $quantity
        ]);

        exit;
    }

    header("Location: " . BASE_URL . "cart.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| REMOVER PRODUTO
|--------------------------------------------------------------------------
*/

function handle_remove()
{
    $id = (int) ($_GET['id'] ?? 0);


    if ($id > 0) {

        unset(
            $_SESSION['cart'][$id]
        );

    }


    /*
    | Volta para o carrinho
    */

    header(
        "Location: " . BASE_URL . "cart.php"
    );

    exit;
}

?>