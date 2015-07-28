# Introduction #
This page details the configuration process for the MNHNL Reptiles form. It assumes a level of experience in setting forms up.

This form was designed for use with 3 specific surveys, the set up for which is detailed below. It could be used for other surveys, with suitable modification to the attributes used. The set up below includes setting up the attributes for the following surveys:
  1. Common Wall Lizard Survey in Luxembourg.
  1. Smooth Snake Survey in Luxembourg.
  1. Sand Lizard Survey in Luxembourg.

Prerequisites: the Attributes for the MNHNL butterflies and MNHNL bats forms should have been loaded in.

# Warehouse #
  1. Open up a browser window to the warehouse.
  1. Make note of Website ID and password to be used - create a new one if needed.

# Database #
  1. Open up pgAdmin on the server.
  1. Run support file reptiles.sql. This will load in the required termlists and custom attributes.
  1. Make a note of the ids for the **Lux5KSquare** term and the **LizardLocation** term in the **ReptileLocation** termlist. _If the locations are to be separated between the surveys, separate term IDs must be used for the different surveys._
  1. Take a copy of the support file reptile\_locations.sql.
  1. Edit this copy and replace all occurrences of **`<locType>`** with the **Lux5KSquare** term id noted above, and replace all occurrences of **`<website_id>`** with the website ID noted above.
  1. Run this modified file against the database.
  1. Make a note the the sample attribute id for '**No Observation**'.
  1. Make a note of the occurrence attribute ids for 'Reptile Occurrence Type' (later referred to as **`<typeID>`**), 'Reptile Occurrence Stage' (later referred to as **`<stageID>`**), 'Reptile Occurrence Sex' (later referred to as **`<sexID>`**), 'Reptile Occurrence Behaviour' (later referred to as **`<behaviourID>`**).
  1. Make a note of the meaning ids of the following terms (it is easiest to look directly at the '**detail\_termlists\_terms**' view in pgAdmin).
|Term|Termlist|later referred to as|Meaning ID|
|:---|:-------|:-------------------|:---------|
|Dead specimen|Reptile Type|**`<typeDead>`**    |          |
|Slough|Reptile Type|**`<typeSlough>`**  |          |
|Specimen|Reptile Type|**`<typeSpec>`**    |          |
|Undetermined|Reptile Type|**`<typeUndet>`**   |          |
|Egg |Reptile Stage|**`<stageEgg>`**    |          |
|Juvenile|Reptile Stage|**`<stageJuv>`**    |          |
|Adult|Reptile Stage|**`<stageAdult>`**  |          |
|Undetermined|Reptile Stage|**`<stageUndet>`**  |          |
|Female|Reptile Sex|**`<sexFem>`**      |          |
|Male|Reptile Sex|**`<sexMale>`**     |          |
|Undetermined|Reptile Sex|**`<sexUndet>`**    |          |
|Basking|Reptile Behaviour|**`<behavBask>`**   |          |
|Feeding|Reptile Behaviour|**`<behavFeed>`**   |          |
|Fighting|Reptile Behaviour|**`<behavFight>`**  |          |
|Hunting|Reptile Behaviour|**`<behavHunt>`**   |          |
|Inactivity|Reptile Behaviour|**`<behavInact>`**  |          |
|Lethargy|Reptile Behaviour|**`<behavLeth>`**   |          |
|Resting|Reptile Behaviour|**`<behavRest>`**   |          |
|Swimming|Reptile Behaviour|**`<behavSwim>`**   |          |
|Undetermined|Reptile Behaviour|**`<behavUndet>`**  |          |

# Warehouse #
  1. Prepare and upload species list
  1. Make a note of the list ID.

**The following has to be repeated, once for each survey.**

# Warehouse #
  1. Create a sub list for the main species list, and copy down the appropriate taxon ("Podarcis muralis" for the Common Wall Lizard Survey, "Coronella austriaca" for the Smooth Snake Survey, and "Lacerta agilis" for the Sand Lizard Survey). This list is used to provide an initial selection in the species grid when the sample is created. Adding to the list will increase the initial set appropriately. For additional rows in the species grid, the full list of species may be choosen from. Make a note of the sublist ID.
  1. Create a survey, and attach it to the Website above. Make a note of its name and ID.
  1. Bring up the surveys, and choose the '**setup attributes**' link for the survey created above.
  1. Within samples attribute set, create the following blocks: '**Conditions**', '**General**', '**Weather**', '**Species**', '**No Observation**'.
  1. Put the '**General**' and '**Weather**' blocks into the '**Conditions**' block.
  1. Put the '**No Observation**' block into the '**Species**' block.
  1. For the Common Wall Lizard Survey, add the following existing attributes, and place them into the '**General**' block: '**Reptile Survey 1**', '**Duration**', '**Suitability Checkbox**', '**Picture Provided**'.
  1. For the Smooth Snake and Sand Lizard Surveys, add the following existing attributes, and place them into the '**General**' block: '**Reptile Survey 2**', '**Duration**', '**Suitability Checkbox**', '**Picture Provided**'.
  1. Add the following existing attributes, and place them into the '**Weather**' block: '**Temperature (Celcius)**', '**Cloud Cover**', '**Rain Checkbox**'.
  1. Add the following existing attribute, and place it into the '**No Observation**' block: '**No observation**'.
  1. Add the following existing attributes, but leave them outside the blocks: '**CMS User ID**', '**CMS Username**', '**Email**'.
  1. Save the layout.
  1. Within the occurrences attribute set, add the following existing attributes, but leave them outside the blocks: '**Count**', '**Occurrence Reliability**', '**Counting**', '**Reptile Occurrence Type**', '**Reptile Occurrence Stage**', '**Reptile Occurrence Sex**', '**Reptile Occurrence Behaviour**'.
  1. Save the layout.

