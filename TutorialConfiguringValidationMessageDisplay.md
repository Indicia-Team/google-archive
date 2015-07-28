# Controlling Validation Message Display #

By default, Indicia will check the content of a form being posted and if any of the controls contain data which does not validate, then it will reload the form showing the validation errors. The default behaviour is to display a message underneath each control that has a problem. There are 4 possible behaviours though and you can mix and match any or all of them. To set the behaviours, insert the following code after the `require` for **data\_entry\_helper.php** but before the form's actual code:
```
data_entry_helper::$validation_mode=array('message','hint','colour','icon');
```
Obviously you don't necessarily want to specify all these options - just include the options you want. Here's a description of what they each do.

## message ##
This displays the validation text in a message. The message is normally beneath the control, but you can change this by updating the template `$indicia_templates['validation_message']`.

## hint ##
This makes the validation message appear as a hint over the control.

## colour ##
This changes the control's colour by applying the css error class `ui-state-error`.

## icon ##
This displays an icon after the control. You can change the template for the inserted icon HTML by changing the `$indicia_templates['validation_icon']` template.