
<?php

require_once __DIR__ . '/../config/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/*
|--------------------------------------------------------------------------
| CONFIGURAÇÃO DO DIAGNÓSTICO
|--------------------------------------------------------------------------
| true: mostra o erro detalhado no carrinho. Use apenas localmente.
| false: mostra mensagem genérica e mantém detalhes no log do PHP.
*/
$debug_checkout = true;

$etapa = 'Inicialização';
$transacao_iniciada = false;

function checkout_log(string $mensagem): void
{
    error_log('[CHECKOUT] ' . $mensagem);
}

function checkout_fail(string $mensagem): void
{
    throw new RuntimeException($mensagem);
}

try {
    checkout_log('========== NOVA TENTATIVA ==========');
    checkout_log('Método HTTP: ' . ($_SERVER['REQUEST_METHOD'] ?? 'desconhecido'));
    checkout_log('BASE_URL: ' . BASE_URL);

    // 1. Verifica método HTTP
    $etapa = 'Verificação do método HTTP';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        checkout_fail(
            'O controlador recebeu uma requisição que não é POST.'
        );
    }

    // 2. Verifica conexão com o banco
    $etapa = 'Conexão com o banco';

    if (!isset($conn) || !($conn instanceof mysqli)) {
        checkout_fail('A variável $conn não contém uma conexão mysqli válida.');
    }

    if (mysqli_connect_errno()) {
        checkout_fail(
            'Erro de conexão: ' . mysqli_connect_error()
        );
    }

    $db_result = mysqli_query($conn, 'SELECT DATABASE() AS nome_banco');
    $db_info = mysqli_fetch_assoc($db_result);
    $nome_banco = $db_info['nome_banco'] ?? null;

    checkout_log('Banco conectado: ' . ($nome_banco ?? 'nenhum'));

    if (!$nome_banco) {
        checkout_fail('Nenhum banco de dados está selecionado.');
    }

    // 3. Verifica sessão e autenticação
    $etapa = 'Verificação da sessão';

    checkout_log('Nome da sessão: ' . session_name());
    checkout_log('ID de sessão disponível: ' . (session_id() !== '' ? 'sim' : 'não'));
    checkout_log('Chaves da sessão: ' . implode(', ', array_keys($_SESSION)));

    $user_id = (int) ($_SESSION['id'] ?? 0);

    if ($user_id <= 0) {
        checkout_fail(
            'Não foi encontrado um user_id válido em $_SESSION. ' .
            'Verifique qual chave o login do Nos de Luz utiliza.'
        );
    }

    checkout_log('ID do usuário encontrado: ' . $user_id);

    // 4. Verifica carrinho
    $etapa = 'Verificação do carrinho';

    $cart = $_SESSION['cart'] ?? [];

    if (!is_array($cart) || empty($cart)) {
        checkout_fail(
            'O carrinho está vazio ou $_SESSION["cart"] não é um array.'
        );
    }

    checkout_log('Quantidade de produtos diferentes no carrinho: ' . count($cart));

    // 5. Verifica se as tabelas existem no banco selecionado
    $etapa = 'Verificação das tabelas';

    $tabelas_necessarias = [
        'products',
        'orders',
        'orders_items'
    ];

    foreach ($tabelas_necessarias as $tabela) {
        $stmt = mysqli_prepare(
            $conn,
            'SELECT COUNT(*) AS total
             FROM information_schema.tables
             WHERE table_schema = DATABASE()
               AND table_name = ?'
        );

        mysqli_stmt_bind_param($stmt, 's', $tabela);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $linha = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($stmt);

        if ((int) ($linha['total'] ?? 0) !== 1) {
            checkout_fail(
                "A tabela '{$tabela}' não foi encontrada no banco '{$nome_banco}'."
            );
        }

        checkout_log("Tabela encontrada: {$tabela}");
    }

    // 6. Verifica colunas utilizadas pelo checkout
    $etapa = 'Verificação das colunas';

    $colunas_necessarias = [
        'products' => ['id', 'price', 'quantity'],
        'orders' => [
            'id', 'user_id', 'total',
            'payment_status', 'order_status'
        ],
        'orders_items' => [
            'id', 'order_id', 'product_id', 'quantity', 'price'
        ]
    ];

    foreach ($colunas_necessarias as $tabela => $colunas) {
        foreach ($colunas as $coluna) {
            $stmt = mysqli_prepare(
                $conn,
                'SELECT COUNT(*) AS total
                 FROM information_schema.columns
                 WHERE table_schema = DATABASE()
                   AND table_name = ?
                   AND column_name = ?'
            );

            mysqli_stmt_bind_param($stmt, 'ss', $tabela, $coluna);
            mysqli_stmt_execute($stmt);

            $resultado = mysqli_stmt_get_result($stmt);
            $linha = mysqli_fetch_assoc($resultado);

            mysqli_stmt_close($stmt);

            if ((int) ($linha['total'] ?? 0) !== 1) {
                checkout_fail(
                    "Coluna '{$tabela}.{$coluna}' não encontrada."
                );
            }
        }
    }

    checkout_log('Todas as colunas necessárias foram encontradas.');

    // 7. Inicia transação
    $etapa = 'Início da transação';

    mysqli_begin_transaction($conn);
    $transacao_iniciada = true;

    // 8. Valida produtos, preços e estoque
    $etapa = 'Validação dos produtos e do estoque';

    $orders_items = [];
    $total = 0.0;

    foreach ($cart as $id => $quantity) {
        $product_id = filter_var($id, FILTER_VALIDATE_INT);
        $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

        if (
            $product_id === false ||
            $product_id === null ||
            $product_id <= 0
        ) {
            checkout_fail("ID de produto inválido no carrinho: " . (string) $id);
        }

        if (
            $quantity === false ||
            $quantity === null ||
            $quantity < 1 ||
            $quantity > 100
        ) {
            checkout_fail("Quantidade inválida para o produto {$product_id}.");
        }

        checkout_log(
            "Validando produto {$product_id}, quantidade {$quantity}."
        );

        $stmt = mysqli_prepare(
            $conn,
            'SELECT id, price, quantity
             FROM products
             WHERE id = ?
             FOR UPDATE'
        );

        mysqli_stmt_bind_param($stmt, 'i', $product_id);
        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);
        $produto = mysqli_fetch_assoc($resultado);

        mysqli_stmt_close($stmt);

        if (!$produto) {
            checkout_fail(
                "Produto {$product_id} não existe na tabela products."
            );
        }

        $estoque = (int) $produto['quantity'];

        if ($estoque < $quantity) {
            checkout_fail(
                "Estoque insuficiente para o produto {$product_id}. " .
                "Solicitado: {$quantity}; disponível: {$estoque}."
            );
        }

        $price = (float) $produto['price'];

        if (!is_finite($price) || $price < 0) {
            checkout_fail("Preço inválido para o produto {$product_id}.");
        }

        $total += round($price * $quantity, 2);

        $orders_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    $total = round($total, 2);

    checkout_log('Total calculado: ' . number_format($total, 2, '.', ''));

    // 9. Confere se o usuário existe.
    // A coluna users.id é presumida aqui; ajuste se sua tabela usar outro nome.
    $etapa = 'Verificação do usuário';

    $stmt = mysqli_prepare(
        $conn,
        'SELECT id FROM users WHERE id = ?'
    );

    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);

    $resultado = mysqli_stmt_get_result($stmt);
    $usuario = mysqli_fetch_assoc($resultado);

    mysqli_stmt_close($stmt);

    if (!$usuario) {
        checkout_fail(
            "O usuário {$user_id} não foi encontrado na tabela users. " .
            'Confirme a tabela e a chave usada pelo sistema de login.'
        );
    }

    // 10. Insere o pedido principal
    $etapa = 'Inserção do pedido em orders';

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO orders
            (user_id, total, payment_status, order_status)
         VALUES (?, ?, 'pendente', 'pendente')"
    );

    mysqli_stmt_bind_param($stmt, 'id', $user_id, $total);
    mysqli_stmt_execute($stmt);

    $order_id = mysqli_insert_id($conn);

    mysqli_stmt_close($stmt);

    if ($order_id <= 0) {
        checkout_fail(
            'O pedido foi executado, mas o banco não retornou um ID válido.'
        );
    }

    checkout_log("Pedido criado dentro da transação. ID: {$order_id}");

    // 11. Insere itens e atualiza estoque
    foreach ($orders_items as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        $etapa = "Inserção do produto {$product_id} em orders_items";

        $stmt = mysqli_prepare(
            $conn,
            'INSERT INTO orders_items
                (order_id, product_id, quantity, price)
             VALUES (?, ?, ?, ?)'
        );

        mysqli_stmt_bind_param(
            $stmt,
            'iiid',
            $order_id,
            $product_id,
            $quantity,
            $price
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $etapa = "Atualização do estoque do produto {$product_id}";

        $stmt = mysqli_prepare(
            $conn,
            'UPDATE products
             SET quantity = quantity - ?
             WHERE id = ? AND quantity >= ?'
        );

        mysqli_stmt_bind_param(
            $stmt,
            'iii',
            $quantity,
            $product_id,
            $quantity
        );

        mysqli_stmt_execute($stmt);

        $afetadas = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        if ($afetadas !== 1) {
            checkout_fail(
                "O estoque do produto {$product_id} não foi atualizado."
            );
        }
    }

    // 12. Confirma a transação
    $etapa = 'Confirmação da transação';

    mysqli_commit($conn);
    $transacao_iniciada = false;

    checkout_log("COMPRA CONCLUÍDA. Pedido {$order_id} confirmado.");

    unset($_SESSION['cart']);
    unset($_SESSION['checkout_error']);

    header('Location: ' . BASE_URL . 'checkout/checkout-success.php?confirm=true');
    exit;

} catch (Throwable $e) {
    if ($transacao_iniciada) {
        try {
            mysqli_rollback($conn);
        } catch (Throwable $rollback_error) {
            checkout_log(
                'Erro adicional no rollback: ' . $rollback_error->getMessage()
            );
        }
    }

    $detalhe = sprintf(
        'Etapa: %s | Erro: %s | Arquivo: %s | Linha: %d',
        $etapa,
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    );

    checkout_log($detalhe);

    if ($debug_checkout) {
        $_SESSION['checkout_error'] = $detalhe;
    } else {
        $_SESSION['checkout_error'] =
            'Não foi possível concluir o pedido. Confira o carrinho e tente novamente.';
    }

    header('Location: ' . BASE_URL . 'cart.php');
    exit;
}

?>

