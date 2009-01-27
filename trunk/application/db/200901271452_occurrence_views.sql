-- View: detail_occurrences

DROP VIEW IF EXISTS detail_occurrences;

CREATE OR REPLACE VIEW detail_occurrences AS 
 SELECT o.id, o.confidential, o.comment, 
	o.taxa_taxon_list_id, t.taxon,
	s.entered_sref, s.entered_sref_system, s.location_name, s.date_start, s.date_end, s.date_type, 
	s.location_id, l.name as location, l.code as location_code,	
	(d.first_name::text || ' '::text) || d.surname::text AS determiner,
	o.created_by_id, c.username AS created_by, o.created_on, o.updated_by_id, u.username AS updated_by, o.updated_on
   FROM occurrences o
   JOIN taxa_taxon_lists ttl on ttl.id=taxa_taxon_list_id
   JOIN taxa t on t.id=ttl.taxon_id
   JOIN users c ON c.id = o.created_by_id
   JOIN users u ON u.id = o.updated_by_id
   JOIN people d on d.id=o.determiner_id
   JOIN samples s ON s.id = o.sample_id
   LEFT JOIN locations l ON l.id = s.location_id;

DROP VIEW IF EXISTS list_occurrences;

CREATE OR REPLACE VIEW list_occurrences AS
 SELECT su.title as "survey", l.name AS "location", 
 	s.date_start, s.date_end, s.date_type, (d.first_name::text || ' '::text) || d.surname::text AS determiner, t.taxon
   FROM occurrences o
   JOIN samples s ON o.sample_id = s.id
   JOIN people d ON o.determiner_id = d.id
   JOIN locations l ON s.location_id = l.id
   JOIN taxa_taxon_lists ttl ON o.taxa_taxon_list_id = ttl.id
   JOIN taxa t ON ttl.taxon_id = t.id
   JOIN surveys su ON s.survey_id = su.id;
