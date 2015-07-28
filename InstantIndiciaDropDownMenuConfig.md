# How to add a drop down menu in Instant Indicia #

There are several ways you can add a drop down menu to Instant Indicia powered websites, or indeed any Drupal website, mostly involving the installation of modules. This approach uses the [Nice Menus](http://drupal.org/project/nice_menus) module to create the drop down menus and in addition we use the [Special Menu Items](http://drupal.org/project/special_menu_items) module to allow parent menu items to be created that don’t have a page themselves.

## Steps ##

1)	Visit each of the two module pages above and download the zip files for the current recommended release for 6.x.

2)	Unzip each of the downloaded files into your sites/all/modules directory in your Drupal installation.

3)	Log into your Drupal site as administrator, then visit **Site building > Modules**. Search through the list for Nice Menus and Special Menu Items and tick the boxes to enable them, then click the **Save configuration** button at the bottom.

4)	Once the modules are installed, you need to use the **blocks** system to configure where on the page the Nice Menu should be displayed. Select **Site building > Blocks** from the menu.

5)	Look down the page for the **Nice Menu 1 (Nice Menu)** block. Use the drop down box beside it to select the **Navigation** section then click the **Save Blocks** button.

6)	Once the page has saved, click the **configure** link beside the Nice Menu 1 (Nice Menu) block in the Navigation section. Change the **Menu Parent** to **<Primary Links>** (which should be fairly near the bottom of the list) and change the **Menu Style** to **down**. There are a number of other settings on this page but these are the only ones that you need to change to get it working. You might like to also change the **Menu Name** to “Primary Links menu” or similar.

7)	Next you need to lay out your menu. Select **Site building > Menus > List menus** from the menu then choose the **Primary Links** menu, as this menu is normally the site’s main menu.

8)	We first want to add a top-level item, e.g. a section called “About” under which we can put pages “About this site” and “Contact Us”. So, click the **Add Item** link.

9)	You should now see the edit page for a new menu item. Set the **Path** to nolink because we want this menu item to be a parent item which does not have a page associated with it. Set the **Menu Link Title** to **About**.  You can optionally set a **Description** at this point, or the item’s **Weight** to position the item in the correct location in the menu, although you may find this easier to do by drag and drop when viewing the whole list. Click the **Save** button to return to the list of menu items.

10)	From this page you can re-organise your menu items by dragging the arrow symbol markers around in the list. Drag them slightly to the right if you want to make an item a child of another item. Remember to click the **Save configuration** button when you have finished making changes.

Don’t forget that you can create new parent menu items using the **Add Item** link, or on any Drupal page (including an Indicia form) the **Edit** view has a **Menu settings** page which lets you create a menu item for the page, so that you can then return to this page and organise your menu using drag and drop.