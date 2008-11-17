<div class="termlist">
<?php echo $table ?>
<br/>
<form action="<?php echo url::site().'term/create/'.$this->termlist_id; ?>" method="post">
<input type="submit" value="New term" />
</form>
</div>
