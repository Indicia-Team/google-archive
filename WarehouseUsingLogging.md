# Using debug logging on the Warehouse #

In order to diagnose problems that occur when running the Warehouse it is sometimes necessary to obtain a detailed log of what is happening on the server. For example, any exceptions that occur will show the user an "Unable to complete request" page, but to diagnose and fix this you need to know what the exception was.

The log files themselves are kept in the application/logs directory in a file called yyyy-mm-dd.log.php, with one created for each day.

Indicia logs information to the level of detail specified in its configuration. To change the configuration, open the file application/config/config.php using a text editor. Locate the following code:
```
/**
 * Log thresholds:
 *  0 - Disable logging
 *  1 - Errors and exceptions
 *  2 - Warnings
 *  3 - Notices
 *  4 - Debugging
 */
$config['log_threshold'] = 1;
```

As you can see you can change the log threshold setting to a value from 0 to 4. Setting this to 4 will result in large log files but the most detail possible so is recommended only whilst developing or diagnosing problems. Any changes you make to this file take immediate effect.