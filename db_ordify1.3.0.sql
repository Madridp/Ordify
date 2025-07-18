/*
Navicat MySQL Data Transfer

Source Server         : XAMPP
Source Server Version : 100414
Source Host           : localhost:3306
Source Database       : db_ordify

Target Server Type    : MYSQL
Target Server Version : 100414
File Encoding         : 65001

Date: 2023-01-13 10:02:23
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for options
-- ----------------------------
DROP TABLE IF EXISTS `options`;
CREATE TABLE `options` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) DEFAULT NULL,
  `val` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of options
-- ----------------------------

-- ----------------------------
-- Table structure for pedidos
-- ----------------------------
DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero` varchar(8) DEFAULT '',
  `id_proveedor` int(11) DEFAULT 0,
  `subtotal` float(10,2) unsigned DEFAULT 0.00,
  `total` float(10,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `metodo_pago` varchar(30) DEFAULT NULL,
  `status_pago` varchar(20) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  `total_pagado` float(10,2) DEFAULT 0.00,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_entrega` datetime DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pedidos
-- ----------------------------
INSERT INTO `pedidos` VALUES ('1', '371113', '1', '6767.24', '7850.00', 'completado', 'Pedido de 50 piezas', null, null, null, '0.00', '2021-03-19 00:00:00', '2021-03-14 14:40:05', '12312313', '2021-01-20 16:57:11', '2021-03-14 15:21:09');
INSERT INTO `pedidos` VALUES ('2', '509484', '2', '2456.90', '2850.00', 'completado', '', 'efectivo', 'pagado', '2022-12-22 12:52:40', '2850.00', '2021-03-22 00:00:00', '2021-03-14 14:24:02', '123435354', '2021-02-10 14:27:25', '2022-12-22 12:52:40');
INSERT INTO `pedidos` VALUES ('3', '379885', '1', '1939.66', '2250.00', 'completado', 'Notas', 'mercado_pago', 'pagado', '2022-12-22 12:10:22', '2250.00', '2021-03-31 00:00:00', '2021-03-14 14:34:57', 'sdfasd54', '2021-03-13 15:12:49', '2022-12-22 12:10:22');
INSERT INTO `pedidos` VALUES ('4', '427337', '1', '413.79', '480.00', 'completado', 'Notas para el pedido cool.', 'tarjeta_debito', 'pendiente', null, '0.00', '2021-04-05 00:00:00', '2021-03-14 14:06:35', '21a472cfe1ddd58e0a2fe3b7faf1b40cd48806697321ed284e937e90125a51bf', '2021-03-14 13:54:41', '2023-01-03 12:24:24');
INSERT INTO `pedidos` VALUES ('5', '118359', '1', '32715.52', '37950.00', 'completado', '', 'tarjeta_debito', 'parcial', '2022-12-22 12:51:23', '35000.00', '2021-04-02 00:00:00', '2021-03-14 15:12:05', 'abdd476ba8844229992a865a4169265cb27a4188013f61deb7f622ae62e69544', '2021-04-02 15:08:59', '2022-12-22 12:51:23');
INSERT INTO `pedidos` VALUES ('6', '770589', '2', '4827.59', '5600.00', 'completado', 'Unas notas cools.', 'spei', 'reembolsado', null, '5600.00', '2021-05-03 00:00:00', '2021-03-14 15:43:42', '29f243ba351624389c1805452e5b0b77dbfa66ebbabf50f3973c95afeb447dea', '2021-05-03 15:42:58', '2022-12-22 12:50:54');
INSERT INTO `pedidos` VALUES ('10', '626245', '2', '4827.59', '5600.00', 'cancelado', null, 'spei', 'cancelado', null, '0.00', '2022-12-22 12:23:20', '2021-03-14 15:43:42', '236b0244a662a7f66690c33915e3b55c76d977a1007ff7849140f5d66930f152', '2022-12-19 15:50:44', '2022-12-22 12:26:19');
INSERT INTO `pedidos` VALUES ('11', '202041', '2', '4827.59', '5600.00', 'pendiente', null, null, 'pendiente', null, '0.00', '2023-01-11 21:34:38', '2021-03-14 15:43:42', '1dffd0dc701b14ecf1d01abba0d4edc6eb6463307f076e4a7cdfdc550593d308', '2022-12-22 12:55:05', '2023-01-11 21:34:38');
INSERT INTO `pedidos` VALUES ('13', '883986', '2', '2133.62', '2475.00', 'pendiente', 'Probando notas dos punto cero. sasd', 'spei', 'pendiente', null, '0.00', '2023-01-03 14:56:25', '2023-01-31 00:00:00', 'e439f76af551f1382c8bef4a73d94a8397b2c025ae849028b4d489e5cb36aad6', '2023-01-03 14:02:54', '2023-01-12 15:37:13');

-- ----------------------------
-- Table structure for pedidos_productos
-- ----------------------------
DROP TABLE IF EXISTS `pedidos_productos`;
CREATE TABLE `pedidos_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `variante` varchar(100) DEFAULT NULL,
  `corte` varchar(100) DEFAULT '',
  `cantidad` int(11) DEFAULT 0,
  `precio` float(10,2) DEFAULT 0.00,
  `subtotal` float(10,2) DEFAULT 0.00,
  `total` float(10,2) DEFAULT 0.00,
  `cancelados` int(11) DEFAULT 0,
  `recibidos` int(11) DEFAULT 0,
  `danados` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of pedidos_productos
-- ----------------------------
INSERT INTO `pedidos_productos` VALUES ('1', '1', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('2', '1', '4', 'blackadam.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('3', '1', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('4', '1', '2', 'nightwing.pdf - S', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '3', '0');
INSERT INTO `pedidos_productos` VALUES ('5', '1', '2', 'nightwing.pdf - M', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '4', '0');
INSERT INTO `pedidos_productos` VALUES ('6', '1', '2', 'nightwing.pdf - L', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('7', '1', '3', 'jiren.pdf - S', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '4', '0');
INSERT INTO `pedidos_productos` VALUES ('8', '1', '3', 'jiren.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '2', '0');
INSERT INTO `pedidos_productos` VALUES ('9', '1', '3', 'jiren.pdf - L', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('10', '1', '1', 'nightwing.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('11', '2', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('12', '2', '3', 'jiren.pdf - S', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '3', '0');
INSERT INTO `pedidos_productos` VALUES ('13', '2', '3', 'jiren.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '4', '0');
INSERT INTO `pedidos_productos` VALUES ('14', '2', '2', 'nightwing.pdf - S', null, 'VA Manga corta', '1', '150.00', '129.31', '150.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('15', '2', '2', 'nightwing.pdf - M', null, 'VA Manga corta', '1', '150.00', '129.31', '150.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('16', '2', '2', 'nightwing.pdf - L', null, 'VA Manga corta', '1', '150.00', '129.31', '150.00', '0', '1', '0');
INSERT INTO `pedidos_productos` VALUES ('17', '3', '2', 'nightwing.pdf - S', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '6', '0');
INSERT INTO `pedidos_productos` VALUES ('18', '3', '2', 'nightwing.pdf - M', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('19', '3', '2', 'nightwing.pdf - L', null, 'VA Manga corta', '5', '150.00', '646.55', '750.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('20', '4', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '1', '160.00', '137.93', '160.00', '0', '1', '0');
INSERT INTO `pedidos_productos` VALUES ('21', '4', '4', 'blackadam.pdf - M', null, 'VA Manga larga', '1', '160.00', '137.93', '160.00', '0', '1', '0');
INSERT INTO `pedidos_productos` VALUES ('22', '4', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '1', '160.00', '137.93', '160.00', '0', '1', '0');
INSERT INTO `pedidos_productos` VALUES ('23', '5', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('24', '5', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('25', '5', '2', 'nightwing.pdf - M', null, 'VA Manga corta', '13', '150.00', '1681.03', '1950.00', '0', '13', '0');
INSERT INTO `pedidos_productos` VALUES ('26', '5', '1', 'nightwing.pdf - S', null, 'VA Manga larga', '25', '160.00', '3448.28', '4000.00', '0', '10', '0');
INSERT INTO `pedidos_productos` VALUES ('27', '5', '1', 'nightwing.pdf - M', null, 'VA Manga larga', '124', '160.00', '17103.45', '19840.00', '0', '124', '0');
INSERT INTO `pedidos_productos` VALUES ('28', '5', '3', 'jiren.pdf - L', null, 'VA Manga larga', '12', '160.00', '1655.17', '1920.00', '0', '10', '0');
INSERT INTO `pedidos_productos` VALUES ('29', '5', '3', 'jiren.pdf - S', null, 'VA Manga larga', '54', '160.00', '7448.28', '8640.00', '0', '50', '0');
INSERT INTO `pedidos_productos` VALUES ('30', '6', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('31', '6', '4', 'blackadam.pdf - M', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('32', '6', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '6', '0');
INSERT INTO `pedidos_productos` VALUES ('33', '6', '1', 'nightwing.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '5', '0');
INSERT INTO `pedidos_productos` VALUES ('55', '10', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('56', '10', '4', 'blackadam.pdf - M', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('57', '10', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('58', '10', '1', 'nightwing.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('59', '11', '4', 'blackadam.pdf - L', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('60', '11', '4', 'blackadam.pdf - M', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('61', '11', '4', 'blackadam.pdf - S', null, 'VA Manga larga', '10', '160.00', '1379.31', '1600.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('62', '11', '1', 'nightwing.pdf - M', null, 'VA Manga larga', '5', '160.00', '689.66', '800.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('66', '13', '5', 'Mousepad XL 40 x 30 - rojo - Impresos', 'rojo', 'Impresos', '5', '99.00', '426.72', '495.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('67', '13', '5', 'Mousepad XL 40 x 30 - negro - Impresos', 'negro', 'Impresos', '5', '99.00', '426.72', '495.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('68', '13', '5', 'Mousepad XL 40 x 30 - verde - Impresos', 'verde', 'Impresos', '5', '99.00', '426.72', '495.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('69', '13', '5', 'Mousepad XL 40 x 30 - amarillo - Impresos', 'amarillo', 'Impresos', '5', '99.00', '426.72', '495.00', '0', '0', '0');
INSERT INTO `pedidos_productos` VALUES ('70', '13', '5', 'Mousepad 40 x 30 - gris - Impresos', 'gris', 'Impresos', '5', '99.00', '426.72', '495.00', '0', '0', '0');

-- ----------------------------
-- Table structure for posts
-- ----------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_padre` bigint(20) DEFAULT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `id_ref` bigint(20) DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permalink` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contenido` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deadline_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of posts
-- ----------------------------
INSERT INTO `posts` VALUES ('1', 'tracking', null, null, '1', 'Rastreo de pedido', 'https://envia.com/rastreo?label=784692564530', '784692564530', null, null, null, null, '2021-03-14 14:28:05', '2021-03-14 14:28:06');
INSERT INTO `posts` VALUES ('3', 'variante', '0', '0', '0', 'Manga larga dos', '', 'Atributo de producto', 'public', '', '2023-01-03 13:39:28', '2023-01-03 13:39:28', '2023-01-03 13:39:28', '2023-01-03 13:39:28');
INSERT INTO `posts` VALUES ('4', 'variante', '0', '0', '0', 'Manga larga uno dos', '', 'Atributo de producto', 'public', '', '2023-01-03 13:39:33', '2023-01-03 13:39:33', '2023-01-03 13:39:33', '2023-01-03 13:39:37');
INSERT INTO `posts` VALUES ('5', 'variante', '0', '0', '0', 'Impresos', '', 'Atributo de producto', 'public', '', '2023-01-03 13:41:24', '2023-01-03 13:41:24', '2023-01-03 13:41:24', '2023-01-03 13:41:24');
INSERT INTO `posts` VALUES ('6', 'variante', '0', '0', '0', 'Manga corta', '', 'Atributo de producto', 'public', '', '2023-01-03 14:56:48', '2023-01-03 14:56:48', '2023-01-03 14:56:48', '2023-01-03 14:56:48');
INSERT INTO `posts` VALUES ('20', 'adjunto', '0', '1', '13', 'Adjunto de pedido', 'http://localhost:8848/sistemas/ordify/assets/uploads/wimitp2r72vu-9stteltby0mh-q5rzkw6xxrva.jpg', 'wimitp2r72vu-9stteltby0mh-q5rzkw6xxrva.jpg', 'public', null, '2023-01-12 16:56:52', '2023-01-12 16:56:52', '2023-01-12 16:56:52', '2023-01-12 16:56:52');
INSERT INTO `posts` VALUES ('21', 'adjunto', '0', '1', '13', 'Adjunto de pedido', 'http://localhost:8848/sistemas/ordify/assets/uploads/cvmdy73zpx0c-cxxauwg0bm1k-q1sxwwhkmu4v.jpg', 'cvmdy73zpx0c-cxxauwg0bm1k-q1sxwwhkmu4v.jpg', 'public', null, '2023-01-12 16:56:53', '2023-01-12 16:56:53', '2023-01-12 16:56:53', '2023-01-12 16:56:53');

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `variantes` varchar(255) DEFAULT NULL,
  `precio` float(10,2) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `corte` varchar(255) DEFAULT NULL,
  `adjuntos` text DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES ('1', 'nightwing.pdf', '', '', 'S|M|L', '160.00', 'rtxiaoobaabh-eip22oixsehr-plywf3etwye7.jpg', 'VA Manga larga', '', '2021-03-12 16:55:24');
INSERT INTO `productos` VALUES ('2', 'nightwing.pdf', '', '', 'S|M|L', '150.00', 'jsifl2hkothf-pyi8o2ecasan-d5fz8ccd5bkm.jpg', 'VA Manga corta', '', '2021-03-12 16:55:36');
INSERT INTO `productos` VALUES ('3', 'jiren.pdf', '', '', 'S|M|L', '160.00', 'z4rqj13wfpim-spktmkshuoat-0ct2kpwcjexd.jpg', 'VA Manga larga', '', '2021-03-12 16:55:55');
INSERT INTO `productos` VALUES ('4', 'blackadam.pdf', '', '', 'S|M|L|XL', '160.00', 'ulockvytdoah-r43luwi4ush3-7du9hezoep8q.jpg', 'VA Manga larga', 'https://listado.mercadolibre.com.mx/_CustId_255738894', '2021-03-12 16:56:09');
INSERT INTO `productos` VALUES ('5', 'Mousepad 40 x 30', '4268275269', '', 'rojo|amarillo|verde|negro|gris', '99.00', null, 'Manga corta', '', '2023-01-03 13:41:39');

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `razon_social` varchar(255) DEFAULT NULL,
  `rfc` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  `actualizado` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of proveedores
-- ----------------------------
INSERT INTO `proveedores` VALUES ('1', 'John Doe', 'Vital Army', 'XEXX010101000', 'jslocal2@localhost.com', '', 'Una dirección en México #123', '2021-03-12 16:54:57', null);
INSERT INTO `proveedores` VALUES ('2', 'Luis Doe', 'SAIS DAD de CV', 'XEXX010101001', 'jslocal@localhost.com', '', 'Dirección en México #001-A', '2021-03-12 16:56:58', null);

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role` varchar(20) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `creado` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES ('1', 'admin', 'John Doe Admin', 'bee', 'jslocal@localhost.com', '$2y$10$SvksOtsc2WmbSExYbywlteuvvvFsuugszQ5AE0mqy37aQuyMm7AzS', '2021-03-14 13:04:29');
