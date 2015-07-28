# Introduction #

This page explains how to install the Joomla Indicia component. Before starting, you should have access to a working Indicia server, plus a working installation of Joomla version 1.5.9 or later. At the moment you will also need a SubVersion client to access the code, though it will be packaged for download when ready. Ideally you should also have a GeoServer installation, configured with a feature type that exposes the data you will be capturing using the component. This data should include the taxa\_taxon\_list\_id attribute so that the Joomla component can filter the displayed map by species.


# Details #
  1. First, using your SubVersion client checkout the code from http://indicia.googlecode.com/svn/joomla/trunk/ into a new folder.
  1. Now, copy the files from this folder directly into the root of your Joomla installation, placing the files inside existing folders as required.
  1. From the files you downloaded, find the file administrator\components\com\_indicia\indicia.xml and zip it to create a new file. Place this file in a temporary location on your disk.
  1. In the Joomla administrator interface, select Extensions then Install/Uninstall from the menu. In the section Upload Package File, select your zip file and upload it.

The component is now installed. You can now [add the component to a menu item](JoomlaConfiguration.md) so that a data entry page is accessible from your website.