# Drupal #
  1. Create a new IForm Content Node. Fill in the details as follows then save it.
  * Choose a 'Title of Page'.
  * Enter Website ID and its password, noted at start.
  * Select Form Category 'MNHNL forms'.
  * Choose Form 'MNHNL Reptiles'.
  * Load the settings form.
### Other Iform Parameters ###
  * View Access Control: unchecked.
  * Permission name for view access control: blank.
  * Survey: Choose the survey created in step 3 from drop down list.
  * Sample Method: Leave as '<please select>'.
  * Default Values: Leave as 'occurrence:record\_status=C'.
  * Redirect to page aftere successful data entry: blank.
  * Display notification after save: unchecked.
### Initial Map View ###
  * Centre of Map Latitude: 49.75
  * Centre of Map Longitude: 6.16
  * Map Zoom Level: 9
  * Map Width: 100%
  * Map Height: 600
  * Remember position: unchecked.
### Base Map Layers ###
  * Preset Base Layers: Check 'Google Hybrid' only.
  * All other fields: blank
### Advanced Base Map Layers ###
  * All fields: blank
### Other Map Settings ###
  * WMS layers from Geoserver: blank
  * Controls to add to map: (each on separate line in textarea) layerSwitcher panZoom SiteSelector
  * Allowed Spatial Ref Systems: 2169
### Georeferencing ###
  * Leave all fields as default.
### User Interface ###
  * Interface Style Option: Wizard
  * Show Progress through Wizard/Tabs: Checked
  * Show email field even if logged in: unchecked
  * Show user profile fields even if logged in: unchecked
  * Client Side Validation: Checked.
  * Form structure: Change the assigned values for the following 3 lines to
`@`attrRestrictions=**`<typeID>`**:**`<stageID>`**:**`<typeDead>`**,`*`:**`<typeSlough>`**,**`<stageUndet>`**:**`<typeSpec>`**,`*`:**`<typeUndet>`**,**`<stageUndet>`**;**`<stageID>`**:**`<sexID>`**:**`<stageAdult>`**,`*`:**`<stageEgg>`**,**`<sexUndet>`**:**`<stageJuv>`**,**`<sexFem>`**,**`<sexMale>`**,**`<sexUndet>`**:**`<stageUndet>`**,**`<sexUndet>`**;**`<stageID>`**:**`<behaviourID>`**:**`<stageAdult>`**,`*`:**`<stageEgg>`**,**`<behavUndet>`**:**`<stageJuv>`**,**`<behavBask>`**,**`<behavFeed>`**,**`<behavFight>`**,**`<behavHunt>`**,**`<behavInact>`**,**`<behavLeth>`**,**`<behavRest>`**,**`<behavSwim>`**,**`<behavUndet>`**:**`<stageUndet>`**,`*`<br />
`@`ParentLocationTypeID=**`<X>`**<br />
`@`LocationTypeID=**`<Y>`**<br />
Where **`<X>`** is the term id for '**Lux5KSquare**' noted above, **`<Y>`** is the term id for '**LizardLocation**' noted above (_TBC Different terms for different surveys?_), and the entries in the **`@`attrRestrictions** line are as found above. Note that the exact puctuation is very important, as are the different terms for 'Undetermined'.<br />
  * Attribute Termlist Language Filter: checked.
  * Skip initial grid of Data: unchecked.
  * Grid Report: leave as 'reports\_for\_prebuilt\_forms/MNHNL/mnhnl\_reptiles'.
  * Save button below all pages?: unchecked.
  * Include Location Tools: checked.
  * Location Tools Location Type ID Filter: set to term id for 'Lux5KSquare' noted above.
  * Attribute Validation Rules: set to **`smpAttr:<X>,no_observation`**, where **`<X>`** is the sample attribute id for '**No Observation**' noted above.
### Species ###
  * Single or Multiple occurrences per sample: Only allow entry of multiple occurrences using a grid.
  * Single Species Selection Control Type: Autocomplete.
  * Occurrence Comment: Checked.
  * Occurrence Confidential: Unchecked.
  * Occurrence Images: Unchecked.
  * Grid Column Widths: blank.
  * Initial Species List: set to the _sub_ taxon list ID above.
  * Extra Species List: set to the _main_ taxon list ID above..
  * Species Names Filter: All names are available.
# NOTES #
After the node has been created, users must be given permissions to do access the node.
The bare minimum user must be given a role which has the following Drupal permissions (where **`<node>`** is the node number for this form):
  * IForm n**`<node>`** user
  * access iform
  * IForm loctools node **`<node>`** user

A manager should be given a role which has the following Drupal permissions (again where **`<node>`** is the node number for this form):
  * IForm n**`<node>`** admin
  * IForm n**`<node>`** user
  * IForm loctools node **`<node>`** admin
  * IForm loctools node **`<node>`** superuser
  * IForm loctools node **`<node>`** user
  * access iform

The manager will need to assign the locations to the users in order for the user to be able to access them.