<div id='attribute_load'>
<fieldset>
<legend>Reuse Attribute</legend>
<ol>
<li>
<label class="wide" for="load_attr_id">Existing Attribute</label>
<select id="load_attr_id" name="load_attr_id" >
	<option value=''>&lt;Please Select&gt;</option>
<?php
	$public_attrs = ORM::factory('occurrence_attribute')->where('public','t')->orderby('caption','asc')->find_all();
	$website_attrs = ORM::factory('occurrence_attributes_website')->where('website_id',$website_id)->find_all();
	$website_list = array();
	foreach ($website_attrs as $website_attr) {
		$attr = ORM::factory('occurrence_attribute', $website_attr->occurrence_attribute_id);
		echo '	<option value="'.$attr->id.'">'.$attr->caption.'</option>';
		$website_list[] = $attr->id;
	}
	foreach ($public_attrs as $attr) {
		if (!in_array($attr->id, $website_list))
			echo '	<option value="'.$attr->id.'">'.$attr->caption.' (Public)</option>';
	}
?>
</select>
</li>
<input type="submit" value="Reuse" name="submit"/>
</ol>
</fieldset>
</div>
