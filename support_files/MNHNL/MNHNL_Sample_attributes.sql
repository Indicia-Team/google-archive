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

--- The following new Sample Attributes are used for COBIMO: Common Bird Monitoring: mnhnl_bird_transect_walks (no survey allocated, direct to website)
--- The Temperature, CMS Username, CMS User ID and Emailcount attribute is a standard one.
--- TBD need to get definitions
--- Wind Force
--- Cloud Cover
--- Walk started at end
--- Closed
--- Reliability of this data
--- Visit number in year
--- Precipitation

//47	Bird Monitoring		Walk started at end	Lookup List	edit
//48	Bird Monitoring		Reliability of this data	Lookup List	edit
//49	Bird Monitoring		Visit number in year	Lookup List	edit
//50	Bird Monitoring		Wind Force	Lookup List	edit
//51	Bird Monitoring		Precipitation	Lookup List	edit
//52	Bird Monitoring		Cloud Cover	Lookup List	edit

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Walk Direction', 'The direction in which a walk along a transect was performed.', now(), 1, now(), 1, 'btw:walk');
SELECT insert_term('A to B', 'eng', null, 'btw:walk');
SELECT tmp_add_term('A � B', 'fra', null, 'btw:walk');
SELECT tmp_add_term('A nach B', 'deu', null, 'btw:walk');
SELECT tmp_add_term('A no B', 'ltz', null, 'btw:walk');
SELECT insert_term('B to A', 'eng', null, 'btw:walk');
SELECT tmp_add_term('B � A', 'fra', null, 'btw:walk');
SELECT tmp_add_term('B nach A', 'deu', null, 'btw:walk');
SELECT tmp_add_term('B no A', 'ltz', null, 'btw:walk');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='btw:walk');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Walk started at end', 'L', now(), 1, now(), 1, (select id from termlists where external_key='btw:walk'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Visit Number', 'First or second visit in the year?', now(), 1, now(), 1, 'btw:visit');
SELECT insert_term('1', 'eng', null, 'btw:visit');
SELECT insert_term('2', 'eng', null, 'btw:visit');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='btw:visit');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Visit number in year', 'L', now(), 1, now(), 1, (select id from termlists where external_key='btw:visit'), 'f', 't');

INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, applies_to_location, multi_value, public, applies_to_recorder, validation_rules) VALUES (
	'Start time', 'T', now(), 1, now(), 1, 'f', 'f', 't', 't', 'time');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, applies_to_location, multi_value, public, applies_to_recorder, validation_rules) VALUES (
	'End time', 'T', now(), 1, now(), 1, 'f', 'f', 't', 't', 'time');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Closed', 'B', now(), 1, now(), 1, 'f', 't');

