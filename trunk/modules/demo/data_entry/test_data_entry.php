<html>
<head>
<title>Indicia external site data entry test page</title>
</head>
<body>
<h1>Indicia Data entry test</h1>
<p>Note that this page requires the PHP curl extension to send requests to the Indicia server.</p>
<?php

	// PHP to catch and submit the POST data from the form
	if (array_key_exists('date', $_POST)) {
		echo '<p>Data submitted</p>';

		include 'data_entry_helper.php';
		$response = data_entry_helper::forward_post_to(
				'http://localhost/indicia/index.php/services/data', 'sample'
		);

		if (array_key_exists('error',$response)) {
			echo 'An error occurred when the data was submitted.';
			echo '<p class="error">'.$response['error'].'</p>';
		} elseif (array_key_exists('warning',$response)) {
			echo 'A warning occurred when the data was submitted.';
			echo '<p class="error">'.$response['error'].'</p>';
		} elseif (array_key_exists('success',$response)) {
			echo 'Data was successfully inserted. The record\'s ID is'.
						$response['error'].'</p>';
		}


	}

?>
<form method="post">
<label for="date">Date:</label>
<input name="date" /><br />
<label for="entered_sref">Spatial Reference:</label>
<input name="entered_sref" /><br />
<input type="hidden" value="osgb" name="entered_sref_system">
<br />
<input type="submit" value="Save" />
</form>
