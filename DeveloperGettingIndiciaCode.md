# Getting the Indicia code #

Indicia's code is hosted on the Google Code website and can be obtained either as a download of the latest stable release via the [Downloads](http://code.google.com/p/indicia/downloads/list) tab, or direct from the code repository for other versions of the code. The code repository uses a source control system called Subversion - this provides audit trails of code changes and version management amongst other things. If you are not familiar with Subversion or version control in general then this might be a good time to read a primer, for example you the [Subversion manual](http://svnbook.red-bean.com/) contains chapters on Version Control Basics and Version Control the Subversion Way which provide a good starting point.

Although it is possible to browse the code via the [Source tab](http://code.google.com/p/indicia/source/checkout), in most cases you will want to use a **Subversion client** to access the code. There are many options available and the best one will depend on your choice of development operating system and code editor. For Windows use a popular choice is [TortoiseSVN](http://tortoisesvn.net/) and I also use the client built into the [NetBeans IDE](http://netbeans.org/) which is multi-platform.

## Trunks, branches and tags ##

Subversion keeps code in a tree structure which maps to a folder structure on your disk. By convention it splits the code for each project or sub-project into 3 folders:
  * **trunk** - This folder contains the latest 'bleeding edge' code and is the one that developers actively commit changes into when they become part of the project core. Although it is not good practice to knowingly commit changes into the trunk which will break the project, it is also not expected that every change will be fully tested for production when it is committed. Therefore **the trunk code should not be used for production servers**.
  * **branches** - A branch is a copy of the code which is being maintained alongside the trunk for one reason or another. For example, a branch might be created when a developer wants to develop a feature and not commit the feature to the trunk until the feature is ready. Or a branch could be created for a developer to write experimental code against. The advantage of using a branch in Subversion over just creating a copy of the code on your disk is that you get full change tracking and other useful features of SVN within the confines of your branch.
  * **tags** - A tag is a snapshot of the code taken at a point in time. In Indicia we use tags to keep copies of the released code that was used for stable downloads for future reference.

## Navigating the Indicia repository ##

### Warehouse ###

The warehouse code is found at indicia.googlecode.com/svn/core/trunk/ (use http for read-only access or https for read-write access if you have commit rights to the project).

When there is a version of the Indicia warehouse which is in testing and nearly ready for release it will be found under indicia.googlecode.com/svn/core/branches/ in a folder named after the version. This allows developers to continue developing against the trunk without risking adversely affecting the release. At this stage, when a developer finds an important bug in the trunk which also exists in the version branch, they should commit that bug to the branch as well as long as they consider that there is no risk of knock-on effects on the rest of the code. Likewise any bugs found during the testing of the version branch must be fixed in both the branch and the trunk code.

To summarise if you want to obtain a stable version of the warehouse, you can obtain it from the downloads page or via the tags folder in Subversion. For a pre-release of the next version visit the branches folder (if one is available at that point in time). For the latest bleeding edge code use the Subversion trunk folder
