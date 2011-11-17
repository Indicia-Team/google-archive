CREATE OR REPLACE FUNCTION tmp_add_term(t character varying(100), lang_iso character(3), list integer, list_external_key character varying) RETURNS integer AS
$BODY$
DECLARE
  m_id integer;
  t_id integer;
  l_id integer;
BEGIN
    l_id := CASE WHEN list IS NULL THEN (SELECT id FROM termlists WHERE external_key=list_external_key) ELSE list END;

    t_id := nextval('terms_id_seq'::regclass);

    INSERT INTO terms (id, term, language_id, created_on, created_by_id, updated_on, updated_by_id)
    VALUES (t_id, t, (SELECT id from languages WHERE iso = lang_iso), now(), 1, now(), 1);

    m_id := currval('meanings_id_seq'::regclass);

    INSERT INTO termlists_terms (term_id, termlist_id, meaning_id, preferred, created_on, created_by_id, updated_on, updated_by_id)
    VALUES (t_id, l_id, m_id, 'f', now(), 1, now(), 1);

    RETURN 1;
END
$BODY$
LANGUAGE 'plpgsql';

--- the count attribute is a standard one.
--- we assume here that the samples attributes have been loaded in already, with their Reliability termlist

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('butterfly distribution', 'Qualitive distribution - how close species was to observer gives a indication of reliability of identification',
	now(), 1, now(), 1, 'butterfly:distribution');
SELECT insert_term(' ', 'eng', null, 'butterfly:distribution');
SELECT insert_term('X', 'eng', null, 'butterfly:distribution');
SELECT insert_term('/', 'eng', null, 'butterfly:distribution');
SELECT insert_term('0', 'eng', null, 'butterfly:distribution');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:distribution');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Butterfly Qual Dist', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:distribution'), 'f', 't');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Num alive', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Num dead', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Excrement', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Occurrence reliability', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:reliability'), 'f', 't');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Counting', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reptile Type', 'Reptile Occurrence Type.', now(), 1, now(), 1, 'reptile:type');
SELECT insert_term('Dead specimen', 'eng', null, 'reptile:type');
SELECT insert_term('Slough', 'eng', null, 'reptile:type');
SELECT insert_term('Specimen', 'eng', null, 'reptile:type');
SELECT insert_term('Undetermined', 'eng', null, 'reptile:type');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:type');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Type', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:type'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reptile Stage', 'Reptile Occurrence Stage.', now(), 1, now(), 1, 'reptile:stage');
SELECT insert_term('Egg', 'eng', null, 'reptile:stage');
SELECT insert_term('Juvenile', 'eng', null, 'reptile:stage');
SELECT insert_term('Adult', 'eng', null, 'reptile:stage');
SELECT insert_term('Undetermined', 'eng', null, 'reptile:stage');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:stage');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Stage', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:stage'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reptile Sex', 'Reptile Occurrence Stage.', now(), 1, now(), 1, 'reptile:sex');
SELECT insert_term('Female', 'eng', null, 'reptile:sex');
SELECT insert_term('Male', 'eng', null, 'reptile:sex');
SELECT insert_term('Pair', 'eng', null, 'reptile:sex');
SELECT insert_term('Undetermined', 'eng', null, 'reptile:sex');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:sex');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Sex', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:sex'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reptile Behaviour', 'Reptile Occurrence Behaviour.', now(), 1, now(), 1, 'reptile:behaviour');
SELECT insert_term('Basking', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Displaying', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Feeding', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Fighting', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Hunting', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Inactivity', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Lethargy', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Mating', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Ovipositing', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Resting', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Swimming', 'eng', null, 'reptile:behaviour');
SELECT insert_term('Undetermined', 'eng', null, 'reptile:behaviour');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:behaviour');
INSERT INTO occurrence_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Behaviour', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:behaviour'), 'f', 't');
