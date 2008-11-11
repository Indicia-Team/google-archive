<!-- Generates a paginated grid for table view. Requires a number of variables passed to it:
$columns - array of column names
$pagination - the pagination object
$body - gridview_table object.
-->
<script type="text/javascript" src='/application/views/gridview.js' ></script>
<div class='pager'>
<?php echo $pagination ?>
</div>
<table id='pageGrid'>
<thead>
<tr class='headingRow'>
<?php 
foreach ($columns as $name => $dbtype) {
	echo "<th class='gvCol'>".ucwords($name)."</th>";
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

