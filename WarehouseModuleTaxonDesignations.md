# Taxon Designations module #

The Taxon Designations module (in modules/taxon\_designations) enables support for a list of taxon designation types and the association of these designation types to individual taxa in the database. It creates the following tables in the database:
  * taxon\_designations - a list of taxon designation types.
  * taxa\_taxon\_designations - associations from taxon designation types to the taxa in the database.

The list of taxon designation types is available on the Admin menu in the Warehouse under the Taxon Designations entry and there is an additional tab for the details of a taxon where it is possible to create the associations between taxa and designation types.

The Admin > Taxon Designations page allows import by CSV file like other Warehouse data entities, but also supports upload of data in the JNCC Conservation Designations spreadsheet format. To do this, first ensure that your species are already created on the Warehouse and have their External Key populated with the associated NBN taxon version keys. Copy the table from the master list tab of the spreadsheet into its own Excel file. Then delete the following columns to reduce the file size: Category, Taxon Group, Authority, Attributes, Designated\_Name, Common Name, IUCN Criteria Version, Criteria Description, Source Description, Comments, Taxon Group Sort Order, Reporting Category Sort Order, Designation Sort Order, Link to Species Records on NBN Gateway.

Save this as a csv (MSDOS format) file. Now check the file size is less than your current PHP file upload limit (likely to be 4 MB) and split into several files, each smaller than the limit, keeping the column titles in all the file parts.

Now, use the second upload box on the designations list page called **Upload a Designations Spreadsheet (CSV) file into this list** to upload each of the CSV files in turn. This should create the designation types required as well as associating them with the taxa in your database.