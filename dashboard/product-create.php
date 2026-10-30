<?php
$page_title = "Nós de Luz - Novo Produto";

require_once __DIR__ . '/../app/config/config.php';
require_once BASE_PATH . '/app/middleware/admin.php';

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
            <span class="badge secondary red mb-3 <?= ($alert === "register_error") ? "d-inline-block" : "d-none" ?>">
                Falha ao cadastrar produto.
            </span>
            <span class="badge secondary red mb-3 <?= ($alert === "image_error") ? "d-inline-block" : "d-none" ?>">
                Falha ao enviar a imagem.
            </span>
            <span class="badge secondary green mb-3 <?= ($alert === "register_success") ? "d-inline-block" : "d-none" ?>">
                Produto criado com sucesso.
            </span>

            <!-- FORMULÁRIO -->

            <form
                action="<?= BASE_URL ?>app/controllers/product-controller.php"
                method="POST"
                enctype="multipart/form-data"
            >
                <input type="hidden" name="action" value="register">
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
                                            placeholder="Ex: Colar de Pérolas..."
                                            type="text"
                                            minlength="2"
                                            maxlength="250"
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
                                            placeholder="59.90"
                                            type="number"
                                            min="0"
                                            step="0.01"
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
                                            placeholder="10"
                                            type="number"
                                            min="0"
                                            step="1"
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
                                            placeholder="Ex: Colares"
                                            type="text"
                                            maxlength="50"
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
                                            placeholder="Descreva o produto..."
                                            rows="6"
                                            maxlength="5000"
                                            required
                                        ></textarea>
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
                                    <div class="form-field col-12">
                                        <label for="image">
                                            Imagem
                                        </label>
                                        <input
                                            class="form-control"
                                            name="image"
                                            id="image"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            required
                                        >
                                        <small class="text-muted">
                                            Formatos permitidos: JPG, PNG e WEBP.
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
                            Cadastrar Produto
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once BASE_PATH . '/components/layout/scripts.php'; ?>