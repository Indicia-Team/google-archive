$(document).ready(function() {
  
function enable_map() {
  var enable=$('#edit-field-summary-report-grouping-value-region').attr('checked');
  if (enable) {
    $('#edit-field-summary-report-display-value-map-wrapper').show();
  } else {
    $('#edit-field-summary-report-display-value-map-wrapper').hide();
    // ensure option is not checked as invalid
    $('#edit-field-summary-report-display-value-map').attr('checked', false);
  }
}

function enable_map_settings() {
  var enable=$('#edit-field-summary-report-display-value-map').attr('checked');
  if (enable) {
    $('.group-map-settings').show();
  } else {
    $('.group-map-settings').hide();
  }
}
 
$('#edit-field-summary-report-grouping-value-region').change(enable_map);
$('#edit-field-summary-report-grouping-value-species-group').change(enable_map);
$('#edit-field-summary-report-grouping-value-year').change(enable_map);
$('#edit-field-summary-report-grouping-value-month').change(enable_map);

$('#edit-field-summary-report-display-value-map').change(enable_map_settings);
$('#edit-field-summary-report-display-value-pie').change(enable_map_settings);
$('#edit-field-summary-report-display-value-bar').change(enable_map_settings);
$('#edit-field-summary-report-display-value-table').change(enable_map_settings);

// set the initial state
enable_map();
enable_map_settings()

});