
/*

Before running, ensure warehouse scripts are up to date so taxon_groups table has a parent_id field. 

 # Ensure that the scheduled_tasks process has been run and the indexes are completely populated.
 # Take any dependent sites offline. 
 # Run the 2 exports stored in the UKSI database to output text files of the required data.
 # Open the files in notepad and save as UTF8.
 # Open the files in Notepad++ and convert to UTF8 without BOM
 # Check the taxon list ID is correct in this script - it is the parameter to the function call at the end.
 # Makes sure there is a schema called UKSI ready in the database
 # Check that the COPY statements have the correct path to the script files set, currently c:\tmp
 # Search for queries marked TEST and run the subqueries, to check nothing stupid is deleted.
 # Run this script. Takes about 1.5 hrs
 # Recommend manually running scheduled_tasks with no timeout set. Takes about 3 minutes.
   Or delete the variables entries for cache_taxa_taxon_lists and cache_taxon_searchterms then run multiple times

*/

SET search_path=uksi, public;

-- Table: uksi.preferred_names

-- DROP TABLE preferred_names;

CREATE TABLE preferred_names
(
  organism_key character(16),
  taxon_version_key character(16),
  item_name character varying,
  authority character varying,
  parent_tvk character(16),
  parent_key_web integer,
  parent_key character(16),
  taxon_rank_key character(16),
  sequence integer, -- Taxon rank sequence
  long_name character varying, -- Taxon rank name
  short_name character varying(10),
  marine_flag boolean,
  terrestrial_freshwater_flag boolean
)
WITH (
  OIDS=FALSE
);

-- Table: all_names

-- DROP TABLE all_names;

