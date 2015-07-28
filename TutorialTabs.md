# Introduction #

The Indicia data entry helper library has built in support for tabbed and wizard interfaces in your data entry forms. Both interface styles can be achieved in a very similar way, since wizards can be created by using a tabbed interface with hidden tabs and buttons to navigate between 'pages'. Enabling either style can be achieved with the following steps.

First, place all the controls in your form inside a single `<div>` element and set the id to 'tabs'.

Then, divide the contents of this wrapper div up into separate `<div>` elements giving each a unique id. Your form might now look like:
```
<form method="post">
<?php
  // Get authorisation tokens to update and read from the Warehouse.
  echo data_entry_helper::get_auth($config['website_id'], $config['password']);
  $readAuth = data_entry_helper::get_read_auth($config['website_id'], $config['password']);
?>
<div id="tabs">
<div id="details">
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
</div>
<div id="where">
<?php
echo data_entry_helper::map();
?>
</div>
</div>
<input type="submit" class="ui-state-default ui-corner-all" value="Save" />
</form>
```

Next, insert the following code beneath the `<div='tabs'>` element to setup the tabs, changing the array to contain the id for each 'tab div' as the key, and the caption to display as the value for each array entry:
```
<div id="tabs">
<?php
data_entry_helper::enable_tabs(array('divId'=>'tabs')); 
echo data_entry_helper::tab_header(array('tabs'=>array(
  '#details'=>'Details',
  '#where'=>'Where'  
)));
?>
```

If you want this to be a wizard interface with hidden tabs, change the enable tabs call to:
```
data_entry_helper::enable_tabs(array(
  'divId'=>'tabs'
  'style'=>'wizard'
)); 
```

This hides the tab strip at the top of the panel, but you also need to create some buttons to navigate each page. Inside each div representing the individual pages, insert the following code:
```
echo data_entry_helper::wizard_buttons(array(
  'divId'=>'...',
  'page'=>'...'
));
```
For the page parameter, you need to specify a value of _first_ for the first page, _last_ for the last page, and _middle_ for the other pages. The divId must be the ID of the div you have tabified, in our case tabs.