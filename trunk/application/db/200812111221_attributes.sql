-- Add validation rules to attributes

ALTER TABLE occurrence_attributes ADD COLUMN validation_rules character varying;
ALTER TABLE occurrence_attributes ALTER COLUMN validation_rules SET STORAGE EXTENDED;
COMMENT ON COLUMN occurrence_attributes.validation_rules IS 'Validation rules defined for this attribute, for example: number, required,max[50].';

ALTER TABLE sample_attributes ADD COLUMN validation_rules character varying;
ALTER TABLE sample_attributes ALTER COLUMN validation_rules SET STORAGE EXTENDED;
COMMENT ON COLUMN sample_attributes.validation_rules IS 'Validation rules defined for this attribute, for example: number, required,max[50].';

ALTER TABLE location_attributes ADD COLUMN validation_rules character varying;
ALTER TABLE location_attributes ALTER COLUMN validation_rules SET STORAGE EXTENDED;
COMMENT ON COLUMN location_attributes.validation_rules IS 'Validation rules defined for this attribute, for example: number, required,max[50].';

-- Add termlist_id to attributes, in case they are a term from a termlist

ALTER TABLE occurrence_attributes ADD COLUMN termlist_id integer;
ALTER TABLE occurrence_attributes ALTER COLUMN termlist_id SET STORAGE PLAIN;
COMMENT ON COLUMN occurrence_attributes.termlist_id IS 'For attributes which define a term from a termlist, provides the ID of the termlist the term can be selected from.';

ALTER TABLE occurrence_attributes
  ADD CONSTRAINT fk_occurrence_attributes_termlists FOREIGN KEY (termlist_id)
      REFERENCES termlists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE sample_attributes ADD COLUMN termlist_id integer;
ALTER TABLE sample_attributes ALTER COLUMN termlist_id SET STORAGE PLAIN;
COMMENT ON COLUMN sample_attributes.termlist_id IS 'For attributes which define a term from a termlist, provides the ID of the termlist the term can be selected from.';

ALTER TABLE sample_attributes
  ADD CONSTRAINT fk_sample_attributes_termlists FOREIGN KEY (termlist_id)
      REFERENCES termlists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE location_attributes ADD COLUMN termlist_id integer;
ALTER TABLE location_attributes ALTER COLUMN termlist_id SET STORAGE PLAIN;
COMMENT ON COLUMN location_attributes.termlist_id IS 'For attributes which define a term from a termlist, provides the ID of the termlist the term can be selected from.';

ALTER TABLE location_attributes
  ADD CONSTRAINT fk_location_attributes_termlists FOREIGN KEY (termlist_id)
      REFERENCES termlists (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;

-- The links between attributes and the websites they are available for also need a survey_id, in case they are restricted to only being
-- available for one survey

ALTER TABLE occurrence_attributes_websites ADD COLUMN restrict_to_survey_id integer;
ALTER TABLE occurrence_attributes_websites ALTER COLUMN restrict_to_survey_id SET STORAGE PLAIN;
COMMENT ON COLUMN occurrence_attributes_websites.restrict_to_survey_id IS 'Foreign key to the survey table. For attributes that are only applicable to a given survey, identifies the survey.';

ALTER TABLE occurrence_attributes_websites
  ADD CONSTRAINT fk_occurrence_attributes_websites_survey FOREIGN KEY (restrict_to_survey_id)
      REFERENCES surveys (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;      

CREATE INDEX fki_occurrence_attributes_websites_survey
  ON occurrence_attributes_websites
  USING btree
  (restrict_to_survey_id);

ALTER TABLE sample_attributes_websites ADD COLUMN restrict_to_survey_id integer;
ALTER TABLE sample_attributes_websites ALTER COLUMN restrict_to_survey_id SET STORAGE PLAIN;
COMMENT ON COLUMN sample_attributes_websites.restrict_to_survey_id IS 'Foreign key to the survey table. For attributes that are only applicable to a given survey, identifies the survey.';

ALTER TABLE sample_attributes_websites
  ADD CONSTRAINT fk_sample_attributes_websites_survey FOREIGN KEY (restrict_to_survey_id)
      REFERENCES surveys (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;      

CREATE INDEX fki_sample_attributes_websites_survey
  ON sample_attributes_websites
  USING btree
  (restrict_to_survey_id);

ALTER TABLE location_attributes_websites ADD COLUMN restrict_to_survey_id integer;
ALTER TABLE location_attributes_websites ALTER COLUMN restrict_to_survey_id SET STORAGE PLAIN;
COMMENT ON COLUMN location_attributes_websites.restrict_to_survey_id IS 'Foreign key to the survey table. For attributes that are only applicable to a given survey, identifies the survey.';

ALTER TABLE location_attributes_websites
  ADD CONSTRAINT fk_location_attributes_websites_survey FOREIGN KEY (restrict_to_survey_id)
      REFERENCES surveys (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION;      

CREATE INDEX fki_locationattributes_websites_survey
  ON location_attributes_websites
  USING btree
  (restrict_to_survey_id);

