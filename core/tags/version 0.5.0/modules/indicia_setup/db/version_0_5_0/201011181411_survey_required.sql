UPDATE samples SET survey_id=1 WHERE survey_id IS NULL;

ALTER TABLE indicia.samples
   ALTER COLUMN survey_id SET NOT NULL;