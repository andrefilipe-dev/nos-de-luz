<?php

$carrinho = $_SESSION['carrinho'] ?? [];
$quantidadeCarrinho = 0;

foreach ($carrinho as $item) {
    $quantidadeCarrinho += $item['quantidade'] ?? 0;
}

?>

<header class="site-header">
    <nav class="navbar navbar-expand-lg">
        <div class="custom-main-container">
            <div class="container-fluid px-0">

                <!-- Logo -->

                <a href="../index.php" class="navbar-brand">
                    All <span>Dyne</span>
                </a>

                <!-- Botão mobile -->

                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarAllDyne"
                    aria-controls="navbarAllDyne"
                    aria-expanded="false"
                    aria-label="Abrir menu"
                >

                    <i class="bi bi-list"></i>
                </button>

                <!-- links do navbar -->

                <div
                    class="collapse navbar-collapse"
                    id="navbarAllDyne"
                >
                    <ul class="navbar-nav ms-auto align-items-lg-center">

                        <li class="nav-item">
                            <a href="../index.php" class="nav-link active">
                                Início
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../catalogo.php" class="nav-link">
                                Catálogo
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../sobre.php" class="nav-link">
                                Sobre Nós
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="../contato.php" class="nav-link">
                                Contato
                            </a>
                        </li>

                        <li class="nav-item">
                            <a
                                href="index.php"
                                class="nav-link cart-link"
                                aria-label="Carrinho"
                            >
                                <i class="bi bi-cart3"></i>
                                <?php if ($quantidadeCarrinho > 0): ?>

                                    <span class="cart-count">
                                        <?= $quantidadeCarrinho ?>
                                    </span>

                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>