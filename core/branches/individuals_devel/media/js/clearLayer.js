
/* Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 */

/**
 * @requires OpenLayers/Control.js
 */

/**
 * Class: OpenLayers.Control.ClearLayer
 * The clear layer control provides a button linked to a vector layer such as the edit
 * layer which clears the content of the layer.
 * 
 * Inherits from:
 *  - <OpenLayers.Control>
 *  
 */
OpenLayers.Control.ClearLayer = OpenLayers.Class(OpenLayers.Control, {

    /**
     * Property: layers
     * {Array(<OpenLayers.Layer.Vector>} The array of layers this control will work on,
     * or the layer if the control was configured with a single layer
     */
    layers: null,
    
    /**
     * Property: clearReport
     * If true then a report map is cleared when the button is clicked.
     */
    clearReport: false,

    /**
     * Constructor: OpenLayers.Control.ClearLayer
     * A control to delete the contents of a vector layer.
     * 
     * Parameters:
     * options - {Object} An optional object whose properties will be used
     *     to extend the control.
     */
    initialize: function(layers, options) {
        OpenLayers.Control.prototype.initialize.apply(this, [options]);
        this.layers = layers;
    },

    /**
     * APIMethod: destroy
     */
    destroy: function() {
        this.deactivate();        
        OpenLayers.Control.prototype.destroy.apply(this, arguments);
    },

    activate: function() {
      // layers could be an array or a single layer
      if (this.layers.constructor.toString().indexOf("Array") == -1) {
        this.layers.removeAllFeatures();
      } else {
        $.each(this.layers, function(idx, layer) {
          layer.removeAllFeatures();
          if (layer.map.editLayer===layer) {
            // if clearing the edit layer, clear the imp-geom field
            $('#imp-geom').val('');
          }
        });
      }
      if (this.clearReport) {
        $('#hidden-wkt').val('');
        $('#orig-wkt').val('');
        if (typeof indiciaData.reportlayer!=="undefined") {
          indiciaData.reportlayer.removeAllFeatures();
          $.each(indiciaData.reports, function(i, reportGroup) {
            $.each(reportGroup, function(j, grid) {
              grid.mapRecords(grid[0].settings.dataSource);
              grid[0].settings.offset=0;
              grid.reload(true);
            });
          });
        }
      }
    },
   
    
    CLASS_NAME: "OpenLayers.Control.ClearLayer"
});

