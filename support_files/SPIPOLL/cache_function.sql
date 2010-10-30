
CREATE OR REPLACE FUNCTION spipoll_get_term(arg_term_id integer) RETURNS text AS $$
DECLARE
	--- set language to indicia id of the language required (1 is eng):
	languageId integer := 8;
	curterm refcursor;
	termValue text;
BEGIN
	OPEN curterm FOR SELECT t.term::text FROM termlists_terms tt JOIN terms t ON t.id = tt.term_id AND t.language_id = languageId WHERE tt.meaning_id = arg_term_id ;
	FETCH curterm INTO termValue;
	IF FOUND THEN
		return termValue;
	ELSE
		RAISE WARNING 'Unrecognised Term ID %', arg_term_id;
		return (arg_term_id)::text;
	END IF;
END;
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION build_spipoll_cache(arg_survey_id integer) RETURNS integer AS $$
DECLARE
	-- following should be updated to reflect values in Database
	protocol_attr_id	integer := 33;
	closed_attr_id		integer := 30;
	cms_userid_attr_id	integer := 18;
	cms_username_attr_id	integer := 19;
	flower_type_attr_id		integer := 16;
	habitat_attr_id		integer := 3;
	hive_attr_id		integer := 2;
	email_attr_id		integer := 8;
	start_time_attr_id		integer := 28;
	end_time_attr_id		integer := 29;
	sky_attr_id		integer := 34;
	shade_attr_id		integer := 35;
	temp_attr_id		integer := 36;
	wind_attr_id		integer := 37;
	insect_foraging_attr_id		integer := 14;
	insect_number_attr_id		integer := 15;
	---
	mySrefSystem		integer := 27572;
	---
	collectionrow	samples%ROWTYPE;
	count	integer;
	old_id	integer;
	rowsampleattributevalue sample_attribute_values%ROWTYPE;
	curlocation refcursor;
	rowlocation locations%ROWTYPE;
	rowlocationattributevalue	location_attribute_values%ROWTYPE;
	curlocationimage refcursor;
	rowlocationimage location_images%ROWTYPE;
	curflower refcursor;
	rowflower occurrences%ROWTYPE;
	curdetermination refcursor;
	rowdetermination determinations%ROWTYPE;
	curflowerimage refcursor;
	rowflowerimage occurrence_images%ROWTYPE;
	rowoccurrenceattributevalue	occurrence_attribute_values%ROWTYPE;
	rowsession		samples%ROWTYPE;
	rowinsect	occurrences%ROWTYPE;
	curinsectdetermination refcursor;
	rowinsectdetermination determinations%ROWTYPE;
	curinsectimage refcursor;
	rowinsectimage occurrence_images%ROWTYPE;
	status				integer;
