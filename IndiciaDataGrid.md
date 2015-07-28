# Introduction #

IndiciaDataGrid is a plugin to jQuery allowing simple client-side lookup and display of data held in the Indicia core. Access is done through the data services.


# Dependencies #

The plugin depends on jQuery (built against version 1.3.1) and on hasharray.js, an implementation of an ordered associative array (despite the name, it does not use hashes).

# Usage #

## Files ##

Four files are used to run the datagrid (paths are relative to indicia root):

  * client\_helpers/datagrid.js
  * media/js/jquery-1.3.1.js (or newer version)
  * media/js/hasharray.js
  * media/css/datagrid.css

These should be referenced in the head of the page that will display the datagrid:

```

<link rel='stylesheet' type='text/css' href='../../media/css/datagrid.css' />
<script type='text/javascript' src='../../media/js/jquery-1.3.1.js' ></script>
<script type='text/javascript' src='../../media/js/hasharray.js' ></script>
<script type='text/javascript' src='../../client_helpers/datagrid.js' ></script>

```


## Initialisation ##

The datagrid can be attached to any container-level DOM element - typically a div will be used for this purpose. We call the datagrid as follows:

```
(function($) {
$(document).ready(function(){
$('_identifier_').indiciaDataGrid(_modelname_, _options_);
});
})(jQuery);
```

(In order to play nice with other javascript libraries, indicia demo code avoids the use of $ for the jQuery object. In order to avoid typing jQuery all the time, we use a closure to get it back again.)

_modelname_ should be the name of the model to look up data for from the data services - i.e. the call will be to index.php/services/data/_modelname_.

_options_ is optional and should be an options object.

### Options ###

The options object takes the following parameters:

  * cssHeader
  * cssSortHeader
  * cssAsc
  * cssDesc
  * indiciaSvc
  * dataColumns
  * actionColumns
  * itemsPerPage
  * multiSort
  * parameters
  * formatPager

cssHeader, cssSortHeader, cssAsc and cssDesc let you specify the css classes for headers, sortable headers, headers sorting ascending and headers sorting descending, respectively.

indiciaSvc lets you specify the URL for the indicia data service to be used for fetching data.

dataColumns accepts an array of columns to display. If present, only columns named here will be displayed in the grid.

actionColumns is used to add further columns to the grid not representing data but providing links to other pages that may depend on items in the grid. It takes an object used as a hash. The key should be the text to display in the column. The value is the address of the link to use. Segments of the form £column\_name£ in the value will be replaced with the record's value for that column.

itemsPerPage is the number of records to display on each page.

multiSort is a boolean flag indicating whether sorting on multiple columns should be enabled. Currently, this is not supported by the indicia data services, so enabling this will just break things.

parameters accepts an object (used as a hash) of key/value pairs to pass to the data services.

formatPager takes a function used to format the pager object that's displayed on the screen. The function should take one parameter (foo), the indiciaDataGrid object itself. Whilst full access to this object is granted, the items of most use to the pager are:

`foo.page` - number of the page currently being displayed.
`foo.recordCount` - total number of records.
`foo.settings.itemsPerPage` - number of records to display per page.

The function should return a string which will be treated as html and injected into the page. The css classes 'first, previous, next and last' should be used in the obvious way to create links to other pages. At the moment there's no way of specifying an arbitrary page number, though this could be added.

Default settings for these parameters are given:

```
{
	cssHeader: "header",
	   cssSortHeader: "headerSort",
	   cssAsc: "headerSortUp",
	   cssDesc: "headerSortDown",
	   indiciaSvc: "http://localhost/indicia/index.php/services/data",
	   dataColumns: null,
	   actionColumns: {},
	   itemsPerPage: 10,
	   multiSort: false,
	   parameters: {},
	   formatPager: formatPager
      };
```

where formatPager is a function defined within the indiciaDataGrid plugin:

```
      function formatPager(div){
	var pageNo = div.page;
	var totalPages = Math.ceil(div.recordCount / div.settings.itemsPerPage);
	var pagerString = (pageNo == 1) ? "<< | < | " : "<a href='' class='first'>&lt;&lt;</a> | <a href='' class='previous'>&lt;</a> | ";
	pagerString += (pageNo == totalPages) ? pageNo + " | > | >>" : pageNo + " | <a href='' class='next'>&gt;</a> | <a href='' class='last'>&gt;&gt;</a>";
	return pagerString;
      }

```

## Example ##

```

(function($) {
$(document).ready(function(){
$('div#grid').indiciaDataGrid('occurrence', {actionColumns: {view : "occurrence.php?id=£id£"}});
});
})(jQuery);

```


