# Introduction #

**This tutorial needs completion and review**

When you post an Indicia form back to the Warehouse, the Warehouse will check the contents of the form against the validation rules defined for each field. However, that involves a round trip which may be inefficient simply to check that a required field is populated. Indicia supports the jQuery Validation Plugin to apply validation rules to the controls when you click the form's submit button within the browser, saving the need to communicate with the server in many cases.

Note that if JavaScript is disabled in the browser, then the form validation will still take place on the server.

# Adding support to your form #

To enable support for client-side validation in your form, the first thing to do is add the following code into the PHP, before the code which declares the controls:
```
data_entry_helper::enable_validation('form id');
```
The single parameter must be a string which matches the HTML id of the form element containing the controls to validate.

Some default rules will be applied to the controls which you attach to fields in the core data model, for example sample:date will always be required. If you need to override these defaults, they are defined in data\_entry\_helper::$default\_validation\_rules.

As well as these defaults, you will also want to be able to attach your own rules to the controls, especially custom attribute controls which are not in the data model. To do this, you can add a setting called validation to the control's options parameter. This contains an array of the rules you want to apply. For example, an email text input control might have the code:
```
$r .= data_entry_helper::text_input(array(
        'label'=>lang::get('email'),
        'fieldname'=>'smpAttr:'.$args['email_attr_id'],
        'validation'=>array('required','email')
      ));
```

This would check that the control is populated, as well as ensuring it is of the correct format for an email.

_Note: the list of validation rules currently supported is not yet complete. Supported options are email, required and url._

If you have a wizard style interface build using the enable\_tabs method with style set to wizard, then the wizard\_buttons method automatically calls validation for the current page each time the user tries to access the next page.