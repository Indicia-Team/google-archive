<p>This page allows you to specify a new website that will use the services provided by this Indicia Core Module instance.</p>
<form class="cmxform" action="<?php echo url::site().'website/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($form['id']) ?>" />
<fieldset>
<legend>Website details</legend>
<ol>
<li>
<label for="title">Title</label>
<input id="title" name="title" value="<?php echo html::specialchars($form['title']) ?>" />
</li>
<li>
<label for="description">Description</label>
<textarea rows="7" id="description" name="description"><?php echo html::specialchars($form['description']); ?></textarea>
</li>
</ol>
</fieldset>
<input type="submit" value="Save" />
</form>
