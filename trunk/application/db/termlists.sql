-- Table: termlists

-- DROP TABLE termlists;

CREATE TABLE termlists
(
  id integer NOT NULL,
  title character varying(100) NOT NULL, -- Title of the termlist.
  description text, -- Description of the termlist.
  website_id integer, -- Foreign key to the websites table. Identifies the
website that this termlist is owned by, or null if publicly owned.
  parent_id integer, -- Foreign key to the termlists table. Identifies the
parent list when a list is a subset of another.
  deleted boolean NOT NULL DEFAULT false, -- Identifies if the termlist has
been marked as deleted.
  CONSTRAINT pk_termlists PRIMARY KEY (id),
  CONSTRAINT fk_parent_termlist FOREIGN KEY (parent_id)
      REFERENCES termlists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_website FOREIGN KEY (website_id)
      REFERENCES websites (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE termlists OWNER TO postgres;
COMMENT ON COLUMN termlists.title IS 'Title of the termlist.';
COMMENT ON COLUMN termlists.description IS 'Description of the termlist.';
COMMENT ON COLUMN termlists.website_id IS 'Foreign key to the websites
table. Identifies the website that this termlist is owned by, or null if
publicly owned.';
COMMENT ON COLUMN termlists.parent_id IS 'Foreign key to the termlists
table. Identifies the parent list when a list is a subset of another.';
COMMENT ON COLUMN termlists.deleted IS 'Identifies if the termlist has been
marked as deleted.';