--- The following new Occurrence Attributes are used for Butterflies1: Butterfly Monitoring: mnhnl_butterflies
--- The Temperature, CMS Username, CMS User ID and Email attribute is a standard one.
--- Start Time and End Time is used from COBIMO Above.

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('HabitatType', 'Habitat Type for this Section.', now(), 1, now(), 1, 'butterfly:habitat');
SELECT insert_term('URB', 'eng', null, 'butterfly:habitat'); ---  Milieux urbanis�s
SELECT insert_term('AGR', 'eng', null, 'butterfly:habitat'); --- Cultures annuelles
SELECT insert_term('PRA', 'eng', null, 'butterfly:habitat'); --- Prairies et herbages
SELECT insert_term('FFE', 'eng', null, 'butterfly:habitat'); --- For�ts de feuillus
SELECT insert_term('PEL', 'eng', null, 'butterfly:habitat'); --- Pelouses
SELECT insert_term('FRI', 'eng', null, 'butterfly:habitat'); --- Friches
SELECT insert_term('BUI', 'eng', null, 'butterfly:habitat'); --- Milieux buissonneux
SELECT insert_term('ZHU', 'eng', null, 'butterfly:habitat'); --- Zones humides
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:habitat');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Habitat type', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:habitat'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'No observation', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reliability', 'Reliability of data for this Section.', now(), 1, now(), 1, 'butterfly:reliability');
SELECT insert_term('1', 'eng', null, 'butterfly:reliability'); --- Reliable data
SELECT insert_term('2', 'eng', null, 'butterfly:reliability'); --- Weakly reliable data
SELECT insert_term('3', 'eng', null, 'butterfly:reliability'); --- Unreliable data
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:reliability');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Survey reliability', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:reliability'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Observer', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('MNHNL Month', 'Survey month', now(), 1, now(), 1, 'butterfly:Month');
SELECT insert_term('April', 'eng', null, 'butterfly:Month'); 
SELECT insert_term('May', 'eng', null, 'butterfly:Month'); 
SELECT insert_term('June', 'eng', null, 'butterfly:Month'); 
SELECT insert_term('July', 'eng', null, 'butterfly:Month'); 
SELECT insert_term('August', 'eng', null, 'butterfly:Month'); 
SELECT insert_term('September', 'eng', null, 'butterfly:Month'); 
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:Month');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'MNHNL Month', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:Month'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('MNHNL Number In Month', 'Survey number within this month', now(), 1, now(), 1, 'butterfly:numInMonth');
SELECT insert_term('1', 'fra', null, 'butterfly:numInMonth'); 
SELECT insert_term('2', 'fra', null, 'butterfly:numInMonth'); 
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:numInMonth');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Number in month', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:numInMonth'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('MNHNL Wind', 'Beaufort wind Force', now(), 1, now(), 1, 'butterfly:wind');
SELECT insert_term('Force  0: Calm. Smoke rises vertically.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  1: Smoke drift indicates wind direction, still wind vanes.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  2: Wind felt on exposed skin. Leaves rustle, vanes begin to move.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  3: Leaves and small twigs constantly moving, light flags extended.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  4: Dust and loose paper raised. Small branches begin to move.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  5: Branches of a moderate size move. Small trees in leaf begin to sway.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  6: Large branches in motion. Whistling heard in overhead wires.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  7: Whole trees in motion. Effort needed to walk against the wind.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  8: Some twigs broken from trees. Progress on foot is seriously impeded.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force  9: Some branches break off trees, and some small trees blow over.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force 10: Trees are broken off or uprooted, saplings bent and deformed.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force 11: Widespread damage to vegetation. Many roofing surfaces are damaged.', 'eng', null, 'butterfly:wind');
SELECT insert_term('Force 12: Very widespread damage to vegetation. Debris may be hurled about.', 'eng', null, 'butterfly:wind');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:wind');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Wind force', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:wind'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('MNHNL Cloud', 'Percent Cloud Cover', now(), 1, now(), 1, 'butterfly:cloud');
SELECT insert_term('0%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('5%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('10%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('15%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('20%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('25%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('30%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('35%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('40%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('45%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('50%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('55%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('60%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('65%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('70%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('75%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('80%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('85%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('90%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('95%', 'eng', null, 'butterfly:cloud');
SELECT insert_term('100%', 'eng', null, 'butterfly:cloud');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly:cloud');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Cloud cover', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly:cloud'), 'f', 't');

--- The following new Sample Attributes are used for Winter Bats: mnhnl_bats
--- The CMS Username, CMS User ID and Email attribute is a standard one.
--- Need to Check reliabilty WRT reliability in COBIMO above.
--- No observation is used from Butterfly1 above.

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('BatRegularFollowup', 'Suitablity for regular follow ups', now(), 1, now(), 1, 'bats:followup');
SELECT insert_term('Appropriate', 'eng', null, 'bats:followup');
SELECT tmp_add_term('Appropri�', 'fra', null, 'bats:followup');
SELECT insert_term('A bit appropriate', 'eng', null, 'bats:followup');
SELECT tmp_add_term('Peu appropri�', 'fra', null, 'bats:followup');
SELECT insert_term('Inappropriate', 'eng', null, 'bats:followup');
SELECT tmp_add_term('Inappropri�', 'fra', null, 'bats:followup');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:followup');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Site followup', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:followup'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('BatVisit', 'Visit Number in Year', now(), 1, now(), 1, 'bats:visit');
SELECT insert_term('1/1', 'eng', null, 'bats:visit');
SELECT insert_term('1/2', 'eng', null, 'bats:visit');
SELECT insert_term('2/2', 'eng', null, 'bats:visit');
SELECT insert_term('1/3', 'eng', null, 'bats:visit');
SELECT insert_term('2/3', 'eng', null, 'bats:visit');
SELECT insert_term('3/3', 'eng', null, 'bats:visit');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:visit');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Bat visit', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:visit'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('BatCavityOpening', 'Cavity Opening', now(), 1, now(), 1, 'bats:cavityopening');
SELECT insert_term('Blocked off', 'eng', null, 'bats:cavityopening');
SELECT tmp_add_term('Fermeture artificielle', 'fra', null, 'bats:cavityopening');
SELECT insert_term('Impenetrable for bats', 'eng', null, 'bats:cavityopening');
SELECT tmp_add_term('Imperm�able au passage des chiropt�res', 'fra', null, 'bats:cavityopening');
SELECT insert_term('Impenetrable for humans', 'eng', null, 'bats:cavityopening');
SELECT tmp_add_term('Imperm�able au passage des hommes', 'fra', null, 'bats:cavityopening');
SELECT insert_term('Defective locking system', 'eng', null, 'bats:cavityopening');
SELECT tmp_add_term('Syst�me de fermeture d�fectueux', 'fra', null, 'bats:cavityopening');
SELECT insert_term('Unstable entrance or entrance threatened with obstruction', 'eng', null, 'bats:cavityopening');
SELECT tmp_add_term('Entr�e instable ou menac�e d�obstruction', 'fra', null, 'bats:cavityopening');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:cavityopening');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Cavity entrance', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:cavityopening'), 't', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Cavity entrance comment', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('BatDisturbances', 'List of Disturbances', now(), 1, now(), 1, 'bats:disturbances');
SELECT insert_term('Noise', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Bruit', 'fra', null, 'bats:disturbances');
SELECT insert_term('Instability', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Instabilit�', 'fra', null, 'bats:disturbances');
SELECT insert_term('Vibrations', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Vibrations', 'fra', null, 'bats:disturbances');
SELECT insert_term('Artificial light', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Lumi�re artificielle', 'fra', null, 'bats:disturbances');
SELECT insert_term('Risk of temporary flooding', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Risques d�inondation temporaire', 'fra', null, 'bats:disturbances');
SELECT insert_term('Toxic waste present', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Pr�sence de d�chets toxiques', 'fra', null, 'bats:disturbances');
SELECT insert_term('Other', 'eng', null, 'bats:disturbances');
SELECT tmp_add_term('Autre', 'fra', null, 'bats:disturbances');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:disturbances');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Disturbances', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:disturbances'), 't', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Disturbances other comment', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('HumanFrequentation', 'Site Type', now(), 1, now(), 1, 'bats:humanfreq');
SELECT insert_term('None', 'eng', null, 'bats:humanfreq');
SELECT tmp_add_term('Nulle', 'fra', null, 'bats:humanfreq');
SELECT insert_term('Occasional', 'eng', null, 'bats:humanfreq');
SELECT tmp_add_term('Occasionnelle', 'fra', null, 'bats:humanfreq');
SELECT insert_term('Moderate', 'eng', null, 'bats:humanfreq');
SELECT tmp_add_term('Mod�r�e', 'fra', null, 'bats:humanfreq');
SELECT insert_term('Intense', 'eng', null, 'bats:humanfreq');
SELECT tmp_add_term('Intense', 'fra', null, 'bats:humanfreq');
SELECT insert_term('Unknown', 'eng', null, 'bats:humanfreq');
SELECT tmp_add_term('Inconnue', 'fra', null, 'bats:humanfreq');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:humanfreq');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Human frequentation', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:humanfreq'), 'f', 't');
--- TBD use standard temperature as outside temp
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Temp Exterior', 'F', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Temp Int 1', 'F', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Temp Int 2', 'F', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Temp Int 3', 'F', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Humid Exterior', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Humid Int 1', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Humid Int 2', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Humid Int 3', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Positions marked', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reliability', 'Reliability of data for this Section.', now(), 1, now(), 1, 'bats:reliability');
SELECT insert_term('1 - Reliable', 'eng', null, 'bats:reliability');
SELECT tmp_add_term('1 - Fiable', 'fra', null, 'bats:reliability');
SELECT insert_term('2 - Weakly reliable', 'eng', null, 'bats:reliability');
SELECT tmp_add_term('2 � Peu fiable', 'fra', null, 'bats:reliability');
SELECT insert_term('3 - Unreliable', 'eng', null, 'bats:reliability');
SELECT tmp_add_term('3 � Non fiable', 'fra', null, 'bats:reliability'); 
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats:reliability');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Reliability', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats:reliability'), 'f', 't');
--- NEW
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Accompanied By', 'T', now(), 1, now(), 1, 'f', 't');

--- The following new Occurrence Attributes are used for Butterflies2: Butterfly de Jours: mnhnl_butterflies2
--- The Temperature, CMS Username, CMS User ID and Email attribute is a standard one.
--- Start Time is used from COBIMO above.
--- Cloud Cover, No observation are used from Butterfly1 above.
--- Reliability is used from Bats above.

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('MNHNL Butterfly2 Passage', 'Survey month', now(), 1, now(), 1, 'butterfly2:Passage');
SELECT insert_term('May', 'eng', null, 'butterfly2:Passage');
SELECT tmp_add_term('Mai', 'fra', null, 'butterfly2:Passage'); 
SELECT insert_term('June', 'eng', null, 'butterfly2:Passage');
SELECT tmp_add_term('Juin', 'fra', null, 'butterfly2:Passage'); 
SELECT insert_term('July', 'eng', null, 'butterfly2:Passage');
SELECT tmp_add_term('Juillet', 'fra', null, 'butterfly2:Passage'); 
SELECT insert_term('August', 'eng', null, 'butterfly2:Passage');
SELECT tmp_add_term('Ao�t', 'fra', null, 'butterfly2:Passage'); 
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly2:Passage');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Passage', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly2:Passage'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Duration', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Windspeed', 'I', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Rain', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Butterfly2 Target Species', 'Butterfly2 Target Species', now(), 1, now(), 1, 'butterfly2:targetspecies');
SELECT insert_term('Euphydryas aurinia', 'eng', null, 'butterfly2:targetspecies');
SELECT insert_term('Lycaena helle', 'eng', null, 'butterfly2:targetspecies');
SELECT insert_term('Lycaena dispar', 'eng', null, 'butterfly2:targetspecies');
SELECT insert_term('Phengaris arion', 'eng', null, 'butterfly2:targetspecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='butterfly2:targetspecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Butterfly2 Target Species', 'L', now(), 1, now(), 1, (select id from termlists where external_key='butterfly2:targetspecies'), 't', 't');

--- The following new Sample Attributes are used for Reptiles: mnhnl_reptiles
--- The Temperature, CMS Username, CMS User ID and Email attributes are standard ones.
--- Wind Force, Cloud Cover, No observation are used from Butterfly1 above.
--- Rain and Duration are used from Butterflies2 above.

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('ReptileTargetSpecies', 'Reptile Target Species', now(), 1, now(), 1, 'reptile:targetSpecies');
SELECT insert_term('Sand Lizard (Lacerta agilis)', 'eng', null, 'reptile:targetSpecies');
SELECT insert_term('Common Wall Lizard (Podarcis muralis)', 'eng', null, 'reptile:targetSpecies');
SELECT insert_term('Smooth Snake (Coronella austriaca)', 'eng', null, 'reptile:targetSpecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:targetSpecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'ReptileTargetSpecies', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:targetSpecies'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Reptile Visit', 'Reptile Visit', now(), 1, now(), 1, 'reptile:visit');
SELECT insert_term('1', 'eng', null, 'reptile:visit');
SELECT insert_term('2', 'eng', null, 'reptile:visit');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='reptile:visit');
INSERT INTO sample_attributes (	caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Reptile Visit', 'L', now(), 1, now(), 1, (select id from termlists where external_key='reptile:visit'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (	
	'Unsuitability', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Picture provided', 'B', now(), 1, now(), 1, 'f', 't');

--- The following new Sample Attributes are used for Summer Bats: mnhnl_bats2
--- The CMS Username, CMS User ID and Email attribute is a standard one.
--- Use Disturbances other comment, Site followup, Accompanied by from Bats1.
--- Start Time and End Time is used from COBIMO Above.
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bat2Visit', 'Visit Number in Year', now(), 1, now(), 1, 'bats2:visit');
SELECT insert_term('1 of 1', 'eng', null, 'bats2:visit');
SELECT insert_term('1 of 2', 'eng', null, 'bats2:visit');
SELECT insert_term('2 of 2', 'eng', null, 'bats2:visit');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:visit');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Bat2 visit', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:visit'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Sketch provided', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bat2Disturbances', 'List of Disturbances', now(), 1, now(), 1, 'bats2:disturbances');
SELECT insert_term('Artificial light illuminating the entrance hole', 'eng', null, 'bats2:disturbances');
SELECT insert_term('Planned renovations', 'eng', null, 'bats2:disturbances');
SELECT insert_term('Renovations in progress', 'eng', null, 'bats2:disturbances');
SELECT insert_term('Renovations recently completed', 'eng', null, 'bats2:disturbances');
SELECT insert_term('Other', 'eng', null, 'bats2:disturbances');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:disturbances');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Disturbances2', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:disturbances'), 't', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bat2Precipitation', 'List of types of precipitation', now(), 1, now(), 1, 'bats2:precipitation');
SELECT insert_term('None', 'eng', null, 'bats2:precipitation');
SELECT insert_term('Drizzle', 'eng', null, 'bats2:precipitation');
SELECT insert_term('Showers', 'eng', null, 'bats2:precipitation');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:precipitation');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Precipitation2', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:precipitation'), 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bats2SurveyMethod', 'List of types of precipitation', now(), 1, now(), 1, 'bats2:surveymethod');
SELECT insert_term('Presence recording', 'eng', null, 'bats2:surveymethod');
SELECT insert_term('Picture of the maternity', 'eng', null, 'bats2:surveymethod');
SELECT insert_term('Count at dusk emergence', 'eng', null, 'bats2:surveymethod');
SELECT insert_term('Count in maternity roost', 'eng', null, 'bats2:surveymethod');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:surveymethod');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Bats2SurveyMethod', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:surveymethod'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'No record', 'B', now(), 1, now(), 1, 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Species Comment', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bats2Institution', 'List of types of precipitation', now(), 1, now(), 1, 'bats2:institution');
SELECT insert_term('Administration de la Nature et des For�ts', 'eng', null, 'bats2:institution');
SELECT insert_term('Centre de Recherche Public - Gabriel Lippmann', 'eng', null, 'bats2:institution');
SELECT insert_term('Mus�e national d''histoire naturelle', 'eng', null, 'bats2:institution');
SELECT insert_term('natur&�mwelt', 'eng', null, 'bats2:institution');
SELECT insert_term('Naturpark Obersauer', 'eng', null, 'bats2:institution');
SELECT insert_term('Naturpark Our', 'eng', null, 'bats2:institution');
SELECT insert_term('ProChirop - B�ro f�r Fledertierforschung und -schutz', 'eng', null, 'bats2:institution');
SELECT insert_term('SIAS - Biologische Station Naturzenter', 'eng', null, 'bats2:institution');
SELECT insert_term('SICONA Centre', 'eng', null, 'bats2:institution');
SELECT insert_term('SICONA Ouest', 'eng', null, 'bats2:institution');
SELECT insert_term('Station biologique SICONA', 'eng', null, 'bats2:institution');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:institution');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Institution', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:institution'), 't', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Bats2TargetSpecies', 'List of types of precipitation', now(), 1, now(), 1, 'bats2:targetspecies');
SELECT insert_term('Rhinolophus ferrumequinum (Grand rhinolophe/Greater horseshoe bat/Gro�e Hufeisennase)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Rhinolophus hipposideros (Petit rhinolophe/Lesser horsehoe bat/Kleine Hufeisennase)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Barbastella barbastellus (Barbastelle commune/Barbastelle/Mopsfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Eptesicus nilssonii (S�rotine de Nilsson/Northern bat/Nordfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Eptesicus serotinus (S�rotine commune/Serotine/Breitfl�gelfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis bechsteinii (Murin de Bechstein/Bechstein''s bat/Bechsteinfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis brandtii (Murin de Brandt/Brandt''s bat/Gro�e Bartfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis dasycneme (Murin des marais/Pond bat/Teichfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis daubentonii (Murin de daubenton/Daubenton''s bat/Wasserfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis emarginatus (Murin � oreilles �chancr�es/Geoffroy''s bat/Wimperfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis myotis (Grand murin/Greater mouse-eared bat/Gro�e Mausohr)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis mystacinus (Murin � moustaches/Whiskerd bat/Kleine Bartfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Myotis nattereri (Murin de Natterer/Natterer''s bat/Fransenfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Nyctalus leisleri (Noctule de Leisler/Leisler''s bat/Kleine Abendsegler)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Nyctalus noctula (Noctule commune/Common noctule/Gro�e Abendsegler)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Pipistrellus nathusii (Pipistrelle de Nathusius/Nathusius''s pipistrelle/Rauhautfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Pipistrellus pipistrellus (Pipistrelle commune/Common pipistrelle/Zwergfledermaus)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Plecotus auritus (Oreillard commun/Common long-eared bat/Braune Langohr)', 'eng', null, 'bats2:targetspecies');
SELECT insert_term('Plecotus austriacus (Oreillard m�ridional/Grey long-eared bat/Graues Langohr)', 'eng', null, 'bats2:targetspecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='bats2:targetspecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Target Species', 'L', now(), 1, now(), 1, (select id from termlists where external_key='bats2:targetspecies'), 't', 't');

--- The following new Sample Attributes are used for Dormice: mnhnl_dynamic_2
--- Start Time and End Time are used from Cobimo.
--- Standard Temperature is used.
--- Cloud cover used from Butterflies1.
--- Rain used from Butterflies1.
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Orientation', 'Orientation', now(), 1, now(), 1, 'dormice:orientation');
SELECT insert_term('N', 'eng', null, 'dormice:orientation');
SELECT insert_term('NNE', 'eng', null, 'dormice:orientation');
SELECT insert_term('NE', 'eng', null, 'dormice:orientation');
SELECT insert_term('ENE', 'eng', null, 'dormice:orientation');
SELECT insert_term('E', 'eng', null, 'dormice:orientation');
SELECT insert_term('ESE', 'eng', null, 'dormice:orientation');
SELECT insert_term('SE', 'eng', null, 'dormice:orientation');
SELECT insert_term('SSE', 'eng', null, 'dormice:orientation');
SELECT insert_term('S', 'eng', null, 'dormice:orientation');
SELECT insert_term('SSW', 'eng', null, 'dormice:orientation');
SELECT insert_term('SW', 'eng', null, 'dormice:orientation');
SELECT insert_term('WSW', 'eng', null, 'dormice:orientation');
SELECT insert_term('W', 'eng', null, 'dormice:orientation');
SELECT insert_term('WNW', 'eng', null, 'dormice:orientation');
SELECT insert_term('NW', 'eng', null, 'dormice:orientation');
SELECT insert_term('NNW', 'eng', null, 'dormice:orientation');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:orientation');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Orientation', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:orientation'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Site suitability', 'Suitability of site.', now(), 1, now(), 1, 'dormice:siteSuitability');
SELECT insert_term('1 - Suitable site', 'eng', null, 'dormice:siteSuitability');
SELECT insert_term('2 - Weakly suitable site', 'eng', null, 'dormice:siteSuitability');
SELECT insert_term('3 - Unsuitable site', 'eng', null, 'dormice:siteSuitability');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:siteSuitability');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Site suitability', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:siteSuitability'), 'f', 't');

INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Precision', 'I', now(), 1, now(), 1, 'f', 't');

--- should the following perhaps be location based?
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DormiceHabitatType', 'Dormice Habitat Type', now(), 1, now(), 1, 'dormice:habitattype');
SELECT insert_term('Forest edge', 'eng', null, 'dormice:habitattype');
SELECT insert_term('Hedgerow', 'eng', null, 'dormice:habitattype');
SELECT insert_term('Forest stand', 'eng', null, 'dormice:habitattype');
SELECT insert_term('Plantation forest', 'eng', null, 'dormice:habitattype');
SELECT insert_term('Open area in forest', 'eng', null, 'dormice:habitattype');
SELECT insert_term('Other', 'eng', null, 'dormice:habitattype');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:habitattype');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Dormice habitat type', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:habitattype'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Dormice habitat type other', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DormiceSuccession', 'Dormice Succession Type', now(), 1, now(), 1, 'dormice:succession');
SELECT insert_term('Clearcut forest', 'eng', null, 'dormice:succession');
SELECT insert_term('Forest windfall', 'eng', null, 'dormice:succession');
SELECT insert_term('Brambles', 'eng', null, 'dormice:succession');
SELECT insert_term('Early-succession forest', 'eng', null, 'dormice:succession');
SELECT insert_term('Coppice forest', 'eng', null, 'dormice:succession');
SELECT insert_term('Early timberland forest', 'eng', null, 'dormice:succession');
SELECT insert_term('Mature timberland forest', 'eng', null, 'dormice:succession');
SELECT insert_term('Other', 'eng', null, 'dormice:succession');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:succession');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Dormice succession', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:succession'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Dormice succession other', 'T', now(), 1, now(), 1, 'f', 't');
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DormiceBorderingHabitatType', 'Dormice Bordering Habitat Type', now(), 1, now(), 1, 'dormice:borderinghabitattype');
SELECT insert_term('Broadleaved forest', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Coniferous forest', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Mixed forest', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Fallow', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Forest windfall', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Heathland', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Arable land', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Industrial wasteland', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Grassland', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Orchard', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Wetland', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Rocky area', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Urban area', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Road', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Path', 'eng', null, 'dormice:borderinghabitattype');
SELECT insert_term('Other', 'eng', null, 'dormice:borderinghabitattype');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:borderinghabitattype');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Dormice bordering habitat type', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:borderinghabitattype'), 't', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, multi_value, public) VALUES (
	'Dormice bordering habitat other', 'T', now(), 1, now(), 1, 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DormiceShrubDiversity', 'Dormice Shrub Diversity', now(), 1, now(), 1, 'dormice:shrubdiversity');
SELECT insert_term('1 - 3 species', 'eng', null, 'dormice:shrubdiversity');
SELECT insert_term('3 - 5 species', 'eng', null, 'dormice:shrubdiversity');
SELECT insert_term('5 - 10 species', 'eng', null, 'dormice:shrubdiversity');
SELECT insert_term('+ 10 species', 'eng', null, 'dormice:shrubdiversity');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dormice:shrubdiversity');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Dormice shrub diversity', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dormice:shrubdiversity'), 'f', 't');

--- The following new Sample Attributes are used for Site Based Amphibians: this uses the same form as the Reptiles.
--- Unsuitability comes from Reptiles.

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AmphibianSitesTargetSpecies', 'Site Based Amphibian Target Species', now(), 1, now(), 1, 'amphibian:targetSpecies');
SELECT insert_term('Yellow-bellied Toad (Bombina variegata)', 'eng', null, 'amphibian:targetSpecies');
SELECT insert_term('Natterjack Toad (Bufo calamita)', 'eng', null, 'amphibian:targetSpecies');
SELECT insert_term('Common Tree Frog (Hyla arborea)', 'eng', null, 'amphibian:targetSpecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:targetSpecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'AmphibianSitesTargetSpecies', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:targetSpecies'), 'f', 't');

-- will use this for Squares as well.
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AmphibianVisit', 'Visit for Amphibians, both sites and squares', now(), 1, now(), 1, 'amphibian:visit');
SELECT insert_term('1', 'eng', null, 'amphibian:visit');
SELECT insert_term('2', 'eng', null, 'amphibian:visit');
SELECT insert_term('3', 'eng', null, 'amphibian:visit');
SELECT insert_term('4', 'eng', null, 'amphibian:visit');
SELECT insert_term('5', 'eng', null, 'amphibian:visit');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:visit');
INSERT INTO sample_attributes (	caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Amphibian Visit', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:visit'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AmphibianSitesSurveyMethod', 'Survey Method for site based Amphibians', now(), 1, now(), 1, 'amphibian:surveymethod');
SELECT insert_term('Diurnal control', 'eng', null, 'amphibian:surveymethod');
SELECT insert_term('Mark-release-recapture', 'eng', null, 'amphibian:surveymethod');
SELECT insert_term('Diurnal clutch counting', 'eng', null, 'amphibian:surveymethod');
SELECT insert_term('Nocturnal calling survey', 'eng', null, 'amphibian:surveymethod');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:surveymethod');
INSERT INTO sample_attributes (	caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Amphibian Sites Survey Method', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:surveymethod'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('TerrestrialHabitat', 'TerrestrialHabitat', now(), 1, now(), 1, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Arable land', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Broadleaved forest', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Coniferous forest', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Grassland', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Heathland', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Industrial wasteland', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Mixed forest', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Orchard', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Rocky area', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Shrub-covered area', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Urban area', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Vineyard', 'eng', null, 'amphibian:TerrestrialHabitat');
SELECT insert_term('Wetland', 'eng', null, 'amphibian:TerrestrialHabitat');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:TerrestrialHabitat');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Terrestrial habitat', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:TerrestrialHabitat'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AquaticHabitat', 'AquaticHabitat', now(), 1, now(), 1, 'amphibian:AquaticHabitat');
SELECT insert_term('Canal/channel', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Ditch/drain', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Lake', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Pond', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('River', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Rut', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Settling tank', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Storm-water basin', 'eng', null, 'amphibian:AquaticHabitat');
SELECT insert_term('Stream', 'eng', null, 'amphibian:AquaticHabitat');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:AquaticHabitat');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Aquatic habitat', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:AquaticHabitat'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Flow', 'Flow', now(), 1, now(), 1, 'amphibian:flow');
SELECT insert_term('Standing', 'eng', null, 'amphibian:flow');
SELECT insert_term('Running', 'eng', null, 'amphibian:flow');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:flow');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Flow', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:flow'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Permanence', 'Permanence', now(), 1, now(), 1, 'amphibian:permanence');
SELECT insert_term('Permanent', 'eng', null, 'amphibian:permanence');
SELECT insert_term('Temporary', 'eng', null, 'amphibian:permanence');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:permanence');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Permanence', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:permanence'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Rating', 'Rating', now(), 1, now(), 1, 'amphibian:rating');
SELECT insert_term('None', 'eng', null, 'amphibian:rating');
SELECT insert_term('Low', 'eng', null, 'amphibian:rating');
SELECT insert_term('Medium', 'eng', null, 'amphibian:rating');
SELECT insert_term('High', 'eng', null, 'amphibian:rating');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:rating');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Insolation', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Fish density', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Herbaceous', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Shrubs', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Trees', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Hydrophytic vegetation', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:rating'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Water depth', 'Water depth', now(), 1, now(), 1, 'amphibian:aquaticdepth');
SELECT insert_term('< 10 cm', 'eng', null, 'amphibian:aquaticdepth');
SELECT insert_term('10-25 cm', 'eng', null, 'amphibian:aquaticdepth');
SELECT insert_term('25-50 cm', 'eng', null, 'amphibian:aquaticdepth');
SELECT insert_term('> 50 cm', 'eng', null, 'amphibian:aquaticdepth');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:aquaticdepth');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Maximal depth', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:aquaticdepth'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Amphibian Recording Summary', 'Amphibian Recording Class', now(), 1, now(), 1, 'amphibian:recording');
SELECT insert_term('Species recorded', 'eng', null, 'amphibian:recording');
SELECT insert_term('No observations', 'eng', null, 'amphibian:recording');
SELECT insert_term('No records taken', 'eng', null, 'amphibian:recording');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian:recording');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Amphibian Recording Summary', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian:recording'), 'f', 't');

--- The following new Sample Attributes are used for Square Based Amphibians: this uses the same form as the Reptiles and Amphibians(Sites).
--- Most shared with sites based survey

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AmphibianSquaresTargetSpecies', 'Squares Based Amphibian Target Species', now(), 1, now(), 1, 'amphibian2:targetSpecies');
SELECT insert_term('Common Midwife Toad (Alytes obstetricans)', 'eng', null, 'amphibian2:targetSpecies');
SELECT insert_term('Edible Frog (Rana kl. esculenta)', 'eng', null, 'amphibian2:targetSpecies');
SELECT insert_term('Common Frog (Rana temporaria)', 'eng', null, 'amphibian2:targetSpecies');
SELECT insert_term('Great Crested Newt (Triturus cristatus)', 'eng', null, 'amphibian2:targetSpecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian2:targetSpecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'AmphibianSquaresTargetSpecies', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian2:targetSpecies'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('AmphibianSquaresSurveyMethod', 'Survey Method for Squares based Amphibians', now(), 1, now(), 1, 'amphibian2:surveymethod');
SELECT insert_term('Diurnal control', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Diurnal adult counting', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Diurnal clutch counting', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Nocturnal calling survey', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Adult counting (net)', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Adult counting (torch)', 'eng', null, 'amphibian2:surveymethod');
SELECT insert_term('Adult counting (trap)', 'eng', null, 'amphibian2:surveymethod');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='amphibian2:surveymethod');
INSERT INTO sample_attributes (	caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Amphibian Squares Survey Method', 'L', now(), 1, now(), 1, (select id from termlists where external_key='amphibian2:surveymethod'), 'f', 't');

--- DragonFly Form:
--- Use the BTW Visit, normal Start and end dates and Sample reliability.
--- Use Amphibian Terrestrial Habitat, Aquatic Habitat, Flow, Permanence, Insolation, Fish density, Herbaceous, Shrubs, Trees, Hydrophytic vegetation, Maximal Depth
--- Standard Temperature, Wind force, Cloud cover and Rain

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DragonflyTargetSpecies', 'Dragonfly Target Species', now(), 1, now(), 1, 'dragonfly:targetSpecies');
SELECT insert_term('Lilypad Whiteface (Leucorrhinia caudalis)', 'eng', null, 'dragonfly:targetSpecies');
SELECT insert_term('Mercury Bluet (Coenagrion mercuriale)', 'eng', null, 'dragonfly:targetSpecies');
SELECT insert_term('Orange-Spotted Emerald (Oxygastra curtisii)', 'eng', null, 'dragonfly:targetSpecies');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dragonfly:targetSpecies');
INSERT INTO sample_attributes (caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'DragonflyTargetSpecies', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dragonfly:targetSpecies'), 'f', 't');

INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('DragonflySurveyMethod', 'Survey Method for Squares based Amphibians', now(), 1, now(), 1, 'dragonfly:surveymethod');
SELECT insert_term('Presence recording', 'eng', null, 'dragonfly:surveymethod');
SELECT insert_term('Imago count', 'eng', null, 'dragonfly:surveymethod');
SELECT insert_term('Exuviae count', 'eng', null, 'dragonfly:surveymethod');
UPDATE termlists_terms SET sort_order = 10*id WHERE termlist_id = (SELECT id FROM termlists WHERE external_key='dragonfly:surveymethod');
INSERT INTO sample_attributes (	caption, data_type, created_on, created_by_id, updated_on, updated_by_id, termlist_id, multi_value, public) VALUES (
	'Dragonfly Survey Method', 'L', now(), 1, now(), 1, (select id from termlists where external_key='dragonfly:surveymethod'), 't', 't');


