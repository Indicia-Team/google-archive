<h1>Subset of: <?php echo $model->parent->title ?></h1>
<form class="cmxform"  name='editList' action='' method=post>
<fieldset>
<legend>List Details</legend>
<ol>
<li>
<label for="title">Title</label>
<input id="title" />
</li>
<li>
<label for="description">Description</label>
<textarea rows=7 id="description"></textarea>
</li>
<li>
<label for="website_id">Owned by</label>
<input id="website_id" readonly='readonly'/>
</li>
</ol>
<input type="submit" value="Submit" />
<input type="button" value="Delete" />
</fieldset>
<fieldset>
<legend>Metadata</legend>
<ol>
<li>
<label for="created">Created:</label>
<input id="created" readonly='readonly'/>
</li>
<li>
<label for="created_by">Created by:</label>
<input id="created_by" readonly='readonly'/>
</li>
<li>
<label for="last_update">Last Updated:</label>
<input id="last_update" readonly='readonly' value=<?php echo "'Waiting for users module'" ?>/>
</li>
<li>
<label for="updated_by">Updated by:</label>
<input id="updated_by" readonly='readonly' value=<?php echo "'Waiting for users module'" ?>/>
</li>
</ol>
</fieldset>
</form>
<?php echo $termtable ?>
