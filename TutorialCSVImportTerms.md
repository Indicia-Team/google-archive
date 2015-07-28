# Introduction #

To configure the Indicia Warehouse quickly there is a facility for importing comma-separated variable (csv) files in some places. This can help populate some of the lists if you already have the information in a database or a spreadsheet (which can usually save files in the csv format).

Specifically, this option exists for taxa in a species list, taxon groups, terms in a term list, locations, surveys, languages, titles


## Example - Uploading a term list ##

Term lists are a way to control the values that can be entered in a field on your recording form. Imagine you want to have a field on a recording form for specifying the vice county of a record. You are going to have to specify a custom sample attribute in the warehouse to accommodate this. If you create a term list of vice counties then you can set the sample attribute to be of type lookup list, join it to my term list, and ensure that only values in the term list are allowable.

Rather than input each term individually I am going to demonstrate importing them from a csv file. I have already got a suitable file prepared. The first few lines look like this.

> VC Number,VC Name,Language<br>1,West Cornwall (with Scilly),English<br>2,East Cornwall,English<br>3,South Devon,English<br>4,North Devon,English</li></ul>

Note that the first line contains headings for the columns below. These headings should be alpha-numeric. If you encounter an error message "Disallowed key characters in global data" when uploading the file then check your headings.<br>
<br>
Now follow these steps.<br>
<br>
<ol><li>Having logged in to the Indicia Warehouse goto Lookup Lists > Term Lists.<br>
</li><li>Click on the new termist button and enter list details - a title, description and owner - then Save.<br>
</li><li>Find your term list in the grid and click on the edit button.<br>
</li><li>Select the terms tab, click on the browse button, select your file and click on upload csv file.<br>
</li><li>On the form that follows you have to map the columns in your csv file on to the fields of a term. As you can see in the screen shot below, my VC Number column is going to map on to the sort order of the terms, the VC Name is the term itself, and Language maps on to Term Language. The phrase "(lookup Existing Record)" means that the column entry is going to be looked up and compared to a value in anoter table and, therefore, it must exist in that table already. In this example I couldn't specify a language that is not already declared.<br>
</li><li>Now click the upload data button and your list will be imported. Read the subsequent screen and check for errors.</li></ol>

Caveat. You could hit a PHP timeout after 30 seconds when uploading so big lists will cause problems! It is on the issues list already as something to fix. The 113 vice counties only took 5 seconds or so, however.<br>
<hr />
<img src='http://indicia.googlecode.com/svn/wiki/import_terms.png' />