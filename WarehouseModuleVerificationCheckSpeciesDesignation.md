# Setting up species designation/rarity verification checks #

**Introduced in Indicia 0.8**

The Indicia automated verification check system allows you to flag any designated taxon as needing further checks. Therefore if you attach a designation “Rare” to a species, all records of that species will be highlighted to experts during the verification process.

# Setup #

## 1. Ensure the correct warehouse modules are installed ##
If you are not the administrator of the warehouse, please pass the information in this section on to the warehouse administrator who can enable the appropriate modules for you.

The modules you need to enable are **taxon\_designations**, **verification\_check** and **verification\_check\_species\_designation**. To enable these, edit the ` $config['modules']` defined in your application/config/config.php file on the warehouse. Add the modules to the end of the list as follows, excluding any that are already in the list:

```
$config['modules'] = array
(
  ...
  MODPATH.'taxon_designations',
  MODPATH.'verification_check',
  MODPATH.'verification_check_species_designation'
);
```

Now, log into the warehouse and visit the **index.php/home/upgrade** path to make the required database changes. Finally, delete the file(s) called indicia-menu**.** in the application/cache folder.

## 2. Enable verification checks for the website registration ##

Before Indicia will process data in your website for verification checks, you have to enable the option to automatically check data against verification check rules. To do this, select **Admin > Websites** from the menu and click the **edit** link. Tick the **Enable auto-verification checks** checkbox and save the registration.

## 3. Setup a designation ##

This step requires you to either have the admin login for the warehouse, or to at least have a login as the admin of a website registered on the warehouse. If you don’t have either of these then please pass this information on to the warehouse administrator who can setup the required designation for you.

Now that the taxon designations module is installed on the warehouse as well as the verification check modules for species designations, you can create a designation on the warehouse. We are going to create a simple informal designation called “Rare”. First we need to create a designation category called “informal” so we can group similar designations. Log into the warehouse, then select **Lookup Lists > Termlists** from the menu. In the list of termlists, search for and click **edit** for the termlist called **Taxon designation categories**. Select the **Terms** tab. If the category you want already exists then there is no need to add another, otherwise click **New Term**. Set the **Term Name** to “Informal”, the **Language** to “English” then press **Save**.

Next, select **Admin > Taxon Designations** from the menu. This shows you a list of designations already available on the system, so check that the designation you want does not already exist. You can skip the next step if it does. Click **New taxon designation** then fill in **Title** as “Rare”, **Category** as “Informal” and press **Save**.

## 4. Attach the designation to a species ##

Next, select **Lookup Lists > Species lists** from the menu and click **edit** for the species list containing your rare species. Select the **Taxa** tab and search for the species you want to flag as rare then click the **edit** link for the species. Click the **Designations** tab then click the **New designation** button. It is enough to simply set the **Designation** to “rare” then click **Save**.

## 5. Running the checks. ##

Ideally, the warehouse administrator will schedule a task so that verification checks are run periodically, e.g. every hour. If this is not the case, or if you need to immediately run the checks for any reason, simply point your browser at the path index.php/scheduled\_tasks relative to your Indicia warehouse installation. This will force the newly added information to be checked.

## 6. Updating designations in bulk. ##

The designations for each taxon can take a while to add if you are doing them one by one as suggested above. Fortunately there is a possibility to do this by uploading a spreadsheet in CSV file format. The spreadsheet must match the exact columns as required by the warehouse. If you navigate to **Admin > Taxon Designations** on the warehouse there is information at the bottom on the columns you must set up. Here is an example of a spreadsheet which could be used to attach a designation of Rare to a single species. The first 5 columns map directly to columns in the designation itself and will either be used to lookup an existing designation type or will be used to create a new one if no match is found. Taxon and taxon external key are both used to lookup the taxon to join to. The remaining columns are attributes of the relationship between the taxon and the designation. You must specify at least a designation title, designation category and description.

|designation title|designation code|designation abbr|designation description|designation category|taxon|taxon external key|start date|source|geographic constraint|
|:----------------|:---------------|:---------------|:----------------------|:-------------------|:----|:-----------------|:---------|:-----|:--------------------|
|Rare             |	               |	               |	                      |Informal            |Somateria mollissima |	                 |	         |	     |	                    |