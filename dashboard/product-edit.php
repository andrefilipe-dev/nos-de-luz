<?php
$page_title = "Nós de Luz - Editar Produto";

require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: " . BASE_URL . "dashboard/products.php");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "SELECT id, name, quantity, price, description, image, category
     FROM products
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$product) {
    header("Location: " . BASE_URL . "dashboard/products.php");
    exit;
}

$alert = $_GET['alert'] ?? '';

require_once BASE_PATH . '/components/layout/head.php';
require_once BASE_PATH . '/components/layout/header.php';
?>

<div class="custom-main-container min-vh-100 section-spacer">

    <div class="row g-3">

        <div class="col-12">

            <!-- ALERTAS -->

            <span class="badge secondary red mb-3 <?= ($alert === "data_empty") ? "d-inline-block" : "d-none" ?>">
                Preencha todos os campos obrigatórios.
            </span>

            <span class="badge secondary red mb-3 <?= ($alert === "update_error") ? "d-inline-block" : "d-none" ?>">
                Falha ao atualizar produto.
            </span>

            <span class="badge secondary red mb-3 <?= ($alert === "image_error") ? "d-inline-block" : "d-none" ?>">
                Falha ao atualizar a imagem.
            </span>

            <span class="badge secondary green mb-3 <?= ($alert === "update_success") ? "d-inline-block" : "d-none" ?>">
                Produto atualizado com sucesso.
            </span>


            <form
                action="<?= BASE_URL ?>app/controllers/product-controller.php"
                method="POST"
                enctype="multipart/form-data"
            >

                <input type="hidden" name="action" value="edit">

                <input
                    type="hidden"
                    name="id"
                    value="<?= e($product['id']) ?>"
                >


                <div class="d-flex flex-column gap-3">


                    <!-- INFORMAÇÕES DO PRODUTO -->

                    <div class="card no-hover">

                        <div class="card-body">

                            <div class="form-content">

                                <div class="form-header">

                                    <div class="icon-box">
                                        <i class="ri-shopping-bag-line"></i>
                                    </div>

                                    <h3 class="form-title">
                                        Informações do Produto
                                    </h3>

                                </div>


                                <div class="form-body row g-3">


                                    <!-- NOME -->

                                    <div class="form-field col-12">

                                        <label for="name">
                                            Nome
                                        </label>

                                        <input
                                            class="form-control"
                                            name="name"
                                            id="name"
                                            type="text"
                                            minlength="2"
                                            maxlength="250"
                                            value="<?= e($product['name']) ?>"
                                            required
                                        >

                                    </div>


                                    <!-- PREÇO -->

                                    <div class="form-field col-6">

                                        <label for="price">
                                            Preço
                                        </label>

                                        <input
                                            class="form-control"
                                            name="price"
                                            id="price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            value="<?= e($product['price']) ?>"
                                            required
                                        >

                                    </div>


                                    <!-- QUANTIDADE -->

                                    <div class="form-field col-6">

                                        <label for="quantity">
                                            Quantidade
                                        </label>

                                        <input
                                            class="form-control"
                                            name="quantity"
                                            id="quantity"
                                            type="number"
                                            min="0"
                                            step="1"
                                            value="<?= e($product['quantity']) ?>"
                                            required
                                        >

                                    </div>


                                    <!-- CATEGORIA -->

                                    <div class="form-field col-12">

                                        <label for="category">
                                            Categoria
                                        </label>

                                        <input
                                            class="form-control"
                                            name="category"
                                            id="category"
                                            type="text"
                                            maxlength="50"
                                            value="<?= e($product['category']) ?>"
                                            required
                                        >

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- DESCRIÇÃO -->

                    <div class="card no-hover">

                        <div class="card-body">

                            <div class="form-content">

                                <div class="form-header">

                                    <div class="icon-box">
                                        <i class="ri-file-text-line"></i>
                                    </div>

                                    <h3 class="form-title">
                                        Descrição
                                    </h3>

                                </div>


                                <div class="form-body row g-3">

                                    <div class="form-field col-12">

                                        <label for="description">
                                            Descrição do produto
                                        </label>

                                        <textarea
                                            class="form-control"
                                            name="description"
                                            id="description"
                                            rows="6"
                                            maxlength="5000"
                                            required
                                        ><?= e($product['description']) ?></textarea>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- IMAGEM -->

                    <div class="card no-hover">

                        <div class="card-body">

                            <div class="form-content">

                                <div class="form-header">

                                    <div class="icon-box">
                                        <i class="ri-image-line"></i>
                                    </div>

                                    <h3 class="form-title">
                                        Imagem do Produto
                                    </h3>

                                </div>


                                <div class="form-body row g-3">


                                    <!-- IMAGEM ATUAL -->

                                    <div class="form-field col-12">

                                        <label>
                                            Imagem atual
                                        </label>

                                        <div>
                                            <img
                                                src="<?= BASE_URL . e($product['image']) ?>"
                                                alt="<?= e($product['name']) ?>"
                                                style="width: 150px; height: 150px; object-fit: cover;"
                                            >
                                        </div>

                                    </div>


                                    <!-- NOVA IMAGEM -->

                                    <div class="form-field col-12">

                                        <label for="image">
                                            Alterar imagem
                                        </label>

                                        <input
                                            class="form-control"
                                            name="image"
                                            id="image"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                        >

                                        <small class="text-muted">
                                            Deixe vazio para manter a imagem atual.
                                        </small>

                                    </div>


                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- BOTÃO -->

                    <div class="d-flex w-100 justify-content-end p-0">

                        <button
                            type="submit"
                            class="btn secondary"
                        >
                            Salvar Alterações
                        </button>

                    </div>


                </div>

            </form>

        </div>

    </div>

</div>

<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>