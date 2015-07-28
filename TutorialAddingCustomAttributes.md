# Introduction #

This tutorial guides you through the process of adding a custom attribute and controlling the contents of the attribute using a termlist. The example we will use is a Weather attribute. There are examples of custom attributes added to a data entry form in the Data Entry 2 example on the demo page (http://testwarehouse.indicia.org.uk/modules/demo/index.php).

Before starting your survey, you should have registered a website and survey on the Core Module, and have built a basic data entry page.

# Steps #

  1. First you need to create a termlist to store your list of possible values in. Select Lookup Lists\Termlists from the menu, then click New termlist. Specify the title as Weather. For this tutorial you can leave the other fields, but in many cases you may wish to specify that the owner of this attribute is your website, rather than the Core Module.
  1. Save your termlist, then click the edit link beside it in the list. Since you will need the ID of this termlist later, click the Show/Hide Metadata link and make a note of the termlist's ID.
  1. Click the View Terms button. Click New Term, then type "Sunny" as the Term Name, select English as the language, and press Submit.
  1. Repeat the previuos step for the terms "Cloudy" and "Rainy".
  1. Now we need to add the attribute itself. Click Custom Attributes\Occurrence Attributes on the menu. Now select your website and survey then press Filter.
  1. Click New Occurrence Attribute. Type in Weather in the Caption field, select Lookup List as the data type, then select Weather in the Termlist dropdown. Leave the other fields as they are for this tutorial.
  1. Press Save. Now click the edit link beside your new attribute, then click the Show/Hide Metadata link and make a note of the ID of your attribute.
  1. Ok, you now have a termlist with controlled terminology, plus a custom occurrence attribute for the Weather. To add this into your PHP data entry form, insert the following code, replacing <<Attribute ID>> and <<Termlist ID>> with the values you noted earlier:
```
<?php
echo data_entry_helper::radio_group(array(
  'label' => 'Weather',
  'fieldname'=>'occAttr:<<Attribute ID>>',
  'table'=>'termlists_term',
  'captionField'=>'term',
  'valueField'=>'id',
  'extraParams'=>$readAuth + array('termlist_id' => <<Termlist ID>>)
));
?>
```

That's it! Save your PHP file and view it in a web browser to check the new attribute. The code given here adds a group of radio buttons, but of course you may like to use a select control, a list box, or even a custom block of HTML for each item. Have a look at the code in the demo module's test\_data\_entry.php for demonstrations of the relevant select, listbox and list\_in\_template methods in the data entry helper class.

When you want to add a custom sample attribute, follow a similar procedure but, in the code, replace "occAttr" with "smpAttr".