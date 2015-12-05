
drop table dg_transito_negativo;

CREATE TABLE `dg_transito_negativo` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL DEFAULT '0',
  `id_orden` int(11) NOT NULL DEFAULT '0',
  `stock_transito_actual` int(11) NOT NULL DEFAULT '0',
  `stock_transito_nuevo` int(11) NOT NULL DEFAULT '0',
  `cantidad_pendiente` int(11) NOT NULL DEFAULT '0',
  `cantidad_user` int(11) NOT NULL DEFAULT '0',
  `tipo_accion` char(50) NOT NULL,
  `username` char(50) NOT NULL,
  `fecha_sistema` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_log`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ;


INSERT INTO `dg_transito_negativo` (`id_log`, `id_item`, `id_orden`, `stock_transito_actual`, `stock_transito_nuevo`, `cantidad_pendiente`, `cantidad_user`, `username`, `fecha_sistema`) VALUES 
(1, 222, 2989, 1, 1, 1, 1, 'marian', '2012-01-02 13:52:04'),
(2, 222, 2989, 1, 1, 1, 1, 'marian', '2012-01-02 13:52:05'),
(3, 221, 2989, 2, 2, 2, 2, 'marian', '2012-01-02 13:52:29'),
(4, 221, 2989, 2, 2, 2, 2, 'marian', '2012-01-02 13:52:29'),
(5, 221, 2989, 2, 2, 2, 2, 'marian', '2012-01-02 13:52:54'),
(6, 221, 2989, 2, 2, 2, 2, 'marian', '2012-01-02 13:52:54'),
(7, 235, 2989, 100, 100, 100, 100, 'marian', '2012-01-02 13:53:08'),
(8, 235, 2989, 100, 100, 100, 100, 'marian', '2012-01-02 13:53:09'),
(9, 235, 2989, 100, 100, 100, 100, 'marian', '2012-01-02 13:53:27'),
(10, 235, 2989, 100, 100, 100, 100, 'marian', '2012-01-02 13:53:28'),
(11, 2964, 2987, 5, 10, 5, 10, 'marian', '2012-01-02 15:10:12'),
(12, 2964, 2987, 10, 10, 10, 10, 'marian', '2012-01-02 15:10:12'),
(13, 105, 2999, 30, 30, 30, 30, 'marian', '2012-01-04 14:42:09'),
(14, 105, 2999, 30, 30, 30, 30, 'marian', '2012-01-04 14:42:10'),
(15, 106, 2999, 5, 5, 5, 5, 'marian', '2012-01-04 14:43:00'),
(16, 106, 2999, 5, 5, 5, 5, 'marian', '2012-01-04 14:43:00'),
(17, 2629, 2998, 30, 40, 20, 30, 'marian', '2012-01-05 14:37:08'),
(18, 2629, 2998, 40, 40, 30, 30, 'marian', '2012-01-05 14:37:08'),
(19, 1455, 2943, 12, 11, 12, 11, 'marian', '2012-01-05 20:33:33'),
(20, 1455, 2943, 11, 11, 11, 11, 'marian', '2012-01-05 20:33:33');