<?php 
$i = 0;
foreach ($table as $item) {
	echo "<tr class='";
	echo ($i%2 == 0) ? "evenRow" : "oddRow";
	echo "'>";
	foreach (array_intersect_key($item->as_array(), $columns) as $field) {
		echo "<td>".$field."</td>";
	}
	$i++;
	echo "</tr>";
}
?>
