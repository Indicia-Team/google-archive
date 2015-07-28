# Templates and Theming #

The Indicia data\_entry\_helper class generates HTML for each control that you add to the web page. You can take control of the output HTML by modifying the templates you require. In addition, certain controls such as the date picker have an appearance controlled by a theme. This tutorial guides you through the process of changing the templates and theme for your web page.

Before starting the tutorial, please complete the [Building a Basic Data Entry Page](TutorialBuildingBasicPage.md) tutorial.

## Templates ##

The templates for the data\_entry\_helper class are defined in a global array variable called `$indicia_templates` and in most cases there is an entry in this array with the same name as the control's method name. There are also entries for "prefix" and "suffix" which are inserted before and after each control, and "label" to define the template for a label. Here are some examples to give you an idea what can be done. Try inserting this code into your tutorial data entry page underneath the line that requires the data\_entry\_helper.php file and feel free to play around to see how it affects your page. Many of the templates end with a `\n` - this is just to output clean HTML into the page with new lines for each control, but is not a necessary part of the template functionality.
```
global $indicia_templates;

// Change the 'soft' new line between each control into a hard rule.
$indicia_templates['suffix']="<hr/>\n";

// Prevent each label from having a colon after it.
$indicia_templates['label']='<label for="{fieldname}">{label}</label>'."\n";

// Force a double width date picker control
$indicia_templates['date_picker']='<input type="text" size="60" class="date {class}" id="{id}" name="fieldname" value="{default}"/><style type="text/css">.embed + img { position: relative; left: -21px; top: -1px; }</style>';

```

When building your own templates it is highly advisable to take a copy of the existing one and tweak it rather than build from scratch, as some of the tags wrapped in { } are essential for the control's operation.

## Theming ##

The date picker control is a good example of a themed control, where the drop down calendar has a definable appearance. The Indicia Warehouse uses the same theming system for its own user interface, which is based on the [JQuery UI theming](http://jqueryui.com/docs/Theming/API) system. In the standard installation of the Warehouse there is an alternative theme available called redmond. You can switch to this theme by setting a global variable, placing this code in a PHP block:
```
global $indicia_theme;
$indicia_theme='redmond';
```

There are a couple of nifty things about this theming system you should be aware of. Firstly, making your own theme is a piece of cake since there is a tool to do this called [Themeroller](http://jqueryui.com/themeroller/). You can either download an existing theme, or tweak and create your own to download. Having built or selected a theme, click on the Download your theme link, ensure atleast the widgets you are using are selected, then click the Download button near the bottom right. This will give you a zip file, containing a folder called css (plus one or two others you don't need). You need to unzip the folder inside this css folder to a location on your webserver, and give the folder a sensible name. For example, the folder could be visible from http:\\www.mysite.com\media\themes\my-new-theme. Change the name of the css file in this folder, if necessary, to jquery-ui.custom.css

Now, to use this theme, you need to set the $indicia\_theme global variable as before, but also set the $indicia\_theme\_path variable to tell Indicia where to look for the theme folder:
```
{{{
global $indicia_theme, $indicia_theme_path;
$indicia_theme_path='http:\\www.mysite.com\media\themes';
$indicia_theme='my-new-theme';
}}}
```

Also, you should take a look at the [Theming API documentation](http://jqueryui.com/docs/Theming/API) and use these CSS classes when building the other HTML components on your data entry pages if you want to take full advantage of the theming support.