# Upgrading an Indicia Warehouse Server #

If you have an existing Indicia Warehouse which needs to be upgraded, the following steps are required.

  1. Endeavour to notify users that you are upgrading the warehouse and that it will be temporarily unavailable.
  1. Stop your web server to prevent changes being made to the database by users or, better still, keep it running but deny access to users.
  1. Make a backup of your existing Indicia installation folder and database for safe keeping.
  1. Download the new version from the downloads page.
  1. Now, unzip the files and copy the contents directly over the contents of your existing installation folder.
  1. Restart your web server if you stopped it.
  1. Next, log into your Indicia Warehouse and visit the home page. You should see a notification that an upgrade needs to be run. Click the button to upgrade your warehouse.
  1. Re-enable access to the warehouse if you had denied it.

That's it!

## Developer Notes ##

If you are maintaining an Indicia Warehouse which is being kept up to date from the Subversion repository rather than downloaded releases, you won't see the upgrade notification on the home page after an SVN update unless the application version has changed. However, there are a couple of options for upgrading your database on a more ad-hoc basis:
  1. You can enable a facility to automatically run any new script files which appear in the latest scripts folder (modules/indicia\_setup/db/version\_x\_x\_x). This may have a small affect on performance though it should not be too significant. To do this, copy the file modules/indicia\_setup/config/upgrade.php.example and name the new file upgrade.php.
  1. Rather than automatically running any scripts that have been added to the setup when you performed SVN update, you can manually ask Indicia to bring the database fully up to date. To do this, just log in then visit the URL /index.php/home/upgrade. The advantage of this approach is that Indicia does not have to scan for upgrade scripts each time it is accessed, so the performance will be better.

The upgrade process places each set of scripts required for upgrade in a folder called modules/indicia\_setup/db/version\_x\_x\_x, where x\_x\_x reflects the version number. In addition, any code that is required for an upgrade can be placed in a method called version\_x\_x\_x placed in the Upgrade\_Model class, in modules/indicia\_setup/models/upgrade.php.