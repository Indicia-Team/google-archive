# Importing Data #

The warehouse contains a set of import facilities allowing you to quickly populate it with existing data. In general you will be required to prepare a CSV file containing one column per field you wish to import and the fields must map to the input boxes you'd see in the warehouse when manually inputting a single record. For example, when creating a species there is a box for inputting synonyms that expects you to provide data in a specific format - the CSV file should use this same format in the Synonyms column. At the bottom of each warehouse grid is a file upload box which lets you select the CSV file to upload and a wizard will guide you through the process. At the end, if any records did not upload successfully then you will be given the opportunity to download just those records and their error messages to correct and re-upload.

## Additional tips ##

  * When importing species, provide a column and map this to the **_Other Fields_** > **Codes** field. This can contain a list of codes to attach to the taxon, such as Bradley Fletcher numbers, GBIF numbers etc. The types of code must be first configured in the Taxon Code Types termlist. Provide the codes on separate lines, with the type followed by a | then the code itself. E.g.
```
Bradley Fletcher|123
GBIF|456
```