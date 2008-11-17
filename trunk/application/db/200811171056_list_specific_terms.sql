DROP VIEW gv_terms;

ALTER TABLE terms
DROP COLUMN parent_id,
DROP COLUMN meaning_id,
DROP COLUMN preferred,
DROP CONSTRAINT fk_term_parent,
DROP CONSTRAINT fk_term_meaning;

ALTER TABLE termlists_terms
ADD COLUMN parent_id integer, -- Foreign key to the termlist_terms table. For heirarchical data, identifies the parent term.
ADD COLUMN meaning_id integer, -- Foreign key to the meaning table - identifies synonymous terms within this list.
ADD COLUMN preferred BOOLEAN NOT NULL DEFAULT FALSE, -- Flag set to true if the term is the preferred term amongst the group of terms with the same meaning.
ADD CONSTRAINT fk_termlists_term_parent FOREIGN KEY (parent_id)
	REFERENCES termlists_terms (id) MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION,
ADD CONSTRAINT fk_termlists_term_meaning FOREIGN KEY (meaning_id)
	REFERENCES meanings (id) MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION;

COMMENT ON COLUMN termlists_terms.parent_id IS 'Foreign key to the termlist_terms table. For heirarchical data, identifies the parent term.';
COMMENT ON COLUMN termlists_terms.meaning_id IS 'Foreign key to the meaning table - identifies synonymous terms within this list.';
COMMENT ON COLUMN termlists_terms.preferred IS 'Flag set to true if the term is the preferred term amongst the group of terms with the same meaning.';