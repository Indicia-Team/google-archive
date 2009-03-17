-- View: indicia.gv_samples

DROP VIEW gv_samples;

CREATE OR REPLACE VIEW gv_samples AS 
 SELECT s.id, s.date_start, s.date_end, s.date_type, s.entered_sref AS "Spatial Ref.", s.entered_sref_system, s.location_name AS "Location Name", s.deleted, su.title, l.name AS "Location"
   FROM indicia.samples s
   LEFT JOIN indicia.surveys su ON s.survey_id = su.id
   LEFT JOIN indicia.locations l ON s.location_id = l.id;

