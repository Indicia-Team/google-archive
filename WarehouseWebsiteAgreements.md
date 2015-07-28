# Website Agreements #

Website agreements are a feature introduced into Indicia in version 0.8 which allow data sharing agreements between websites sharing a website to be defined. Examples of uses of website agreements are:

  1. To define a group of websites that all allow each other include their data in reports.
  1. To allow a single website to provide verification, moderation or data flow task management for a network of other websites that then only have to be responsible for data capture.
  1. To allow a central reporting portal to be built which reports on data from several other websites.

When setting up an agreement, you always associate websites to an agreement record, never directly to each other. So if you want a pair of websites to have an agreement between them you still create an agreement record to act as a link between them. The website agreement record defined what is allowed for the websites in the agreement, for example do they have to provide records for reporting, or is it optional? Then when a link is created between a website and a website agreement you define exactly how that website will participate, e.g. which of the optional data sharing arrangements will it participate in.

Agreements work by providing hints to the reporting system to determine the records to return when performing reports for various tasks. Hints can be provided as to whether a website should provide or receive data for any of the following tasks: reporting, peer review, verification, data flow or moderation.

## Setting up website agreements ##

There are two things to do to set up website agreements ready for use. First agreements must be defined, then websites must be associated with those agreements (participate in the agreements). Website agreements are set up on the warehouse and require administrative access to the warehouse to do so. When logged in as an administrator there is a Website Agreements item on the Admin menu. Create an agreement as you would any other item on the warehouse. When creating the agreement provide a **title** and **description**, then there are a series of options to define whether providing or receiving data for each of the defined tasks (reporting, peer review, verification, data flow or moderation) is disabled, optional or required. There is also an possibility to specify that the task is optional but must be set by a core administrator. For example in the third example at the top of this page, you might not want the other websites to be able to report on each other’s data, only the central portal.

Once an agreement has been created, select to edit the website you want to join to the agreement. This can be done by someone with admin rights to the warehouse or just the website. Then select the Agreements tab and click the **Join Website Agreement** button. This lets you add the website to an agreement and also to define which of the optional data sharing tasks this website wishes to participate in. You must be logged in as a warehouse administrator to change options for tasks that were defined as requiring setup by an administrator. Options you cannot change will be greyed out.

## Using agreements in reporting ##

If you are interested in how to write queries or reports that use the website agreement information then read on. The logic required to work out where data should flow is fairly complex, since to work out whether another website’s data should be included in a report you must check the settings for the other website providing data for that task, as well as the website running the report receiving the data for the task. To simplify the querying substantially, a table has been created called **index\_websites\_website\_agreements**, the reason for this name will become clear in a moment. This view contains a **from\_website\_id** and **to\_website\_id** with a list of flags defining if data is provided or received for each of the tasks. In general then, you would write a query that joins to this view using the current website’s ID to filter on the from\_website\_id and then checking the receives_... flag for the relevant task. Here’s an example which lists all the occurrences that the current website (ID=2) should verify:
```
SELECT o.*
FROM occurrences o
INNER JOIN index_websites_website_agreements i 
    ON i.to_website_id=o.website_id 
    AND i.receives_for_verification=true
WHERE i.from_website_id=2
```_

The the logic for defining the contents of this table can be found in the view **build\_index\_websites\_website\_agreements**.