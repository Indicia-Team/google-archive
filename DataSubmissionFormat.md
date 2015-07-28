# Introduction #

The following example uses JSON notation. Support for XML schemas is not yet supported.

# Details #

## Posting to the entity web services ##

Each entity that is supported by the webservices is represented by a single URL at .../index.php/services/data/_entity\_name_, for example .../index.php/services/data/sample. An http POST sent to this URL with a parameter called submission containing a JSON representation of the object you wish to post will add that object to the database. The structure of the json parameter is:

```
{ "id" : "_Model Name",
	"fields" : 
	{
		"_field id" :
		{ "value" : "_field value" },
		"_field id 2" :
		{ "value" : "_field value 2"}
	}
}
```

### Data types ###

  * **boolean** - when posting a boolean value, pass the string values 't' or 'f' for true or false respectively.

## Posting to the save service ##

In addition to the URLs representing each entity, there is a service that allows more complex data to be posted, for example a sample containing several occurrences. An example submission to the Indicia save web service follows. The json string representing the data you want to post should be sent to the
URL at .../index.php/services/data/save as a POST parameter called submission. Items prefixed with an underscore should be replaced with the implied value.

```

{ "submission" :
	{ "entries" : [
		{ "model" :
			{
				"id" : "_Model Name",
				"fields" : 
				{
					"_field id" :
					{ "value" : "_field value" },
					"_field id 2" :
					{ "value" : "_field value 2"}
				},
				"fkFields" :
				{
					"_field id (fk_modelname)" :
					{
						"fkTable" : "_name of model this is a fk to",
						"fkSearchField" : "_field to search in",
						"fkSearchValue" : "_value to search for",
						"fkIdField" : "_field to return value from"
					}
					
				},
				"superModels" : [
				{ 
					"fkId" : "_foreign key field in model to insert supermodel id",
					"model" :
					{
						"id" : "_Model Name",
						"fields" : 
							....
					}
					....
				}],
				"subModels" : [
				{ 
					"fkId" : "_foreign key field in submodel to insert model id",
					"model" :
					{
						"id" : "_Model Name",
						"fields" : 
							....
					}
					....
				}],
				"metaFields" : 
				{
					"_field id" :
					{ "value" : "_value" },
					"_field id 2" :
					{ "value" : "_value 2" }
				}
			}
		},
		{ "model" : 
			....
		}]
	}
}

```

## Using a transaction ID for async posting ##

If you are writing a form which uses AJAX to do form posting, you may wish to do asynchronous posts so that the user interface does not lock up during the save operation. An example might be a grid of occurrences which save automatically when the input focus leaves a row. The [jQuery Form plugin](http://jquery.malsup.com/form/) is available in Indicia and supports both synchronous and asynchronous posting of forms. However, you must be aware that your JavaScript cannot post the form straight to the Warehouse for security reasons and must first format the form into a submission anyway. Indicia provides a Drupal AJAX Proxy module which allows forms to be posted to a proxy script; this script then attaches write authentication tokens to the form and formats it as a submission, then sends it to the Warehouse.

One problem you will face if you elect to use asynchronous posting is how to identify the row that is being referred to in the response from the Warehouse. For example, if there is a validation error, then how can the code know which row that error relates to? The answer is to use a transaction\_id field in your form. So, in this case you might include the following:
```
<input id="transaction_id" name="transaction_id" value="row-id"/>
```
The JavaScript code will change the value of this input to a unique identifier for the row being posted. Then, the response from the Warehouse will include a transaction\_id element which can be used to identify the row to attach the error to.