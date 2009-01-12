<html>
<head>
<title>Indicia external site species checklist test page</title>
<link rel="stylesheet" href="../../../media/css/ui.datepicker.css" type="text/css" media="screen">
<link rel="stylesheet" href="demo.css" type="text/css" media="screen">
<link rel="stylesheet" href="../../../media/css/jquery.autocomplete.css" />

<script type="text/javascript" src="../../../media/js/jquery-1.2.6.js"></script>
<script type="text/javascript" src="../../../media/js/ui.core.js"></script>
<script type="text/javascript" src="../../../media/js/ui.datepicker.js"></script>
<script type="text/javascript" src="../../../media/js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="../../../media/js/json2.js"></script>
</head>
<body>
<h1>Indicia Species Checklist Test</h1>
<?php
include 'data_entry_helper.php';
include 'data_entry_config.php';
?>
<form method='post'>
<?php echo data_entry_helper::species_checklist(1, array(1,2), array()); ?>
</form>
</body>
</html>
