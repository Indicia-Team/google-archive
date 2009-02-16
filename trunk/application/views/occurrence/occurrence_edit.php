<?php echo html::script(array(
	'media/js/jquery.ajaxQueue.js',
	'media/js/jquery.bgiframe.min.js',
	'media/js/thickbox-compressd.js',
	'media/js/jquery.autocomplete.js'
), FALSE); ?>
<script type="text/javascript" >
$(document).ready(function() {
	$("input#determiner").autocomplete("<?php echo url::site() ?>services/data/person", {
		minChars : 1,
		mustMatch : true,
		extraParams : {
			orderby : "caption",
			mode : "json",
			deleted : 'false'
		},
		parse: function(data) {
			var results = [];
			var obj = JSON.parse(data);
			$.each(obj, function(i, item) {
				results[results.length] = {
					'data' : item,
					'value' : item.id,
					'result' : item.caption };
			});
			return results;
		},
		formatItem: function(item) {
			return item.caption;
		},
		formatResult: function(item) {
			return item.id;
		}
	});
	$("input#determiner").result(function(event, data){
alert(data.id);
		$("input#determiner_id").attr('value', data.id);
	});
});
</script>
<form class="cmxform"  name='editList' action="<?php echo url::site().'occurrence/save' ?>" method="POST">
<input type="hidden" name="id" id="id" value="<?php echo html::specialchars($model->id); ?>" />
<fieldset>
<legend>Occurrence Details</legend>
<?php print form::hidden('website_id'); ?>
<ol>
<li>
<label for='taxon'>Taxon:</label>
<?php print form::input('taxon', $model->taxa_taxon_list->taxon->taxon); 
print form::hidden('taxa_taxon_list_id', $model->taxa_taxon_list_id); 
echo html::error_message($model->getError('taxa_taxon_list_id')); ?>
</li>
<li>
<label for='date'>Date:</label>
<?php print form::input('date');
echo html::error_message($model->taxa_taxon_list->taxon->getError('taxon')); ?>
</li>
<li>
<label for='determiner'>Determiner:</label>
<?php print form::input('determiner');
print form::hidden('determiner_id');
echo html::error_message($model->getError('determiner_id')); ?>
</li>
</ol>
</fieldset>
<?php echo $metadata ?>
<input type="submit" name="submit" value="Submit" />
<input type="submit" name="submit" value="Delete" />