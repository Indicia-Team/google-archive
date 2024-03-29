-- Table: cache_termlists_terms

-- DROP TABLE cache_termlists_terms;

CREATE TABLE cache_termlists_terms
(
  id integer NOT NULL,
  preferred boolean,
  termlist_id integer,
  termlist_title character varying,
  website_id integer,
  preferred_termlists_term_id integer,
  parent_id integer,
  sort_order integer,
  term character varying,
  language_iso character varying(3),
  "language" character varying(50),
  preferred_term character varying,
  preferred_language_iso character varying(3),
  preferred_language character varying(50),
  meaning_id integer,
  cache_created_on timestamp without time zone NOT NULL,
  cache_updated_on timestamp without time zone NOT NULL,
  CONSTRAINT pk_cache_termlists_terms PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
COMMENT ON TABLE cache_termlists_terms IS 'A cache of all termlist_terms entry including joins to the most likely fields to be required of any query. Updated when the scheduled_tasks action is called so should not be used when fully up to date information is required.';

-- Index: ix_cache_termlists_terms_language_iso

-- DROP INDEX ix_cache_termlists_terms_language_iso;

CREATE INDEX ix_cache_termlists_terms_language_iso
  ON cache_termlists_terms
  USING btree
  (language_iso);

-- Index: ix_cache_termlists_terms_parent_id

-- DROP INDEX ix_cache_termlists_terms_parent_id;

CREATE INDEX ix_cache_termlists_terms_parent_id
  ON cache_termlists_terms
  USING btree
  (parent_id);

-- Index: ix_cache_termlists_terms_sort_order

-- DROP INDEX ix_cache_termlists_terms_sort_order;

CREATE INDEX ix_cache_termlists_terms_sort_order
  ON cache_termlists_terms
  USING btree
  (sort_order);

-- Index: ix_cache_termlists_terms_term

-- DROP INDEX ix_cache_termlists_terms_term;

CREATE INDEX ix_cache_termlists_terms_term
  ON cache_termlists_terms
  USING btree
  (term);

-- Index: ix_cache_termlists_terms_termlist_id

-- DROP INDEX ix_cache_termlists_terms_termlist_id;

CREATE INDEX ix_cache_termlists_terms_termlist_id
  ON cache_termlists_terms
  USING btree
  (termlist_id);

  -- Index: ix_cache_termlists_terms_meaning_id

-- DROP INDEX ix_cache_termlists_terms_meaning_id;

CREATE INDEX ix_cache_termlists_terms_meaning_id
  ON cache_termlists_terms
  USING btree
  (meaning_id);


