ALTER TABLE cache_occurrences ADD COLUMN location_id integer;

UPDATE cache_occurrences co
SET location_id=s.location_id
FROM samples s
WHERE s.id=co.sample_id AND s.deleted=false AND s.location_id IS NOT NULL;