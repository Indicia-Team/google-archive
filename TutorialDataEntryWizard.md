# Introduction #

The [Building a basic PHP data entry page](TutorialBuildingBasicPage.md) demonstrates how to construct a single web page used for entering an occurrence. However, to keep things simple it is often beneficial to construct a "wizard" style of interface for data entry. There are 2 methods of doing this, firstly where the wizard pages are each individual HTML pages. The second method involves putting each wizard page on hidden panels which are shown only as required. This tutorial describes the first method, for the hidden panels method see the tutorial [Converting a data entry page to a tabbed interface](TutorialTabs.md).

Before starting, you should have a working data entry page which you would like to split into several pages.

  1. On each of your PHP pages in the wizard, in order to setup PHP sessions insert the following at the very top of the file:
```
<?php session_start(); ?>
```
  1. At the start of the wizard, e.g. when the user accesses the link to the wizard's first page, call `data_entry_helper::clear_session();`. This ensures the wizard data starts fresh and no previous values are remembered.
  1. Copy the page several times, one for each page plus one for a page at the end that will save the occurrence and acknowledge the user's contribution. Remove the unwanted controls from each page and leave the save code only on the last page. Now, ensure that the submit button for each page's form takes you to the next page in sequence.
  1. Insert the following into each page: `data_entry_helper::add_post_to_session();`. This ensures that any data posted into the page from a previous page is saved to the PHP session. On the save page, ensure this happens at the start of the save code.
  1. On the save page, after the add\_post\_to\_session call, but before the rest of the code, extract the data from the session using the data\_entry\_helper then use this to wrap the data up for submission. E.g. your code should read:
```
$data = data_entry_helper::extract_session_array();
$sampleMod = data_entry_helper::wrap($data, 'sample');
$occurrenceMod = data_entry_helper::wrap($data, 'occurrence');
...
```
  1. Also on the save page, you can output a custom block of HTML only if the result is a success by checking the result in the response. For example:
```
if (array_key_exists('success', $response)) {
	// Code here to redirect to a success page
} else {
	echo data_entry_helper::dump_errors($response);
}
```