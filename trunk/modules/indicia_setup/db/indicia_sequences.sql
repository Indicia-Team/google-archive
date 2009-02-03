DROP SEQUENCE IF EXISTS i_schema.titles_id_seq;
DROP SEQUENCE IF EXISTS i_schema.user_tokens_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrence_comments_id_seq;
DROP SEQUENCE IF EXISTS i_schema.system_id_seq;
DROP SEQUENCE IF EXISTS i_schema.users_websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.users_id_seq;
DROP SEQUENCE IF EXISTS i_schema.taxon_meanings_id_seq;
DROP SEQUENCE IF EXISTS i_schema.taxon_groups_id_seq;
DROP SEQUENCE IF EXISTS i_schema.taxa_taxon_lists_id_seq;
DROP SEQUENCE IF EXISTS i_schema.surveys_id_seq;
DROP SEQUENCE IF EXISTS i_schema.site_roles_id_seq;
DROP SEQUENCE IF EXISTS i_schema.samples_id_seq;
DROP SEQUENCE IF EXISTS i_schema.sample_attributes_websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.sample_attributes_id_seq;
DROP SEQUENCE IF EXISTS i_schema.sample_attribute_values_id_seq;
DROP SEQUENCE IF EXISTS i_schema.roles_id_seq;
DROP SEQUENCE IF EXISTS i_schema.people_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrences_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrence_images_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrence_attributes_websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrence_attributes_id_seq;
DROP SEQUENCE IF EXISTS i_schema.occurrence_attribute_values_id_seq;
DROP SEQUENCE IF EXISTS i_schema.locations_websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.locations_id_seq;
DROP SEQUENCE IF EXISTS i_schema.location_attributes_websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.location_attributes_id_seq;
DROP SEQUENCE IF EXISTS i_schema.location_attribute_values_id_seq;
DROP SEQUENCE IF EXISTS i_schema.websites_id_seq;
DROP SEQUENCE IF EXISTS i_schema.terms_id_seq;
DROP SEQUENCE IF EXISTS i_schema.termlists_terms_id_seq;
DROP SEQUENCE IF EXISTS i_schema.termlists_id_seq;
DROP SEQUENCE IF EXISTS i_schema.taxon_lists_id_seq;
DROP SEQUENCE IF EXISTS i_schema.taxa_id_seq;
DROP SEQUENCE IF EXISTS i_schema.meanings_id_seq;
DROP SEQUENCE IF EXISTS i_schema.languages_id_seq;
SET check_function_bodies = false;
--
-- Definition for sequence languages_id_seq (OID = 117419) : 
--
CREATE SEQUENCE i_schema.languages_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence meanings_id_seq (OID = 117455) : 
--
CREATE SEQUENCE i_schema.meanings_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence taxa_id_seq (OID = 117526) : 
--
CREATE SEQUENCE i_schema.taxa_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence taxon_lists_id_seq (OID = 117539) : 
--
CREATE SEQUENCE i_schema.taxon_lists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence termlists_id_seq (OID = 117551) : 
--
CREATE SEQUENCE i_schema.termlists_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence termlists_terms_id_seq (OID = 117561) : 
--
CREATE SEQUENCE i_schema.termlists_terms_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence terms_id_seq (OID = 117567) : 
--
CREATE SEQUENCE i_schema.terms_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence websites_id_seq (OID = 117591) : 
--
CREATE SEQUENCE i_schema.websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence location_attribute_values_id_seq (OID = 118325) : 
--
CREATE SEQUENCE i_schema.location_attribute_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence location_attributes_id_seq (OID = 118327) : 
--
CREATE SEQUENCE i_schema.location_attributes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence location_attributes_websites_id_seq (OID = 118329) : 
--
CREATE SEQUENCE i_schema.location_attributes_websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence locations_id_seq (OID = 118331) : 
--
CREATE SEQUENCE i_schema.locations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence locations_websites_id_seq (OID = 118333) : 
--
CREATE SEQUENCE i_schema.locations_websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrence_attribute_values_id_seq (OID = 118335) : 
--
CREATE SEQUENCE i_schema.occurrence_attribute_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrence_attributes_id_seq (OID = 118337) : 
--
CREATE SEQUENCE i_schema.occurrence_attributes_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrence_attributes_websites_id_seq (OID = 118339) : 
--
CREATE SEQUENCE i_schema.occurrence_attributes_websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrence_images_id_seq (OID = 118341) : 
--
CREATE SEQUENCE i_schema.occurrence_images_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrences_id_seq (OID = 118343) : 
--
CREATE SEQUENCE i_schema.occurrences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence people_id_seq (OID = 118345) : 
--
CREATE SEQUENCE i_schema.people_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence roles_id_seq (OID = 118347) : 
--
CREATE SEQUENCE i_schema.roles_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence sample_attribute_values_id_seq (OID = 118349) : 
--
CREATE SEQUENCE i_schema.sample_attribute_values_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence sample_attributes_id_seq (OID = 118351) : 
--
CREATE SEQUENCE i_schema.sample_attributes_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence sample_attributes_websites_id_seq (OID = 118353) : 
--
CREATE SEQUENCE i_schema.sample_attributes_websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence samples_id_seq (OID = 118355) : 
--
CREATE SEQUENCE i_schema.samples_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence site_roles_id_seq (OID = 118357) : 
--
CREATE SEQUENCE i_schema.site_roles_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence surveys_id_seq (OID = 118359) : 
--
CREATE SEQUENCE i_schema.surveys_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence taxa_taxon_lists_id_seq (OID = 118361) : 
--
CREATE SEQUENCE i_schema.taxa_taxon_lists_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence taxon_groups_id_seq (OID = 118363) : 
--
CREATE SEQUENCE i_schema.taxon_groups_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence taxon_meanings_id_seq (OID = 118365) : 
--
CREATE SEQUENCE i_schema.taxon_meanings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence users_id_seq (OID = 118367) : 
--
CREATE SEQUENCE i_schema.users_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence users_websites_id_seq (OID = 118369) : 
--
CREATE SEQUENCE i_schema.users_websites_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence system_id_seq (OID = 119202) : 
--
CREATE SEQUENCE i_schema.system_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence occurrence_comments_id_seq (OID = 119213) : 
--
CREATE SEQUENCE i_schema.occurrence_comments_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence user_tokens_id_seq (OID = 119245) : 
--
CREATE SEQUENCE i_schema.user_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Definition for sequence titles_id_seq (OID = 119331) : 
--
CREATE SEQUENCE i_schema.titles_id_seq
    START WITH 10
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
--
-- Comments
--
COMMENT ON SCHEMA public IS 'standard public schema';
