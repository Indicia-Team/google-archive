DROP FUNCTION reduce_precision(geom_in geometry, confidential boolean, sensitivity_precision integer, sref_system character varying);

CREATE OR REPLACE FUNCTION reduce_precision(geom_in geometry, confidential boolean, sensitivity_precision integer, sref_system character varying)
  RETURNS geometry AS
$BODY$
DECLARE geom geometry;
DECLARE geomltln geometry;
DECLARE r geometry;
DECLARE precisionM integer;
DECLARE x float;
DECLARE y float;
DECLARE sref_metadata record;
DECLARE current_srid integer;
BEGIN
  -- Copy geom_in as values cannot be assigned to parameters in postgres <= 8.4
  geom = geom_in;
  IF confidential = true OR sensitivity_precision IS NOT NULL THEN
    precisionM = CASE
      WHEN sensitivity_precision IS NOT NULL THEN sensitivity_precision
      ELSE 1000
    END;
    -- If already low precision, then can return as it is
    IF sqrt(st_area(geom)) >= sensitivity_precision THEN
      r = geom;
    ELSE
      SELECT INTO sref_metadata srid, treat_srid_as_x_y_metres FROM spatial_systems WHERE code=lower(sref_system);
      -- look for some preferred grids to see if we are in range. 
      geom = st_transform(st_centroid(geom_in), 4326);
      current_srid=null;
      IF st_x(geom) BETWEEN -10 AND 5 AND st_y(geom) BETWEEN 48 AND 65 THEN -- rough check for OSGB
        geom = st_transform(st_centroid(geom_in), 27700);
        IF st_x(geom) BETWEEN 0 AND 700000 AND st_y(geom) BETWEEN 0 AND 14000000 THEN -- exact check for OSGB
          current_srid = 27700;
        END IF;
      END IF;
      IF current_srid IS NULL THEN
        IF COALESCE(sref_metadata.treat_srid_as_x_y_metres, false) THEN
          geom = st_transform(geom_in, sref_metadata.srid);
          current_srid = sref_metadata.srid;
        ELSE
          current_srid = 900913;
          geom=geom_in;
        END IF;
      END IF;
      -- need to reduce this to a square on the grid
      x = floor(st_xmin(geom)::NUMERIC / precisionM) * precisionM;
      y = floor(st_ymin(geom)::NUMERIC / precisionM) * precisionM;
      r = st_geomfromtext('polygon((' || x::varchar || ' ' || y::varchar || ',' || (x + precisionM)::varchar || ' ' || y::varchar || ','
       || (x + precisionM)::varchar || ' ' || (y + precisionM)::varchar || ',' || x::varchar || ' ' || (y + precisionM)::varchar || ','
       || x::varchar || ' ' || y::varchar || '))', current_srid);
      IF current_srid<>900913 THEN
        r = st_transform(r, 900913);
      END IF;
    END IF;
  ELSE
    r = geom;
  END IF;
RETURN r;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
