<?php

require_once __DIR__ . '/../config/config.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    case 'register':
        handle_register($conn);
        exit;

    case 'edit':
        handle_edit($conn);
        exit;

    default:
        http_response_code(400);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/products.php"
        );

        exit;
    }

function handle_register($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $name = trim($_POST['name'] ?? '');
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (
        $name === '' ||
        $description === '' ||
        $category === '' ||
        $quantity < 0 ||
        $price < 0 ||
        !isset($_FILES['image'])
    ) {
        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-create.php?alert=data_empty"
        );

        exit;
    }


    /*
     * 1. Cria o produto inicialmente sem imagem
     */

    $image = '';

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO products
        (name, quantity, price, description, image, category)
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sidsss",
        $name,
        $quantity,
        $price,
        $description,
        $image,
        $category
    );


    if (!mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-create.php?alert=register_error"
        );

        exit;
    }


    /*
     * 2. Pegamos o ID gerado pelo MySQL
     */

    $product_id = mysqli_insert_id($conn);


    /*
     * 3. Verificamos a imagem
     */

    $uploaded_image = $_FILES['image'];


    if ($uploaded_image['error'] !== UPLOAD_ERR_OK) {

        mysqli_stmt_close($stmt);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-create.php?alert=image_error"
        );

        exit;
    }


    /*
     * 4. Descobrimos a extensão
     */

    $extension = strtolower(
        pathinfo(
            $uploaded_image['name'],
            PATHINFO_EXTENSION
        )
    );


    /*
     * 5. Permitimos somente algumas extensões
     */

    $allowed_extensions = [
        'jpg',
        'jpeg',
        'png',
        'webp'
    ];


    if (!in_array($extension, $allowed_extensions, true)) {

        mysqli_stmt_close($stmt);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-create.php?alert=image_error"
        );

        exit;
    }


    /*
     * 6. Nome final da imagem
     */

    $filename = 'product_' . $product_id . '.' . $extension;


    /*
     * 7. Pasta física onde a imagem será salva
     */

    $upload_directory =
        BASE_PATH . '/assets/images/products/';


    /*
     * 8. Caminho físico completo
     */

    $upload_path =
        $upload_directory . $filename;


    /*
     * 9. Move a imagem
     */

    if (!move_uploaded_file(
        $uploaded_image['tmp_name'],
        $upload_path
    )) {

        mysqli_stmt_close($stmt);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-create.php?alert=image_error"
        );

        exit;
    }


    /*
     * 10. Caminho que será salvo no banco
     */

    $image = 'assets/images/products/' . $filename;


    /*
     * 11. Atualiza o produto com o caminho da imagem
     */

    $stmt_update = mysqli_prepare(
        $conn,
        "UPDATE products
         SET image = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmt_update,
        "si",
        $image,
        $product_id
    );

    mysqli_stmt_execute($stmt_update);


    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_update);


    /*
     * 12. Volta para a página de produtos
     */

    header(
        "Location: " .
        BASE_URL .
        "dashboard/products.php?alert=register_success"
    );

    exit;
}

function handle_edit($conn)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $id = (int) ($_POST['id'] ?? 0);

    $name = trim($_POST['name'] ?? '');

    $quantity = (int) ($_POST['quantity'] ?? 0);

    $price = (float) ($_POST['price'] ?? 0);

    $description = trim($_POST['description'] ?? '');

    $category = trim($_POST['category'] ?? '');


    /*
     * Validação
     */

    if (
        $id <= 0 ||
        $name === '' ||
        $description === '' ||
        $category === '' ||
        $quantity < 0 ||
        $price < 0
    ) {

        header(
            "Location: " .
            BASE_URL .
            "dashboard/product-edit.php?id=" .
            $id .
            "&alert=data_empty"
        );

        exit;
    }


    /*
     * Busca a imagem atual
     */

    $stmt = mysqli_prepare(
        $conn,
        "SELECT image
         FROM products
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $current_product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if (!$current_product) {

        header(
            "Location: " .
            BASE_URL .
            "dashboard/products.php"
        );

        exit;
    }


    $image = $current_product['image'];


    /*
     * Verifica se foi enviada uma nova imagem
     */

    if (
        isset($_FILES['image']) &&
        $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $uploaded_image = $_FILES['image'];


        if ($uploaded_image['error'] !== UPLOAD_ERR_OK) {

            header(
                "Location: " .
                BASE_URL .
                "dashboard/product-edit.php?id=" .
                $id .
                "&alert=image_error"
            );

            exit;
        }


        /*
         * Descobre a extensão
         */

        $extension = strtolower(
            pathinfo(
                $uploaded_image['name'],
                PATHINFO_EXTENSION
            )
        );


        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'webp'
        ];


        if (!in_array($extension, $allowed_extensions, true)) {

            header(
                "Location: " .
                BASE_URL .
                "dashboard/product-edit.php?id=" .
                $id .
                "&alert=image_error"
            );

            exit;
        }


        /*
         * Novo nome baseado no ID
         */

        $filename = 'product_' . $id . '.' . $extension;


        /*
         * Caminho físico
         */

        $upload_directory =
            BASE_PATH . '/assets/images/products/';


        $upload_path =
            $upload_directory . $filename;


        /*
         * Salva a nova imagem
         */

        if (!move_uploaded_file(
            $uploaded_image['tmp_name'],
            $upload_path
        )) {

            header(
                "Location: " .
                BASE_URL .
                "dashboard/product-edit.php?id=" .
                $id .
                "&alert=image_error"
            );

            exit;
        }


        /*
         * Caminho que será salvo no banco
         */

        $image =
            'assets/images/products/' . $filename;
    }


    /*
     * Atualiza o produto
     */

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE products
         SET name = ?,
             quantity = ?,
             price = ?,
             description = ?,
             image = ?,
             category = ?
         WHERE id = ?"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "sidsssi",
        $name,
        $quantity,
        $price,
        $description,
        $image,
        $category,
        $id
    );


    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        header(
            "Location: " .
            BASE_URL .
            "dashboard/products.php?alert=update_success"
        );

        exit;
    }


    mysqli_stmt_close($stmt);


    header(
        "Location: " .
        BASE_URL .
        "dashboard/product-edit.php?id=" .
        $id .
        "&alert=update_error"
    );

    exit;
}
?>