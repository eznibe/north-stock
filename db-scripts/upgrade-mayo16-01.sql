
alter table item modify column precio_ref float(11,4);

UPDATE Item SET precio_ref = precio_nac * 15 WHERE precio_nac IS NOT NULL AND precio_nac <> 0;
