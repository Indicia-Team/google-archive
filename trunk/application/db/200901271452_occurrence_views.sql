-- View: detail_occurrences

-- DROP VIEW detail_occurrences;

CREATE OR REPLACE VIEW detail_occurrences AS 
 SELECT o.id, o.confidential, o.comment, 
	o.taxa_taxon_list_id, t.taxon,
	s.entered_sref, s.entered_sref_system, s.location_name, s.date_start, s.date_end, s.date_type, 
	s.location_id, l.name as location, l.code as location_code,	
	p.first_name as determiner_first_name, p.surname as determiner_surname,
	o.created_by_id, c.username AS created_by, o.created_on, o.updated_by_id, u.username AS updated_by, o.updated_on
   FROM occurrences o
   JOIN taxa_taxon_lists ttl on ttl.id=taxa_taxon_list_id
   JOIN taxa t on t.id=ttl.taxon_id
   JOIN users c ON c.id = o.created_by_id
   JOIN users u ON u.id = o.updated_by_id
   JOIN people p on p.id=o.determiner_id
   JOIN samples s ON s.id = o.sample_id
   LEFT JOIN locations l ON l.id = s.location_id;

-- View: list_occurrences

-- DROP VIEW list_occurrences;

CREATE OR REPLACE VIEW list_occurrences AS 
 SELECT o.id, o.confidential, t.taxon,
	s.entered_sref, s.entered_sref_system, s.date_start, s.date_end, s.date_type 
   FROM occurrences o
   JOIN taxa_taxon_lists ttl on ttl.id=taxa_taxon_list_id
   JOIN taxa t on t.id=ttl.taxon_id
   JOIN samples s ON s.id = o.sample_id;
   
