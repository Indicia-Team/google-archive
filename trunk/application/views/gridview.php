<!-- Generates a paginated grid for table view. Requires a number of variables passed to it:
$columns - array of column names
$pagination - the pagination object
$body - gridview_table object.
-->
<script type="text/javascript">

</script>
<?php echo $pagination ?>
<table class='pageGrid'>
<thead>
<tr class='headingRow'>
<?php 
foreach ($columns as $name => $dbtype) {
	echo "<th>".$name."</th>";
} 
?>
</tr>
</thead>
<?php echo $body ?>
</table>
<?php echo $pagination ?>
