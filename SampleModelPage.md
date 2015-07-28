This page describes and outlines the structure of an indicial Model, representing a database entity.

# Model Structure #

Model name: for table _items_, the model should have name _Item\_Model_. Unusual pluralisations should be configured in config/inflector.php

```

class Sample_Model extends ORM {

}

```

Where parent/child relationships exist, the model should extend ORM\_Tree in place of ORM.

## Class variables ##

_$search\_field_ should define the default field to search when querying this model.

```
protected $search_field='title';
```

_$additional\_csv\_fields_ defines any special fields which can be uploaded for this model from a CSV file, with the key of each item being the field name and the value being the caption. For an example of this, refer to the occurrence model which declares up to 3 images that can be uploaded from a CSV file.

```
protected $additional_csv_fields=array('fieldname'=>'caption');
```

_$has\_attributes_ is set to true in any model which supports custom attributes.

```
protected $has_attributes=true;
```

_$attrs\_submission\_name_ is set for any model that supports custom attributes. This gives the name of a metafield that can be included in submission data to list the custom attribute values for the submission. However, note that this method of passing custom attributes is now deprecated in favour of passing a field in the normal fields list of the submission.

```
protected $attrs_submission_name='locAttributes';
```

_$attrs\_field\_prefix_ gives the text that is combined with the custom attribute ID to form the name of the custom attribute item. This forms the name of the HTML control in a form which refers to the custom attribute and therefore provides the name of the data value in the submission fields data. For example, a sample attribute ID 3 would be called smpAttr:3 on the form.

```
protected $attrs_field_prefix='locAttr';
```

### Relationships ###

Relationships should be defined as outlined [here](http://docs.kohanaphp.com/libraries/orm#defining_relationships_in_orm).


## Class Methods ##

### Mandatory methods ###

_validate_ should take a Validation array, add required rules and then call the parent method. Any fields that are not referred to by the validation rules must be passed to the parent validation method in an $extraFields array. For example:

```

public function validate(Validation $array, $save = false) {
  // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
  $array->pre_filter('trim');
  $array->add_rules('title', 'required', 'length[1,100]');
  // Explicitly add those fields for which we don't do validation
  $extraFields = array(
    'description',
    'deleted'
  );
  return parent::validate($array, $save, $extraFields);
}

```

### Optional Methods ###

_caption_ should return a 'caption' for this model - for example, a caption for a person might be a combination of first name and surname. Defaults to:

```

	/**
	 * Return a displayable caption for the item, defined as the content of the field with the
	 * same name as search_field.
	 */
	public function caption()
	{
		return $this->__get($this->search_field);
	}

```

_get\_submission\_structure_ is used when the submission of a model record typically involves submitting other associated model data at the same time. For example this will make the associated model fields available during CSV file upload into the model. The occurrence model provides a good example allowing a sample parent to be uploaded with the occurrences:
```
 /**
  * Defines a submission structure for occurrences that lets samples be submitted at the same time, e.g. during CSV upload.
  */
  public function get_submission_structure() {
    return array(
        'model'=>$this->object_name,
        'superModels'=>array(
          'sample'=>array('fk' => 'sample_id')
        )     
    );
  }
```

_getSubmittableFields_ should return a list of fields that this model will accept and save into the database. Keys that should be used to link to foreign models should be prefixed with `fk_`. This method will default to:

```
        /**
	 * Returns an array of fields that this model will take when submitting. By default, this
	 * will return the fields of the underlying table, but where submodels are involved this
	 * may be overridden to include those also.
	 *
	 * When called with true, this will also add fk_ columns for any _id columns in the model.
	 */
	public function getSubmittableFields($fk = false) {
		$a = $this->table_columns;

		if ($fk == true) {
			foreach ($this->table_columns as $name => $type) {
				if (substr($name, -3) == "_id") {
					syslog(LOG_DEBUG, $name." added as fk field.");
					$a["fk_".substr($name, 0, -3)] = $type;
				}
			}
		}

		return $a;
	}

```

_preSubmit_ is called before any data is saved to the database and should tidy up data to be submitted to this model (submodels will call their own _preSubmit_ methods). Will default to:

```

        /**
	 * Ensures that the save array is validated before submission. Classes overriding
	 * this method should call this parent method after their changes to perform necessary
	 * checks unless they really want to skip them.
	 */
	protected function preSubmit(){
		//Overridden code happens here.

		// Ensure that the only fields being submitted are those present in the model.
		$this->submission['fields'] = array_intersect_key(
			$this->submission['fields'], $this->table_columns);


		// Where fields are numeric, ensure that we don't try to submit strings to
		// them.
		foreach ($this->submission['fields'] as $a => $b) {
			if ($b['value'] == '') {
				$type = $this->table_columns[$a];
				syslog(LOG_DEBUG, "Column ".$a." has type ".$type);
				switch ($type) {
					case 'int':
						$this->submission['fields'][$a]['value'] = null;
						break;
					}
			}
		}
```

_fixed\_values\_form_ allows a model to declare a form that is displayed before an import of data into the model. The form specifies controls which allow the user to specify values which will apply for every single imported record. For example, when importing a list of samples it is highly likely that the list will relate to a single survey, so the fixed values form could declare a control allowing selection of sample:survey\_id. This method returns an array with each entry being a control keyed by the field name the control is for. The definition of a control is identical in structure to the definition of parameters in XML reports, and therefore is an array with the following values:

**display** provides the display caption for the control.

**description** provides the control description which will be displayed alongside the control as help text.

**datatype** provides the type of data the control accepts, e.g. text, lookup.

**population\_call** allows a lookup control to specify the data contained in the list, obtained via a web service call.

**lookup\_values** is an alternative to population\_call and provides an associative array of values available for selection in the lookup control.

**linked\_to** is used for lookups which has a set of available options that are linked to another lookup's selected value. The linked\_to value is set to the name of the other lookup.

**linked\_filter\_field** is used in conjunction with **linked\_to** and allows a field to be specified as a filter in the lookup's web service population call, with the value of the field being set to the other lookup's selected value. For example, the following sets a survey ID selection control populated with the list of surveys for the website ID selected in the first control.
```
public function fixed_values_form() {
  return array(
    'website_id' => array( 
      'display'=>'Website', 
      'description'=>'Select the website to import records into.', 
      'datatype'=>'lookup',
      'population_call'=>'direct:website:id:title' 
    ),
    'survey_id' => array(
      'display'=>'Survey', 
      'description'=>'Select the survey to import records into.', 
      'datatype'=>'lookup',
      'population_call'=>'direct:survey:id:title',
      'linked_to'=>'website_id',
      'linked_filter_field'=>'website_id'
    )
  );
}
```