ALTER TABLE users
ADD COLUMN username character varying(30) NOT NULL,
ADD COLUMN password character varying;

CREATE OR REPLACE VIEW gv_users AS
 SELECT u.id, u.username, cr.title as core_role 
   FROM users u
   LEFT JOIN core_roles cr on u.core_role_id = cr.id;
