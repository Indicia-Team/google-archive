<p>This page allows you to specify the details of a taxon group..</p>
<form class="cmxform" action="<?php echo url::site().'taxon_group/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($taxon_group->id); ?>" />
<fieldset>
<legend>Taxon Group details</legend>
<ol>
<li>
<label for="title">Title</label>
<input id="title" name="title" value="<?php echo html::specialchars($taxon_group->title); ?>" />
<?php echo html::error_message($taxon_group->getError('title')); ?>
</li>
</ol>
</fieldset>
<input type="submit" value="Save" />
</form>