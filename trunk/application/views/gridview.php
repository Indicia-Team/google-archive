<!-- Generates a paginated grid for table view. Requires a number of variables passed to it:
$columns - array of column names
$pagination - the pagination object
$body - gridview_table object.
-->
<script type="text/javascript" src='<?php echo url::base() ?>application/views/gridview.js' ></script>
<div id='gvFilter'>
<form name='Filter' action='' method='get'>
Search for
<input type='text' name='filters'/>
in <select name='columns'>
<?php foreach ($columns as $name => $dbtype) {
	echo "<option value='".$name."'>".$name."</option>";
}
?>
</select>
<input id='gvFilterButton' type='submit' value='Search'/>
</form>
</div>
<table id='pageGrid'>
<thead>
<tr class='headingRow'>
<?php
foreach ($columns as $name => $dbtype) {
	echo "<th class='gvCol'>".ucwords($name)."</th>";
}
foreach ($actionColumns as $name => $action) {
	echo "<th class='gvAction'>".ucwords($name)."</th>";
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
