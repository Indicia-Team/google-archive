# Introduction #

The MNHNL Dynamic Form 1 prebuilt form is able to automatically build a form for you entirely from the configuration of a survey on the Indicia Warehouse, therefore reducing not only the configuration but also the programming required for a new survey setup. This tutorial introduces you to the steps required for setting up this form.

Before starting, you should have a working Warehouse installation available and also a working installation of Drupal with the IForm module installed. You should also have a website setup on the Warehouse, and know the ID and password for that website.

For this tutorial we will create a new survey with a custom attribute to store the occurrence abundance as free text, and the sample has the recorder’s details as attributes.

# Create a new Survey in the Indicia Warehouse #

First log in to the Warehouse, then select Lookup Lists -> Surveys from the menu. Click the New Survey button, enter a title, description and select the website this survey is being set up for. Finally, click Save, then take a note of the ID of the survey which is shown in the list of surveys.

# Create any custom attributes that don’t already exist #

If your survey needs any attributes that are not already registered in the Warehouse, they need to be created now. In our case we will use existing attributes for the recorder details, but will create a new attribute for the occurrence abundance free text.

Select Custom Attributes -> Occurrence Attributes from the menu.

![http://indicia.googlecode.com/svn/wiki/occurrence_attributes_list.png](http://indicia.googlecode.com/svn/wiki/occurrence_attributes_list.png)

Click the New Occurrence Attribute button. Set the Caption to Abundance, data type to Text, check the Available to other websites option and leave the other controls as they are. For other attributes you can try the different data types available here, such as terms from a lookup list defined by a termlist. Each data type has a different set of validation rules that you can apply, but note that at this stage you are defining the validation rules that apply to ALL websites and surveys that use this attribute. For example, if you make abundance required, then it would be required in all surveys that share the same attribute which may not be the desired effect! Press Save at the bottom to store the new occurrence custom attribute.

![http://indicia.googlecode.com/svn/wiki/occurrence_attribute_detail.png](http://indicia.googlecode.com/svn/wiki/occurrence_attribute_detail.png)

# Configure the attributes on the form #

Now you need to associate the attributes with the survey and configure them so they appear in the correct place on the form. Select Lookup Lists -> Surveys from the menu. Click the Setup Attributes link in the row for your new survey.

![http://indicia.googlecode.com/svn/wiki/survey_list.png](http://indicia.googlecode.com/svn/wiki/survey_list.png)

You will now see the screen that allows you to link attributes to your survey, position them within the form, and configure them for specific use within that survey. The screen has a selector to let you choose the kind of attributes to configure (1), some blank placeholders where the various attributes will appear (2), a control allowing you to create blocks which can be used to group controls together to make things like tabs (3) and a control allowing you to add an existing attribute to the survey (4).

![http://indicia.googlecode.com/svn/wiki/blank_survey_edit_attributes.png](http://indicia.googlecode.com/svn/wiki/blank_survey_edit_attributes.png)

First, select the CMS User ID attribute from the existing attribute list, then click Add existing attribute and repeat this for the CMS Username attribute. Note that the attributes appear in the placeholders above, but are outlined in red indicating that the changes have not yet been saved. Also, note that these attributes are special attributes in that they will automatically capture the user ID and username of the user in Drupal if they are logged in without actually appearing on the screen. If the user is not logged into Drupal, then these attributes will be ignored and will not appear on the form, unlike the other attributes available.

![http://indicia.googlecode.com/svn/wiki/edit_attributes_showing_new.png](http://indicia.googlecode.com/svn/wiki/edit_attributes_showing_new.png)

Now, add the First Name, Last Name and Email attributes to the survey as before. These are also special attributes, in that they are only displayed when the user is not logged in, since if they are logged into the Drupal Content Management System the user will not want to type in these details repeatedly for each record. Also add the Weather and Happy for Contact attributes, which are standard attributes that will appear on the form whether or not you are logged in. Finally, press Save.
Next, select Occurrences in the “Display Attributes For” drop down at the top, and click the Go button. You will see the blank placeholders again, just like before we started adding sample attributes, but this time the view is showing the custom attributes attached to each occurrence rather than each sample. Select Abundance in the Existing attribute box at the bottom and click Add existing attribute, then press Save.

# Setup the form in Drupal #

Login to Drupal with a user who has permissions to create Indicia Form content (e.g. as the administrator).
Select Create Content -> Indicia Forms from the navigation menu in Drupal. If you cannot see this menu item (as it depends on your site configuration) try going to the URL /node/add/iform within your site.
Enter a page title, e.g. Test dynamic form. Select MNHNL Dynamic 1 from the indicia form drop down list, and the web page will update with the list of parameters required to set up the form you selected.
Setup\_dynamic\_form.png

Expand the Other Iform Attributes section and specify at least the Survey which you noted earlier. The other options available provide further configuration and help text is provided beside each one, but you can leave them in their default state for now.

![http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_other.png](http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_other.png)

Expand the Initial Map View section and specify the latitude and longitude of the centre of the map area you want to gather records for, or you can leave these at the global settings defined in your site configuration. Also specify a map zoom level, typically a number in the range of around 8-12 depending the size of the area to be covered. You may need to experiment to get these settings right.

![http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_initial_map.png](http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_initial_map.png)

Expand the Base Map Layers section and tick Google Hybrid. Again you can experiment with these settings or even provide your own web mapping service (WMS) details.

Expand the Georeferencing section to specify how the place search control works. Leave the default settings for a general purpose search of UK place names.

![http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_georef.png](http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_georef.png)

Expand the Map section to specify the format of the spatial references that the form will accept. This is a comma separated list of systems which might include OSGB (=British National Grid), or the 4 digit code of a projection defined by the European Petroleum Survey Group, e.g. 4326 for GPS lat long references using the WGS84 projection, or 2169 for Gauss-Lux references.

![http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_georef.png](http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_georef.png)

Expand the Species section and at least specify a species checklist to use in the **Initial Species List** or **Extra Species List** entries. There are several other configurations in this section, including whether you are allowing entry of a list of records via a grid or a single record.

Finally the User Interface section lets you choose from Tabs, Wizard or all-in-one form styles, whether the place search control is present, and whether a control for picking from the list of existing controls is present. You can leave these at the default settings.

![http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_interface.png](http://indicia.googlecode.com/svn/wiki/setup_dynamic_form_interface.png)

Last but not least, click the Save button at the bottom of the page. This should load the form into Drupal, showing an empty list of occurrences as non have been created yet.

![http://indicia.googlecode.com/svn/wiki/dynamic_form_created.png](http://indicia.googlecode.com/svn/wiki/dynamic_form_created.png)

If you click the Add New Sample button, you will see the basic form we have designed, with 3 tabs. The grid on the first Species tab shows our list of species and includes a column called Abundance since this is the attribute we defined for occurrences in this survey. Don't forget that the first name, surname and email addresses are not going to be visible since they are not necessary when you are logged in. but you can see the Weather and Happy for Contact attributes on the Other Information tab.

Ok, we have made a start and got a working form, so let's try some configuration options:

  1. On the Drupal form, click the Edit link at the top. Expand the User Interface section and change Interface Style Option to Wizard. Press Save, then click the Add New Sample button to view the form again. This time you have a Wizard with Next and Previous buttons instead of a tabbed interface.

  1. Click the Edit link again, and this time try checking some different layers in the Base Map Layers section. Note, if you specify more than one, then there will be a + icon in the top right of the map allowing the user to choose the layer. Click Save then Add New Sample to check your form again.

The Indicia Warehouse also lets you re-order the controls, move them to different tabs and place them inside "fieldsets". Here's how.

In the Warehouse, select Lookup Lists -> Surveys from the menu, then click edit attributes in the row for your survey. First we'll try grouping our attributes into a fieldset called My Stuff, so we can separate them from the built-in attributes. To do this, in the Block name input box near the bottom, enter "My Stuff" then click Add Block. This will create a "block" at the top of the page called My Stuff, with placeholders into which you can drag other blocks and controls.

![http://indicia.googlecode.com/svn/wiki/edit_attributes_new_block.png](http://indicia.googlecode.com/svn/wiki/edit_attributes_new_block.png)

Now, lets drop our controls into this block. To do this, click and hold the move (+) icon to the left of the Happy for Contact? and Weather attributes and drag them to the placeholders in our new block. You can see when they are dragged over the right place as the placeholder will go yellow.

![http://indicia.googlecode.com/svn/wiki/edit_attributes_drag_control_to_block.png](http://indicia.googlecode.com/svn/wiki/edit_attributes_drag_control_to_block.png)

You can simply drag and drop the controls to the placeholders in the lists to re-order them at any time. Now, press Save, and re-load the input form. Notice anything? Has anything changed? If your Indicia installation is set up correctly with data caching enabled, then nothing will have changed since the form will be loading itself from the cache. Great for performance, but not so great when you are trying to design a form...

Fortunately there are a couple of solutions for this. You can find the cache folder (which by default will be in your Drupal installation, under sites/all/modules/iform/client\_helpers/cache) and delete all the files in it. But this can be laborious after each change to the form you want to review. A better way is to add &nocache to the URL, so that Indicia is instructed to ignore its cached files. For example, I can use this URL to access the latest version of my form: http://localhost/drupal/node/6?newSample&nocache.

Next, try creating another block called My Tab, but this time drag the whole My Stuff block inside the My Tab block, creating 2 nested blocks. The outer block will be rendered as a tab or wizard page, and the inner block will be rendered as a fieldset. The blocks should look like this:

![http://indicia.googlecode.com/svn/wiki/edit_attributes_nested_blocks.png](http://indicia.googlecode.com/svn/wiki/edit_attributes_nested_blocks.png)

Now save it and reload the form with the &nocache parameter. You should find a new tab has appeared on your form containing your custom attributes:

![http://indicia.googlecode.com/svn/wiki/edit_attributes_my_tab.png](http://indicia.googlecode.com/svn/wiki/edit_attributes_my_tab.png)

If you want to add controls to the existing tabs that can be done as well. As we have seen, controls inside a single level block will be added to a fieldset created on the Other Information tab. So, if you rename your outer block from My Tab to Other Information (by clicking the rename link to the right of the block name in the attribute editor, entering the new name, clicking Apply then Save), and reload the form, you will find your controls are back on the Other Information tab. More usefully, you can create an outer block called 'species' or 'place' to add your controls to the other 2 tabs as well.

## Configuring the list of species available ##

**Introduced in Indicia 0.8**

If you are using the dynamic form to accept records via a species checklist grid then you can take fine control over the list of species available to pick from. To do this, edit your form then expand the **Species** section. In this section look for the input called **Field used to filter taxa** and change this to on of the options. You can elect to filter by the species' preferred name, the title of a taxon group or the meaning ID of the species (which can be obtained from the species' details on the warehouse). Then specify the filter you want in the next box. So, for example, if you wanted to limit the list to a few species you could set **Field used to filter taxa** to **Taxon group title** then in the next box specify (for example):
```
Andrena nitida
Andrena fulva
Andrena apicata
```
Save the form and you should find this species restriction has been applied.