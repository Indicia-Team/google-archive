<?php
$allowed = array(
  'data_entry/simple_data_entry.php',
  'data_entry/test_data_entry.php',
  'data_entry/species_checklist.php',
  'map_data_entry_helper.php',
  'map_javascript_classes.php',
  'occurrence_grid.php',
  'occurrence.php',
  'map_polygon_capture.php',
  'valid.php',
  'advanced_data_entry/header.php',
  'advanced_data_entry/list.php',
  'advanced_data_entry/save.php',
  'advanced_data_entry/success.php',
  'advanced_data_entry/map.php',
);

if ($_GET['file']) {
  if (in_array($_GET['file'], $allowed)) {
    highlight_file($_GET['file']);
  } else {
    echo "file not in list of allowed source code files";
  }
}
?>
