<html>
<head>
<link rel='stylesheet' type='text/css' href='../../media/css/datagrid.css' />
<script type='text/javascript' src='../../media/js/jquery-1.3.1.js' ></script>
<script type='text/javascript' src='../../media/js/hasharray.js' ></script>
<script type='text/javascript' src='../../client_helpers/datagrid.js' ></script>
<script type='text/javascript'>
(function($) {
$(document).ready(function(){
$('div#grid').indiciaDataGrid('occurrence', { actionColumns: Array(Array("edit", "data_entry/test_data_entry.php?id=£id£"))});
});
})(jQuery);
</script>
<title>Occurrence Grid Demo</title>
</head>
<body>
<div id='grid'>
</div>
</body>
</html>