# Standalone Distribution Map 1 #

All the prebuilt form library is often used from the context of the Drupal content management system, it is possible to use many of the forms from standalone PHP simply by reviewing the list of parameters in the form's get\_parameters method and supplying this as an array to the form's get\_form method. Here is an example PHP page:

```
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
    <head>
        <title>Occurence Map</title>
        <link rel="stylesheet" href="demo.css" type="text/css" media="screen"/>
        <link rel="stylesheet" href="../../media/css/default_site.css" type="text/css" media="screen"/>
        <?php
        include '../../client_helpers/data_entry_helper.php';
        require 'data_entry_config.php';
        require '../../client_helpers/prebuilt_forms/distribution_map_1.php';
        
        echo data_entry_helper::dump_header(); ?>
    </head>
    <body>
        <div id="wrap">
            <h1>Occurence Map</h1>
            <?php
            echo iform_distribution_map_1::get_form(array(
                'website_id' => $config['website_id'],
                'taxon_list_id' => '5',
                'map_centroid_lat' => '54',
                'map_centroid_long' => '13',
                'map_zoom' => '8',
                'show_all_species' => 'true',
                'wms_feature_type' => 'indicia:detail_occurrences',
                'map_width' => '900',
                'map_height' => '600',
                'preset_layers' => array(
                    'openlayers_wms' => '0',
                    'virtual_earth' => '0',
                    'google_hybrid' => '5',
                    'google_physical' => '0',
                    'google_streets' => '0',
                    'google_satellite' => '8')
            ));
            echo data_entry_helper::dump_javascript();
            ?>
        </div>
    </body>
</html>
```