
alter table dg_transito_negativo add column tipo_accion char(50) default 'manual';

update item set stock_transito = 1000 where id_item = 303;
update item set stock_transito = 1200 where id_item = 853;
