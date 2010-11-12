CREATE OR REPLACE VIEW indicia.list_termlists_terms AS 
 SELECT tlt.id, tlt.term_id, t.term, tlt.termlist_id, tl.title AS termlist, tl.website_id, tl.external_key AS termlist_external_key, l.iso
   FROM indicia.termlists_terms tlt
   JOIN indicia.termlists tl ON tl.id = tlt.termlist_id AND tl.deleted=false
   JOIN indicia.terms t ON t.id = tlt.term_id AND t.deleted=false
   JOIN indicia.languages l ON l.id = t.language_id AND l.deleted=false
  WHERE tlt.deleted = false 
  ORDER BY tlt.sort_order, t.term;


DROP VIEW indicia.detail_termlists_terms;

CREATE OR REPLACE VIEW indicia.detail_termlists_terms AS 
 SELECT tlt.id, tlt.term_id, t.term, tlt.termlist_id, tl.title AS termlist, tlt.meaning_id, tlt.preferred, tlt.parent_id, tp.term AS parent, tlt.sort_order,
 tl.website_id, tlt.created_by_id, c.username AS created_by, tlt.updated_by_id, u.username AS updated_by, l.iso
   FROM indicia.termlists_terms tlt
   JOIN indicia.termlists tl ON tl.id = tlt.termlist_id AND tl.deleted=false
   JOIN indicia.terms t ON t.id = tlt.term_id AND t.deleted=false
   JOIN indicia.languages l ON l.id = t.language_id AND l.deleted=false
   JOIN indicia.users c ON c.id = tlt.created_by_id
   JOIN indicia.users u ON u.id = tlt.updated_by_id
   LEFT JOIN indicia.termlists_terms tltp ON tltp.id = tlt.parent_id
   LEFT JOIN indicia.terms tp ON tp.id = tltp.term_id
  WHERE tlt.deleted = false
  ORDER BY tlt.sort_order, t.term;