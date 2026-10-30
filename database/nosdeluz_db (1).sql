-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/10/2026 às 23:30
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `nosdeluz_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_status` enum('Pendente','Aprovado','Recusado') NOT NULL DEFAULT 'Pendente',
  `order_status` enum('Pendente','Preparando','Enviado','Entregue','Cancelado') NOT NULL DEFAULT 'Pendente',
  `date_register` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `payment_status`, `order_status`, `date_register`) VALUES
(5, 4, 539.91, 'Pendente', 'Pendente', '2026-10-12 17:27:23'),
(6, 4, 502.96, 'Aprovado', 'Entregue', '2026-10-12 17:33:20'),
(7, 1, 46.46, 'Aprovado', 'Preparando', '2026-10-22 18:23:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders_items`
--

CREATE TABLE `orders_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders_items`
--

INSERT INTO `orders_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(7, 5, 1, 9, 59.99),
(8, 6, 1, 5, 59.99),
(9, 6, 3, 3, 67.67),
(10, 7, 4, 2, 23.23);

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(256) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `quantity`, `price`, `description`, `image`, `category`) VALUES
(1, 'Bolsa de Crochê Artesanal', 48, 59.99, 'Bolsa de crochê branca, artesanal e delicada, perfeita para o dia a dia.', 'assets/images/products/product_1.jpeg', 'Bolsa'),
(3, 'Conjunto Pulseira + Brinco', 60, 39.90, 'Brinco e pulseira artesanais com pérolas, elegantes e delicados', 'assets/images/products/product_3.jpeg', 'Conjunto'),
(4, 'Brinco', 19, 24.90, 'Brinco artesanal dourado com detalhes em pérolas, delicado e elegante.', 'assets/images/products/product_4.jpeg', 'Brincos'),
(6, 'Pulseira', 80, 34.90, 'Pulseira artesanal com miçangas e pedras coloridas, cheia de estilo e charme.', 'assets/images/products/product_6.jpeg', 'Pulseiras');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `address` varchar(250) NOT NULL,
  `cep` varchar(8) NOT NULL,
  `address_number` varchar(20) NOT NULL,
  `address_complement` varchar(100) DEFAULT NULL,
  `role` enum('Cliente','Admin') NOT NULL,
  `status` enum('Ativo','Inativo') NOT NULL,
  `vip` enum('sim','nao') NOT NULL DEFAULT 'nao',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `date_register` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `cpf`, `address`, `cep`, `address_number`, `address_complement`, `role`, `status`, `vip`, `reset_token`, `reset_token_expires`, `date_register`) VALUES
(1, 'rogério', 'rogerio@gmail.com', '$2y$10$zPlCl6hA5OEF5oeDAAeDVejyUMSHcb9rX9sqVXhSgDVsGFt/2FvLK', '12345678912', '21989898982', 'uma rua aí', '21321321', '123', 'apartamento 67', 'Cliente', 'Ativo', 'sim', NULL, NULL, '2026-09-29 19:04:25'),
(2, 'vadimilson', 'vadimilson@gmail.com', '$2y$10$zPlCl6hA5OEF5oeDAAeDVejyUMSHcb9rX9sqVXhSgDVsGFt/2FvLK', '21989898983', '12345678914', 'uma rua aí', '21321322', '123', 'apartamento 67', 'Admin', 'Ativo', 'nao', '55658d077f7cf8700b556182b36991da08e2f3af844b4cc0786eacade09d574d', '2026-10-04 00:07:06', '2026-09-29 19:04:25'),
(4, 'jurema', 'jurema@gmail.com', '$2y$10$qYynDt05FuZdRXz6h7aWue4k3YCpeuwHvsADGw6lMTKC8mETg/u4O', '21112121122', '12123123214', '123213123', '12321312', '123123123', '213123', 'Cliente', 'Ativo', 'nao', NULL, NULL, '2026-10-12 16:27:19'),
(5, 'adm', 'adm@gmail.com', '$2y$10$NQWLbaDdiPLIhNooze6p3.xWD8DmWdTbaMmQrLWWN6yRSijeAL/Ga', '67856785678', '56787685785', 'rua vinte', '24545342', '2342', 'bloco dois', 'Cliente', 'Ativo', 'nao', NULL, NULL, '2026-10-27 01:54:57');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Índices de tabela `orders_items`
--
ALTER TABLE `orders_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_items_orders` (`order_id`),
  ADD KEY `fk_orders_items_products` (`product_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `orders_items`
--
ALTER TABLE `orders_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Restrições para tabelas `orders_items`
--
ALTER TABLE `orders_items`
  ADD CONSTRAINT `fk_orders_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_items_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
