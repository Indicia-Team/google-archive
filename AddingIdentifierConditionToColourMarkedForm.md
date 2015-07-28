# Notes on adding Identifier Condition information to the WWT Colour-marked Wildfowl prebuilt form. #

## Introduction ##
This note was intended to give an example of adding an identifier attribute, but it turned out this wasn't the solution to the requirement, so this account covers changes to add a custom attribute which is associated with the observation of an identifier to the identifier data on the form. This is a more complex but more informative exercise. This note is based on [r4336](https://code.google.com/p/indicia/source/detail?r=4336).

## The Requirement ##
  1. To provide a method for users of the colour-marked wildfowl form to record information regarding the condition of identifiers noted during an observation.
  1. For each identifier on an observation, recorders can pick zero, one or many values from a predefined list of conditions.
  1. The list of conditions should be tailored to the identifier type.

## Design Considerations ##

### Data model ###
Condition is an attribute of an identifier at a given time as noted during an observation by a specific recorder. So although the condition relates to a specific identifier, we can't record this data with the identifier, we need to record it either with the observation or the table which links an observation with an identifier (identifiers\_subject\_observations).

I considered storing condition in an attribute of the identifiers\_subject\_observations table (ISO) as there is one ISO for each identifier reported during an observation,. The alternative of storage as a custom attribute of the subject\_observation (SO) would require the creation of a custom attribute type for each identifier recordable on an observation. So the simpler option appeared to be to create a condition attribute on ISO.

I then considered the issue of conditions not being mutually exclusive, so one identifier may exhibit several conditions during one observation. Recording multiple values in a single condition field does not work well, but could be supported by a multi-value custom attribute. This left the choice of using the existing custom attributes for SO or extending the data model to support custom attributes on ISO. The later was not attractive as ISO is largely a join table and no other join tables on indicia have custom attributes. I initially felt I would need a compelling reason to add custom attributes to ISO and identifier condition wasn't a good enough justification when an alternative was available, so I opted for storing condition in 4 multi-value custom attributes on subject\_observation.

However, as I progressed through development, I found I was repeatedly writing 4 pieces of similar code when only one should be needed, and when a new identifier was added to the form, all these code areas would need to be revisited to add a fifth piece of code. I also considered the difficulty of reporting on identifier conditions for a report such as 'condition history for an identifier'. Any report from an identifier context would require a lot of navigation across tables to gather the related condition data. In the light of this, I reviewed the data design and opted to create custom attributes for ISO after all. Creation of these tables and warehouse code will not be covered in this document.

### User Interface ###
Identifier conditions can occur together, for example, a coloured ring could be both faded and cracked. For a multiple option UI, the most natural choice is a check-box set, but putting a set of 7-9 check-boxes in each identifier panel could over-emphasise this feature. Another option is a drop-down select box or a list box although multiple selection with these controls is difficult and error prone. A third option would be to provide a button in each identifier panel to open a dialog box in which we could display a check-box set, this might combine the ease of check-boxes with small space requirements of a button, but it's rather trickier to program and may be more prone to bugs in certain browsers.

I initially opted to use the multiple selection control but during early testing, it proved so difficult to use that I abandoned it and decided to use the more intuitive check-box group. This also has the advantage of being more easily changed to a check-box pop-up if that is required later.

As this information is not recorded frequently, locking values was not considered to be required.

### Different condition lists ###
The predefined condition options will be set up in a termlist on indicia. I thought about creating a termlist for each identifier, but opted for a combined list and to filter into subsets for each identifier type during the form configuration. Not only did this only require creation and maintenance of one term list, but it gave more flexibility to change the terms offered on the form. It also should prove easier to build on the approach as more identifiers are added to the form.

## Implementation ##

### Overview ###

