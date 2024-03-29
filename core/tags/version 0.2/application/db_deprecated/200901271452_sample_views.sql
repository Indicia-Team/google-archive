DROP VIEW IF EXISTS detail_samples;

CREATE OR REPLACE VIEW detail_samples AS 
 SELECT s.id, s.entered_sref, s.entered_sref_system, s.geom, s.location_name, s.date_start, s.date_end, s.date_type, 
	s.location_id, l.name as location, l.code as location_code,	
	s.created_by_id, c.username AS created_by, s.created_on, s.updated_by_id, u.username AS updated_by, s.updated_on
   FROM samples s
   LEFT JOIN locations l ON l.id = s.location_id
   LEFT JOIN surveys su ON s.survey_id = su.id
   JOIN users c ON c.id = s.created_by_id
   JOIN users u ON u.id = s.updated_by_id; 

DROP VIEW IF EXISTS list_samples;

CREATE OR REPLACE VIEW list_samples AS
 SELECT s.id, su.title as "survey", l.name AS "location", 
 	s.date_start, s.date_end, s.date_type, s.entered_sref, s.entered_sref_system
   FROM samples s
   LEFT JOIN locations l ON s.location_id = l.id
   LEFT JOIN surveys su ON s.survey_id = su.id;
