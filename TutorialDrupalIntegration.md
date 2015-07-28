# ![http://indicia.googlecode.com/svn/wiki/druplicon.small.png](http://indicia.googlecode.com/svn/wiki/druplicon.small.png) Writing your own code to integrate Indicia and Drupal #

The Drupal content management system is one of the most popular and powerful content management systems available. This tutorial explains how to get Drupal to host a custom Indicia data entry page. As an alternative, if you are willing to stick to one of the [prebuilt IForms](PrebuiltIForms.md), you can use the [Drupal IForm Module](UsingDrupalIForm.md). The tutorial is based on Drupal 6 but the principles should apply to earlier versions as well. It is recommended that you complete the tutoral [Building a basic PHP data entry page](TutorialBuildingBasicPage.md) first.

This tutorial assumes that:
  * You have already installed [Drupal](http://www.drupal.org)
  * You have administration rights to the Drupal website
  * You are able to copy files into the Drupal website installation folder (e.g. using the CPanel File Manager for hosted websites)
  * You have a website and survey registered on an Indicia Warehouse server.


# Steps #

By default, Drupal does not allow you to run your own PHP code embedded into its pages, but this is what you need to be able to do to drop in the Indicia components. So, the first step is enable support for PHP pages as content. To do this, log in to your Drupal site and in the admin menu select Administer >> Site Building >> Modules. About half way down the list of modules, tick the module called PHP Filter then click the Save Configuration button at the bottom.

![http://indicia.googlecode.com/svn/wiki/enable_drupal_php_filter.png](http://indicia.googlecode.com/svn/wiki/enable_drupal_php_filter.png)


Embedding PHP code into your content pages has risks, because if you had a malicious editor of the website they could break the site. As a result of this the default behaviour is for the PHP filter to be only enabled for administrators of the website. If you want to change this, for example to allow a programmer to create PHP pages, then the menu option you need is Administer >> Site Building >> Input formats. Then, click the configure link beside the PHP code row.

![http://indicia.googlecode.com/svn/wiki/drupal_input_formats.png](http://indicia.googlecode.com/svn/wiki/drupal_input_formats.png)


The page that follows allows you to select which roles can use the PHP filter.

![http://indicia.googlecode.com/svn/wiki/drupal_php_config.png](http://indicia.googlecode.com/svn/wiki/drupal_php_config.png)


Ok, now we are ready to start integrating Indicia into Drupal. The first step is to make sure the Indicia client\_helpers folder is available. An easy way to do this is to copy the folder from an Indicia install or existing website into the root folder of your Drupal installation. You also need to set up the parameters in the helper\_config.php file, which is explained in [up the helper\_config file](Setting.md).

We need a Drupal menu to access the Indicia web pages we create, so if you haven't already created a suitable site menu structure let's create one now. There is a useful tutorial on doing this in the Drupal cookbook at [Working with menus](http://drupal.org/node/120632). Use this tutorial to create a menu called Data Entry and add it to the left region on the page. Note that it won't appear until you actually add some menu items to it.

The next step is to test that the links to Indicia are working correctly. To do this we will create a content page and try to access Indicia's system\_check function to see if everything is OK. First, select Create content >> Page from the admin menu in Drupal. Enter a title for your page (e.g. Enter a species record), then click the Menu settings link. Enter the menu item title and select the menu you just created in the Parent item drop down. Then, enter the following code in the Body box:
```
<?php 
require "client_helpers/data_entry_helper.php";

echo data_entry_helper::system_check(true);
?> 
```
Your page should look something like this:

![http://indicia.googlecode.com/svn/wiki/drupal_indicia_test_page.png](http://indicia.googlecode.com/svn/wiki/drupal_indicia_test_page.png)


Now, we need to tell Drupal that the page contains PHP code, so scroll down and click the Input format link, then select PHP code:

![http://indicia.googlecode.com/svn/wiki/drupal_select_php_format.png](http://indicia.googlecode.com/svn/wiki/drupal_select_php_format.png)


Now scroll to the bottom and save the page. With any luck your menu will appear on the left of the page. Click it and you should see something like the following, which confirms that your Indicia Warehouse links are working correctly.

![http://indicia.googlecode.com/svn/wiki/drupal_test_success.png](http://indicia.googlecode.com/svn/wiki/drupal_test_success.png)


If that worked then the setup is done and we just need to write some code for the data entry page. As this tutorial isn't about writing the code – that is covered elsewhere – here is “one I prepared earlier”. Just click the Edit link at the top of your page, then page this code in and change the configuration at the top to specify the website ID, password and survey ID you are using:
```
<?php 
$config['website_id']=1;
$config['password']='password';
$config['survey_id']=1;

require "../../client_helpers/data_entry_helper.php";

// Catch a submission to the form and send it to Indicia
if ($_POST)
{
   $submission = data_entry_helper::build_sample_occurrence_submission($_POST);
   $response = data_entry_helper::forward_post_to(
      'save', $submission
   );
   echo data_entry_helper::dump_errors($response);
}

?>
<form method="post" >
<fieldset>
<?php
// Get authentication information
echo data_entry_helper::get_auth($config['website_id'], $config['password']);
$readAuth = data_entry_helper::get_read_auth($config['website_id'], $config['password']);
?>
<input type='hidden' id='website_id' name='website_id' value='<?php echo $config['website_id']; ?>' />
<input type='hidden' id='survey_id' name='survey_id' value='<?php echo $config['survey_id']; ?>' />
<input type='hidden' id='record_status' name='occurrence:record_status' value='C' />
<?php echo data_entry_helper::autocomplete(array(
  'label' => 'Taxon',
  'fieldname' => 'occurrence:taxa_taxon_list_id', 
  'table' => 'taxa_taxon_list', 
  'captionField' => 'taxon',
  'valueField' => 'id',
  'extraParams' => $readAuth
));
echo data_entry_helper::date_picker(array(
  'label' => 'Date',
  'fieldname' => 'sample:date'
)); 
echo data_entry_helper::sref_and_system(array(
    'label'=>'Spatial Reference'
));
echo data_entry_helper::map_panel(array(
  'presetLayers' => array('virtual_earth')  
)); 
?>

<input type="submit" value="Save" />
</fieldset>

</form>
<?php echo data_entry_helper::dump_javascript(); ?>
```

Press the Save button and if all is well, you should see something like this:

![http://indicia.googlecode.com/svn/wiki/drupal_example_unstyled.jpg](http://indicia.googlecode.com/svn/wiki/drupal_example_unstyled.jpg)


Great! You can give it a try and save a test record now if you like. The only thing is the form layout is not very tidy. You can either add your own CSS file to the theme in Drupal, or a quick way to tidy things up is to ask Indicia to include it's default stylesheet on the page. Edit the page, then insert a call to the data entry helper’s link\_default\_stylehsheet method following code beneath the line which requires the data\_entry\_helper.php file. It should look like:
```
<?php 
$config['website_id']=1;
$config['password']='password';
$config['survey_id']=1;

require "client_helpers/data_entry_helper.php";
data_entry_helper::link_default_stylesheet();
etc...
```

Now, save the page and the layout should be a bit tidier. Here’s a final screenshot showing the webpage in action:

![http://indicia.googlecode.com/svn/wiki/drupal_example_styled.jpg](http://indicia.googlecode.com/svn/wiki/drupal_example_styled.jpg)


**Tip**: When editing PHP in Drupal using this technique, it is easy to make mistakes so when you save the page you just see the PHP error and nothing else – no link back to the edit page to fix it! Fortunately the fix is easy – just append /edit to the URL in the browser's address bar then reload the page to get back into edit mode, allowing you to fix the error.