The implementation of this change is a rather lengthy process but it can be broken down to a number of steps. As guidance to the overall process, the steps are listed here

  1. Create a new termlist on indicia for the identifier condition options (Warehouse, SQL script)
  1. Add a new 'system function' to ISO custom attributes for identifier\_condition (Warehouse, PHP code)
  1. Create the Identifier Condition custom attribute (Warehouse, forms interface)
  1. Add code to the colour-marked form for the identifier conditions (Client, PHP code)
    1. Add term filters to the form configuration for each identifier type
    1. Adding the condition controls to the colour-marked form
      1. Specifying which controls to show on identifier panels
      1. Displaying the controls
    1. Data submission to the warehouse
    1. Reloading data for redisplay and editing

### Create a new termlist for the identifier condition options ###
Indicia termlists provide a means of specifying a defined vocabulary for describing something. The values of the terms have no specific meaning to the system so they can be changed and extended as required without programming. We need a defined list of identifier conditions so a termlist meets this need.

There are several ways to create termlists. They can be created and added to by using the warehouse forms, they can be uploaded as .csv files or they can be input directly to the database using an SQL script. With the exception of very small lists or minor amendments, it's best to use .csv files or SQL scripts on a production system as these can be tested, are repeatable and can be loaded quickly. I have chosen to use an SQL script for this task as I have direct database access.

The script is shown here.

```
INSERT INTO termlists (title, description, created_on, created_by_id, updated_on, updated_by_id, external_key)
VALUES ('Identifier Condition', 'Lookup list of identifier conditions (problems and remedies) noted during subject_observations in the Groups and Individuals module.', now(), 1, now(), 1, 'indicia:assoc:identifier_condition');

SELECT insert_term('Chipped', 'eng', 10, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Cracked', 'eng', 20, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Gun hole', 'eng', 30, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Leg ring lost', 'eng', 40, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Ring above tarsus', 'eng', 50, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Ring over web', 'eng', 60, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Sprung', 'eng', 70, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Stained/faded', 'eng', 80, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Transmitter retrieved', 'eng', 90, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Twisted', 'eng', 100, null, 'indicia:assoc:identifier_condition');
SELECT insert_term('Unknown', 'eng', 110, null, 'indicia:assoc:identifier_condition');
```
(if running from pg\_admin, remember to SET search\_path TO indicia,public; first.)

The above script first creates the termlist, then uses the 'insert\_term' function to add terms. This is helpful as termlists are implemented by a number of related tables which are handled properly by this helper function.

Two points worthy of note are the external key and sorting. The external key (indicia:assoc:identifier\_condition) provides a unique identity for this termlist which can be used to refer to it from code, where we won't know the database key for the list in advance. The structure of the name is a convention to avoid name clashes.

The third parameter to insert\_term allows us to specify the value for sorting in the sort\_order field. This allows the order of term presentation to be controlled and this can be changed if required through the warehouse forms interface to termlists.

### Add a new 'system function' to ISO custom attributes for identifier\_condition ###

'System functions' are labels that can be attached to custom attribute types to identify their purpose for indicia processing logic.

Custom attributes are very flexible and can be created without any code being written. The flipside of such flexibility is that the indicia code doesn't attach any specific meaning to custom attribute types, it can't as it doesn't know in advance what they will be called or what internal keys they will have, so it can't recognise them.

To make predefined processing possible on specific custom attribute types, indicia code can define names for custom attributes which it wants to process in a specific way. Then, we can create an attribute and label it with that predefined name so that the indicia code knows this attribute has a specified purpose. These predefined labels are called system functions.

System function names are specific to the custom attributes for a particular indicia entity. In the next section we will be creating a custom attribute type for identifiers\_subject\_observation. So we need to define a system function for use by identifiers\_subject\_observation custom attributes. We do this in PHP code in  identifiers\_subject\_observation\_attribute\_Model::get\_system\_functions() which is in the file modules/individuals\_and\_associations/models/identifiers\_subject\_observation\_attribute.php.

