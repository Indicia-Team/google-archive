# Model View Controller code for the warehouse #

The Indicia warehouse uses [MVC](http://en.wikipedia.org/wiki/Model–view–controller) (Model-View-Controller) to provide a consistent structure to the code. This is based on the MVC flavour provided by the [Kohana framework](http://kohanaframework.org/) but with several custom extensions. These articles assume you have a basic knowledge of PHP code and MVC.

Each database entity has code defined in 3 files, the model, the view and the controller. In addition a database script is required that defines the table schema as well as, normally, 3 views for different usages of the table data.

[Database Scripts required for each database entity](WarehouseCodeAddEntityScripts.md)

[Model code required for each database entity](WarehouseCodeAddEntityModel.md)

[Controller code required for each database entity](WarehouseCodeAddEntityController.md)

[View code required for each database entity](WarehouseCodeAddEntityView.md)