<!-- Generates a paginated grid for table view. Requires a number of variables passed to it:
$columns - array of column names
$pagination - the pagination object
$body - gridview_table object.
-->
<script type="text/javascript">
$(document).ready(function(){
	$('#pagination a').each(function (i){
		this.click(function() {
			('#pageGrid').load('index.php/' + jQuery.url.segment(2) + '/'
				+ this.html() + '/' + jQuery.url.segment(4));
			return false;
		});
	});
});
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
