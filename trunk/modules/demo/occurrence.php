<?php
include '../../client_helpers/data_entry_helper.php';
 $entity = null;
// If we have POST data, we're posting a comment.
if ($_POST){
 // syslog(LOG_DEBUG, print_r($_POST, true));
 $comments = data_entry_helper::wrap($_POST, 'occurrence_comment');
 $submission = array('submission' => array('entries' => array(
 array ( 'model' => $comments ))));
 $response = data_entry_helper::forward_post_to('save', $submission);
 // We look at the id parameter passed in the get string
 } else if (array_key_exists('id', $_GET)){
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
 } 
 
 function getField($fname){
   global $entity;
   if ($entity != null && array_key_exists($fname, $entity)){
     return $entity[$fname];
   } else {
     return null;
   }
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
     $("input#submitComment").click(function(e){
       $.ajax({
	 type: 'POST',
	      url: '#',
	      data: { email_address : $('div.addComment #email_address').val(),
	      comment : $('div.addComment #comment').val(),
	      occurrence_id : <?php echo $entity['id']; ?> },
	      success: function(xhr, status){},
	      error: function(xhr, status, error){}});
     });
   });
 })(jQuery);
 </script>
 <title>Occurrence Viewer: Occurrence no <?php echo getField('id'); ?></title>
 </head>
 <body>
 <h1>Occurrence Details.</h1>
 <div class='viewform'>
 <ol>
 <li><span class='label'>Taxon:</span><span class='item'><?php echo getField('taxon'); ?></span></li>
 <li><span class='label'>Date:</span><span class='item'><?php echo getField('date_start').' to '. $entity['date_end']; ?></span></li>
 <li><span class='label'>Date Type:</span><span class='item'><?php echo getField('date_type'); ?></span></li>
 <li><span class='label'>Location:</span><span class='item'><?php echo getField('location'); ?></span></li>
 <li><span class='label'>Determiner:</span><span class='item'><?php echo getField('determiner'); ?></span></li>
 <li><span class='label'>Created By:</span><span class='item'><?php echo getField('created_by'); ?></span></li>
 <li><span class='label'>Created On:</span><span class='item'><?php echo getField('created_on'); ?></span></li>
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
 <input type='text' id='email_address' name='email_address' value='' />
 <?php endif; ?>
 <textarea id='comment' name='comment' rows='5'></textarea>
 </fieldset>
 <input type='button' id='submitComment' value='Post' />
 <input type='button' id='cancelComment' value='Cancel' />
 </form>
 </div>
 </div>
 </body>
 </html>