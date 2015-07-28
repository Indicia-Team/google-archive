# Displaying a Loading message #

If your form is quite large or complex, it may take a little time to load so it can be a good idea to display a loading message on the screen whilst it loads. The data entry helper class has 2 methods to help you do this, `loading_block_start` and `loading_block_end`. Simply insert a call to `loading_block_start` at the start of the block of HTML that you want to be hidden until the page is ready, then `loading_block_end` after the block. Here is a modification of the code in the basic data entry tutorial which illustrates this:
```
<body>
<div id="wrap">
<h1>Basic Data Entry Tutorial Code</h1>
<?php
if ($_POST) {
  $submission = data_entry_helper::build_sample_occurrence_submission($_POST);
  $response = data_entry_helper::forward_post_to('save', $submission);
  echo data_entry_helper::dump_errors($response);
}
/************************************************************************/
/* Define the start of the block that is hidden until the page is ready */
/************************************************************************/
echo data_entry_helper::loading_block_start();
?>
<p>This data entry page illustrates the final results of a data entry page built using the
<a href="http://code.google.com/p/indicia/wiki/TutorialBuildingBasicPage">Building a Basic Data Entry Page</a> tutorial.
<form method="post">
<?php
  // Get authorisation tokens to update and read from the Warehouse.
  echo data_entry_helper::get_auth($config['website_id'], $config['password']);
  $readAuth = data_entry_helper::get_read_auth($config['website_id'], $config['password']);
?>
<input type='hidden' id='website_id' name='website_id' value='<?php echo $config['website_id']; ?>' />
<input type='hidden' id='record_status' name='record_status' value='C' />
<?php
echo data_entry_helper::autocomplete(array(
    'label'=>'Species',
    'fieldname'=>'occurrence:taxa_taxon_list_id',
    'table'=>'taxa_taxon_list',
    'captionField'=>'taxon',
    'valueField'=>'id',
    'extraParams'=>$readAuth + array('taxon_list_id' => $config['species_checklist_taxon_list'])
));
echo data_entry_helper::date_picker(array(
    'label'=>'Date',
    'fieldname'=>'sample:date'
));
echo data_entry_helper::map();
echo data_entry_helper::select(array(
    'label'=>'Survey',
    'fieldname'=>'sample:survey_id',
    'table'=>'survey',
    'captionField'=>'title',
    'valueField'=>'id',
    'extraParams' => $readAuth
));
echo data_entry_helper::textarea(array(
    'label'=>'Comment',
    'fieldname'=>'sample:comment',
    'class'=>'wide',
));
?>

<input type="submit" class="ui-state-default ui-corner-all" value="Save" />
</form>
<?php
/************************************************************************/
/* Define the end of the block that is hidden until the page is ready   */
/************************************************************************/
echo data_entry_helper::loading_block_end(); 
echo data_entry_helper::dump_javascript(); 
?>
</div>
</body>
```