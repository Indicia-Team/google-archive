<?php
include '../../client_helpers/data_entry_helper.php';
// We look at the id parameter passed in the get string
if (array_key_exists('id', $_GET)){
  $url = 'http://localhost/indicia/index.php/services/data/occurrence/'.$_GET['id'];
  $url .= "?mode=json&view=detail";
  $session = curl_init($url);
  curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
  $entity = json_decode(curl_exec($session), true);
  $entity = $entity[0];
} else {
  $entity = null;
}
?>
<html>
<head>
<link rel='stylesheet' href='../../media/css/viewform.css' />
<title>Occurrence Viewer: Occurrence no <?php echo $entity['id']; ?></title>
</head>
<body>
<h1>Occurrence Details.</h1>
<div class='viewform'>
<ol>
<li><span class='label'>Taxon:</span><span class='item'><?php echo $entity['taxon']; ?></span></li>
<li><span class='label'>Date:</span><span class='item'><?php echo $entity['date_start'].' to '. $entity['date_end']; ?></span></li>
<li><span class='label'>Date Type:</span><span class='item'><?php echo $entity['date_type']; ?></span></li>
<li><span class='label'>Location:</span><span class='item'><?php echo $entity['location']; ?></span></li>
<li><span class='label'>Determiner:</span><span class='item'><?php echo $entity['determiner']; ?></span></li>
<li><span class='label'>Created By:</span><span class='item'><?php echo $entity['created_by']; ?></span></li>
<li><span class='label'>Created On:</span><span class='item'><?php echo $entity['created_on']; ?></span></li>
</ol>
</div>
</body>
</html>