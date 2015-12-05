ALTER TABLE Grupo ADD COLUMN agrupacion_contable int DEFAULT 0;

CREATE INDEX by_item ON Log (id_item);
