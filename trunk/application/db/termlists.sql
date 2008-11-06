\c opal

CREATE TABLE termlists (
id integer NOT NULL,
parent_id integer,
title varchar(100),
description text,
PRIMARY KEY(id),
CONSTRAINT fk_Termlists_Termlists
FOREIGN KEY (parent_id)
REFERENCES termlists (id)
ON DELETE NO ACTION
ON UPDATE NO ACTION);

CREATE INDEX fk_Termlists_Termlists ON termlists (parent_id);
