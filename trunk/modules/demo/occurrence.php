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
  
  // Now grab the list of occurrence comments.
  $url = 'http://localhost/indicia/index.php/services/data/occurrence_comment';
  $url .= "?mode=json&occurrence_id=".$_GET['id'];
  $csess = curl_init($url);
  curl_setopt($csess, CURLOPT_RETURNTRANSFER, true);
  $comments = json_decode(curl_exec($csess), true);
} else {
  $entity = null;
}
?>
<html>
<head>
<link rel='stylesheet' href='../../media/css/viewform.css' />
<link rel='stylesheet' href='../../media/css/comments.css' />
<script type="text/javascript" src="../../media/js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="../../media/js/ui.core.js"></script>
<script type='text/javascript'>
(function($){
$(document).ready(function(){
  $("div#addComment").hide();
  $("div#addCommentToggle").click(function(e){
    $("div#addComment").toggle('slow');
  });
});
})(jQuery);
</script>
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
<div id='comments'>
<?php
foreach ($comments as $comment){
 echo "<div class='comment'>";
 echo "<div class='header'>";
 echo "<span class='user'>";
 echo $comment['username'];
 echo "</span>";
 echo "<span class='timestamp'>";
 echo $comment['updated_on'];
 echo "</span>";
 echo "</div>";
 echo "<div class='commentText'>";
 echo $comment['comment'];
 echo "</div>";
 echo "</div>";
 }
 ?>
 <div id='addCommentToggle'>Add Comment</div>
 <div id='addComment'>
 <form>
 <fieldset>
 <legend>Add New Comment.</legend>
 <!-- pointless check here - eventually we replace this with a check of whether a user is logged in -->
 <?php if (false): ?>
 <!-- Here we put details of the logged in user -->
 <?php else: ?>
 <label for='email_address'>E-mail:</label>
 <input type='text' name='email_address' value='' />
 <?php endif; ?>
 <textarea class='comment' name='comment' rows='5'></textarea>
 </fieldset>
 <input type='button' value='Post' />
 <input type='button' value='Cancel' />
 </form>
 </div>
 </div>
 </body>
 </html>