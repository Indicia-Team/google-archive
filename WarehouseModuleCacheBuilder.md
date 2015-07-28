# Cache Builder Warehouse Module #

The Cache Builder generates 4 tables in the database, **cache\_occurrences**, **cache\_taxa\_taxon\_lists**, **cache\_taxon\_searchterms** and **cache\_termlists\_terms**. These contain the most useful fields for occurrences, taxa, taxon searching and term records respectively, allowing many report queries to be built with little or no need to join to other tables, hence improving performance considerably.

The negative effect of using cache tables is that the data will be slightly out of date as they are only updated when the index.php/scheduled\_tasks path is visited on the server (normally set to run using Windows Task Scheduler or Cron on a regular basis). This issue is mitigated with cache\_occurrences by immediately inserting records into the cache as they are added to the occurrences table, though updates and deletes are only processed when the scheduled\_tasks path is hit.

## Installation ##

The Cache Builder is installed by default on installations performed after version 0.8.1 of the warehouse. It can be installed on other warehouses by editing the application/config/config.php file using a text editor, then finding the definition of the `$config['modules']` array. Ensure that this contains the following line:
```
MODPATH.'cache_builder',
```

Now, log into the warehouse with administrator rights, then visit the path index.php/home/upgrade to ensure that the Cache Builder's database tables are created.

The cache\_builder will populate a small part of the cache each time the task scheduler is triggered. If a full index rebuild is required, specify a url parameter called **force\_cache\_rebuild**, e.g.

`www.mysite.com/indicia/index.php/scheduled_tasks?force_cache_rebuild`

## Using the cache tables ##

Each of the cache tables can be used in report queries in place of the underlying model tables. For example, if you need to get the date, grid ref and species of a record, using the cache tables this information is all available in cache\_occurrences. Using the underlying "raw" tables, this information must be obtained with a query that joins from `samples` -> `occurrences` -> `taxa_taxon_lists` -> `taxa`. Not only is the query complexity reduced massively, the performance of the query is considerably increased.

The following tables are included:

  * **cache\_occurrences** - Use this table to get the key information of each occurrence record, including basic sample and taxon information.
  * **cache\_taxa\_taxon\_lists** - Use this table to get the key information for taxa, including the name, common and preferred name and list information.
  * **cache\_taxon\_searchterms** - This table provides a searchable index of taxon names that might be used in a lookup. It is a little different to the other cache tables in that it is not intended for reporting, rather it is to ensure that AJAX species name searches can operate using as simple (and therefore fast) a query as possible. It contains a row for each taxon name, as well as simplified derivative names (enabling tolerance of punctuation and spacing issues when searching), plus rows for each searchable taxonomic code (**taxon\_codes** table). Use of this table when searching is an option available in the data entry systems called Cache Lookups.
  * **cache\_termlists\_terms** - This table provides the key information required for each termlist term record, used for population of lookups and reporting.