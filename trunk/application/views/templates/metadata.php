<fieldset>
<legend>Metadata</legend>
<ol>
<li>
<label for="created">Created:</label>
<input id="created_on" name="created_on" readonly='readonly' value="<?php echo html::specialchars($model->created_on); ?>" />
</li>
<li>
<label for="created_by">Created by:</label>
<input type="hidden" id="created_by_id" name="created_by_id" value="<?php echo html::specialchars($model->created_by_id); ?>" />
<input readonly='readonly' value="<?php echo (($model->created_by_id != null) ? (html::specialchars($model->created_by->person->surname)) : ''); ?>" />
</li>
<li>
<label for="last_update">Last Updated:</label>
<input id="last_update" name="created_on" readonly='readonly' value="<?php echo html::specialchars($model->updated_on); ?>" />
</li>
<li>
<label for="updated_by">Updated by:</label>
<input type="hidden" name="updated_by_id" id="updated_by_id" value="<?php echo html::specialchars($model->updated_by_id); ?>" />
<input readonly='readonly' value="<?php echo (($model->updated_by_id != null) ? (html::specialchars($model->updated_by->person->surname)) : ''); ?>" />
</li>
</ol>
</fieldset>
