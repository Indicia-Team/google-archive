# Database scripts required to add a new warehouse database entity #

Database schema and other database update scripts are released by creating **.sql** files in the **modules/indicia\_setup/db/version\_x\_x\_x** folder where x\_x\_x is replaced by the version of the Indicia warehouse that the update is being applied for. Each file must be named with the date and time in yyyymmddhhmm format, followed by an underscore then a brief description. E.g. a script to updated a view called _list\_occurrences_ might be called _201202041957\_list\_occurrences.sql_. By naming the scripts in this way it ensures that they can be run in the correct order.

When writing scripts, it is important to ensure that:
  1. The database schema name is removed from the script, e.g. if a script includes `CREATE TABLE indicia.new_table` as part of it, this should be rewritten `CREATE TABLE new_table`. Some installations of Indicia may have opted not to use the default schema name so this is important.
  1. All change ownership statements (which are included in the scripts created by **pgAdmin**) are removed as they are not necessary. As before, other installations of Indicia may have opted to use a non-default username to connect to the database, so any change ownership statements would break on those systems as the username would not exist. The upgrade scripts are run using the same connection as the rest of the warehouse code so the objects will have the correct owner by default anyway.
  1. All table objects are created in accordance with the [conventions of the Kohana Object Relational Mapping](http://docs.kohanaphp.com/libraries/orm/starting#orm_conventions) (ORM) implementation. In particular:
    1. Table names are created using lowercase and the plural form with underscores to separate words. E.g. use _survey\_events_ not _SurveyEvent_.
    1. All tables have a primary key called _id_ which has a _serial_ datatype.
    1. Foreign keys are named after the singular version of the related table, followed by an underscore then _id_. E.g. a table containing a relationship to the survey\_event might have a field _survey\_event\_id_. Although this should be the default, where it is necessary to use a different field name to provide better meaning, or where there are several foreign key fields in one table that relate to the same table, they can be named differently as long as the model code includes information on the field names used. An example of this is the _created\_by\_id_ and _updated\_by\_id_ fields in most tables, which are both foreign keys to the _user_ table.
    1. When creating a join table (a table whose sole purpose is to provide a many-to-many relationship between two other tables), the table must be called _table1\_table2_ where table1 is the name of the first table in alphabetic order and table2 is the name of the second table in alphabetic order. For example, the table which links _users_ and _websites_ together is called _users\_websites_.
For each table that you create, consider whether you need to create the following views:
  1. For any table that is exposed for data access via the data services, create a view called list\_myrecords where myrecords is the table name (plural). Create an upgrade script for this in your module as described above. This view should contain the minimum details required to provide the basic information for the record as this view is generally used for quick lookups against the data.
  1. For any table that is exposed for data access via the data services, you should also create a view called detail\_myrecords where myrecords is the table name (plural). Create an upgrade script for this in your module as described above. This view should expose more comprehensive information for each record, joining in other parts of the data model as required.
  1. For any table that is visible in the warehouse via an index table, create a view called gv\_myrecords where myrecords is the table name (plural). Gv is short for gridview and represents the view used to display data in the index table for each data entity. So, if the grid needs to include column values from other tables, these tables must be joined into the view and the columns included in the list of selected fields.

See Also: [Updating the database schema](UpdatingDatabaseSchema.md)


<table width='100%'><tr>
<td align='left'>Previous: <a href='WarehouseCodeAddEntity.md'>Model View Controller code for the warehouse</a></td>
<td align='right'>Next: <a href='WarehouseCodeAddEntityModel.md'>Creating the model code</a></td>
</tr></table>