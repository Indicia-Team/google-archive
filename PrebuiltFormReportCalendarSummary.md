# Introduction #

The report calendar summary form is used to output a summary of the results of a report as one of or both of:
  * a table of numbers,
  * a chart (either bar or line).
The summarisation involves adding together the results that occur in a particular week for a particular value of a defined field in the report, and displaying those on a week by week basis. The weeks form the horizontal axis of the chart, and the columns of the table. The groupings (e.g. taxa) form the series of the chart, and the rows of the table.
It is possible to configure the form as follows:
  * output table only
  * output chart only
  * output both the chart and the table at the same time
  * output the chart and table, but switchable so only one is visible at any one time.
It follows that the report used is assumed to be a date based report. The count can either be a simple count of the records, or a numeric field can be summed.

It is possible to define the start day of the week and to output a week number column in the grid (with a configurable start point). It is also possible to restrict the weeks displayed to a defined range of week numbers.

# Configuring  the report calendar summary prebuilt form #
Some knowledge of the report you are going to use is required in order to configure this form.

To use this prebuilt form from Drupal, select Create Content then Indicia Forms from the menu as for any other prebuilt form. Specify a title for the page then a little down the page enter your website ID and password. The Report Calendar Summary form is available from the Select Form drop-down after choosing the Reporting category first.

Once you have selected the Report Calendar Summary form click the Load Settings Form button. Drupal will load the settings form for this IForm which contains a number of options, each with help text explaining what they are for. A list of them is provided below:

When you have finished with the settings save your page and test the report. It should show the calendar.

## Other IForm Parameters ##
**View access control**: Standard IForm configuration item that enables the functionaliity to restrict which roles are able to view the form.

**Permission name for view access control**: Standard IForm configuration item that defines the Drupal permission that users must have in order to view the form.

**Redirect to page after successful data entry**: Standard IForm configuration item: not used as this form has no data entry functionality.

**Display notification after save**: Standard IForm configuration item: not used as this form has no data entry functionality.
## Report Settings ##
**Report Name**: Select the report you want to use to populate the grid.

**Preset Parameter Values**: If you want to provide a parameter value for a report, you can specify them in this box. For example you might specify survey\_id=4 to restrict the data to survey 4. Some reports may expect some fixed values like this. The form itself provides the 'date\_from' and 'date\_to' parameters, and optionally user\_id and location\_id parameters. Any additional parameters for the report must be entered here, as this form does not display an input panel for any missing parameters.

**Vertical Axis**: The field in the report which is used to group the data together into the rows of the table and series on the chart.

**Count Column**: This is the field in the report which is used to store the count associated with a report record. If left blank, the grid assumes a count of one per record. If filled in and the field is empty, a value of zero is assumed.
## Report Output ##
**Output data table**: Enables the display of the data in table format.

**Output chart**: Enables the display of the data in chart format.

**Simultaneous output formats**: If both the data table and the chart are to be output, then this determines whether they are display together at the same time, or one at a time with the user is provided with a control to choose between them.

**Default output type**: If the data table and the chart are to be output one at a time, then this determines which is displayed by default.
## Controls ##
These options define the controls available in a bar at the top of the page.

**Date Filter type**: Determines whether to include a year selection control, or just restrict the dates to this current year. This defines the start and end dates provided to the report.

**Include user filter**: Determines whether to include a user\_id filter control for the report. If not selected and a user\_id is required by the report, it must be set in the **Preset Parameter Values** above.

**Drupal Permission for Manager mode**: defines the Drupal permission that users must have in order to select the user\_id filter from a drop down list. Users which do not have this permission have the user\_id set to themselves. The value of the user\_id is the CMS (Drupal) user id (not the Indicia user id). Leaving this field blank will mean nobbody has the permission to access the drop down. Using "authenticated user" will allow all logged in users access to the drop-down. When provided to a user, there is an "All Users" option in the drop down, which sets the user\_id in the report parameter list to a empty string.

**Only Users who have entered data**: This determines who appears on the list of users in the drop down. If not selected, then the list is all users registered in the CMS. If selected then the list is restricted to those users who have entered data - indicated by the CMS User ID attribute lodged against a sample.

**Sample Method**: The allocation of attributes to samples can be restricted by sample method. This parameter is used in the look up to find the CMS User ID when restricting the drop down list.

**Include location filter**: Determines whether to include a location\_id drop down filter control for the report.

**Make location list user specific**: Determines whether the list of locations in the drop down list is restricted to those allocated to the user identified by the user\_id control. If not selected, or "All Users" is selected in the user drop down, then all locations for this website\_id are displayed. If selected, the CMS User ID attribute must be defined for this location type (see below) or all location types.

