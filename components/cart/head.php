
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Nós de Luz'; ?></title>

    <base href="<?php echo BASE_URL; ?>">

    <!-----IMPORTAÇÃO DE BIBLIOTECAS DE ESTILO----->
    <link rel="stylesheet" href="<?= BASE_URL?>assets/vendor/bootstrap/css/bootstrap.min.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/index.css">

    <link rel="icon" type="image/png" href="<?= BASE_URL ?>assets/images/logo_minimalistic.png">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendor/remixicon/remixicon.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendor/devicon/devicon.min.css">
    
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendor/aos/aos.css">

    <style>
        .btn-cart-added:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            opacity: 1;
            cursor: default;
        }

        .vip-info {
            flex: 1 1 220px;
        }

        .vip-info p {
            margin-bottom: 10px;
        }

        .vip-info i {
            margin-right: 5px;
        }

        .vip-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(0, 0, 0, 0.7);
        }

        .vip-modal.is-open {
            display: flex;
        }

        .vip-modal-content {
            position: relative;
            width: 100%;
            max-width: 460px;
            padding: 30px;
            border-radius: 12px;
            background: var(--bs-body-bg, #fff);
            color: var(--bs-body-color, #212529);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.3);
        }

        .vip-modal-content h3 {
            margin: 16px 0;
        }

        .vip-modal-content p {
            line-height: 1.6;
        }

        .vip-modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            border: none;
            background: transparent;
            color: inherit;
            font-size: 30px;
            cursor: pointer;
        }

        .vip-contact {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            background: rgba(128, 128, 128, 0.12);
        }

        .vip-contact a {
            text-decoration: none;
            overflow-wrap: anywhere;
        }

        .vip-modal-content .icon-box {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

