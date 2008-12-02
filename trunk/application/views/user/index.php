<div class="termlist">
<?php echo $table ?>
<br />
Note that all Users must have an associated 'Person' - in order to create a new user the 'Person' must exist first.<br />
<form action="<?php echo url::site(); ?>person/create_from_user">
<input type="submit" value="New person" />
</form>
</div>