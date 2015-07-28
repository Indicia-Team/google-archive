# Getting Started #

First off, welcome to the Indicia project and thanks for taking the time to check it out. It's early days for Indicia yet, but we hope you will find enough here to be useful right now and more importantly to see the potential for the project in future.

At the moment this page is just a scratchpad for a few things you may find useful when getting started as an Indicia developer.

## Coding Standards ##

A few words on coding standards. I don't want to bore you with lists of naming conventions that you must learn, but there are a couple of tips that will help us all get along and work on each other's code without rewriting it every time we open a new code file.

Firstly, please take the time to look over your code and consider if it is well structured, readable and maintainable. It might be tempting to rush changes into the code but that will no doubt slow things down in the long run. That also means keep your method short, simple and break them up into several smaller methods if necessary.

Secondly, please comment code using the standards required of [phpDocumentor](http://www.phpdoc.org/). That will help us auto-generate all the documentation for the project when the time comes.

Thirdly, when writing SQL directly against the database, please avoid using SELECT `*` - the system will try to automatically convert geometry fields to WKT, but it can't do this if it doesn't know they're there. Also note that any field name ending in 'geom' will be treated this way, so don't use that for any fields that aren't geometry data.

## Handy Tutorials ##

[Introduction to OpenLayers Workshop](http://workshops.opengeo.org/openlayers-intro/)

[Installing Eclipse PDT for PHP development](http://2tbsp.com/node/40)