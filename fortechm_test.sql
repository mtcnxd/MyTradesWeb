-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 23-03-2022 a las 19:43:24
-- Versión del servidor: 5.7.37-log-cll-lve
-- Versión de PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fortechm_test`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`fortechm`@`localhost` PROCEDURE `update_wallet_performance` (IN `date` INT)  NO SQL
SELECT * FROM wallet_balance$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_balance`
--

CREATE TABLE `wallet_balance` (
  `id` int(11) NOT NULL,
  `book` varchar(10) NOT NULL,
  `price` float NOT NULL,
  `amount` double NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `wallet_balance`
--

INSERT INTO `wallet_balance` (`id`, `book`, `price`, `amount`, `date`, `status`) VALUES
(254, 'btc_usd', 56375.4, 0.00002798, '2021-12-02 15:52:54', 1),
(241, 'btc_usd', 56860, 0.00087851, '2021-12-01 05:53:02', 1),
(228, 'ltc_usd', 208.86, 0.14301639, '2021-11-30 16:51:01', 1),
(189, 'ltc_mxn', 5270.04, 0.18880312, '2021-11-14 06:36:29', 1),
(257, 'btc_usd', 56375.4, 0.0007468, '2021-12-02 15:54:18', 1),
(184, 'btc_mxn', 1316410, 0.00075584, '2021-11-13 01:49:14', 1),
(185, 'btc_mxn', 1308900, 0.00076018, '2021-11-13 05:37:21', 1),
(261, 'btc_usd', 56500, 0.00002272, '2021-12-02 16:01:32', 1),
(187, 'ltc_mxn', 5314.05, 0.18723948, '2021-11-13 18:24:27', 1),
(188, 'ltc_mxn', 5303.82, 0.18760063, '2021-11-13 18:58:27', 1),
(256, 'btc_usd', 56375.4, 0.00002935, '2021-12-02 15:52:56', 1),
(226, 'ltc_usd', 208.86, 0.03230966, '2021-11-30 16:50:59', 1),
(227, 'ltc_usd', 208.86, 0.01664052, '2021-11-30 16:51:00', 1),
(258, 'btc_usd', 56375.4, 0.00005555, '2021-12-02 15:57:08', 1),
(255, 'btc_usd', 56375.4, 0.00001282, '2021-12-02 15:52:55', 1),
(225, 'ltc_usd', 208.86, 0.04720082, '2021-11-30 16:50:58', 1),
(238, 'btc_usd', 56920, 0.00087759, '2021-12-01 05:31:19', 1),
(239, 'btc_usd', 56900, 0.0008779, '2021-12-01 05:31:22', 1),
(240, 'btc_usd', 56930.2, 0.00087743, '2021-12-01 05:47:43', 1),
(260, 'btc_usd', 56500, 0.00001696, '2021-12-02 16:01:31', 1),
(262, 'btc_usd', 56500, 0.00084443, '2021-12-02 16:01:33', 1),
(246, 'bch_mxn', 12333, 0.000995, '2021-12-01 13:56:04', 1),
(247, 'bch_mxn', 12333, 0.0010945, '2021-12-01 13:56:05', 1),
(248, 'bch_mxn', 12333, 0.0010945, '2021-12-01 13:56:07', 1),
(249, 'bch_mxn', 12333, 0.07749353, '2021-12-01 13:56:30', 1),
(259, 'btc_usd', 56375.4, 0.00001357, '2021-12-02 15:57:08', 1),
(251, 'comp_usd', 283.01, 0.06955813, '2021-12-01 17:16:20', 1),
(252, 'comp_usd', 283.37, 0.17628012, '2021-12-01 17:24:32', 1),
(253, 'comp_usd', 283.01, 0.10694623, '2021-12-01 17:25:12', 1),
(264, 'xrp_usd', 0.9741, 51.01153953, '2021-12-02 16:55:10', 0),
(265, 'link_usd', 24.78, 2.01583938, '2021-12-02 19:37:04', 0),
(266, 'comp_usd', 271.1, 0.1842512, '2021-12-02 21:32:52', 1),
(267, 'bch_mxn', 12101.2, 0.08209957, '2021-12-02 21:37:39', 1),
(268, 'ltc_usd', 205.87, 0.24263127, '2021-12-02 21:40:58', 1),
(269, 'xrp_usd', 0.9839, 5.70613801, '2021-12-02 21:44:38', 0),
(325, 'eth_mxn', 65605.6, 0.00011487, '2022-02-08 05:02:11', 1),
(324, 'eth_mxn', 65605.6, 0.00040298, '2022-02-08 05:01:46', 1),
(279, 'link_usd', 24.97, 1.74828753, '2021-12-02 21:55:21', 0),
(280, 'btc_mxn', 1173890, 0.0008476, '2021-12-03 19:01:52', 1),
(281, 'btc_mxn', 1173890, 0.0008476, '2021-12-03 19:01:53', 1),
(289, 'btc_usd', 49740, 0.00301441, '2021-12-10 13:50:26', 1),
(326, 'eth_mxn', 65605.6, 0.01464854, '2022-02-08 05:05:57', 1),
(294, 'link_usd', 26.75, 0.99905, '2022-01-08 06:29:21', 0),
(292, 'xrp_usd', 0.9215, 215.56500017, '2021-12-26 18:39:24', 1),
(293, 'link_usd', 25.2, 5.09759346, '2022-01-07 16:11:24', 0),
(295, 'link_usd', 26.75, 4.12318205, '2022-01-08 06:29:24', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_config`
--

CREATE TABLE `wallet_config` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `bitso_key` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `bitso_secret` varchar(20) COLLATE utf8_spanish_ci NOT NULL,
  `notify_01` varchar(10) COLLATE utf8_spanish_ci NOT NULL,
  `notify_02` varchar(10) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `wallet_config`
--

INSERT INTO `wallet_config` (`id`, `id_user`, `bitso_key`, `bitso_secret`, `notify_01`, `notify_02`) VALUES
(1, 1, 'TMJEPCYmIv', '', '0', 'checked');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_currencys`
--

CREATE TABLE `wallet_currencys` (
  `id` int(11) NOT NULL,
  `book` varchar(15) COLLATE utf8_spanish_ci NOT NULL,
  `currency` varchar(25) COLLATE utf8_spanish_ci NOT NULL,
  `file` varchar(15) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `wallet_currencys`
--

INSERT INTO `wallet_currencys` (`id`, `book`, `currency`, `file`) VALUES
(1, 'btc_mxn', 'Bitcoin', '1.png'),
(2, 'eth_mxn', 'Ethereum', '2.png'),
(3, 'ltc_mxn', 'Litecoin', '3.png'),
(4, 'mana_mxn', 'Mana', '4.png'),
(5, 'xrp_mxn', 'Ripple', '6.png'),
(6, 'bat_mxn', 'Bat', '5.png'),
(7, 'bch_mxn', 'Bitcoin Cash', '7.png'),
(8, 'btc_usd', 'Bitcoin', '1.png'),
(9, 'link_usd', 'Chainlink', '8.png'),
(10, 'eth_usd', 'Ethereum', '2.png'),
(11, 'ltc_usd', 'Litecoin', '3.png'),
(12, 'comp_usd', 'Compound', '9.png'),
(13, 'xrp_usd', 'Ripple', '6.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_performance`
--

CREATE TABLE `wallet_performance` (
  `id` int(11) NOT NULL,
  `amount` varchar(20) NOT NULL,
  `difference` varchar(10) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wallet_users`
--

CREATE TABLE `wallet_users` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `username` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `password` varchar(30) COLLATE utf8_spanish_ci NOT NULL,
  `email` varchar(30) COLLATE utf8_spanish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `wallet_users`
--

INSERT INTO `wallet_users` (`id`, `name`, `username`, `password`, `email`) VALUES
(1, 'Marcos Tzuc Cen', 'mtc.nxd@gmail.com', 'nodoubt', 'mtc.nxd@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `wallet_balance`
--
ALTER TABLE `wallet_balance`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wallet_config`
--
ALTER TABLE `wallet_config`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wallet_currencys`
--
ALTER TABLE `wallet_currencys`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wallet_performance`
--
ALTER TABLE `wallet_performance`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `wallet_users`
--
ALTER TABLE `wallet_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wallet_balance`
--
ALTER TABLE `wallet_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=339;

--
-- AUTO_INCREMENT de la tabla `wallet_config`
--
ALTER TABLE `wallet_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `wallet_currencys`
--
ALTER TABLE `wallet_currencys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `wallet_performance`
--
ALTER TABLE `wallet_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `wallet_users`
--
ALTER TABLE `wallet_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
