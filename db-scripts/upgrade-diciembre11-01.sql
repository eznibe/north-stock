CREATE TABLE DG_transito_negativo
(
	id_log int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	id_item int DEFAULT 0 NOT NULL,
	id_orden int DEFAULT 0 NOT NULL,
   	stock_transito_actual int DEFAULT 0 NOT NULL,
   	stock_transito_nuevo int DEFAULT 0 NOT NULL,
   	cantidad_pendiente int DEFAULT 0 NOT NULL,
   	cantidad_user int DEFAULT 0 NOT NULL,
   	username char(50) NOT NULL,
	fecha_sistema timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);


UPDATE Item SET stock_transito = 0 WHERE id_item = 306;
UPDATE Item SET stock_transito = 0 WHERE id_item = 311;
UPDATE Item SET stock_transito = 0 WHERE id_item = 1503;
UPDATE Item SET stock_transito = 0 WHERE id_item = 1820;