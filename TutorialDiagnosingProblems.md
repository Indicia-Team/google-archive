# Diagnosing Data Entry Page Problems #

If you have followed a tutorial or placed Indicia data entry code onto your website and it is not working, this page includes some tips on how to diagnose problems.

## Steps ##

The first thing to do is ensure that the data\_entry\_helper.php class is loading. If you are referring to it in the code like the following and this itself does not generate errors, then the paths must be Ok:
```
<?php  
require '<path>/data_entry_helper.php';
?>
```

Next, include the following line in your page code, inside the html section.
```
<?php
echo data_entry_helper::system_check();
?>
```

This will place an information box onto your page with a summary of checks made against your server regarding it's ability to run the Indicia code. Now, load your page and check that all the tests are listed as successful. If it works, you should see something like the following embedded in your page, depending on your page style:

![http://indicia.googlecode.com/svn/wiki/system_check.png](http://indicia.googlecode.com/svn/wiki/system_check.png)

If not, you might see warnings like in the following example:

![http://indicia.googlecode.com/svn/wiki/system_check_failed.png](http://indicia.googlecode.com/svn/wiki/system_check_failed.png)

Hopefully this information should help you know where to look.

If you have followed this procedure and are still not sure why it does not work, please post a message on the [Indicia forum](http://forums.nbn.org.uk/viewforum.php?id=19).