# Introduction #

This page details some of the functionality used to improve the performance of your data entry pages. Before starting, you should have a working Indicia data entry page, for example by following the [Building a basic PHP data entry page](TutorialBuildingBasicPage.md) tutorial.



&lt;STRONG&gt;

NB

&lt;/STRONG&gt;

 As detailed below, caching of data is switched 

&lt;STRONG&gt;

ON

&lt;/STRONG&gt;

 by default.

## Caching of control data ##
When you create a control on your web page which interacts with Indicia's data, this requires your website to issue a request to the Indicia Warehouse using a web service. Indicia then queries the warehouse and returns the results to you. An example might be a select box populated from a termlist such as DAFOR abundance codes. Although this is normally nearly instantaneous, when your web page has many controls, or when there are lots of users loading the pages simultaneously, this approach can slow things down.

In order to speed things up and also reduce loading on the Indicia Warehouse, a facility is provided to cache the data calls made for the following controls: listbox, select, location\_select, species\_checklist, list\_in\_template, checkbox group and radio group. This data caching can be set at a website level, or this global setting may be overridden at an individual control level.

When data caching takes place, a local copy of the data is stored following a request for data to the Indicia Warehouse. This local copy is then used until the timeout period expires, after which point the next time the data is required a new request is made to the Indicia Warehouse (which is then stored locally, etc).

### Setting the Website-wide Data Cache default ###
You can set the default data cache status for your website by setting a specific global variable, placing this code in a PHP block:
```
global $indicia_cachetimeout;
$indicia_cachetimeout=7200;
```
The value assigned to the variable is the length of time before the cached data expires and a new set is retrieved from the Indicia Warehouse, measured in seconds. The example given above is two hours.



&lt;STRONG&gt;

If you do not set this global variable, the website-wide data caching defaults to a time period of one hour.

&lt;/STRONG&gt;



If you set this global variable, and the value is not numeric, or if the value is less than or equal to zero, then the website-wide data caching will be 'off'. This means that every time a control is generated, the required data will be retrieved from the Indicia Warehouse.

### Setting the Data Cache functionality at individual Control level ###
For the controls mentioned above (listbox, select, location\_select, species\_checklist, list\_in\_template, checkbox group and radio group) there is an additional entry that may be added to the options array, as follows:
```
echo data_entry_helper::select(array(
    'label'=>'Survey',
    'fieldname'=>'sample:survey_id',
    'table'=>'survey',
    'captionField'=>'title',
    'valueField'=>'id',
    'extraParams' => $readAuth,
    'cachetimeout' => 60)); 
```
Again, the value assigned to the 'cachetimeout' entry is the length of time before the cached data expires and a new set is retrieved from the Indicia Warehouse, measured in seconds. The example given above is one minute.

When provided, this setting takes priority over the Website-wide cache default.

If you do not provide this entry, it will default to the website-wide setting.

If you provide this entry, but if the value is not numeric, or if the value is less than or equal to zero, then the data caching will be 'off' for this control.  This means that every time this control is generated, the required data will be retrieved from the Indicia Warehouse.

### Notes ###
The data is cached at a request level: if several controls (e.g. on different pages) request the same data, then they will use the same cached data.

The cache files are currently stored in the upload directory. **You must make sure that the upload directory exists or caching is disabled. By default, the location for the upload directory is in the same folder as the client\_helpers folder which you are loading data\_entry\_helper.php from.**

The autocomplete and treeview controls are excluded from the list of controls for which data may be cached, as they request their data dynamically.