DROP TABLE IF EXISTS titles;
CREATE TABLE titles (
    id integer NOT NULL,
    title character varying(10)
);

ALTER TABLE ONLY titles
    ADD CONSTRAINT pk_titles PRIMARY KEY (id);

Insert into titles (id, title) VALUES (1, 'Mr');
Insert into titles (id, title) VALUES (2, 'Mrs');
Insert into titles (id, title) VALUES (3, 'Miss');
Insert into titles (id, title) VALUES (4, 'Ms');
Insert into titles (id, title) VALUES (5, 'Master');
Insert into titles (id, title) VALUES (6, 'Dr');

ALTER TABLE people
ADD COLUMN title_id integer, --Optional Foreign key to the titles table
ADD COLUMN address character varying(200), --Optional address
ADD CONSTRAINT fk_person_title FOREIGN KEY (title_id) REFERENCES titles(id);

COMMENT ON COLUMN titles.title IS 'Persons title';
COMMENT ON COLUMN people.title_id IS 'Foreign key to the titles table.';
COMMENT ON COLUMN people.created_by_id IS 'Optional persons address.';

CREATE SEQUENCE titles_id_seq
    START WITH 10
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
ALTER SEQUENCE titles_id_seq OWNED BY titles.id;
SELECT pg_catalog.setval('titles_id_seq', 10, false);
