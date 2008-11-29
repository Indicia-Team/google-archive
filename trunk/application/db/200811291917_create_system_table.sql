CREATE TABLE "system"
(
  id_system integer NOT NULL,
  "version" character varying(10) NOT NULL DEFAULT ''::character varying,
  "name" character varying(30) NOT NULL DEFAULT ''::character varying,
  repository character varying(150) NOT NULL DEFAULT ''::character varying,
  release_date date,
  CONSTRAINT id_system PRIMARY KEY (id_system)
)
WITH (OIDS=FALSE);

INSERT INTO system (id_system, version, name, repository, release_date) VALUES (1, '0.1', '', 'http://indicia.googlecode.com/svn/tag/version_0_1', '2009-01-15');
