# Introduction #

This tutorial takes you step by step through the process of building a basic data entry page using Indicia and PHP. It assumes you are able to build a basic HTML page, and a little knowledge of PHP is useful though not essential.

Before you can build an Indicia webpage you need access to an Indicia Warehouse server. You have a choice to either host these yourself (see Installation) or use the services provided by another institution (such as the [Biological Records Centre](http://www.brc.ac.uk)).

The first two sections of the following tutorial are performed by your Warehouse administrator. If you are using services provided by another institution then these will be done for you and you will be sent a URL, username and password which you then use to log in to the Indicia Warehouse and set up your recording scheme details.

# Register the website #
  1. Login to the Indicia Warehouse as a user with administrative privileges.
  1. Select Admin\Websites from the menu at the top.
  1. Click the New Website button.
  1. Enter the title of your website, e.g. “Tutorial”.
  1. Fill in the URL of the website you will be setting up, e.g. http://www.mytutorial.com
  1. Specify a password which will be used to authenticate that data posted to the server actually came from your website, and enter it again in the Retype Password box to confirm it.
  1. Click the Save button.
  1. Later in this tutorial you will need the ID of the website you have just created. To obtain this, click the Edit link next to the website in the grid, then click the Show/Hide Metadata link in the bottom right of the page. Note the ID in the box that appears.

# Register the website administrator #
  1. Select Lookup Lists\People from the menu.
  1. Click the New Person button.
  1. Enter the details of the person who will act as the administrator of the website you are building, including their email address.
  1. Select Admin\Users from the menu.
  1. Find the person record you just created, then click Edit User Details in that row.
  1. In the User’s Details, enter a password for the user and confirm it in the Repeat Password control.
  1. Note the Username that has been generated or change it if required.
  1. In the Website Roles list, select Admin in the drop down box next to the Website you created.
  1. Save the page then select Me\Logout from the menu.

# Create a species list to record against #
  1. First, login as the user you created in the previous section.
  1. Select Lookup Lists\Taxon Groups from the menu.
  1. Click the New Taxon Group button and specify the title of a taxonomic group you would like to create a species list for, then save it.
  1. Select Lookup Lists\Species Lists from the menu.
  1. Click the New Species List button.
  1. Enter a list title, e.g. “Dorset Butterflies”.
  1. Enter a description for your list.
  1. In the Owned By box, select the website that this list will be used for.
  1. Press the Submit button.
  1. Select the Edit option next to the new species list you have created.
  1. Select the “View the contents of this list” link beneath the details of the list.
  1. Click the New Taxon button.
  1. Specify the Taxon Name, the Language for this term, and select the Taxon Group you previously created. All other fields are optional, but enter Common Names and Synonyms one on each line if required. Click the Submit button then repeat this for each taxon you require.
  1. Finally, in order to use this list on a data entry page, you need to know the ID of the list. To do this, select Lookup Lists\Species Lists from the menu and select the Edit option for the new list you created. Now click the Show/Hide Metadata link, and note the ID that is displayed in the box that appears.

# Build a data entry page #
  1. This part of the tutorial assumes you have access to a webserver that is running PHP 5 and are able to create web pages on the server. You should have a folder ready to contain the web pages on this server. Also, throughout this section of the tutorial, the website running the Indicia Warehouse will be referred to as `<warehouse_url>`. Make sure you replace this with the real URL!
  1. The building of PHP pages using Indicia is simplified by using the supplied Data Entry Helper class. This is a static class (so you don’t have to create an instance of it to use it) which outputs ready made pieces of HTML and JavaScript into your page. To use this, download the client\_helpers folder from the downloads section of this website and unzip it into the folder your web page will be accessed from on the server. This will give you a folder called client\_helpers.
  1. If you are using an account on the testwarehouse.indicia.org.uk and you are developing on a webserver on your local machine, then the client helpers are already configured sufficiently for you to complete this tutorial. If this is not the case though, please use the information in [Setting up the Helper Configuration File](SetupHelperConfig.md) to configure your client helpers before proceeding with the rest of this tutorial.
  1. Now, create a basic, blank PHP web page using your site template but with no content.
  1. Just inside your `<head>` element, paste in the following:
```
<?php	require 'client_helpers/data_entry_helper.php'; ?>
```
  1. You will need an HTML form in which to place the data entry controls. Paste this into your web page to create the basic form container. You need to replace websiteID with the ID of the website you created earlier, and websitePassword with its password. Make sure you use the website password, not your user password! The record\_status field sets the default status of the entered occurrence to 'Completed'.
```
<form method="post">
<?php
	// Get authorisation tokens to update and read from the Warehouse.
	echo data_entry_helper::get_auth(websiteID, 'websitePassword');
	$readAuth = data_entry_helper::get_read_auth(websiteID, 'websitePassword');
?>
<input type='hidden' id='website_id' name='website_id' value='websiteId' />
<input type='hidden' id='record_status' name='record_status' value='C' />

</form>
<?php echo data_entry_helper::dump_javascript(); ?>
```
  1. Ok, you’ve now got a form “Indicia enabled” and ready to put controls into. Note the last call, to dump\_javascript? That is because the data entry helper will dynamically build the required script for your page. Rather than inserting it higgledy piggledy into the html, it keeps things tidier if it is all kept in one place. The next task is to add a simple text box to allow selection of a species from your species list. Remember the ID of the species list we created earlier? Paste the following code into your page, just above the `</form>`, and replace `My ID` with your ID so there is just a number in the quotes.
```
<?php 
echo data_entry_helper::autocomplete(array(
    'label'=>'Species',
    'fieldname'=>'occurrence:taxa_taxon_list_id',
    'table'=>'taxa_taxon_list',
    'captionField'=>'taxon',
    'valueField'=>'id',
    'extraParams'=>$readAuth + array('taxon_list_id' => 'My ID')
)); 
?>
```
  1. This outputs the HTML label and control required. If want to output your own label using standard HTML, you can just omit the 'label' entry from the array. Now, try loading your page to check it works. With any luck you have a handy little auto-complete control for your species list, from just the lines of code above. Ours looks like the following: <br />![http://indicia.googlecode.com/svn/wiki/bde_tutorial_species_autocomplete.png](http://indicia.googlecode.com/svn/wiki/bde_tutorial_species_autocomplete.png)
  1. Next, add the following code for a date picker inside the PHP code block you just created, then save and test it:
```
echo data_entry_helper::date_picker(array(
    'label'=>'Date',
    'fieldname'=>'sample:date'
));
```
  1. The datepicker drop-down panel can get stuck behind other controls, such as maps, which also try to appear in the foreground. To fix this, you need the following code in your web page's stylesheet. If you don’t have a CSS file in your web-page template, you can paste the this code into your HTML page directly, just above the line that reads `</head>`. You also need to prefix it with `<style type="text/css">` and suffix it with `</style>` to mark out that it contains styling information. Note that although this approach works, having an external CSS file for your styling information is good practice.
```
#ui-datepicker-div {
z-index: 1000;
}
```
  1. Just to be sure, save your PHP file and reload the page in the web browser to check it works. It should look something like the following: <br />![http://indicia.googlecode.com/svn/wiki/bde_tutorial_datepicker.png](http://indicia.googlecode.com/svn/wiki/bde_tutorial_datepicker.png)
  1. Now, add the following code to your page under the date picker code inside the PHP block. This will add a map for picking spatial references in either British National Grid, or Lat Long format, a Select control for the survey to post data into, and a Comment text box.
```
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
<br />
```
  1. You might be wondering why we are using the data\_entry\_helper methods to output simple HTML controls like textarea and text inputs. This is because behind the scenes Indicia can output code that displays validation errors against each control and also to populate controls with their values when reloading a record. The data entry controls we need for a very basic form are done now, but you will also need to provide a method of submitting this data to the Indicia services where it will be stored. First, add a simple Submit button to your form (just above the `</form>` close tag). This button will post the data back to the same page – of course you could easily modify this to post the data to a different PHP page and process it from there.
```
<input type="submit" value="Save" class="ui-state-default ui-corner-all" />
```
  1. The class attribute in the button code above sets the button to use the same theme as the HTML output by the data entry helper to keep it looking consistent. The data entry form is complete now as far as the controls are concerned. Loaded into a browser, it should look something like this: <br />![http://indicia.googlecode.com/svn/wiki/bde_tutorial_page.png](http://indicia.googlecode.com/svn/wiki/bde_tutorial_page.png)
  1. We now need some code to capture the data posted from the data entry form and send it to Indicia. Here’s the code you need to paste in, just beneath the opening `<body>` element is a good place. This code grabs the data from the form's post array, and structures it into a submission which the Indicia Warehouse can understand before forwarding it on to the Warehouse. Finally, it retrieves any errors in the response, or displays a success message if appropriate.
```
<?php
if ($_POST) {
	$submission = data_entry_helper::build_sample_occurrence_submission($_POST);
	$response = data_entry_helper::forward_post_to('save', $submission);
	echo data_entry_helper::dump_errors($response);
}
?>
```

That’s it! You’ve built a basic data entry form using Indicia. If you want to learn more, there are some other tutorials on this Wiki or you can browse the [developer documentation for the Data Entry Helper class](http://www.biodiverseit.co.uk/indicia/dev/docs/Client/Config/data_entry_helper.html).

## Additional Tips ##
Indicia allows you to store extended characters such as those with diacritic marks, or even from international character sets in your data. It does this by encoding the data using _utf8_ encoding. So, if you want these characters to display correctly on your web pages, include the following in your template's `<head>` section:
```
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
```