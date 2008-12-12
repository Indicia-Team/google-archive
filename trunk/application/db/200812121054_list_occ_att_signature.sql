-- View: list_occurrence_attributes

DROP VIEW list_occurrence_attributes;

CREATE OR REPLACE VIEW list_occurrence_attributes AS 
 SELECT oa.id, oa.caption, oa.data_type, oa.termlist_id, oa.multi_value, oaw.website_id, (((oa.id || '|'::text) || oa.data_type::text) || '|'::text || COALESCE(oa.termlist_id::text, ''::text))  AS signature
   FROM occurrence_attributes oa
   LEFT JOIN occurrence_attributes_websites oaw ON oaw.occurrence_attribute_id = oa.id;

ALTER TABLE list_occurrence_attributes OWNER TO opal;
