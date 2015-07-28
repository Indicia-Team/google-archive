# Introduction #

This tutorial is not complete. At the moment it is just a placeholder for information about using the mapping controls.

## Example of adding a custom WMS base layer ##
```
    data_entry_helper::$onload_javascript .= "var baseLayer = new OpenLayers.Layer.WMS(
      'OpenLayers WMS', 
      'http://labs.metacarta.com/wms/vmap0', 
      {layers: 'basic', 'sphericalMercator': true}
    );\n";    
    return data_entry_helper::map_panel(array(
      'readAuth' => $readAuth,
      'presetLayers' => array('google_satellite'),
      'layers' => array('baseLayer')    
    ));
```