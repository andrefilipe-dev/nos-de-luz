<?php
$cart = $_SESSION['cart'] ?? [];
$cartCount = array_sum($cart);
?>

<header class="">
  <nav class="site-header navbar navbar-expand-md">
    <div class="custom-main-container d-flex align-items-center justify-content-between">

      <!-- LOGO PRINCIPAL DINÂMICA -->
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
        <a class="navbar-brand" href="<?= BASE_URL ?>dashboard/index.php">Nós de luz</a>
      <?php else: ?>
        <a class="navbar-brand" href="<?= BASE_URL ?>#hero">Nós de luz</a>
      <?php endif; ?>

      
      <!-- CARRINHO MOBILE -->
      <a class="nav-link d-block d-md-none ms-auto me-3 cart-header-link"
        href="<?= BASE_URL ?>cart.php">

        <div class="icon-box cart-icon-wrapper">
          <i class="ri-shopping-cart-2-line"></i>

          <span class="cart-badge <?= $cartCount > 0 ? '' : 'd-none' ?>">
            <?= $cartCount ?>
          </span>
        </div>
      </a>

      <!-- BOTÃO HAMBÚRGUER MOBILE -->
      <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#top-navbar"
        aria-controls="top-navbar">
        <i class="ri-menu-2-line"></i>
      </button>

      <!-- ESTRUTURA OFFCANVAS -->
      <div class="offcanvas offcanvas-start w-100" tabindex="-1" id="top-navbar" aria-labelledby="top-navbarLabel">

        <!-- TOPO DO OFFCANVAS MOBILE -->
        <div class="offcanvas-header d-md-none w-100 d-flex align-items-center justify-content-between px-4 py-2">
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
            <a class="navbar-brand" href="<?= BASE_URL ?>dashboard/index.php">Nós de luz</a>
          <?php else: ?>
            <a class="navbar-brand" href="<?= BASE_URL ?>#hero">Nós de luz</a>
          <?php endif; ?>
          <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#top-navbar"
            aria-controls="top-navbar">
            <i class="ri-close-line"></i>
          </button>
        </div>

        <!-- CORPO DO MENU / LINKS DINÂMICOS -->
        <div class="offcanvas-body ms-md-auto px-4 p-md-0 me-3">
          <ul class="navbar-nav m-md-auto align-items-md-center gap-3">

            <!-- CENÁRIO 1: USUÁRIO É ADMINISTRADOR -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Admin'): ?>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>dashboard/index.php">Dashboard</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>dashboard/products.php">Produtos</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>dashboard/users.php">Usuários</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>dashboard/orders.php">Pedidos</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>dashboard/reports.php">Relatórios</a>
              </li>

              <!-- CENÁRIO 2: VISITANTE LOGADO (Links Institucionais da Landing Page) -->
            <?php else: ?>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>#hero">Início</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>#about">Sobre</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>#products">Produtos</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>catalog.php">Catálogo</a>
              </li>
              <li class="nav-item pb-3 pb-md-0">
                <a class="nav-link" href="<?= BASE_URL ?>#contact">Contato</a>
              </li>
              <li class="nav-item pb-3 pb-md-0 d-none d-md-block">
                <a class="nav-link cart-header-link" href="<?= BASE_URL ?>cart.php">

                  <div class="icon-box cart-icon-wrapper">
                    <i class="ri-shopping-cart-2-line"></i>

                    <span class="cart-badge <?= $cartCount > 0 ? '' : 'd-none' ?>">
                      <?= $cartCount ?>
                    </span>
                  </div>

                </a>
              </li>
            <?php endif; ?>

            <!-- BOTÃO / DROPDOWN MOBILE (Abaixo dos links no Offcanvas) -->
            <li class="nav-item pb-3 pb-md-0 d-md-none">
              <?php if (!isset($_SESSION['id'])): ?>
                <a class="btn primary" href="<?= BASE_URL ?>register.php">Comece Hoje</a>
              <?php else: ?>
                <div class="dropdown">
                  <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="icon-box circle sm">
                      <i class="ri-user-line"></i>
                    </div>
                    <span class="user-name">
                      <?= e($_SESSION['name'] ?? '') ?>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>account/profile.php">Meu Perfil</a></li>
                    <?php if ($_SESSION['role'] === 'Cliente'): ?>
                      <li><a class="dropdown-item" href="<?= BASE_URL ?>account/orders.php">Meus Pedidos</a></li>
                    <?php endif; ?>
                    <?php if ($_SESSION['role'] === 'Admin'): ?>
                      <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php">Ver Site</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item"
                        href="<?= BASE_URL ?>app/controllers/auth-controller.php?action=logout">Sair</a></li>
                  </ul>
                </div>
              <?php endif; ?>
            </li>
          </ul>
        </div>

      </div>

      <!-- BOTÃO / DROPDOWN DESKTOP -->
      <div class="d-none d-md-block">
        <?php if (!isset($_SESSION['id'])): ?>
          <a class="btn primary" href="<?= BASE_URL ?>register.php">Comece Hoje</a>
        <?php else: ?>
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
              <div class="icon-box circle sm">
                <i class="ri-user-line"></i>
              </div>
              <span class="user-name">
                <?= e($_SESSION['name'] ?? '') ?>
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>account/profile.php">Meu Perfil</a></li>
              <?php if ($_SESSION['role'] === 'Cliente'): ?>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>account/orders.php">Meus Pedidos</a></li>
              <?php endif; ?>
              <?php if ($_SESSION['role'] === 'Admin'): ?>
                <li><a class="dropdown-item" href="<?= BASE_URL ?>index.php">Ver Site</a></li>
              <?php endif; ?>
              <li><a class="dropdown-item"
                  href="<?= BASE_URL ?>app/controllers/auth-controller.php?action=logout">Sair</a>
              </li>
            </ul>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </nav>
</header>