This function returns an array containing the defined system functions, each with an associated title and description. Currently there are none. We can add a system functions for identifier\_condition by adding this as a new member of the array, so the new get\_system\_functions will be:-
```
  public function get_system_functions() {
    return array(
      // add system function definitions as required in the form
      'identifier_condition' => array(
        'title'=>'Identifier Condition',
        'description'=>'A text or lookup attribute where the value indicates the condition of any attached identifier on this observed organism.',
      ),
    );
  }
```
Save the changes and distribute the updated file to your warehouse so that the new system function labels are available for use in the next step.

### Create the Identifier Condition custom attribute ###

Use the warehouse interface to create ISO custom attributes.

  * log into the warehouse as a user with permission to add custom attributes.
  * Navigate Custom Attributes > Identifier Subject Observation Attributes
  * Click the 'New identifiers subject observation attribute' button to open the form to create a new attribute.
  * Create attribute Identifier Condition, label it with the identifier\_condition system function and link it to the Identifier Condition termlist.
  * Allow multiple values and allocate it to the survey you are using for your colour-marked form.

The attribute creation form should look similar to this:

![http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/iso_attr_create_condition.png](http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/iso_attr_create_condition.png)

Now save your changes.

### An overview of the dynamic form for colour-marked wildfowl ###
The rest of this change involves editing the WWT Colour-marked Wildfowl prebuilt form which is located at client\_helpers/prebuilt\_forms/wwt\_colour\_marked\_report.php

Before we get deep into code, it will help to be aware of some of the key functionality in indicia dynamic forms.

All the forms include a configuration form which allows administrators to set form options which control form behaviour. These options are made available to the main form code for its use.

This form runs within the drupal iform module. When users navigate to this form they really call the iform code, which in turn calls the colour-marked forms 'get\_form' function to display the form in drupal.

Much of what the get\_form function displays is controlled by the form structure configuration within the 'User Interface' section of the configuration options. This allows the administrator to compose the form from a number of components specified in square brackets e.g. `[`location name`]`. The form\_load function reads this layout information and calls a corresponding function to render that part of the form, so if form\_load finds the `[`location name`]` component, it calls a function called get\_control\_locationname() which is responsible for rendering the html for that component.

When the user has completed the data entry and clicks the save button, the drupal iform module is actually called and this calls some functions in the prebuilt form before submitting the data to the warehouse. A key function invoked by iform is our form's get\_submission() function which takes the data sent from the user and converts it into a format that the warehouse can understand.

If the form is invoked with certain parameters, these are used to request data from the warehouse so that it can be redisplayed for editing by the user. For simple data reload, we can handle this within get\_form() but as the data used by the colour-marked form is complex, we reload it in a function called reload\_form\_data().

Although the prebuilt forms are written in PHP, often much of the functionality is enhanced with javascript. This is true for the colour-marked form, but for this change, we don't need to touch the javascript code.

### Add term filters to the form configuration form. ###

