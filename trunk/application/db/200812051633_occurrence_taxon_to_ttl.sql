ALTER TABLE occurrences
DROP CONSTRAINT fk_occurrence_taxon,
DROP COLUMN taxon_id,
ADD taxa_taxon_list_id integer NOT NULL, -- Foreign key to the taxa_taxon_lists table. Identifies the species or other taxon that this is a record of.
ADD   CONSTRAINT fk_occurrence_taxon FOREIGN KEY (taxa_taxon_list_id)
      REFERENCES taxa_taxon_lists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;