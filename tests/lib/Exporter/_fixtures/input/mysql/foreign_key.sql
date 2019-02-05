CREATE TABLE my_main_table (
  id INTEGER NOT NULL AUTO_INCREMENT,
  name VARCHAR(255),
  PRIMARY KEY (id)
);

CREATE TABLE my_secondary_table (
  id INTEGER NOT NULL AUTO_INCREMENT,
  main_id INTEGER NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_my_secondary_table_id_main
    FOREIGN KEY (main_id) REFERENCES my_main_table (id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
