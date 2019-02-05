CREATE TABLE my_main_table (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  name VARCHAR(255)
);

CREATE TABLE my_secondary_table (
  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  main_id INTEGER NOT NULL,
  CONSTRAINT fk_my_secondary_table_id_main
    FOREIGN KEY (main_id) REFERENCES my_main_table (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE INDEX fk_my_secondary_table_id_main ON my_secondary_table (main_id);