BEGIN
	count := 0;
	old_id := -1;
	-- This will be handled as a single transaction, so can delete everything here then rebuild it.
	DELETE FROM spipoll_collections_cache ;
	DELETE FROM spipoll_insects_cache ;
	
	FOR collectionrow IN SELECT * FROM samples
	WHERE parent_id IS NULL AND deleted = false AND survey_id = arg_survey_id ORDER by id
	LOOP
	  DECLARE
	  	cacherow			spipoll_collections_cache%ROWTYPE;
	  	cacheinsecttemplate1	spipoll_insects_cache%ROWTYPE;
	  	cacheinsecttemplate2	spipoll_insects_cache%ROWTYPE;
	  	updated				date;
	  	sessionupdated		date;
	  	temp				text;
	  BEGIN
		status := 2; -- 0 is open, 1 is closed, 2 is don't know
		cacherow.collection_id := collectionrow.id;
		cacheinsecttemplate1.collection_id := collectionrow.id;
		cacherow.datedebut := collectionrow.date_start;
		cacheinsecttemplate1.datedebut := collectionrow.date_start;
		cacherow.datefin := collectionrow.date_end;
		cacheinsecttemplate1.datefin := collectionrow.date_end;
		updated := collectionrow.updated_on;
		OPEN curlocation FOR SELECT * FROM locations WHERE id = collectionrow.location_id AND deleted = false ORDER BY id DESC;
		FETCH curlocation INTO rowlocation;
		IF FOUND THEN
			cacherow.nom := rowlocation.name;
			cacheinsecttemplate1.nom := rowlocation.name;
			cacherow.geom := rowlocation.centroid_geom;
			cacheinsecttemplate1.geom := rowlocation.centroid_geom;
			IF rowlocation.updated_on > updated THEN
				updated := rowlocation.updated_on;
			END IF;
			SELECT ST_asText(ST_Transform(cacherow.geom,mySrefSystem)) INTO temp;
			temp := trim(trailing ')' from trim(leading 'POINT(' from temp));
			cacheinsecttemplate1.srefX = substring(temp for (position(' ' in temp) - 1));
			cacheinsecttemplate1.srefY = substring(temp from (position(' ' in temp) + 1));
			
			FOR rowlocationattributevalue IN SELECT * FROM location_attribute_values WHERE location_id = rowlocation.id AND deleted = false ORDER BY id desc
			LOOP
				CASE rowlocationattributevalue.location_attribute_id
					WHEN habitat_attr_id THEN --- multiple values separated by |
						IF cacheinsecttemplate1.habitat_ids IS NULL THEN
							cacheinsecttemplate1.habitat_ids := '|' || rowlocationattributevalue.int_value || '|';
							cacheinsecttemplate1.habitat := spipoll_get_term(rowlocationattributevalue.int_value);
						ELSE
							cacheinsecttemplate1.habitat_ids := cacheinsecttemplate1.habitat_ids || rowlocationattributevalue.int_value || '|';
							cacheinsecttemplate1.habitat := cacheinsecttemplate1.habitat || ',' || spipoll_get_term(rowlocationattributevalue.int_value);
						END IF;
					WHEN hive_attr_id THEN
						IF cacheinsecttemplate1.nearest_hive IS NULL THEN
							cacheinsecttemplate1.nearest_hive := rowlocationattributevalue.int_value;
						ELSE
							RAISE WARNING 'Multiple Hive Distance Attributes for Collection ID --> %, Location ID --> %, ignored Attr ID --> %', collectionrow.id, rowlocation.id, rowlocationattributevalue.id ;
						END IF;
					ELSE
						RAISE WARNING 'Unrecognised Location Attribute, Location ID --> %, LA ID --> % in LAV ID --> %: ignored', rowlocation.id, rowlocationattributevalue.location_attribute_id, rowlocationattributevalue.id ;
				END CASE;
				IF rowlocationattributevalue.updated_on > updated THEN
					updated := rowlocationattributevalue.updated_on;
				END IF;
			END LOOP;
			cacherow.habitat_ids := cacheinsecttemplate1.habitat_ids;
			--- Before we check the location image etc, we need to retrieve the collection attribute: proceed only if closed.
			FOR rowsampleattributevalue IN SELECT * FROM sample_attribute_values WHERE sample_id = collectionrow.id AND deleted = false ORDER BY id desc
			LOOP
				CASE rowsampleattributevalue.sample_attribute_id
					WHEN closed_attr_id THEN
						IF status = 2 THEN
							status := rowsampleattributevalue.int_value;
						ELSE
							RAISE WARNING 'Multiple Closed Attributes for Collection ID --> %, ignored Attr ID --> %', collectionrow.id, rowsampleattributevalue.id ;
						END IF;
					WHEN cms_username_attr_id THEN
						IF cacherow.username IS NULL THEN
							cacherow.username := rowsampleattributevalue.text_value;
							cacheinsecttemplate1.username :=rowsampleattributevalue.text_value;
						ELSE
							RAISE WARNING 'Multiple Username Attributes for Collection ID --> %, ignored Attr ID --> %', collectionrow.id, rowsampleattributevalue.id ;
						END IF;
					WHEN cms_userid_attr_id THEN
						IF cacheinsecttemplate1.userid IS NULL THEN
							cacheinsecttemplate1.userid := rowsampleattributevalue.int_value;
						ELSE
							RAISE WARNING 'Multiple UserID Attributes for Collection ID --> %, ignored Attr ID --> %', collectionrow.id, rowsampleattributevalue.id ;
						END IF;
					WHEN email_attr_id THEN
						IF cacheinsecttemplate1.email IS NULL THEN
							cacheinsecttemplate1.email := rowsampleattributevalue.text_value;
						ELSE
							RAISE WARNING 'Multiple Email Attributes for Collection ID --> %, ignored Attr ID --> %', collectionrow.id, rowsampleattributevalue.id ;
						END IF;
					WHEN protocol_attr_id THEN
						IF cacheinsecttemplate1.protocol IS NULL THEN
							cacheinsecttemplate1.protocol := spipoll_get_term(rowsampleattributevalue.int_value);
							IF position('(' in cacheinsecttemplate1.protocol) <> 0 THEN
								cacheinsecttemplate1.protocol := substring(cacheinsecttemplate1.protocol for (position('(' in cacheinsecttemplate1.protocol)-2));
							END IF;
						ELSE
							RAISE WARNING 'Multiple Protocol Attributes for Collection ID --> %, ignored Attr ID --> %', collectionrow.id, rowsampleattributevalue.id ;
						END IF;
					ELSE
						RAISE WARNING 'Unrecognised Collection Attribute, Collection ID --> %, SA ID --> % in SAV ID --> %: ignored', collectionrow.id, rowsampleattributevalue.sample_attribute_id, rowsampleattributevalue.id ;
				END CASE;
				IF rowsampleattributevalue.updated_on > updated THEN
					updated := rowsampleattributevalue.updated_on;
				END IF;
			END LOOP;
			CASE status
				WHEN 1 THEN
					OPEN curlocationimage FOR SELECT * FROM location_images WHERE location_id = rowlocation.id AND deleted = false ORDER BY id DESC;
					FETCH curlocationimage INTO rowlocationimage;
					IF FOUND THEN
						cacherow.image_de_environment = rowlocationimage.path;
						cacheinsecttemplate1.image_de_environment = rowlocationimage.path;
						IF rowlocationimage.updated_on > updated THEN
							updated := rowlocationimage.updated_on;
						END IF;
						FETCH curlocationimage INTO rowlocationimage;
						IF FOUND THEN
							RAISE WARNING 'Multiple Locations Images on Location ID --> %, only most recent location used, ignoring ID --> %', rowlocation.id, rowlocationimage.id ;
						END IF;
						OPEN curflower FOR SELECT * FROM occurrences WHERE sample_id = collectionrow.id AND deleted = false ORDER BY id DESC;
						FETCH curflower INTO rowflower;
						IF FOUND THEN
							IF rowflower.updated_on > updated THEN
								updated := rowflower.updated_on;
							END IF;
							cacherow.flower_id = rowflower.id;
							FOR rowoccurrenceattributevalue IN SELECT * FROM occurrence_attribute_values WHERE occurrence_id = rowflower.id AND deleted = false ORDER BY id desc
							LOOP
								CASE rowoccurrenceattributevalue.occurrence_attribute_id
									WHEN flower_type_attr_id THEN
										IF cacherow.flower_type_id IS NULL THEN
											cacherow.flower_type_id = rowoccurrenceattributevalue.int_value;
											cacheinsecttemplate1.flower_type_id = rowoccurrenceattributevalue.int_value;
											cacheinsecttemplate1.flower_type = spipoll_get_term(rowoccurrenceattributevalue.int_value);
										ELSE
											RAISE WARNING 'Multiple Flower Type Attributes for Flower ID --> %, ignored Attr ID --> %', rowflower.id, rowoccurrenceattributevalue.id ;
										END IF;
									ELSE
										RAISE WARNING 'Unrecognised Flower Attribute, Flower ID --> %, OA ID --> % in OAV ID --> %: ignored', rowflower.id, rowoccurrenceattributevalue.occurrence_attribute_id, rowoccurrenceattributevalue.id ;
								END CASE;
								IF rowoccurrenceattributevalue.updated_on > updated THEN
									updated := rowoccurrenceattributevalue.updated_on;
								END IF;
							END LOOP;
							OPEN curdetermination FOR SELECT * FROM determinations WHERE occurrence_id = rowflower.id AND deleted = false ORDER BY id desc;
							FETCH curdetermination INTO rowdetermination;
							IF FOUND THEN
								IF rowdetermination.updated_on > updated THEN
									updated := rowdetermination.updated_on;
								END IF;
								--- flower treated slightly differently to insect - there most always be only one flower and it must have an identification
								IF rowdetermination.taxa_taxon_list_id IS NOT NULL THEN
									cacherow.flower_taxon_ids := '|'||rowdetermination.taxa_taxon_list_id||'|';
								ELSE
									cacherow.flower_taxon_ids := ARRAY(select '|'||unnest(rowdetermination.taxa_taxon_list_id_list)::text||'|')::text;
								END IF;
								cacherow.taxons_fleur_precise := rowdetermination.taxon_extra_info;
								CASE rowdetermination.determination_type
									WHEN 'B' THEN -- Considered incorrect;
										cacheinsecttemplate1.status_fleur = 'Doute';
									WHEN 'C' THEN -- Correct;
										cacheinsecttemplate1.status_fleur = 'Valide';
									WHEN 'I' THEN -- Incorrect;
										cacheinsecttemplate1.status_fleur = 'Invalide';
									WHEN 'R' THEN -- Requires confirmation;
										cacheinsecttemplate1.status_fleur = 'Requires confirmation';
									WHEN 'U' THEN -- Unconfirmed;
										cacheinsecttemplate1.status_fleur = 'Unconfirmed';
									WHEN 'X' THEN -- Unidentified;
										cacheinsecttemplate1.status_fleur = 'Unidentified';
									ELSE --- defaults to 'A' Considered correct;
										cacheinsecttemplate1.status_fleur = '-';
								END CASE;
								OPEN curflowerimage FOR SELECT * FROM occurrence_images WHERE occurrence_id = rowflower.id AND deleted = false ORDER BY id DESC;
								FETCH curflowerimage INTO rowflowerimage;
								IF FOUND THEN
									IF rowflowerimage.updated_on > updated THEN
										updated := rowflowerimage.updated_on;
									END IF;
									cacherow.image_de_la_fleur = rowflowerimage.path;
									cacheinsecttemplate1.image_de_la_fleur = rowflowerimage.path;
									FETCH curflowerimage INTO rowflowerimage;
									IF FOUND THEN
										RAISE WARNING 'Multiple Flower Images on Flower ID --> %, only most recent image used, ignoring ID --> %', rowflower.id, rowflowerimage.id ;
									END IF;
									FOR rowsession IN SELECT * FROM samples WHERE parent_id = collectionrow.id AND deleted = false
									LOOP
										cacheinsecttemplate2 := cacheinsecttemplate1;
										cacheinsecttemplate2.date_de_session = rowsession.date_start;
										--- multiple sessions mean multiple values in collection.
										IF rowsession.updated_on > updated THEN
											sessionupdated := rowsession.updated_on;
										ELSE
											sessionupdated := updated;
										END IF;
										FOR rowsampleattributevalue IN SELECT * FROM sample_attribute_values WHERE sample_id = rowsession.id AND deleted = false ORDER BY id desc
										LOOP
											CASE rowsampleattributevalue.sample_attribute_id
												WHEN sky_attr_id THEN
													IF cacherow.sky_ids IS NULL THEN
														cacherow.sky_ids := '|' || rowsampleattributevalue.int_value || '|';
													ELSE
														cacherow.sky_ids := cacherow.sky_ids || rowsampleattributevalue.int_value || '|';
													END IF;
													IF cacheinsecttemplate2.sky_ids IS NULL THEN
														cacheinsecttemplate2.sky_ids := '|' || rowsampleattributevalue.int_value || '|';
														cacheinsecttemplate2.ciel := spipoll_get_term(rowsampleattributevalue.int_value);
													ELSE
														RAISE WARNING 'Multiple Sky Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												WHEN shade_attr_id THEN
													IF cacherow.shade_ids IS NULL THEN
														cacherow.shade_ids := '|' || rowsampleattributevalue.int_value || '|';
													ELSE
														cacherow.shade_ids := cacherow.shade_ids || rowsampleattributevalue.int_value || '|';
													END IF;
													IF cacheinsecttemplate2.shade_ids IS NULL THEN
														cacheinsecttemplate2.shade_ids := '|' || rowsampleattributevalue.int_value || '|';
														IF rowsampleattributevalue.int_value = 0 THEN
															cacheinsecttemplate2.fleur_a_lombre := 'Non';
														ELSE
															cacheinsecttemplate2.fleur_a_lombre := 'Oui';
														END IF;
													ELSE
														RAISE WARNING 'Multiple Shade Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												WHEN temp_attr_id THEN
													IF cacherow.temp_ids IS NULL THEN
														cacherow.temp_ids := '|' || rowsampleattributevalue.int_value || '|';
													ELSE
														cacherow.temp_ids := cacherow.temp_ids || rowsampleattributevalue.int_value || '|';
													END IF;
													IF cacheinsecttemplate2.temp_ids IS NULL THEN
														cacheinsecttemplate2.temp_ids := '|' || rowsampleattributevalue.int_value || '|';
														cacheinsecttemplate2.temperature := spipoll_get_term(rowsampleattributevalue.int_value);
													ELSE
														RAISE WARNING 'Multiple Temp Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												WHEN wind_attr_id THEN
													IF cacherow.wind_ids IS NULL THEN
														cacherow.wind_ids := '|' || rowsampleattributevalue.int_value || '|';
													ELSE
														cacherow.wind_ids := cacherow.wind_ids || rowsampleattributevalue.int_value || '|';
													END IF;
													IF cacheinsecttemplate2.wind_ids IS NULL THEN
														cacheinsecttemplate2.wind_ids := '|' || rowsampleattributevalue.int_value || '|';
														cacheinsecttemplate2.vent := spipoll_get_term(rowsampleattributevalue.int_value);
													ELSE
														RAISE WARNING 'Multiple Wind Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												WHEN start_time_attr_id THEN
													IF cacheinsecttemplate2.starttime IS NULL THEN
														cacheinsecttemplate2.starttime := rowsampleattributevalue.text_value;
													ELSE
														RAISE WARNING 'Multiple Start Time Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												WHEN end_time_attr_id THEN
													IF cacheinsecttemplate2.endtime IS NULL THEN
														cacheinsecttemplate2.endtime := rowsampleattributevalue.text_value;
													ELSE
														RAISE WARNING 'Multiple Start Time Attributes for Session ID --> %, ignored Attr ID --> %', rowsession.id, rowsampleattributevalue.id ;
													END IF;
												ELSE
													RAISE WARNING 'Unrecognised Session Attribute, Session ID --> %, SA ID --> % in SAV ID --> %: ignored', rowsession.id, rowsampleattributevalue.sample_attribute_id, rowsampleattributevalue.id ;
											END CASE;
											IF rowsampleattributevalue.updated_on > sessionupdated THEN
												sessionupdated := rowsampleattributevalue.updated_on;
											END IF;
										END LOOP;
										FOR rowinsect IN SELECT * FROM occurrences WHERE sample_id = rowsession.id AND deleted = false
										LOOP
										  DECLARE
											cacheinsectrow	spipoll_insects_cache%ROWTYPE;
										  BEGIN
										 	cacheinsectrow := cacheinsecttemplate2;
											cacheinsectrow.insect_id := rowinsect.id;
											cacheinsectrow.geom := cacherow.geom;
											cacheinsectrow.updated := sessionupdated;
											FOR rowoccurrenceattributevalue IN SELECT * FROM occurrence_attribute_values WHERE occurrence_id = rowinsect.id AND deleted = false ORDER BY id desc
											LOOP
												CASE rowoccurrenceattributevalue.occurrence_attribute_id
													WHEN insect_number_attr_id THEN
														IF cacheinsectrow.number_insect IS NULL THEN
															cacheinsectrow.number_insect := spipoll_get_term(rowoccurrenceattributevalue.int_value);
														ELSE
															RAISE WARNING 'Multiple Insect Number Attributes for Insect ID --> %, ignored Attr ID --> %', rowinsect.id, rowoccurrenceattributevalue.id ;
														END IF;
													WHEN insect_foraging_attr_id THEN
														IF cacheinsectrow.notonaflower IS NULL THEN
															IF rowoccurrenceattributevalue.int_value = 0 THEN
																cacheinsectrow.notonaflower := 'Non';
															ELSE
																cacheinsectrow.notonaflower := 'Oui';
															END IF;
														ELSE
															RAISE WARNING 'Multiple Foraging Attributes for Insect ID --> %, ignored Attr ID --> %', rowinsect.id, rowoccurrenceattributevalue.id ;
														END IF;
													ELSE
														RAISE WARNING 'Unrecognised Insect Attribute, Insect ID --> %, OA ID --> % in OAV ID --> %: ignored', rowinsect.id, rowoccurrenceattributevalue.occurrence_attribute_id, rowoccurrenceattributevalue.id ;
												END CASE;
												IF rowoccurrenceattributevalue.updated_on > cacheinsectrow.updated THEN
													cacheinsectrow.updated := rowoccurrenceattributevalue.updated_on;
												END IF;
											END LOOP;
											
											OPEN curinsectdetermination FOR SELECT * FROM determinations WHERE occurrence_id = rowinsect.id AND deleted = false ORDER BY id desc;
											FETCH curinsectdetermination INTO rowinsectdetermination;
											IF FOUND THEN
												IF rowinsectdetermination.updated_on > cacheinsectrow.updated THEN
													cacheinsectrow.updated := rowinsectdetermination.updated_on;
												END IF;
												CASE rowinsectdetermination.determination_type
													WHEN 'B' THEN -- Considered incorrect;
														cacheinsectrow.status_insecte = 'Doute';
													WHEN 'C' THEN -- Correct;
														cacheinsectrow.status_insecte = 'Valide';
													WHEN 'I' THEN -- Incorrect;
														cacheinsectrow.status_insecte = 'Invalide';
													WHEN 'R' THEN -- Requires confirmation;
														cacheinsectrow.status_insecte = 'Requires confirmation';
													WHEN 'U' THEN -- Unconfirmed;
														cacheinsectrow.status_insecte = 'Unconfirmed';
													WHEN 'X' THEN -- Unidentified;
														cacheinsectrow.status_insecte = 'Unidentified';
													ELSE --- defaults to 'A' Considered correct;
														cacheinsectrow.status_insecte = '-';
												END CASE;
												IF rowinsectdetermination.taxa_taxon_list_id IS NOT NULL THEN
													cacheinsectrow.insect_taxon_ids := '|'||rowinsectdetermination.taxa_taxon_list_id||'|';
												ELSE
													cacheinsectrow.insect_taxon_ids := ARRAY(select '|'||unnest(rowinsectdetermination.taxa_taxon_list_id_list)::text||'|')::text;
												END IF;
												IF cacherow.insect_taxon_ids IS NULL THEN
													cacherow.insect_taxon_ids := cacheinsectrow.insect_taxon_ids;
												ELSE
													cacherow.insect_taxon_ids := cacherow.insect_taxon_ids||cacheinsectrow.insect_taxon_ids;
												END IF;
												cacheinsectrow.taxons_insecte_precise := rowinsectdetermination.taxon_extra_info;
												IF cacherow.taxons_insecte_precise IS NULL THEN
													cacherow.taxons_insecte_precise := '|' || rowinsectdetermination.taxon_extra_info || '|';
												ELSE
													cacherow.taxons_insecte_precise := cacherow.taxons_insecte_precise || rowinsectdetermination.taxon_extra_info || '|';
												END IF;
												OPEN curinsectimage FOR SELECT * FROM occurrence_images WHERE occurrence_id = rowinsect.id AND deleted = false ORDER BY id DESC;
												FETCH curinsectimage INTO rowinsectimage;
												IF FOUND THEN
													cacheinsectrow.image_d_insecte = rowinsectimage.path;
													IF rowinsectimage.updated_on > cacheinsectrow.updated THEN
														cacheinsectrow.updated := rowinsectimage.updated_on;
													END IF;
													FETCH curinsectimage INTO rowinsectimage;
													IF FOUND THEN
														RAISE WARNING 'Multiple Insect Images on Insect ID --> %, only most recent image used, ignoring ID --> %', rowinsect.id, rowinsectimage.id ;
													END IF;
													--- RAISE WARNING '%', cacheinsectrow;
													---------------------------------
													INSERT INTO spipoll_insects_cache SELECT cacheinsectrow.*;
													---------------------------------
												END IF;
												CLOSE curinsectimage;
											END IF; --- could be flagged to be identified later 
											CLOSE curinsectdetermination;
										  END;
										END LOOP;
									END LOOP;
									--- RAISE WARNING '%', cacherow;
									---------------------------------
									INSERT INTO spipoll_collections_cache SELECT cacherow.*;
									---------------------------------
									count := count + 1;
								ELSE
									RAISE WARNING 'Could not find Flower Image for Flower ID --> %, Collection % not cached', rowflower.id, collectionrow.id ;
								END IF;
								CLOSE curflowerimage;
							ELSE
								RAISE WARNING 'Could not find Determination for Flower ID --> %, Collection % not cached', rowflower.id, collectionrow.id ;
							END IF;
							CLOSE curdetermination;
							FETCH curflower INTO rowflower;
							IF FOUND THEN
								RAISE WARNING 'Multiple Flowers on Collection ID --> %, only most recent location used, ignoring ID --> %', collectionrow.id, rowflower.id ;
							END IF;
						ELSE
							RAISE WARNING 'Could not find Flower for Collection ID --> %, Collection not cached', collectionrow.id ;
						END IF;
						CLOSE curflower;
					ELSE
						RAISE WARNING 'Could not find Location Image for Location ID --> %, Collection % not cached', rowlocation.id, collectionrow.id ;
					END IF;
					CLOSE curlocationimage;
					FETCH curlocation INTO rowlocation;
					IF FOUND THEN
						RAISE WARNING 'Multiple Locations on Collection ID --> %, only most recent location used, ignoring ID --> %', collectionrow.id, rowlocation.id ;
					END IF;
				WHEN 0 THEN
					RAISE WARNING 'Collection not closed, ID --> %', collectionrow.id ;
				WHEN 2 THEN
					RAISE WARNING 'No closed attribute found for Collection ID --> %', collectionrow.id ;
			END CASE;
		ELSE
			RAISE WARNING 'Could not find Location for Collection ID --> %, Collection not cached', collectionrow.id ;
		END IF;
		CLOSE curlocation;
	  END;
    END LOOP;

	return count;
END;
$$ LANGUAGE plpgsql;

--- BEGIN;
select * from build_spipoll_cache(6);
--- COMMIT;