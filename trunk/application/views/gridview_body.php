<tbody>
<?php 
$i = 0;
foreach ($table as $item) {
		echo "<tr class='";
			echo ($i%2 == 0) ? "evenRow" : "oddRow";
			echo "'>";

				foreach ($item->as_array() as $field) {
							echo "<td>".$field."</td>";
								}
				$i++;
				echo "</tr>";
}
?>
</tbody>
