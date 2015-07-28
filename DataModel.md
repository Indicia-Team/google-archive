## List of Tables ##
The following tables are used by the Indicia Core Module:
<table border='1'>
<blockquote><tr>
<blockquote><th>Table</th>
<th>Comment</th>
</blockquote></tr>
<tr>
<blockquote><td>core_roles</td>
<td>List of user roles for the core site, including no access, site admin, core admin.</td></blockquote></blockquote>

<blockquote></tr>
<tr>
<blockquote><td>geometry_columns</td>
<td>PostGIS table for tracking columns containing geometry data.</td>
</blockquote></tr>
<tr>
<blockquote><td>languages</td></blockquote></blockquote>

<blockquote><td>List of languages known to the system.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>location_attribute_values</td>
<td>Contains values that have been stored for locations against custom attributes.</td>
</blockquote></tr>
<tr></blockquote>

<blockquote><td>location_attributes</td>
<td>List of additional attributes that are defined for the location data.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>location_attributes_websites</td>
<td>Join table which identifies the websites that each location attribute is available for.</td>
</blockquote></tr></blockquote>

<blockquote><tr>
<blockquote><td>locations</td>
<td>List of locations, including wildlife sites and other locations, known to the system.</td>
</blockquote></tr>
<tr>
<blockquote><td>locations_websites</td>
<td>Join table which identifies the locations that are available for data entry on each website.</td></blockquote></blockquote>

<blockquote></tr>
<tr>
<blockquote><td>meanings</td>
<td>List of unique term meanings. All terms that refer to a single meaning are considered synonymous.</td>
</blockquote></tr>
<tr>
<blockquote><td>occurrence_attribute_values</td></blockquote></blockquote>

<blockquote><td>Contains values that have been stored for occurrences against custom attributes.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>occurrence_attributes</td>
<td>List of additional attributes that are defined for the occurrences data.</td>
</blockquote></tr>
<tr></blockquote>

<blockquote><td>occurrence_attributes_websites</td>
<td>Join table which identifies the occurrence attributes that are available when entering occurrence data on each website.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>occurrence_comments</td>
<td>List of comments regarding the occurrence posted by users viewing the occurrence subsequent to initial data entry.</td>
</blockquote></tr></blockquote>

<blockquote><tr>
<blockquote><td>occurrence_images</td>
<td>Lists images that are attached to occurrence records.</td>
</blockquote></tr>
<tr>
<blockquote><td>occurrences</td>
<td>List of occurrences of a taxon.</td></blockquote></blockquote>

<blockquote></tr>
<tr>
<blockquote><td>people</td>
<td>List of all people known to the system.</td>
</blockquote></tr>
<tr>
<blockquote><td>sample_attribute_values</td></blockquote></blockquote>

<blockquote><td>Contains values that have been stored for samples against custom attributes.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>sample_attributes</td>
<td>List of additional attributes that are defined for the sample data.</td>
</blockquote></tr>
<tr></blockquote>

<blockquote><td>sample_attributes_websites</td>
<td>Join table that identifies which websites a sample attribute is defined for.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>samples</td>
<td>List of samples known to the system.</td>
</blockquote></tr></blockquote>

<blockquote><tr>
<blockquote><td>site_roles</td>
<td>List of roles that exist at the online recording website level.</td>
</blockquote></tr>
<tr>
<blockquote><td>spatial_ref_sys</td>
<td>PostGIS table for the list of spatial reference systems that can be transformed.</td></blockquote></blockquote>

<blockquote></tr>
<tr>
<blockquote><td>surveys</td>
<td>List of surveys known to the system.</td>
</blockquote></tr>
<tr>
<blockquote><td>system</td></blockquote></blockquote>

<blockquote><td>Contains system versioning information.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>taxa</td>
<td>List of taxa known to the system.</td>
</blockquote></tr>
<tr></blockquote>

<blockquote><td>taxa_taxon_lists</td>
<td>Join table that defines which taxa belong to which taxon lists.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>taxon_groups</td>
<td>List of higher level taxonomic groups, used to give a label that can quickly confirm that a selected name is in the right taxonomic area.</td>
</blockquote></tr></blockquote>

<blockquote><tr>
<blockquote><td>taxon_lists</td>
<td>List of taxon lists known to the system, including the main species list and all subsets.</td>
</blockquote></tr>
<tr>
<blockquote><td>taxon_meanings</td>
<td>List of distinct taxonomic meanings. Each meaning is associated with several taxa records, each of which are therefore considered to be synonymous with the same species or other taxon.</td></blockquote></blockquote>

<blockquote></tr>
<tr>
<blockquote><td>termlists</td>
<td>List of all controlled terminology lists known to the system. Each termlist is used to store a list of known terms, which can provide a lookup for populating a field, or the values which may be selected when entering data into an auto-complete text box for example.</td>
</blockquote></tr>
<tr>
<blockquote><td>termlists_terms</td></blockquote></blockquote>

<blockquote><td>Join table that identifies the terms that belong to each termlist.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>terms</td>
<td>Distinct list of all terms which are included in termlists.</td>
</blockquote></tr>
<tr></blockquote>

<blockquote><td>user_tokens</td>
<td>Contains tokens stored in cookies used to authenticate users on the core module.</td>
</blockquote><blockquote></tr>
<tr>
<blockquote><td>users</td>
<td>List of all users of the system. Contains login specific information only as each user is also identified as a record in the people table.</td>
</blockquote></tr></blockquote>

<blockquote><tr>
<blockquote><td>users_websites</td>
<td>Join table that identifies the websites that a user has access to.</td>
</blockquote></tr>
<tr>
<blockquote><td>websites</td>
<td>List of data entry websites using this instance of the core module.</td></blockquote></blockquote>

<blockquote></tr>
</blockquote><blockquote></table></blockquote>

## Main Entity Relationship Diagram ##
![http://indicia.googlecode.com/svn/wiki/main%20erd.png](http://indicia.googlecode.com/svn/wiki/main%20erd.png)

## Website related tables Entity Relationship Diagram ##
![http://indicia.googlecode.com/svn/wiki/website%20related%20erd.png](http://indicia.googlecode.com/svn/wiki/website%20related%20erd.png)