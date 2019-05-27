UPDATE Item SET oculto_fob = precio_fob where (oculto_fob is null or oculto_fob = 0) and precio_fob is not null and precio_fob != 0;

UPDATE Item SET oculto_nac = precio_nac where (oculto_nac is null or oculto_nac = 0) and precio_nac is not null and precio_nac != 0;


ALTER TABLE Orden ADD COLUMN descripcion varchar(1024);


ALTER TABLE Item ADD COLUMN agrupacion_contable int DEFAULT 0;

UPDATE Item i join Categoria c on i.id_categoria = c.id_categoria join Grupo g on g.id_grupo = c.id_grupo
SET i.agrupacion_contable = g.agrupacion_contable;
