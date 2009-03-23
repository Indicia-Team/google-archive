/**
 * Reimplementation of some of the methods in spatial-ref.js to move towards compliance with map_helper, and to allow code to
 * be used more generically (i.e. we should not be tied down to specific control names.)
 */

(function($){
  var MapMethods = function(map_id, options){
    this.defaults = {
      indicia_url : 'http://localhost/indicia/',
      input_field_id : 'entered_sref',
      geom_field_id : 'geom'
    };
    var settings = {};
    // Extend the settings with defaults and options.
    $.extend(settings, this.defaults, options);
  };
  
  // Click function handler
  MapMethods.prototype.click = function(){
    
  };
})(jQuery);