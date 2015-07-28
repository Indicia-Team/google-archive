# Extensions to Kohana's Validation Library #

The following extensions are added to the Kohana Validation library through subclassing the valid\_core helper class (application/helpers/MY\_valid.php).

| **Rule name** | **Description** |
|:--------------|:----------------|
|sref\_system   |Validates that the input field represents an understandable spatial reference system. This can either be an EPSG code recognised by PostGIS, or a notation from the application/config/sref\_notations.php file.|
|sref           |Validates that the input field is a valid string for the spatial reference system passed to the rule as a parameter.|
|vague\_date    |Validates that the input field represents a recognisable vague date string.|
|valid\_term    |Validates that the input field is a valid term from the list passed to the validation rule as an ID parameter.|
|valid\_taxon   |Validates that the input field is a valid taxon name from the species list passed to the validation rule as an ID parameter.|
|regex          |Validates that the input field matches the regular expression passed to the validation rule as a parameter|