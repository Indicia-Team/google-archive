<?php

include 'helper_config.php';
global $config;

$uploadpath = $config['uploadpath'];

$fname = $_FILES['imgUploadFile']['tmp_name'];
$fext = array_pop(explode(".", $fname));
$bname = basename($fname, ".$fext");

// Generate a file id to store the image as
$destination = time().rand(0,1000).".".$fext;

if (move_uploaded_file($fname, $uploadpath.$destination)) {
	// Format an image tag and return it
	echo "<img src='$uploadpath.$destination' />";

} else {
	// Return an error message
	echo "<p>There has been an error uploading this file.</p>";
}


