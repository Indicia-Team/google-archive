# Introduction #

When writing data entry pages with Indicia, for the most part you are responsible for defining the captions displayed in the labels and elsewhere on the page. However, in some cases, the data\_entry\_helper class outputs captions for you. This tutorial shows you how to customise these texts, either to change the terminology, or to translate to another language. You can also access the internationalisation functionality from your own PHP code.

# Details #

First in the client\_helpers\lang folder there is a file called custom\_example.php. Rename this file to something appropriate to your terms, for example using the ISO code for the language you are defining such as de.php or fr.php.

Next open this file and also the default.php file. Each of these has a list of key/value pairs a little way down, in the form "key" => "value". The default.php file contains the full list of translatable terms and the custom file you have created can either contain the full list of translated terms, or just those which you want to change. So, if you want to change just a few terms, then you can copy just the rows for your required terms from the default.php. If you want a full translation, then copy and replace the entire list.

Now, go through this list of key value pairs, and change the terms as you like. You can now create multiple copies of the file each with a different language code in the file name. So you might have the files custom.en.php, custom.de.php and custom.fr.php as an example allowing the user interface to display in English, German or French.

Finally, you need to inform the data entry helper to use your terms. This is simply a case of adding the following line to your page code, replacing the custom.php with the actual file name:
```
<?php
require 'client_helpers\lang\custom.de.php';
?>
```

Because Indicia does not include any specification for how the logged in user selects their language, it cannot automatically switch between the available files according to the language. You will need to write some code to require the correct file depending on the selected language. For example, in Drupal you can use the following code:
```
<?php
// Use the Drupal language global var
global $language;
if (file_exists('client_helpers\lang\custom.'.$language->language.'.php') {
  include 'client_helpers\lang\custom.'.$language->language.'.php';
} else {
  include 'client_helpers\lang\custom.en.php';
}
?>
```
Note that, in this example, if the user has selected an unsupported language the form will revert to using the English translation file.

# Using the lang class #

If you want to internationalise your own PHP code language strings, you can simply require the lang.php file (in the same place as data\_entry\_helper.php). This will allow you to call
```
lang::get('language key');
```
which will obtain the localised version of the string called 'language key' for you. Then you can simply include these strings in the custom.