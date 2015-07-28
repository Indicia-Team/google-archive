# Development Conventions #

## Database Upgrade Scripts ##

For each upgrade to the datamodel, a script is added to the trunk\modules\indicia\_setup\db\upgrade\_x\_y\_to\_x\_y\db folder in SubVersion, replacing the x & y with the appropriate numbers for the latest upgrade set. The script makes the required alteration to the database and is saved as a file named using the following convention:

**yyyymmddhhmm\_description\_of\_change.sql**

This way scripts can easily be sorted chronologically and applied in sequence.

## Commits to SubVersion ##

When committing a change to SVN, please provide a comment that clearly explains the change you have made. Also, if the commit fixes or relates to an issue, then please include a reference to that issue on the first line of the comment, for example:

> Fixes [issue 80](https://code.google.com/p/indicia/issues/detail?id=80).
> Changed params to map\_picker method to be compatible with map\_helper class.


Then, set the issue status to fixed and provide a comment which mentions the revision, e.g.

> Fixed in [r800](https://code.google.com/p/indicia/source/detail?r=800).
> Map\_picker and map\_helper now compatible.


Note that by referring to the revision as r... Google's issue tracker can put a hyperlink to the revision details in automatically which is very handy.

## Coding Style ##

  * Keep your methods short - each method should be visible on one screen. Break down into several methods if necessary.
  * Indent all code blocks with 2 spaces.

## PHP ##
  * Comment your code using comments recognised by [phpDocumentor](http://www.phpdoc.org/).
  * Whilst developing, use strict warning messages. Obviously you may not want to do this on a production server but this will ensure you catch potential problems as early as possible. To do this, in your php.ini file, set the following then restart your webserver:
```
error_reporting = E_STRICT|E_ALL
display_errors = On
```

## JavaScript ##

  * The preferred JavaScript library for Indicia development is JQuery.
  * Before checking in any JavaScript code, please check the code using the [JSLint](http://www.jslint.com/) tool.