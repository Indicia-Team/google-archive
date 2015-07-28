# Prebuilt Forms #

Here is a list of the prebuilt forms available in Indicia.

## Ad-hoc cetacean records ##
| File | ad\_hoc\_cetaceans.php |
|:-----|:-----------------------|
| Description | A simple form for entering ad-hoc cetacean sightings. This differs from other forms in the usage of a map to enter the lat long only when the sighting is from the shore, whereas boat records must be entered using the GPS coordinate. |

## Basic 1 - species, date, place, survey and comment ##
| File | basic\_1.php |
|:-----|:-------------|
| Description | A minimal form to illustrate how dynamic forms work. |

## Basic 2 - species, date, place ##
| File | basic\_2.php |
|:-----|:-------------|
| Description | A second minimal form to illustrate how dynamic forms work. |

## Distribution map 1 ##
| File | distribution\_map\_1.php |
|:-----|:-------------------------|
| Description | Displays a distribution map. The map features data for any species, an entire survey, or can be filtered using a URL parameter. It also supports click on points to view details. |
| Documentation | [Example of using a distribution map in standalone PHP](distribution_map_1_standalone.md) |

## Importer ##
| File | importer.php |
|:-----|:-------------|
| Description | A wizard for importing data into Indicia. |
| Documentation | [Configuring the Importer prebuilt form.](prebuilt_importer.md) |

## Dynamic Sample Occurrence ##
| File | dynamic\_sample\_occurrence.php |
|:-----|:--------------------------------|
| Description | Advanced form which uses the attributes associated with a survey to dynamically build a form. Flexible and powerful, should be considered as the starting point for any new surveys. |
| Documentation | [Creating a survey using the Dynamic Sample Occurrence form](TutorialDynamicForm.md) |

## Specific MNHNL Forms ##
These are a set of forms produced for specific requirements of the Biomonitoring Scheme in Luxembourg, which is hosted by the Luxembourg Natural History Museum website (MNHNL). They are all available under the 'MNHNL forms' Category.
| Title | File | Description | Documentation |
|:------|:-----|:------------|:--------------|
| MNHNL Dynamic 1 | mnhnl\_dynamic\_1.php | Advanced form, inherits from Dynamic Sample Occurrence. The only additional functionality is an optional Luxembourg specific header and footer, the inclusion of which controlled by a checkbox in the Form node configuration 'User Interface'. |               |
| MNHNL Dynamic 2 | mnhnl\_dynamic\_2.php | Advanced form, inherits from MNHNL Dynamic 1. Allows optional position recording of individual Occurrences by using a heirarchical sample structure. Used as the basis for recording Amphibians, Reptiles, Dormice and Dragonflies.  Features a standardised Location record creation/selection interface, Shapefile lookup for automatic geolocation (Communes), Recorder names picklist (from designated Drupal users), a custom Target Species Grid, and a flexible custom Species grid. | [Creating a survey using the MNHNL Dynamic 2 form](TutorialMNHNLDynamicForm2.md) |
| MNHNL Winter Bats | mnhnl\_bats.php | Advanced form (inherits from MNHNL Dynamic 1) which is used for recording Bat Hibernation occurrences. Features a standardised Location record creation/selection interface, Shapefile lookup for automatic geolocation (Communes), Recorder names picklist (from designated Drupal users), and a custom Species grid. | [Creating a survey using the MNHNL Winter Bats form](TutorialMNHNLBats.md) |
| MNHNL Summer Bats | mnhnl\_bats2.php | Advanced form (inherits from MNHNL Winter Bats) which is used for recording Bat Roost occurrences. Features custom controls for a Survey Method grid, and Species Grid. | [Creating a survey using the MNHNL Summer Bats form](TutorialMNHNLBats2.md) |
| Bird Transect Walks | mnhnl\_bird\_transect\_walks.php | Advanced custom form used for COBIMO recording (does not inherit). | [Creating a survey using the MNHNL Bird Transect Walks form](TutorialCOBIMO.md) |
| MNHNL Butterflies form | mnhnl\_butterflies.php| Advanced form (inherits from MNHNL Dynamic 1) which is used for recording Butterfly counts in Transects. Not map based, and features custom controls for entering section and grid based counts for a transect. | [Creating a survey using the MNHNL Butterflies form](TutorialMNHNLButterflies.md) |
| Luxembourg Butterfly Biomonitoring (site based)| mnhnl\_butterflies2.php | Advanced form (inherites from MNHNL Dynamic 1) which is used for recording Butterflies (Papillon de Jours) occurrences. Area based: handles multiple sites at once. Features a standardised Location record creation/selection interface, and custom sample attribute and occurrence count grid controls to allow entry of data for multiple sites at once. | [Creating a survey using the MNHNL Butterflies2 form](TutorialMNHNLButterflies2.md) |
| Luxembourg Reptile Biomonitoring | mnhnl\_reptiles.php | <b>Deprecated. Superceeded by MNHNL Dynamic 2</b><br />Advanced form (inherites from MNHNL Dynamic 1) which was used for recording Reptile occurrences. | [Creating a survey using the MNHNL Reptile form](TutorialMNHNLReptile.md) |

## Report Grid ##
| File | report\_grid.php |
|:-----|:-----------------|
| Description | A form for outputting the results of a report in a grid. |
| Documentation | [Using the Report Grid prebuilt form](PrebuiltFormReportGrid.md) |

## Report Map ##
| File | report\_map.php |
|:-----|:----------------|
| Description | A form for outputting the results of a report on a map. |
| Documentation | [Using the Report Map prebuilt form](PrebuiltFormReportMap.md) |

## Report Calendar Grid ##
| File | report\_calendar\_grid.php |
|:-----|:---------------------------|
| Description | A form for outputting the results of a report within a calendar. Its primary use is the calendar based display of previously entered samples for a user, and allowing the invocation of a data entry form for a particular day. It optionally allows for filtering by location. |
| Documentation | [Using the Report Calendar Grid prebuilt form](PrebuiltFormReportCalendarGrid.md) |

## Report Calendar Summary ##
| File | report\_calendar\_summary.php |
|:-----|:------------------------------|
| Description | A form for outputting the results of a report. It summarises the report results into weeks, and displays the results for a year as a chart and/or a table. It optionally allows for filtering by user and location. |
| Documentation | [Using the Report Calendar Summary prebuilt form](PrebuiltFormReportCalendarSummary.md) |

## Verification 3 ##
| File | verification\_3.php |
|:-----|:--------------------|
| Description | A report listing incoming records with facilities to review, verify and reject them. |
| Documentation | [Using the Verification 3 prebuilt form](PrebuiltFormVerification3.md) |