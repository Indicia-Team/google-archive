<p>This page allows you to specify a new custom attribute for an <?php echo $name; ?>.</p>
<form class="cmxform" action="<?php echo url::site().$processpath; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<input type="hidden" name="website_id" id="website_id" value="<?php echo html::specialchars($website_id); ?>" />
<input type="hidden" name="survey_id" id="survey_id" value="<?php echo html::specialchars($survey_id); ?>" />
<input type="hidden" name="disabled_input" id="disabled_input" value="<?php echo html::specialchars($disabled_input); ?>" />
<?php echo $attribute_load ?>
<fieldset>
<legend><?php echo $name; ?> Attribute details</legend>
<ol>
<li>
<label for="caption">Caption</label>
<input id="caption" name="caption" value="<?php echo html::specialchars($model->caption); ?>" <?php echo $enabled ?>/>
<?php echo html::error_message($model->getError('caption')); ?>
</li>
<li>
<label for="data_type">Data Type</label>
<select id="data_type" name="data_type" <?php echo $enabled ?>>
	<option value=''>&lt;Please Select&gt;</option>
<?php
$optionlist = array('T' => 'Text'
				,'L' => 'Lookup List'
				,'I' => 'Integer'
				,'F' => 'Float'
				,'D' => 'Specific Date'
				,'V' => 'Vague Date'
			);
	foreach ($optionlist as $key => $option) {
		echo '	<option value="'.$key.'" ';
		if ($key==$model->data_type)
			echo 'selected="selected" ';
		echo '>'.$option.'</option>';
	}
?>
</select>
<?php echo html::error_message($model->getError('data_type')); ?>
</li>
<li>
<label for="termlist_id">Termlist</label>
<select id="termlist_id" name="termlist_id" <?php echo $enabled ?>>
	<option value=''>&lt;Please Select&gt;</option>
<?php
	if (!is_null($this->auth_filter))
		$termlists = ORM::factory('termlist')->in('website_id',$this->auth_filter['values'])->orderby('title','asc')->find_all();
	else
		$termlists = ORM::factory('termlist')->orderby('title','asc')->find_all();
	foreach ($termlists as $termlist) {
		echo '	<option value="'.$termlist->id.'" ';
		if ($termlist->id==$model->termlist_id)
			echo 'selected="selected" ';
		echo '>'.$termlist->title.'</option>';
	}
?>
</select>
<?php echo html::error_message($model->getError('termlist_id')); ?>
</li>
<li>
<label class="wide" for="multi_value">Allow Multiple Values</label>
<?php echo form::checkbox('multi_value', TRUE, isset($model->multi_value) AND ($model->multi_value == 't'), 'class="vnarrow" '.$enabled ) ?>
</li>
<li>
<label class="wide" for="public">Available to other Websites</label>
<?php echo form::checkbox('public', TRUE, isset($model->public) AND ($model->public == 't'), 'class="vnarrow" '.$enabled ) ?>
</li>

<li><b>Validation Rules:</b></li>
<li><label class="narrow" for="valid_required">Required</label><?php echo form::checkbox('valid_required', TRUE, isset($model->valid_required) AND ($model->valid_required == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_length">Length</label><?php echo form::checkbox('valid_length', TRUE, isset($model->valid_length) AND ($model->valid_length == 't'), 'class="vnarrow" '.$enabled ) ?><input class="narrow" id="valid_length_min" name="valid_length_min" value="<?php echo html::specialchars($model->valid_length_min); ?>" <?php echo $enabled?>/> - <input class="narrow" id="valid_length_max" name="valid_length_max" value="<?php echo html::specialchars($model->valid_length_max); ?>" <?php echo $enabled?>/>
<?php echo html::error_message($model->getError('valid_length')); ?>
</li>
<li><label class="narrow" for="valid_alpha">Alphabetic</label><?php echo form::checkbox('valid_alpha', TRUE, isset($model->valid_alpha) AND ($model->valid_alpha == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_email">Email Address</label><?php echo form::checkbox('valid_email', TRUE, isset($model->valid_email) AND ($model->valid_email == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_url">URL</label><?php echo form::checkbox('valid_url', TRUE, isset($model->valid_url) AND ($model->valid_url == 't'), 'class="vnarrow" '.$enabled  ) ?></li>
<li><label class="narrow" for="valid_alpha_numeric">Alphanumeric</label><?php echo form::checkbox('valid_alpha_numeric', TRUE, isset($model->valid_alpha_numeric) AND ($model->valid_alpha_numeric == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_numeric">Numeric</label><?php echo form::checkbox('valid_numeric', TRUE, isset($model->valid_numeric) AND ($model->valid_numeric == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_standard_text">Standard Text</label><?php echo form::checkbox('valid_standard_text', TRUE, isset($model->valid_standard_text) AND ($model->valid_standard_text == 't'), 'class="vnarrow" '.$enabled ) ?></li>
<li><label class="narrow" for="valid_decimal">Formatted Decimal</label><?php echo form::checkbox('valid_decimal', TRUE, isset($model->valid_decimal) AND ($model->valid_decimal == 't'), 'class="vnarrow" '.$enabled ) ?><input class="narrow" id="valid_dec_format" name="valid_dec_format" value="<?php echo html::specialchars($model->valid_dec_format); ?>" <?php echo $enabled?>/>
<?php echo html::error_message($model->getError('valid_decimal')); ?>
</li>
<li><label class="narrow" for="valid_regex">Regular Expression</label><?php echo form::checkbox('valid_regex', TRUE, isset($model->valid_regex) AND ($model->valid_regex == 't'), 'class="vnarrow" '.$enabled ) ?><input class="narrow" id="valid_regex_format" name="valid_regex_format" value="<?php echo html::specialchars($model->valid_regex_format); ?>" <?php echo $enabled?>/>
<?php echo html::error_message($model->getError('valid_regex')); ?>
</li>
<li><label class="narrow" for="valid_min">Minimum value</label><?php echo form::checkbox('valid_min', TRUE, isset($model->valid_min) AND ($model->valid_min == 't'), 'class="vnarrow" '.$enabled ) ?><input class="narrow" id="valid_min_value" name="valid_min_value" value="<?php echo html::specialchars($model->valid_min_value); ?>" <?php echo $enabled?>/>
<?php echo html::error_message($model->getError('valid_min')); ?>
</li>
<li><label class="narrow" for="valid_max">Maximum value</label><?php echo form::checkbox('valid_max', TRUE, isset($model->valid_max) AND ($model->valid_max == 't'), 'class="vnarrow" '.$enabled ) ?><input class="narrow" id="valid_max_value" name="valid_max_value" value="<?php echo html::specialchars($model->valid_max_value); ?>" <?php echo $enabled?>/>
<?php echo html::error_message($model->getError('valid_max')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" value="Save" name="submit"/>
</form>
