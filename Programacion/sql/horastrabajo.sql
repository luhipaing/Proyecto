-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/11/2025 às 02:38
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `cooperativas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `horastrabajo`
--

CREATE TABLE `horastrabajo` (
  `idHoras` int(9) NOT NULL,
  `user_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `horas` decimal(5,2) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horastrabajo`
--

INSERT INTO `horastrabajo` (`idHoras`, `user_id`, `work_date`, `horas`, `descripcion`, `created_at`) VALUES
(1, 56494869, '2007-05-21', 5.00, 'dsfs', '2025-11-04 21:56:24'),
(12, 56494869, '2007-08-21', 4.00, 'asdas', '2025-11-04 22:04:00'),
(14, 56494869, '2005-04-21', 5.00, 'dfdf', '2025-11-04 22:24:01');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `horastrabajo`
--
ALTER TABLE `horastrabajo`
  ADD PRIMARY KEY (`idHoras`),
  ADD UNIQUE KEY `unique_user_date` (`user_id`,`work_date`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `horastrabajo`
--
ALTER TABLE `horastrabajo`
  MODIFY `idHoras` int(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `horastrabajo`
--
ALTER TABLE `horastrabajo`
  ADD CONSTRAINT `horastrabajo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`idUser`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
