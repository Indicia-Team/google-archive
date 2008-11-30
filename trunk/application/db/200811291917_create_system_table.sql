DROP TABLE IF EXISTS "system";

CREATE TABLE "system" (
    id_system integer NOT NULL,
    "version" character varying(10) DEFAULT ''::character varying NOT NULL,
    "name" character varying(30) DEFAULT ''::character varying NOT NULL,
    repository character varying(150) DEFAULT ''::character varying NOT NULL,
    release_date date
);

DROP SEQUENCE IF EXISTS system_id_system_seq1;

CREATE SEQUENCE system_id_system_seq1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER SEQUENCE system_id_system_seq1 OWNED BY system.id_system;

SELECT pg_catalog.setval('system_id_system_seq1', 1, true);

ALTER TABLE "system" ALTER COLUMN id_system SET DEFAULT nextval('system_id_system_seq1'::regclass);

ALTER TABLE ONLY "system"
    ADD CONSTRAINT id_system PRIMARY KEY (id_system);

INSERT INTO "system" ("id_system", "version", "name", "repository", "release_date") VALUES (1, '0.1', '', 'http://indicia.googlecode.com/svn/tag/version_0_1', '2009-01-15');
