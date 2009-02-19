ALTER TABLE taxa
ADD image_path character varying(500),
ADD description character varying;

ALTER TABLE taxa_taxon_lists
ADD image_path character varying(500),
ADD description character varying;


DROP VIEW grid_occurrences_osgb_10k;
DROP VIEW grid_occurrences_osgb_100k;

DROP VIEW detail_taxa_taxon_lists;

DROP VIEW list_taxa_taxon_lists;


CREATE VIEW detail_taxa_taxon_lists AS
 SELECT ttl.id, ttl.taxon_id, t.taxon, t.authority, ttl.taxon_list_id, tl.title AS taxon_list, ttl.taxon_meaning_id, ttl.preferred, ttl.parent_id, tp.taxon AS parent,
	l.iso as language_iso, t.image_path as taxon_image_path, ttl.image_path, t.description as taxon_description, ttl.description,
	ttl.created_by_id, c.username AS created_by, ttl.updated_by_id, u.username AS updated_by
   FROM taxa_taxon_lists ttl
   JOIN taxon_lists tl ON tl.id = ttl.taxon_list_id
   JOIN taxa t ON t.id = ttl.taxon_id
   JOIN users c ON c.id = ttl.created_by_id
   JOIN users u ON u.id = ttl.updated_by_id
   JOIN languages l ON l.id=t.language_id
   LEFT JOIN taxa_taxon_lists ttlp ON ttlp.id = ttl.parent_id
   LEFT JOIN taxa tp ON tp.id = ttlp.taxon_id
  WHERE ttl.deleted = false;

CREATE VIEW list_taxa_taxon_lists AS
 SELECT ttl.id, ttl.taxon_id, t.taxon, t.authority, ttl.taxon_list_id, ttl.preferred, tl.title AS taxon_list,
	l.iso as language_iso, t.image_path as taxon_image_path, ttl.image_path
   FROM taxa_taxon_lists ttl
   JOIN taxon_lists tl ON tl.id = ttl.taxon_list_id
   JOIN taxa t ON t.id = ttl.taxon_id
   JOIN languages l ON l.id=t.language_id
  WHERE ttl.deleted = false;

CREATE VIEW grid_occurrences_osgb_10k AS
SELECT ttl.taxon, grid.square, grid.geom, o.id as occurrence_id, s.id as sample_id, ttl.id as taxa_taxon_list_id, ttl.taxon_list
FROM occurrences o
INNER JOIN samples s on s.id=o.sample_id
INNER JOIN grids_osgb_10k grid on ST_INTERSECTS(grid.geom,ST_TRANSFORM(s.geom, 27700))
INNER JOIN list_taxa_taxon_lists ttl on ttl.id=o.taxa_taxon_list_id;

CREATE VIEW grid_occurrences_osgb_100k AS
SELECT ttl.taxon, grid.square, grid.geom, o.id as occurrence_id, s.id as sample_id, ttl.id as taxa_taxon_list_id, ttl.taxon_list
FROM occurrences o
INNER JOIN samples s on s.id=o.sample_id
INNER JOIN grids_osgb_100k grid on ST_INTERSECTS(grid.geom,ST_TRANSFORM(s.geom, 27700))
INNER JOIN list_taxa_taxon_lists ttl on ttl.id=o.taxa_taxon_list_id;