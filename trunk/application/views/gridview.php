<!-- Generates a paginated grid for table view. Requires a number of variables passed to it:
$columns - array of column names
$pagination - the pagination object
$body - gridview_table object.
-->
<script type="text/javascript">
var filter = new HashArray();
var sort = new HashArray();
var page;
var queryString;
var baseQueryString;
var pageUrlSegmentNo;

/**
	* Refreshes everything - a shortcut
 */
function refresh(){
	buildQueryString();
	refreshGrid();
	refreshPager();
};

/**
* Refreshes the grid controller from the querystring variable.
 */
function refreshGrid(){
	$("#gvBody").load(queryString);
};

/**
	* Refreshes the pager.
 */
function refreshPager(){
	var pagerString = queryString;
	if (pagerString.charAt(pagerString.length) == '?'){
		pagerString = pagerString + 'type=pager';
	} else {
		pagerString = pagerString + '&type=pager';
	}
	$.ajax({
		url: pagerString,
		cache: false,
		success: function(a){
			$('.pager').html(a);
			pagerLinks();
		}
	});
};

function pagerLinks(){
	$('.pagination a').each(function(i){
		$(this).click(function(e){
			e.preventDefault();
			page = $.url.setUrl($(this).attr('href')).segment(pageUrlSegmentNo);
			refresh();
		});
	});
};


/**
	* Builds a new query string from the filter and sort arrays
 */
function buildQueryString() {
	var sortCols = '';
	var sortDirs = '';
	var filterCols = '';
	var filterStrings = '';

	for (var i = 0; i < sort.size(); i++){
		sortCols = sortCols + sort.getKeyAtIndex(i) + ',';
		sortDirs = sortDirs + sort.getValueAtIndex(i) + ',';
	}
	if (sortCols != '') {
		sortCols = sortCols.substring(0,sortCols.length -1);
		sortDirs = sortDirs.substring(0,sortDirs.length -1);
	}

	for (var i = 0; i < filter.size(); i++){
		filterCols = filterCols + filter.getKeyAtIndex(i) + ',';
		filterStrings = filterStrings + filter.getValueAtIndex(i) + ',';
	}
	if (filterCols != '') {
		filterCols = filterCols.substring(0,filterCols.length -1);
		filterStrings = filterStrings.substring(0,filterStrings.length -1);
	}

	queryString = baseQueryString
		+ page + '/'
		+ $.url.segment(pageUrlSegmentNo + 1) + '?'
		+ ((sortCols != '') ? 'orderby=' + sortCols 
			+ '&direction=' + sortDirs + '&': '')
		+ ((filterCols != '') ?	'columns=' + filterCols 
			+ '&filters=' + filterStrings : '');
};

$(document).ready(function(){

	// Determine the segment number used for the page - the gridview control will
	// always use the last two segments for the page and limit - number of items to
	// show per page.
	pageUrlSegmentNo = $.url.attr('path').split('/').length - 3;

	// Set the base query string
	baseQueryString = $.url.attr('protocol') + '://'
		+ $.url.attr('host')
		+ '/index.php/'
		+ $.url.segment(1) + '/'
		+ $.url.segment(2) + '_gv/'; 
	for (var i = 3; i < pageUrlSegmentNo; i++) {
		baseQueryString += $.url.segment(i) + '/'
	}
	

	//Set initial page
	page = $.url.segment(pageUrlSegmentNo);

	// Paging
	pagerLinks();

	// Sorting
	$('#pageGrid thead th').each(function(i){
		$(this).click(function(e){
			e.preventDefault();
			var a = sort.get($(this).html());
			if (a != undefined) {
				if (a == 'asc') {
					sort.unshift($(this).html(),'desc');
					$(this).removeClass('gvColAsc');
					$(this).addClass('gvColDesc');
				} else {
					sort.remove($(this).html());
					$(this).removeClass('gvColDesc');
					$(this).addClass('gvCol');
				}
			} else {
				sort.unshift($(this).html(), 'asc');
				$(this).removeClass('gvCol');
				$(this).addClass('gvColAsc');
			}
			refresh();
		});
	});

	// Filtration
	$('#gvFilter form').submit(function(e){
		e.preventDefault();
		filter.clear();
		filter.unshift($('select').val(), $('div#gvFilter input:first').val());
		refresh();
	});
});
</script>
<div class='pager'>
<?php echo $pagination ?>
</div>
<table id='pageGrid'>
<thead>
<tr class='headingRow'>
<?php 
foreach ($columns as $name => $dbtype) {
	echo "<th class='gvCol'>".$name."</th>";
} 
?>
</tr>
</thead>
<tbody id='gvBody'/>
<?php echo $body ?>
</tbody>
</table>
<div class='pager'>
<?php echo $pagination ?>
</div>
<div id='gvFilter'>
<form name='Filter' action='' method='get'>
<select name='columns'>
<?php foreach ($columns as $name => $dbtype) {
	echo "<option value='".$name."'>".$name."</option>";
} 
?>
</select>
<input type='text' name='filters'/>
<input id='gvFilterButton' type='submit' value='Filter'/>
</form>
</div>

