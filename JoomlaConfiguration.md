# Introduction #

This page explains how to configure an installed Joomla Indicia component. Before starting you should be logged into your Joomla administration interface and have [installed the Joomla component](JoomlaInstallation.md).

# Details #

To add the component to a menu item, create a new menu item in one of the menus by selecting the menu you want to add to under the Menus menu, then click the New button. Next, select Internal Link\Indicia\Speciespicker\Default Layout. Give your menu item a title such as the name of your survey, then expand the Parameters (Component) section on the right. You need to specify each of the settings as follows:

  * **Title** - title displayed on the first page.
  * **Introduction** - introductory text displayed on first page.
  * **Map Introduction** - introductory text on the distribution map displayed after contribution of a record.
  * **Filtered by taxon text** - additional text appended to the map introduction when the map is filtered by a species. The species name should be denoted in the text by %s.
  * **Thank you for your record** - a thank you message displayed after contribution of a record.
  * **Taxon List ID** - the Indicia ID of the taxon list that data can be entered for.
  * **Method of selecting a species** - select List or Auto-complete. List means that each species will be displayed as a button with a picture if there is one. Auto-complete renders a text box to type the species name, with an auto-complete for the species name.
  * **Google Maps Key** - your Google Maps API key.
  * **Preferred Area** - use this to configure the searches the user performs when finding a place on a map. For example, if you want to enforce a search in a county, type the county name here. If you don't need to restrict it in this way, set it to the same as Preferred Country.
  * **Preferred Country** - as the preferred area, but this is the country that the place search will prioritise.
  * **Initial Map X** - the x coordinate of the initial centre of the map used to select spatial references or for the distribution map. Uses Spherical Mercator projection.
  * **Initial Map Y** - the y coordinate of the initial centre of the map used to select spatial references or for the distribution map. Uses Spherical Mercator projection.
  * **Initial Map Zoom** - the initial zoom level of the map, between 0 and 18.
  * **Feature Type for map** - the name of the feature type you have exposed in GeoServer for drawing a distribution map.
  * **GeoServer URL** - the URL of your GeoServer installation, including the trailing slash.
  * **Website ID** - your Indicia website registration's ID number.
  * **Website Password** - your Indicia website registration's password.
  * **Survey ID** - the ID of the Indicia survey you want to capture records into. If blank, then the component will ask the user to select a survey during data entry.
  * **Indicia Core URL** - the URL of your Indicia installation, including the trailing slash (but not Index.php)
  * **Custom Attributes** - a list of custom attributes to add to the wizard, one per line. This is currently able to support creating a custom radio group or drop down selector. The format of each row is
```
 Field Display Label|attribute ID|radio_group or select|termlist ID
```
> The attribute ID should be specified as smpAttr: or occAttr: followed by the unique ID of the attribute in Indicia. The termlist ID is the list used to provide the values for the select or radio group. A valid custom attribute specification is
```
 Population size|smpAttr:1|radio_group|3
```

Note that for each of the parameters that represent text to display to the user, the actual text displayed is controlled by the language files (intially in the Joomla installation languages\en-GB\en-GB.com\_indicia.ini file). For example, you can specify just "Intro Text" in the parameter, then put into the ini file:
```
INTRO TEXT=Welcome to the wildlife recording page.
```
For other languages, replace en-GB with the RFC 4646 language tag in the folder and file name.