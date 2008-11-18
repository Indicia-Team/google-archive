<form class="cmxform"  name='editList' action="<?php echo url::site().'termlists_term/save' ?>" method="POST">
<fieldset>
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<legend>Term Details</legend>
<ol>
<li>
<input type="hidden" name="term_id" id="term_id" value="<?php echo html::specialchars($model->term_id); ?>" />
<label for="term">Term Name</label>
<input id="term" name="term" value="<?php echo (($model->term_id != null) ? html::specialchars($model->term->term) : ''); ?>"/>
<?php echo html::error_message($model->getError('term')); ?>
</li>
<li>
<label for="language">Language</label>
<input id="language" readonly='readonly' value="<?php echo (($model->term_id != null && $model->term->language_id != null) ? (html::specialchars($model->term->language->language)) : ''); ?>"/>
<?php echo html::error_message($model->getError('language_id')); ?>
</li>
</ol>
</fieldset>
<fieldset>
<legend>Termlist Instance Details</legend>
<ol>
<li>
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo html::specialchars($model->parent_id); ?>" />
<label for="parent">Parent Term</label>
<input id="parent" name="parent" readonly="readonly" value="<?php echo (($model->parent_id != null) ? html::specialchars($model->parent->term->term) : ''); ?>" />
</li>
<li>
<input type="hidden" name="meaning_id" id="meaning_id" value="<?php echo html::specialchars($model->meaning_id); ?>" />
<label for="synonomy">Synonomy</label>
<textarea rows=7 id="synonomy" name="synonomy"><?php echo html::specialchars($synonomy); ?></textarea>
</fieldset>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />
<?php echo html::error_message($model->getError('deleted')); ?>
<?php echo $metadata ?>
</form>

<?php if ($model->id != '' && $table != null) { ?>
	<br />
	<h2> Child Terms </h2>
	<?php echo $table; ?>
<form class="cmxform" action="<?php echo url::site(); ?>termlists_term/create/<?php echo $model->termlist_id; ?>" method="post">
	<input type="hidden" name="parent_id" value=<?php echo $model->id ?> />
	<input type="submit" value="New Child Term" />
	</form>
<?php } ?>
