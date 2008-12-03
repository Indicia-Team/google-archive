<?php echo html::script(array(
	'media/js/jquery.ajaxQueue.js',
	'media/js/jquery.bgiframe.min.js',
	'media/js/thickbox-compressd.js',
	'media/js/jquery.autocomplete.js'
), FALSE); ?>
<script type="text/javascript" >
$(document).ready(function() {
	$("input#parent").autocomplete("<?php echo url::site() ?>index.php/services/data/location", {
		minChars : 1,
		mustMatch : true,
		extraParams : {
			orderby : "name",
			mode : "json"
		},
		parse: function(data) {
			var results = [];
			var obj = JSON.parse(data);
			$.each(obj, function(i, item) {
				results[results.length] = {
					'data' : item,
					'value' : item.id,
					'result' : item.name };
			});
			return results;
		},
		formatItem: function(item) {
			return item.name;
		},
		formatResult: function(item) {
			return item.id;
		}
	});
	$("input#parent").result(function(event, data){
		$("input#parent_id").attr('value', data.id);
	});
});
</script>
<p>This page allows you to specify the details of a location.</p>
<form class="cmxform" action="<?php echo url::site().'location/save'; ?>" method="post">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Location details</legend>
<ol>
<li>
<label for="name">Name</label>
<input id="name" name="name" value="<?php echo html::specialchars($model->name); ?>" />
<?php echo html::error_message($model->getError('name')); ?>
</li>
<li>
<label for="code">Code</label>
<input id="code" name="code" value="<?php echo html::specialchars($model->code); ?>" />
<?php echo html::error_message($model->getError('code')); ?>
</li>
<li>
<label for="centroid_sref">Spatial Ref:</label>
<input id="centroid_sref" name="centroid_sref" value="<?php echo html::specialchars($model->centroid_sref); ?>" />
<?php echo html::error_message($model->getError('centroid_sref')); ?>
</li>
<li>
<label for="centroid_sref_system">Spatial Ref System:</label>
<select class="narrow" id="centroid_sref_system" name="centroid_sref_system" value="<?php echo html::specialchars($model->centroid_sref_system); ?>">
<?php foreach (kohana::config('sref_notations.sref_notations') as $notation=>$caption) {
	echo '<option value="'.$notation.'">'.$caption.'</option>"';}
?>
</select>
<?php echo html::error_message($model->getError('centroid_sref_system')); ?>
</li>
<li>
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo html::specialchars($model->parent_id); ?>" />
<label for="parent">Parent Location</label>
<input id="parent" name="parent" value="<?php echo (($model->parent_id != null) ? html::specialchars(ORM::factory('location', $model->parent_id)->name) : ''); ?>" />
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" value="Save" name="submit"/>
</form>