**Restrict locations to type**: The term name which defines a restriction on the list of locations in the location drop down list. Optional.

**Include Sref in location filter name**: Determines whether to append the location SRef to the end of the location name in the drop down list.
## Date Axis Options ##
**Start of week definition**: Define the first day of the week.

**Week One Contains**: When using week numbers, this defines a date which week one contains.

**Restrict displayed weeks**: Restrict the weeks displayed on the chart and in the table.
## Table Options ##
**Type of header rows to include in the table output**: Determines which combination of week numbers and week commence dates is to be displayed in the table as the column headings.

**Include Total Column**: Determines whether to include a (year) total column at the right of the table, providing a year total for each row.

**Include Total Row**: Determines whether to include a week total column at the bottom of the table.
## Chart Options ##
**Chart Type**: Determines whether to display a line or a bar chart.

**Chart X-axis labels**: Determines whether to use the week number or the week commence date as X-Axis in the chart.

**Include Total Series**: Determines whether to include a weekly total series in the chart.

**Chart Width**: Width of the chart in pixels: if not set then it will automatically fill the space.

**Chart Height**: Height of the chart in pixels.

**Switchable Series**: Determines whether the user has the option to toggle the display of individual series within the chart.

## Advanced Chart Options ##
These options are more complex. For information see the JQPLOT documentation. _Note: when entering this data I have found that sometimes saving does not work unless, after entering the data, the "Edit Source" is selected before saving._

# Example Setup #
If we want
  * a calendar summary based on the "UKBMS Occurrence list for a CMS user" report for survey number 2 (this report is predefined: it takes a location defined by parameter "location\_id", has date\_from and date\_to parameters for filtering, requires a survey\_id in the presets and has a CMS User ID Sample attribute filter), displayed as either a line chart or a table,
  * where the week starts on the day of the week on which the 1st of April occurs,
  * week numbers where week one is also based on the 1st April,
  * restrict the display to week numbers -3 to 30
  * to include filters on users and sites, whose drop down list entries display Transects names and their associated coordinates (Sref)
  * to add up all the entries in the "Abundance Count" occurrence attribute, and provide totals per taxon and per week
  * this to be visible only to "managers"

then we'd set up the parameters as follows:

  * View access control: Yes.
  * Permission name for view access control: manager
  * Report Name: "UKBMS Occurrence list for a CMS user" under UKBMS
  * Preset Parameter Values:
```
survey_id=2
```
  * Vertical Axis: taxon
  * Count Column: Abundance Count
  * Output data table: Yes
  * Output chart: Yes
  * Simultaneous output formats: No
  * Default output type: Data table
  * Date Filter type: User selectable year
  * Include user filter: Yes
  * Drupal Permission for Manager mode: manager
  * Only Users who have entered data: Yes
  * Sample Method: Transect
  * Include location filter: Yes
  * Make location list user specific: Yes
  * Restrict locations to type: Transect
  * Include Sref in location filter name: Yes
  * Start of week definition: date=Apr-01
  * Week One Contains: Apr-01
  * Restrict displayed weeks: -3:30
  * Type of header rows to include in the table output: Both
  * Include Total Column: Yes
  * Include Total Row: Yes
  * Chart Type: line
  * Chart X-axis labels: Week number only.
  * Include Total Series: Yes
  * Chart Width: (Blank)
  * Chart Height: 500
  * Switchable Series: Yes
  * Advanced Chart Options: Axes Options: yaxis: min=0, showMinorTicks=No, tickInterval=5

# Restrictions #
**This form does not support an auto-generated parameters input form for missing parameters**.

The report used **must** return the field "date".

The report may optionally take parameters "date\_from" and "date\_to", which are used by the form to filter the results returned to the year currently being queried. This is advisable but not essential, as any records outside this range will be ignored, but may cause empty rows to be displayed in the table, and empty series to be displayed in the chart.

## User Filter ##
In order to use the user filter, the report must take the parameter "user\_id" to filter the returned results to those samples generated by that particular CMS User.

The use of a restricted list of users for the user drop down list requires that the CMS User ID attribute to be allocated to the samples (if this attribute is restricted to a particular sample method, then this must be set in the "Sample Method" field).

The report must be able to handle the case where the user\_id is an empty string, and take this to mean "All Users".

## Location Filter ##
Similarly, in order to use the location filter, the report must take the parameter "location\_id" to filter the returned results to that location.

The use of user specific restricted lists of locations requires that the CMS User ID attribute to be allocated to the locations (if this attribute is restricted to a particular location\_type, then this must be set in the "Restrict locations to type" field).

The report must be able to handle the case where the location\_id is an empty string, and take this to mean "All Locations".