We need to add form configuration options for each identifier type which will allow filtering of the identifier condition termlist to a subset applicable for the given identifier type. So we need a filter option for each of Neck Collar Conditions, Coloured Ring Conditions and Metal Ring Conditions, each of which will display a check-box group of all identifier conditions so that the administrator can tick those that apply. The configuration options are split into categories and I will put these new options in the Identifiers category as I think that is where an administrator would expect to find them (it doesn't matter from a programming perspective as all options need to have unique names and end up in one big array).

Open the form for edit and find the get\_parameters() function which controls the options presented for form configuration.  As with so much in PHP, this is all based around associative arrays. This function returns a large array with a member for each configuration option. Each member is in turn an array of options which specify an individual configuration option. This data is used to create the options form and when the administrator saves the form options, the chosen option values are written to the drupal database iform table, keyed by the drupal node\_id. When a form for this node is opened, the configuration data is retrieved (from the drupal database or from cache) and loaded into an associative array called $args which is available to the form code to drive the form behaviour.

If we examine an existing option defining a checkbox-group we can get a feel for what information is required. Here is the option definition for  position
```
        array(
          'name'=>'position',
          'caption'=>'Identifier Position',
          'description'=>'The positions on the organism we want to let users record for the identifiers. Tick all that apply.',
          'type'=>'checkbox_group',
          'table'=>'termlists_term',
          'captionField'=>'term',
          'valueField'=>'id',
          'extraParams' => array('termlist_external_key'=>'indicia:assoc:identifier_position','orderby'=>'sort_order'),
          'required' => false,
          'group' => 'Identifiers',
        ),
```
  * The name is used to provide a machine label to this option, so the configured value of this option will become available to our code in $args['position'].
  * Caption is the label that will be shown to the admin in the configuration form and description will provide explanatory text beneath the option.
  * Type determines the form control type we want, a checkbox group in this case.
  * Table tells the control to populate itself from termlist\_term (actually the 'list' view for this table is used by default.)
  * CaptionField and valueFiled are used for the table (view) column names used to populate the checkbox text and value respectively.
  * ExtraParams limits the values returned to those with the given external key and sorts the results by the sort\_order term column.
  * This option is optional and will be displayed in the Identifiers section.

So, if we substitute appropriate values, we can define our first new option ( a neck collar conditions filter) as follows:
```
        array(
          'name'=>'neck_collar_conditions',
          'caption'=>'Neck Collar Conditions',
          'description'=>'The identifier conditions we want to be reportable by recorders when observing a neck collar. Tick all that apply.',
          'type'=>'checkbox_group',
          'table'=>'termlists_term',
          'captionField'=>'term',
          'valueField'=>'id',
          'extraParams' => array('termlist_external_key'=>'indicia:assoc:identifier_condition','orderby'=>'sort_order'),
          'required' => false,
          'group' => 'Identifiers',
        ),
```
This will appear in the Indentifiers section but the position it appears will be controlled by the order that we define the options. I want the new options to appear together at the end of the current Identifiers options, so I place 'neck\_collar\_conditions' after 'default\_leg\_position' and create similar options for coloured\_ring\_conditions and metal\_ring\_conditions immediately after neck\_collar-conditions.

We will now be able to access the filtered condition lists in the form code as $args['neck\_collar\_conditions'], $args['coloured\_ring\_conditions'] and $args['metal\_ring\_conditions'].

If we save the form code and distribute it, we will be able to configure our identifier-specific condition lists, we now need some code which will do something with them.
![http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/condition_filters.png](http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/condition_filters.png)

### Adding the condition controls to the colour-marked form ###
#### Part 1: specifying the request for the conditions attribute ####
This work will be done within the form control functions for species identifier and it's subfunction, identifier, which writes out the form markup for each identifier panel. This first section describes the changes in the species\_identifier component while the next section (part 2) covers the changes to render the conditions control.

Currently, all the fields in the identifier panels map to identifier custom attributes and the existing code makes it easy to add new identfier attributes. However, we need to add identifier subject observation attributes, so we will need to extend the existing code to handle attributes for different entities.

So let's start by looking at the code in species identifier, this prepares the data we need and packages it for each call to identifiers, which displays the identifier markup. We can get a feel for what we may need to do by inspecting existing code for an identifier attribute, say position.

A search of get\_control\_speciesidentifier() for 'position' reveals code for the following:

  * the attribute type for position is determined using the system function.
  * The attribute type id for position is passed into the page javascript.
  * The attribute type id is packaged and passed into each call of the identifiers subfunction.

Currently we have no validation requirements for condition (beyond being a value from the filtered conditions list) and no other identified need to process condition in javascript, so we can skip that, but we do need to write code to identify the condition attribute and pass it to the identifiers subfunction.

As condition is an identifiers_subject\_observation attribute rather than an identifier attribute, we can't just add it to the identifier attribute system function loop, we need a similar section of code for identifiers\_subject\_observation\_attribute. We clone the code as follows:
```
    // get the identifiers subject observation attribute type data
    $dataOpts = array(
      'table' => 'identifiers_subject_observation_attribute',
      'extraParams' => $auth['read'],
    );
    $options['isoAttributeTypes'] = data_entry_helper::get_population_data($dataOpts);
    
    // set up the known system types for subject_observation attributes
    $options['conditionsId'] = -1;
    foreach ($options['isoAttributeTypes'] as $isoAttributeType) {
      if (!empty($isoAttributeType['system_function'])) {
        switch ($isoAttributeType['system_function']) {
          case 'identifier_condition' :
            $options['conditionsId'] = $isoAttributeType['id'];
            break;
        }
      }
    }
```
So let's look at the first call to the Identifiers subfunction under the comment '// setup and call function for neck collar'. The key bit of code which specifies the attributes to output on the identifier panel is the creation of the $options['identifierAttrList'] array.
```
    $options['identifierAttrList'] = array(
      array('typeId' => $options['baseColourId'], 'lockable' => true, 'hidden' => false,),
      array('typeId' => $options['textColourId'], 'lockable' => true, 'hidden' => false,),
      array('typeId' => $options['sequenceId'], 'lockable' => false, 'hidden' => false,),
      array('typeId' => $options['positionId'], 'lockable' => false, 'hidden' => true, 'hiddenValue' => $args['neck_collar_position'],),
    );
```
This is identifier attribute specific, so we could clone this and create an array for identifier subject observation attributes, but this would lead to further cloning if we wanted to include other types of attributes later, plus it wouldn't allow us to intermix attributes of differing entity types in the display order. So a better option is to extend the current identifierAttrList array to cope with all attributes by adding an attribute entity type member to the array. We can now add our conditions attribute to the array and the modified array for neck collar will be
```
    $options['attrList'] = array(
      array('attrType' => 'idn', 'typeId' => $options['baseColourId'], 'lockable' => true, 'hidden' => false,),
      array('attrType' => 'idn', 'typeId' => $options['textColourId'], 'lockable' => true, 'hidden' => false,),
      array('attrType' => 'idn', 'typeId' => $options['sequenceId'], 'lockable' => false, 'hidden' => false,),
      array('attrType' => 'idn', 'typeId' => $options['positionId'], 'lockable' => false, 'hidden' => true, 'hiddenValue' => $args['neck_collar_position'],),
      array('attrType' => 'iso', 'typeId' => $options['conditionsId'], 'lockable' => false, 'hidden' => false,),
    );
```
Conditions will not be recorded often enough to make locking useful, so we don't allow it for condition._

Note, 'idn' and 'iso' are the appropriate standard prefixes for identifier attributes and identifiers\_subject\_observation attributes respectively, a full set of prefixes is listed in submission\_builder::get\_attr\_entity\_prefix() and each entity defines the corresponding prefix in the model class if it has custom attributes.

We make similar changes for the calls to Identifiers for the other three identifiers on the form.


#### Part 2: displaying the conditions control ####
We now have to make compatible changes in Identifiers to cope with the new 'attrList' we are passing in and to handle attributes for different entities., so we look at get\_control\_identifier().

This function writes some hidden form fields then uses a foreach loop on the supplied attributes array to output each attribute.

So first we change identifierAttrList to attrList in the foreach statement. A search confirms it occurs nowhere else. Next we look for code that assumes it is dealing with identifier attributes and generalise it for other attributes.

We find identifier attribute specific code immediately:
```
      // find the definition of this attribute
      $found = false;
      foreach ($options['idnAttributeTypes'] as $attrType) {
        if ($attrType['id']===$attribute['typeId']) {
          $found = true;
          break;
        }
      }
      if (!$found) {
        throw new exception(lang::get('Unknown identifier attribute type id ['.$attribute['typeId'].'] specified for '.
          $options['identifierName'].' in Identifier Attributes array.'));
      }
```
We extend it to cover identifier subject observation attributes:
```
      // find the definition of this attribute
      $found = false;
      if ($attribute['attrType']==='idn') {
        foreach ($options['idnAttributeTypes'] as $attrType) {
          if ($attrType['id']===$attribute['typeId']) {
            $found = true;
            break;
          }
        }
      } else if ($attribute['attrType']==='iso') {
        foreach ($options['isoAttributeTypes'] as $attrType) {
          if ($attrType['id']===$attribute['typeId']) {
            $found = true;
            break;
          }
        }
      }
      if (!$found) {
        throw new exception(lang::get('Unknown '.$attribute['attrType'].' attribute type id ['.$attribute['typeId'].'] specified for '.
          $options['identifierName'].' in Identifier Attributes array.'));
      }
```
Beneath the comment '// setup any data filters' we need to create a query which incorporates the conditions filters configured in the form options. We add a clause to the if-else statement:
```
      } elseif ($attribute['attrType']==='iso' && $options['conditionsId']==$attribute['typeId']) {
        // filter the identifier conditions available
        if ($options['identifierTypeId']==$args['neck_collar_type'] && !empty($args['neck_collar_conditions'])) {
          $query = array('in'=>array('id', $args['neck_collar_conditions']));
        } elseif ($options['identifierTypeId']==$args['enscribed_colour_ring_type'] && !empty($args['coloured_ring_conditions'])) {
          $query = array('in'=>array('id', $args['coloured_ring_conditions']));
        } elseif ($options['identifierTypeId']==$args['metal_ring_type'] && !empty($args['metal_ring_conditions'])) {
          $query = array('in'=>array('id', $args['metal_ring_conditions']));
        }
        $attr_name = 'conditions';
      }
```
We also need to enhance the conditions for the existing data filter attribute type by adding the condition $attribute['attrType']==='idn' as the attribute\_id is no longer unique now we have multiple attribute types.

Under the comment '// output an appropriate control for the attribute data type', the control fieldnames hard-code 'idnAttr', we need to vary this for each attribute entity type so change these 'idnAttr:' to $attribute['attrType'].'Attr:'. A search for 'idn' in this function confirms this prefix is not used in this context elsewhere.

An examination of the control output code for List type attributes shows a select control is used, but it does not support multi-select for attributes which can have multiple values. I tried using a multiselect listbox, but it was really difficult to use, so I have gone with a check-box group.
```
          if ($attribute['attrType']==='iso' && $options['conditionsId']==$attribute['typeId']) {
            $fieldname = $fieldPrefix.$attribute['attrType'].'Attr:'.$attrType['id'];
            $default = array();
            // if this attribute exists on DB, we need to write a hidden with id appended to fieldname and set defaults for checkboxes
            if (is_array(data_entry_helper::$entity_to_load)) {
              $stored_keys = preg_grep('/^'.$fieldname.':[0-9]+$/', array_keys(data_entry_helper::$entity_to_load));
              foreach ($stored_keys as $stored_key) {
                $r .= '<input type="hidden" name="'.$stored_key.'" value="" />';
                $default[] = array('fieldname' => $stored_key, 'default' => data_entry_helper::$entity_to_load[$stored_key]);
                unset(data_entry_helper::$entity_to_load[$stored_key]);
              }
            }
            $r .= data_entry_helper::checkbox_group(array_merge(array(
              'label' => lang::get($attrType['caption']),
              'fieldname' => $fieldname,
              'table'=>'termlists_term',
              'captionField'=>'term',
              'valueField'=>'id',
              'default'=>$default,
              'extraParams' => $extraParams,
            ), $options));
          } else {
            $r .= data_entry_helper::select(array_merge(array(
              'label' => lang::get($attrType['caption']),
              'fieldname' => $fieldPrefix.$attribute['attrType'].'Attr:'.$attrType['id'],
              'table'=>'termlists_term',
              'captionField'=>'term',
              'valueField'=>'id',
              'blankText' => '<Please select>',
              'extraParams' => $extraParams,
            ), $options));
          }
```
This gives a functional but rather expansive UI so we may wish to revisit this later and investigate making it a popup. Note the writing of hidden inputs for any values already on the database, this ensures we do the right thing (delete them) if existing values are un-ticked in the check-box group. The same technique is already used in this form for attached devices.

We now have a form which presents the conditions list on each identifier panel and allows us to tick any conditions which apply.

![http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/display_conditions.png](http://indicia.googlecode.com/svn/wiki/screenshots/colour_marked_form/display_conditions.png)

The next stage is to correctly submit the conditions data to indicia.

### Data submission to the warehouse ###

Indicia has a client-server architecture which enforces a strong separation of the web client from the warehouse. This is important for security. All client access to the warehouse has to use the interfaces provided by the data services and the report services. For data submission, we use the data services. The indicia data services support 2 styles for sending data to indicia, the first allows the creation or updating or single entities while the second allows the submission of complex structures of related entities. For this form our data spans many database tables so we need to use submission structures. These are documented at ?????. Submission structures are powerful, but the cost of that power is some degree of complexity, so some effort is required to understand them.

The dynamic forms handle the construction of the submission structure in the get\_submission() function and subfunctions. The conditions data is targeted at the identifiers\_subject\_observation attributes so the field values need to be placed in the  identifiers\_subject\_observation part of the submission structure, as entities know how to handle and store their own custom attributes. The get\_submission function will receive the form data in an array called $values (which is really the $_POST array) where it will be in the form $values['idn:0:neck-collar:isoAttr:1']. This will itself contain an array of values in order to support multiple conditions. An example with two conditions ticked is;
```
[idn:0:neck-collar:isoAttr:1] => Array
        (
            [0] => 409
            [1] => 411
        )
```
The array key idn:0:neck-collar:isoAttr:1 is composed of a prefix to make it unique on the form (idn:0:neck-collar:) and the field identifier for the database (isoAttr:1). The submission mechanism only understands the database identifier as it knows nothing of specific forms. When building the submission structure, we need to 'flatten' (i.e. remove the prefix) each key value before putting it in the structure for the  identifiers\_subject\_observation. There is existing code to do this for identifier fields, but it needs extending for  identifiers\_subject\_observation attributes. This code is in add\_observation\_submissions() which is a subfunction of get\_submission(). The existing code is;
```
        $ident_keys = preg_grep('/^idn:'.$idx.':'.$identifier_type.':(identifier|identifiers_subject_observation|idnAttr):/', array_keys($values));
        foreach ($ident_keys as $i_key) {
          $i_key_parts = explode(':', $i_key, 4);
          $values[$i_key_parts[3]] = $values[$i_key];
        }
```
Here preg\_grep returns an array of all the keys in $values (the form post) which need to map to identifier, identifiers\_subject\_observation or identifier\_attributes (idnAttr). We just need to add isoAttr to that list for  identifiers\_subject\_observation\_attributes so the new code is;
```
        $ident_keys = preg_grep('/^idn:'.$idx.':'.$identifier_type.':(identifier|identifiers_subject_observation|idnAttr|isoAttr):/', array_keys($values));
        foreach ($ident_keys as $i_key) {
          $i_key_parts = explode(':', $i_key, 4);
          $values[$i_key_parts[3]] = $values[$i_key];
        }
```
The foreach loop then 'flattens' these keys to just their database table:field names so they are understood by the submission mechanism. So our field  idn:0:neck-collar:isoAttr:1 will be submitted as isoAttr:1 which indicia will store in identifiers\_subject\_observation\_attributes._

We now have a form which can capture the condition and store it. Now we need to change the form to reload conditions for editing.

### Reloading data for redisplay and editing ###

Indicia forms check for existing data when they load up. They look in an array called $entity\_to\_load which is a static member of the helper\_base class and use any values they find with keys which match the field name of the control. So the form needs to ensure the right data is in $entity\_to\_load when the form load. For the colour-marked form, we have to load a lot of data into $entity\_to\_load from different tables so we separate this into its own function reload\_form\_data().

As this change is the first time we have used data on the identifiers\_subject\_observation\_attributes table, we need to extend reload\_form\_data to read in rows from this table which belong to the  identifiers\_subject\_observation rows we are already loading with this code;

```
    // load the identifiers_subject_observation(s) for this sample
    $query = array('in'=>array('subject_observation_id', self::$subjectObservationIds));
    $filter = array('query'=>json_encode($query),);
    $options = array(
      'table' => 'identifiers_subject_observation',
      'extraParams' => $auth['read'] + array('view'=>'detail') + $filter,
      'nocache' => true,
    );
    $isos = data_entry_helper::get_population_data($options);
```
So we need to add the code immediately after this. We can't do it any earlier as we need the keys from the identifiers\_subject\_observations to select the corresponding attribute rows. The new code is;

```
    // load the identifiers_subject_observation_attributes(s) for this sample
    $isoIds = array();
    foreach ($isos as $iso) {
      $isoIds[] = $iso['id'];
    }
    $query = array('in'=>array('identifiers_subject_observation_id', $isoIds));
    $filter = array('query'=>json_encode($query),);
    $options = array(
      'table' => 'identifiers_subject_observation_attribute_value',
      'extraParams' => $auth['read'] + $filter,
      'nocache' => true,
    );
    $isoAttrs = data_entry_helper::get_population_data($options);
```

In the form submission code, we needed to 'flatten' the keys by removing their prefixes. On reload, we need to do the reverse, we need to prepend form specific context data to the front of each table:field key and then put it in the reloaded data array. This is coded in the large for loop commented '// add each identifier to the form data'. We can clone the code for idnAttr to do this.

The code we add is:

```
              $fieldprefix = 'idn:'.$idx.':'.$identifier_type.':isoAttr:';
              foreach ($isoAttrs as $isoAttr) {
                if ($iso['id']===$isoAttr['identifiers_subject_observation_id']) {
                  if (!empty($isoAttr['id'])) {
                    $form_data[$fieldprefix.$isoAttr['identifiers_subject_observation_attribute_id'].':'.$isoAttr['id']] = $isoAttr['raw_value'];
                  }
                }
              }
```
That completes the data reload, but we have to revisit the submission code now. On submission the code works out if it needs to insert, update or delete identifier records. This code is in the build\_identifier\_observation\_submission() function. If the identifier is unchanged, it is not updated and neither is the identifiers\_subject\_observation record. We rely on the update of this record to update any changed iso attributes, so we need extra code to test if iso attributes have changed and to force an update if they have. The test code is;
```
    // see if we have any updates on the isoAttr
    $isoAttrUpdated = count(preg_grep('/^isoAttr:[0-9]+$/', array_keys($values))) > 0;
    if (!$isoAttrUpdated) {
      $keys = preg_grep('/^isoAttr:[0-9]+:[0-9]+$/', array_keys($values));
      foreach ($keys as $key) {
        if ($values[$key]==='') {
          $isoAttrUpdated = true;
          break;
        }
      }
    }
```
and the code to force an update is;
```
    // identifier exists and is unchanged but has iso attributes which have changed
    if ($old_id>0 && $old_id===$new_id && $isoAttrUpdated) {
      // update link to trigger update to isoAttr
      $iso = submission_builder::build_submission(
        $values, array('model'=>'identifiers_subject_observation',));
      $so['subModels'][] = array('fkId' => 'subject_observation_id', 'model' => $iso,);
    }
```
We now have a fully working form, time to test it and see if that's true!