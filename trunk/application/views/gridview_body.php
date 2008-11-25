<?php
$i = 0;
foreach ($table as $item) {
	echo "<tr class='";
	echo ($i%2 == 0) ? "evenRow" : "oddRow";
	echo "'>";
	$fields = array_intersect_key($item->as_array(), $columns); 
	foreach ($fields as $field) {
		echo "<td>";
		if ($field!==NULL)
			echo $field;
		echo "</td>";
	}
	foreach ($actionColumns as $name => $action) {
		echo "<td>";
		$action = preg_replace("/�([a-zA-Z_\-]+)�/e", "\$item->__get('$1')", $action);
		echo html::anchor($action, $name);
		echo "</td>";
	}
	$i++;
	echo "</tr>";
}
?>