CREATE TABLE all_names
(
  recommended_taxon_version_key character(16),
  input_taxon_version_key character(16),
  item_name character varying,
  authority character varying,
  taxon_version_status character(1),
  taxon_type character(1),
  "language" character(2),
  output_group_key character(16),
  rank character varying,
  attribute character varying(100)
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.taxon_groups

-- DROP TABLE uksi.taxon_groups;

CREATE TABLE uksi.taxon_groups
(
  taxon_group_key character(16),
  taxon_group_name character varying,
  description character varying,
  parent character(16)
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.tcn_duplicates

-- DROP TABLE uksi.tcn_duplicates;

CREATE TABLE uksi.tcn_duplicates
(
  organism_key character(16),
  taxon_version_key character(16)
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.all_designation_kinds

-- DROP TABLE uksi.all_designation_kinds;

CREATE TABLE uksi.all_designation_kinds
(
  taxon_designation_type_kind_key character(16),
  kind character varying
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.taxon_designations

-- DROP TABLE uksi.taxon_designations;

CREATE TABLE uksi.taxon_designations
(
  taxon_designation_type_key character(16),
  short_name character varying,
  long_name character varying, 
  description character varying,
  kind character varying,
  status_abbreviation character varying
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.taxa_taxon_designations

-- DROP TABLE uksi.taxa_taxon_designations;

CREATE TABLE uksi.taxa_taxon_designations
(
  short_name character varying,
  date_from date,
  date_to date,  
  status_geographic_area character varying,
  detail character varying,
  recommended_taxon_version_key character(16)
)
WITH (
  OIDS=FALSE
);

-- Table: uksi.taxon_ranks

-- DROP TABLE uksi.taxon_ranks;

CREATE TABLE uksi.taxon_ranks
(
  sort_order integer,
  short_name character varying,
  long_name character varying,
  list_font_italic integer -- capture 0 or 1 and convert to bool later
)
WITH (
  OIDS=FALSE
);

TRUNCATE uksi.preferred_names;
COPY preferred_names FROM 'C:\tmp\preferred_names.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.all_names;
COPY all_names FROM 'C:\tmp\all_names.txt' DELIMITERS ',' QUOTE '"' CSV;

UPDATE uksi.all_names SET language=lower(language);

TRUNCATE uksi.taxon_groups;
COPY taxon_groups FROM 'C:\tmp\taxon_groups.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.tcn_duplicates;
COPY tcn_duplicates FROM 'C:\tmp\tcn_duplicates.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.all_designation_kinds;
COPY all_designation_kinds FROM 'C:\tmp\all_designation_kinds.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.taxon_designations;
COPY taxon_designations FROM 'C:\tmp\taxon_designations.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.taxa_taxon_designations;
COPY taxa_taxon_designations FROM 'C:\tmp\taxa_taxon_designations.txt' DELIMITERS ',' QUOTE '"' CSV;

TRUNCATE uksi.taxon_ranks;
COPY taxon_ranks FROM 'C:\tmp\taxon_ranks.txt' DELIMITERS ',' QUOTE '"' CSV;

CREATE INDEX ix_all_names_recommended_tvk ON uksi.all_names(recommended_taxon_version_key);
CREATE INDEX ix_all_names_input_tvk ON uksi.all_names(input_taxon_version_key);

SET search_path=indicia, public;



/* How to insert taxon_meaning_ids simultaneously into the taxon_meanings table?  Not just a sequence...  */
CREATE OR REPLACE function f_update_uksi (taxonListId INTEGER) RETURNS boolean
    AS $func$
DECLARE
	meaningcount INTEGER;
	i INTEGER;
	startmeaning INTEGER;
	lastcacheupdate timestamp without time zone;
	tdKindListId INTEGER;
	curs CURSOR (kindListId INTEGER) FOR SELECT dk.kind
		FROM uksi.all_designation_kinds dk
		LEFT JOIN cache_termlists_terms ctt 
			ON ctt.term=dk.kind
			AND ctt.termlist_id=kindListId
		WHERE ctt.id IS NULL; 
					
BEGIN 

-- Ensure existing rank info is correct
UPDATE taxon_ranks tr
SET short_name=COALESCE(utr.short_name, utr.long_name), sort_order=utr.sort_order
FROM uksi.taxon_ranks utr
WHERE tr.rank=utr.long_name;

-- Insert any missing ranks
INSERT INTO taxon_ranks(rank, short_name, italicise_taxon, sort_order, created_on, created_by_id, updated_on, updated_by_id)
SELECT utr.long_name, COALESCE(utr.short_name, utr.long_name), case utr.list_font_italic when 1 then true else false end, utr.sort_order, now(), 1, now(), 1
FROM uksi.taxon_ranks utr
LEFT JOIN taxon_ranks tr on tr.rank=utr.long_name
WHERE tr.id IS NULL;

-- Remove any taxa_taxon_lists where the item's TVK is not in the preferred names list and the item is not in use anywhere 
CREATE TEMPORARY TABLE to_process (
  id integer
);

INSERT INTO to_process
SELECT ttl.id
FROM taxa_taxon_lists ttl
	JOIN taxa t ON t.id=ttl.taxon_id
	LEFT JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
	LEFT JOIN occurrences o on o.taxa_taxon_list_id=ttl.id
	LEFT JOIN determinations d ON d.taxa_taxon_list_id=ttl.id
	WHERE ttl.taxon_list_id=taxonListId
	AND an.recommended_taxon_version_key IS NULL
	AND o.id IS NULL
	AND d.id IS NULL;

UPDATE taxa_taxon_lists ttl
SET deleted=true, updated_on=now() 
FROM to_process d 
WHERE d.id=ttl.id
AND deleted=false;

TRUNCATE to_process;

-- Mark any remaining taxa_taxon_lists as not for data entry where the item's TVK is not in the preferred names list but the item is in use 
INSERT INTO to_process
SELECT ttl.id 
	FROM taxa_taxon_lists ttl
	JOIN taxa t ON t.id=ttl.taxon_id
	LEFT JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
	WHERE ttl.taxon_list_id=taxonListId
	AND an.recommended_taxon_version_key IS NULL;

UPDATE taxa_taxon_lists ttl
SET allow_data_entry=false, updated_on=now() 
FROM to_process d 
WHERE d.id=ttl.id;

DROP TABLE to_process;

-- Cleanup orphaned taxa 
UPDATE taxa t
SET deleted=true, updated_on=now()
WHERE id IN (
	SELECT t.id
	FROM taxa t
	LEFT JOIN taxa_taxon_lists ttl ON ttl.taxon_id=t.id AND ttl.deleted=false
	WHERE ttl.id IS NULL
) AND deleted=false;

-- Grab all the missing taxon group names
INSERT INTO taxon_groups (title, created_on, created_by_id, updated_on, updated_by_id, external_key)
SELECT taxon_group_name, now(), 1, now(), 1, taxon_group_key
FROM uksi.taxon_groups tgimp
LEFT JOIN taxon_groups tg ON tg.external_key=tgimp.taxon_group_key AND tg.deleted=false
WHERE tg.id IS NULL;

-- Now they exist, we can update all parents. Also ensure the titles and descriptions are up to date 
-- (as we used the external key to match earlier, the title might not be yet 
UPDATE taxon_groups tg
SET parent_id = tgp.id, title=tgimpc.taxon_group_name, description=tgimpc.description, updated_on=now()
FROM uksi.taxon_groups tgimpc
LEFT JOIN (uksi.taxon_groups tgimpp
  JOIN taxon_groups tgp ON tgp.external_key=tgimpp.taxon_group_key AND tgp.deleted=false
) ON tgimpp.taxon_group_key=tgimpc.parent
WHERE tgimpc.taxon_group_key=tg.external_key
AND (tg.parent_id <> tgp.id OR tg.title <> tgimpc.taxon_group_name OR tg.description <> tgimpc.description)
AND tg.deleted=false;

-- Ensure info is correct for existing taxa
UPDATE taxa t
SET updated_on=now(), taxon_group_id=tg.id, language_id=l.id, external_key=an.recommended_taxon_version_key, search_code=an.input_taxon_version_key,
	authority=an.authority, scientific=(an.taxon_type='S'), taxon_rank_id=tr.id, attribute=an.attribute
FROM uksi.all_names an
JOIN languages l on left(l.iso, 2)=an.language AND l.deleted=false
JOIN taxon_groups tg ON tg.external_key=an.output_group_key AND tg.deleted=false
JOIN taxon_ranks tr ON tr.rank=an.rank AND tr.deleted=false
WHERE an.input_taxon_version_key=t.search_code
AND t.taxon_group_id<>tg.id OR t.language_id<>l.id OR t.external_key<>an.recommended_taxon_version_key OR t.search_code<>an.input_taxon_version_key 
	OR t.authority<>an.authority OR t.scientific<>(an.taxon_type='S') OR t.taxon_rank_id<>tr.id OR t.attribute<>an.attribute;

-- Insert any missing taxa records. 
INSERT INTO taxa (taxon, taxon_group_id, language_id, external_key, search_code, authority, scientific, taxon_rank_id, attribute, created_on, created_by_id, updated_on, updated_by_id)
-- test
SELECT an.item_name, tg.id, l.id, an.recommended_taxon_version_key, an.input_taxon_version_key, an.authority, an.taxon_type='S', tr.id, an.attribute, now(), 1, now(), 1
FROM uksi.all_names an
JOIN languages l on left(l.iso, 2)=an.language AND l.deleted=false
JOIN taxon_groups tg ON tg.external_key=an.output_group_key AND tg.deleted=false
JOIN taxon_ranks tr ON tr.rank=an.rank AND tr.deleted=false
LEFT JOIN taxa t ON t.search_code=an.input_taxon_version_key
WHERE t.id is null;

-- Ensure any existing names are preferred that should be 
UPDATE taxa_taxon_lists SET preferred=true, updated_on=now()
WHERE id IN (
	SELECT ttl.id
	FROM taxa_taxon_lists ttl
	JOIN taxa t ON t.id=ttl.taxon_id
	JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
	WHERE ttl.taxon_list_id=taxonListId
	AND ttl.preferred=false
) AND preferred=false;

startmeaning := nextval('indicia.taxon_meanings_id_seq'::regclass);

-- Find the number of meanings we need to insert
FOR i IN SELECT DISTINCT taxonListId, t.id, now(), 1, now(), 1, true 
	FROM taxa t
	JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
	    AND an.recommended_taxon_version_key=an.input_taxon_version_key
	LEFT JOIN taxa_taxon_lists ttl ON ttl.taxon_list_id=taxonListId AND ttl.taxon_id=t.id AND ttl.preferred=true
	WHERE ttl.id IS NULL
LOOP

	INSERT INTO taxon_meanings(id) VALUES (nextval('indicia.taxon_meanings_id_seq'::regclass));

END LOOP;

-- Insert the preferred names first, we can then attach other names to the meaning_ids created later.
-- We create a custom sequence in order to give us a unique ID per row. 
CREATE SEQUENCE indicia.temp_meaning_seq
  INCREMENT 1
  MINVALUE 1
  MAXVALUE 9223372036854775807
  START 1
  CACHE 1;

PERFORM pg_catalog.setval('indicia.temp_meaning_seq'::regclass, startmeaning);

-- The function set up the sequence for us, so we can auto-insert ttls and use the existing spare meanings generated by the function. 
INSERT INTO taxa_taxon_lists (taxon_list_id, taxon_id, taxon_meaning_id, created_on, created_by_id, updated_on, updated_by_id, preferred)
	SELECT DISTINCT taxonListId, t.id, nextval('temp_meaning_seq'::regclass), now(), 1, now(), 1, true 
	FROM taxa t
	JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
	    AND an.recommended_taxon_version_key=an.input_taxon_version_key
	LEFT JOIN taxa_taxon_lists ttl ON ttl.taxon_list_id=taxonListId AND ttl.taxon_id=t.id AND ttl.preferred=true
	WHERE ttl.id IS NULL;

DROP SEQUENCE indicia.temp_meaning_seq;

-- Insert the non-preferred names, linking them to the existing meaning IDs for the preferred names
INSERT INTO taxa_taxon_lists (taxon_list_id, taxon_id, taxon_meaning_id, created_on, created_by_id, updated_on, updated_by_id, preferred)
SELECT DISTINCT taxonListId, t.id, ttlpref.taxon_meaning_id, now(), 1, now(), 1, false 
FROM taxa t
JOIN uksi.all_names an ON an.input_taxon_version_key=t.search_code
    AND an.recommended_taxon_version_key <> an.input_taxon_version_key
JOIN taxa tpref ON tpref.external_key=t.external_key AND tpref.deleted=false
JOIN taxa_taxon_lists ttlpref ON ttlpref.taxon_id=tpref.id AND ttlpref.deleted=false 
    AND ttlpref.preferred=true AND ttlpref.taxon_list_id=taxonListId
LEFT JOIN taxa_taxon_lists ttl ON ttl.taxon_list_id=taxonListId AND ttl.taxon_id=t.id AND ttl.preferred=false
WHERE ttl.id IS NULL;

-- Set the parent ID for all taxa 
update taxa_taxon_lists cttl
set parent_id=pttl.id, updated_on=now()
from taxa ct -- child taxon
join uksi.preferred_names cpn on cpn.taxon_version_key=ct.external_key -- child preferred names
join taxa pt on pt.external_key=cpn.parent_tvk and pt.deleted=false -- parent taxa
join taxa_taxon_lists pttl on pttl.taxon_id=pt.id and pttl.deleted=false and pttl.taxon_list_id=taxonListId and pttl.preferred=true
where ct.id=cttl.taxon_id and ct.deleted=false and cttl.deleted=false
and cttl.taxon_list_id=taxonListId
and (cttl.parent_id<>pttl.id or cttl.parent_id is null);

-- Set the common taxon ID for all names where a common name is available
update taxa_taxon_lists ttl
set common_taxon_id=tc.id, updated_on=now()
from taxa t -- gives us the non-preferred external_key
join uksi.all_names an on an.input_taxon_version_key=t.external_key
join uksi.preferred_names pn on pn.taxon_version_key=an.recommended_taxon_version_key
join uksi.tcn_duplicates td on td.organism_key=pn.organism_key
join uksi.all_names anc on anc.input_taxon_version_key=td.taxon_version_key -- common name entry
join taxa tc on tc.taxon=anc.item_name -- lookup the common name's taxon id
join taxa_taxon_lists ttlc on ttlc.taxon_id=tc.id and ttlc.deleted=false 
where t.id=ttl.taxon_id and t.deleted=false and ttl.deleted=false
and ttl.taxon_list_id=taxonListId
and ttlc.taxon_meaning_id=ttl.taxon_meaning_id -- ensure the chosen name is of the same meaning
and (ttl.common_taxon_id<>tc.id or ttl.common_taxon_id is null);

-- Cleanup unused taxon groups
UPDATE taxon_groups
SET deleted=true, updated_on=now()
WHERE id in (
  -- TEST
  SELECT DISTINCT tg.id
  FROM taxon_groups tg
  LEFT JOIN taxon_groups tgc ON tgc.parent_id=tg.id AND tgc.deleted=false -- child groups
  -- Left join to taxa for this group OR this group's children 
  LEFT JOIN taxa t on t.taxon_group_id=tg.id AND t.deleted=false  
  LEFT JOIN taxa tc on tc.taxon_group_id=tgc.id AND tc.deleted=false -- child group's taxa
  WHERE t.id IS NULL AND tc.id IS NULL 
  AND tg.external_key IS NOT NULL AND tg.deleted=false
) AND deleted=false;


-- Do a cache update

lastcacheupdate := last_scheduled_task_check FROM system WHERE name='cache_builder';

-- First, do cache_taxa_taxon_lists
create temporary table needs_update_taxa_taxon_lists as select sub.id, cast(max(cast(deleted as int)) as boolean) as deleted 
      from (
      select ttl.id, ttl.deleted or tl.deleted or t.deleted or l.deleted as deleted
      from taxa_taxon_lists ttl
      join taxon_lists tl on tl.id=ttl.taxon_list_id
      join taxa t on t.id=ttl.taxon_id
      join languages l on l.id=t.language_id
      left join taxa tc on tc.id=ttl.common_taxon_id
      where ttl.updated_on>lastcacheupdate or tl.updated_on>lastcacheupdate or t.updated_on>lastcacheupdate or l.updated_on>lastcacheupdate
        or tc.updated_on>lastcacheupdate 
      union
      select ttl.id, ttl.deleted or ttlpref.deleted or tpref.deleted or lpref.deleted or tg.deleted
      from taxa_taxon_lists ttl
      join taxa_taxon_lists ttlpref on ttlpref.taxon_meaning_id=ttl.taxon_meaning_id and ttlpref.preferred=true
      join taxa tpref on tpref.id=ttlpref.taxon_id
      join languages lpref on lpref.id=tpref.language_id
      join taxon_groups tg on tg.id=tpref.taxon_group_id
      where ttlpref.updated_on>lastcacheupdate or tpref.updated_on>lastcacheupdate or lpref.updated_on>lastcacheupdate or tg.updated_on>lastcacheupdate      
      ) as sub
      group by id;

update cache_taxa_taxon_lists cttl
    set preferred=ttl.preferred,
      taxon_list_id=tl.id, 
      taxon_list_title=tl.title,
      website_id=tl.website_id,
      preferred_taxa_taxon_list_id=ttlpref.id,
      parent_id=ttlpref.parent_id,
      taxonomic_sort_order=ttlpref.taxonomic_sort_order,
      taxon=t.taxon || coalesce(' ' || t.attribute, ''),
      authority=t.authority,
      language_iso=l.iso,
      language=l.language,
      preferred_taxon=tpref.taxon || coalesce(' ' || tpref.attribute, ''),
      preferred_authority=tpref.authority,
      preferred_language_iso=lpref.iso,
      preferred_language=lpref.language,
      default_common_name=tcommon.taxon,
      search_name=regexp_replace(regexp_replace(regexp_replace(lower(t.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g'), 
      external_key=tpref.external_key,
      taxon_meaning_id=ttlpref.taxon_meaning_id,
      taxon_group_id = tpref.taxon_group_id,
      taxon_group = tg.title,
      cache_updated_on=now(),
      allow_data_entry=ttlpref.allow_data_entry
    from taxon_lists tl
    join taxa_taxon_lists ttl on ttl.taxon_list_id=tl.id 
    join needs_update_taxa_taxon_lists nu on nu.id=ttl.id
    join taxa_taxon_lists ttlpref on ttlpref.taxon_meaning_id=ttl.taxon_meaning_id and ttlpref.preferred='t' 
    join taxa t on t.id=ttl.taxon_id 
    join languages l on l.id=t.language_id 
    join taxa tpref on tpref.id=ttlpref.taxon_id 
    join taxon_groups tg on tg.id=tpref.taxon_group_id
    join languages lpref on lpref.id=tpref.language_id
    left join taxa tcommon on tcommon.id=ttlpref.common_taxon_id
    where cttl.id=ttl.id;

insert into cache_taxa_taxon_lists (
      id, preferred, taxon_list_id, taxon_list_title, website_id,
      preferred_taxa_taxon_list_id, parent_id, taxonomic_sort_order,
      taxon, authority, language_iso, language, preferred_taxon, preferred_authority, 
      preferred_language_iso, preferred_language, default_common_name, search_name, external_key, 
      taxon_meaning_id, taxon_group_id, taxon_group,
      cache_created_on, cache_updated_on, allow_data_entry
    )
    select distinct on (ttl.id) ttl.id, ttl.preferred, 
      tl.id as taxon_list_id, tl.title as taxon_list_title, tl.website_id,
      ttlpref.id as preferred_taxa_taxon_list_id, ttlpref.parent_id, ttlpref.taxonomic_sort_order,
      t.taxon || coalesce(' ' || t.attribute, ''), t.authority,
      l.iso as language_iso, l.language,
      tpref.taxon as preferred_taxon, tpref.authority as preferred_authority, 
      lpref.iso as preferred_language_iso, lpref.language as preferred_language,
      tcommon.taxon as default_common_name,
      regexp_replace(regexp_replace(regexp_replace(lower(t.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g'), 
      tpref.external_key, ttlpref.taxon_meaning_id, tpref.taxon_group_id, tg.title,
      now(), now(), ttlpref.allow_data_entry
    from taxon_lists tl
    join taxa_taxon_lists ttl on ttl.taxon_list_id=tl.id 
    left join cache_taxa_taxon_lists cttl on cttl.id=ttl.id
    join taxa_taxon_lists ttlpref on ttlpref.taxon_meaning_id=ttl.taxon_meaning_id and ttlpref.preferred='t' 
    join taxa t on t.id=ttl.taxon_id and t.deleted=false
    join languages l on l.id=t.language_id and l.deleted=false
    join taxa tpref on tpref.id=ttlpref.taxon_id 
    join taxon_groups tg on tg.id=tpref.taxon_group_id
    join languages lpref on lpref.id=tpref.language_id
    left join taxa tcommon on tcommon.id=ttlpref.common_taxon_id
    join needs_update_taxa_taxon_lists nu on nu.id=ttl.id
    where cttl.id is null;

-- Then, do cache_taxon_searchterms

create temporary table needs_update_taxon_searchterms as select sub.id, sub.allow_data_entry, cast(max(cast(deleted as int)) as boolean) as deleted       
      from (
      select ttl.id, ttl.allow_data_entry, ttl.deleted or tl.deleted or t.deleted or l.deleted as deleted
      from taxa_taxon_lists ttl
      join taxon_lists tl on tl.id=ttl.taxon_list_id
      join taxa t on t.id=ttl.taxon_id
      join languages l on l.id=t.language_id
      left join taxa tc on tc.id=ttl.common_taxon_id
      where ttl.updated_on>lastcacheupdate or tl.updated_on>lastcacheupdate or t.updated_on>lastcacheupdate or l.updated_on>lastcacheupdate 
        or tc.updated_on>lastcacheupdate 
      union
      select ttl.id, ttl.allow_data_entry, ttl.deleted or ttlpref.deleted or tpref.deleted or lpref.deleted or tg.deleted
      from taxa_taxon_lists ttl
      join taxa_taxon_lists ttlpref on ttlpref.taxon_meaning_id=ttl.taxon_meaning_id and ttlpref.preferred=true
      join taxa tpref on tpref.id=ttlpref.taxon_id
      join languages lpref on lpref.id=tpref.language_id
      join taxon_groups tg on tg.id=tpref.taxon_group_id
      where ttlpref.updated_on>lastcacheupdate or tpref.updated_on>lastcacheupdate or lpref.updated_on>lastcacheupdate or tg.updated_on>lastcacheupdate      
      ) as sub
      group by sub.id, sub.allow_data_entry;

delete from cache_taxon_searchterms where taxa_taxon_list_id in (select id from needs_update_taxon_searchterms where deleted=true or allow_data_entry=false);

delete from cache_taxon_searchterms where name_type='C' and source_id in (
    select tc.id from taxon_codes tc 
    join taxa_taxon_lists ttl on ttl.taxon_meaning_id=tc.taxon_meaning_id
    join needs_update_taxon_searchterms nu on nu.id = ttl.id
    where tc.deleted=true);

update cache_taxon_searchterms cts
    set taxa_taxon_list_id=cttl.id,
      taxon_list_id=cttl.taxon_list_id,
      searchterm=cttl.taxon,
      original=cttl.taxon,
      taxon_group_id=cttl.taxon_group_id,
      taxon_group=cttl.taxon_group,
      taxon_meaning_id=cttl.taxon_meaning_id,
      preferred_taxon=cttl.preferred_taxon,
      default_common_name=cttl.default_common_name,
      preferred_authority=cttl.preferred_authority,
      language_iso=cttl.language_iso,
      name_type=case
        when cttl.language_iso='lat' and cttl.preferred_taxa_taxon_list_id=cttl.id then 'L' 
        when cttl.language_iso='lat' and cttl.preferred_taxa_taxon_list_id<>cttl.id then 'S' 
        else 'V'
      end,
      simplified=false, 
      code_type_id=null,
      source_id=null,
      preferred=cttl.preferred,
      searchterm_length=length(cttl.taxon),
      parent_id=cttl.parent_id,
      preferred_taxa_taxon_list_id=cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id=cttl.id and cts.name_type in ('L','S','V') and cts.simplified=false;

update cache_taxon_searchterms cts
    set taxa_taxon_list_id=cttl.id,
      taxon_list_id=cttl.taxon_list_id,
      searchterm=taxon_abbreviation(cttl.taxon),
      original=cttl.taxon,
      taxon_group_id=cttl.taxon_group_id,
      taxon_group=cttl.taxon_group,
      taxon_meaning_id=cttl.taxon_meaning_id,
      preferred_taxon=cttl.preferred_taxon,
      default_common_name=cttl.default_common_name,
      preferred_authority=cttl.preferred_authority,
      language_iso=cttl.language_iso,
      name_type='A',
      simplified=null, 
      code_type_id=null,
      source_id=null,
      preferred=cttl.preferred,
      searchterm_length=length(taxon_abbreviation(cttl.taxon)),
      parent_id=cttl.parent_id,
      preferred_taxa_taxon_list_id=cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id=cttl.id and cts.name_type='A' and cttl.language_iso='lat';

update cache_taxon_searchterms cts
    set taxa_taxon_list_id=cttl.id,
      taxon_list_id=cttl.taxon_list_id,
      searchterm=regexp_replace(regexp_replace(regexp_replace(lower(cttl.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g'), 
      original=cttl.taxon,
      taxon_group_id=cttl.taxon_group_id,
      taxon_group=cttl.taxon_group,
      taxon_meaning_id=cttl.taxon_meaning_id,
      preferred_taxon=cttl.preferred_taxon,
      default_common_name=cttl.default_common_name,
      preferred_authority=cttl.preferred_authority,
      language_iso=cttl.language_iso,
      name_type=case
        when cttl.language_iso='lat' and cttl.preferred_taxa_taxon_list_id=cttl.id then 'L' 
        when cttl.language_iso='lat' and cttl.preferred_taxa_taxon_list_id<>cttl.id then 'S' 
        else 'V'
      end,
      simplified=true,
      code_type_id=null,
      source_id=null,
      preferred=cttl.preferred,
      searchterm_length=length(regexp_replace(regexp_replace(regexp_replace(lower(cttl.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g')),
      parent_id=cttl.parent_id,
      preferred_taxa_taxon_list_id=cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id=cttl.id and cts.name_type in ('L','S','V') and cts.simplified=true;

update cache_taxon_searchterms cts
  set taxa_taxon_list_id=cttl.id,
    taxon_list_id=cttl.taxon_list_id,
      searchterm=tc.code,
      original=tc.code,
      taxon_group_id=cttl.taxon_group_id,
      taxon_group=cttl.taxon_group,
      taxon_meaning_id=cttl.taxon_meaning_id,
      preferred_taxon=cttl.preferred_taxon,
      default_common_name=cttl.default_common_name,
      preferred_authority=cttl.preferred_authority,
      language_iso=null,
      name_type='C',
      simplified=null,
      code_type_id=tc.code_type_id,
      source_id=tc.id,
      preferred=cttl.preferred,
      searchterm_length=length(tc.code),
      parent_id=cttl.parent_id,
      preferred_taxa_taxon_list_id=cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    join taxon_codes tc on tc.taxon_meaning_id=cttl.taxon_meaning_id 
    join termlists_terms tlttype on tlttype.id=tc.code_type_id
    join termlists_terms tltcategory on tltcategory.id=tlttype.parent_id
    join terms tcategory on tcategory.id=tltcategory.term_id and tcategory.term='searchable'
    where cttl.id=cttl.preferred_taxa_taxon_list_id and cts.taxa_taxon_list_id=cttl.id and cts.name_type = 'C' and cts.source_id=tc.id;
      
insert into cache_taxon_searchterms (
      taxa_taxon_list_id, taxon_list_id, searchterm, original, taxon_group_id, taxon_group, taxon_meaning_id, preferred_taxon,
      default_common_name, preferred_authority, language_iso,
      name_type, simplified, code_type_id, preferred, searchterm_length, parent_id, preferred_taxa_taxon_list_id
    )
    select distinct on (cttl.id) cttl.id, cttl.taxon_list_id, cttl.taxon, cttl.taxon, cttl.taxon_group_id, cttl.taxon_group, cttl.taxon_meaning_id, cttl.preferred_taxon,
      cttl.default_common_name, cttl.authority, cttl.language_iso, 
      case
        when cttl.language_iso='lat' and cttl.id=cttl.preferred_taxa_taxon_list_id then 'L' 
        when cttl.language_iso='lat' and cttl.id<>cttl.preferred_taxa_taxon_list_id then 'S' 
        else 'V'
      end, false, null, cttl.preferred, length(cttl.taxon), cttl.parent_id, cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    left join cache_taxon_searchterms cts on cts.taxa_taxon_list_id=cttl.id and cts.name_type in ('L','S','V') and cts.simplified='f'
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id is null;

insert into cache_taxon_searchterms (
      taxa_taxon_list_id, taxon_list_id, searchterm, original, taxon_group_id, taxon_group, taxon_meaning_id, preferred_taxon,
      default_common_name, preferred_authority, language_iso,
      name_type, simplified, code_type_id, preferred, searchterm_length, parent_id, preferred_taxa_taxon_list_id
    )
    select distinct on (cttl.id) cttl.id, cttl.taxon_list_id, taxon_abbreviation(cttl.taxon), cttl.taxon, cttl.taxon_group_id, cttl.taxon_group, cttl.taxon_meaning_id, cttl.preferred_taxon,
      cttl.default_common_name, cttl.authority, cttl.language_iso, 
      'A', null, null, cttl.preferred, length(taxon_abbreviation(cttl.taxon)), cttl.parent_id, cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join taxa_taxon_lists ttlpref 
      on ttlpref.taxon_meaning_id=cttl.taxon_meaning_id 
      and ttlpref.preferred=true and 
      ttlpref.taxon_list_id=cttl.taxon_list_id
      and ttlpref.deleted=false
    left join cache_taxon_searchterms cts on cts.taxa_taxon_list_id=cttl.id and cts.name_type='A'
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id is null and cttl.language_iso='lat';

insert into cache_taxon_searchterms (
      taxa_taxon_list_id, taxon_list_id, searchterm, original, taxon_group_id, taxon_group, taxon_meaning_id, preferred_taxon,
      default_common_name, preferred_authority, language_iso,
      name_type, simplified, code_type_id, preferred, searchterm_length, parent_id, preferred_taxa_taxon_list_id
    )
    select distinct on (cttl.id) cttl.id, cttl.taxon_list_id, 
      regexp_replace(regexp_replace(regexp_replace(lower(cttl.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g'), 
      cttl.taxon, cttl.taxon_group_id, cttl.taxon_group, cttl.taxon_meaning_id, cttl.preferred_taxon,
      cttl.default_common_name, cttl.authority, cttl.language_iso, 
      case
        when cttl.language_iso='lat' and cttl.id=cttl.preferred_taxa_taxon_list_id then 'L' 
        when cttl.language_iso='lat' and cttl.id<>cttl.preferred_taxa_taxon_list_id then 'S' 
        else 'V'
      end, true, null, cttl.preferred, 
      length(regexp_replace(regexp_replace(regexp_replace(lower(cttl.taxon), E'\\\\(.+\\\\)', '', 'g'), 'ae', 'e', 'g'), E'[^a-z0-9\\\\?\\\\+]', '', 'g')),
      cttl.parent_id, cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    left join cache_taxon_searchterms cts on cts.taxa_taxon_list_id=cttl.id and cts.name_type in ('L','S','V') and cts.simplified=true
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id is null;

insert into cache_taxon_searchterms (
      taxa_taxon_list_id, taxon_list_id, searchterm, original, taxon_group_id, taxon_group, taxon_meaning_id, preferred_taxon,
      default_common_name, preferred_authority, language_iso,
      name_type, simplified, code_type_id, source_id, preferred, searchterm_length, parent_id, preferred_taxa_taxon_list_id
    )
    select distinct on (tc.id) cttl.id, cttl.taxon_list_id, tc.code, tc.code, cttl.taxon_group_id, cttl.taxon_group, cttl.taxon_meaning_id, cttl.preferred_taxon,
      cttl.default_common_name, cttl.authority, null, 'C', null, tc.code_type_id, tc.id, cttl.preferred, length(tc.code), 
      cttl.parent_id, cttl.preferred_taxa_taxon_list_id
    from cache_taxa_taxon_lists cttl
    join taxon_codes tc on tc.taxon_meaning_id=cttl.taxon_meaning_id and tc.deleted=false
    left join cache_taxon_searchterms cts on cts.taxa_taxon_list_id=cttl.id and cts.name_type='C' and cts.source_id=tc.id
    join termlists_terms tlttype on tlttype.id=tc.code_type_id and tlttype.deleted=false
    join termlists_terms tltcategory on tltcategory.id=tlttype.parent_id and tltcategory.deleted=false
    join terms tcategory on tcategory.id=tltcategory.term_id and tcategory.term='searchable' and tcategory.deleted=false
    join needs_update_taxon_searchterms nu on nu.id=cttl.id and nu.deleted=false
    where cts.taxa_taxon_list_id is null;

-- Insert the designation kinds that are missing
tdKindListId := id from termlists where external_key='indicia:taxon_designation_categories';

FOR kindrecord IN curs(tdKindListId) LOOP
  PERFORM insert_term(kindrecord.kind, 'eng', tdKindListId, null);
END LOOP;

-- Insert any designations that are missing

INSERT INTO taxon_designations (title, code, abbreviation, description, category_id, created_on, created_by_id, updated_on, updated_by_id)
SELECT
  td.long_name, 
  td.status_abbreviation,
  td.short_name,   
  td.description, 
  ctt.id,
  now(),
  1,
  now(),
  1
FROM uksi.taxon_designations td
JOIN cache_termlists_terms ctt ON ctt.term=td.kind
LEFT JOIN taxon_designations tdexist ON tdexist.abbreviation=td.short_name
WHERE tdexist.id IS NULL;

-- Update existing links with the latest source, constraints etc. Also remove links between designations and 
-- taxa that are no longer required, if the date_to has kicked in.
update taxa_taxon_designations ttd
set start_date=uttd.date_from,
geographical_constraint=uttd.status_geographic_area,
source=substring(uttd.detail from 'Source: (.+)'),
deleted=case when uttd.date_to is null then false else true end
from uksi.taxa_taxon_designations uttd
join taxon_designations td on td.abbreviation=uttd.short_name
join cache_taxa_taxon_lists cttl on cttl.external_key=uttd.recommended_taxon_version_key and cttl.preferred=true
join taxa_taxon_lists ttl on ttl.id=cttl.id
where ttd.taxon_id=ttl.taxon_id and ttd.taxon_designation_id=td.id;

-- Insert any missing links
insert into taxa_taxon_designations (
	taxon_id, taxon_designation_id, created_on, created_by_id, updated_on, updated_by_id, 
	start_date, source, geographical_constraint
)
select 
	ttl.taxon_id, td.id, now(), 1, now(), 1, 
	uttd.date_from, substring(uttd.detail from 'Source: (.+)'), uttd.status_geographic_area
from uksi.taxa_taxon_designations uttd
join taxon_designations td on td.abbreviation=uttd.short_name
join cache_taxa_taxon_lists cttl on cttl.external_key=uttd.recommended_taxon_version_key and cttl.preferred=true
join taxa_taxon_lists ttl on ttl.id=cttl.id
left join taxa_taxon_designations ttd on ttd.taxon_id=ttl.taxon_id and ttd.taxon_designation_id=td.id and ttd.deleted=false
where ttd.id is null;

UPDATE system SET last_scheduled_task_check=now() WHERE name='cache_builder';

DROP TABLE needs_update_taxa_taxon_lists;

DROP TABLE needs_update_taxon_searchterms;

-- Insert aggregates? 

RETURN true;
	
END
$func$ LANGUAGE plpgsql;

SELECT f_update_uksi(1);

DROP FUNCTION f_update_uksi(integer);

