/**
* jQuery datagrid that hooks up to Indicia data services.
* We apply this to some sort of container - all elements will be dropped into this.
* @requires jQuery v1.2.3
*/

(function($) {
  $.extend({
    indiciaDataGrid: function() {
      this.recordCount = null;
      
      this.defaults = {	
	cssHeader: "header",
	   cssAsc: "headerSortUp",
	   cssDesc: "headerSortDown",
	   sortInitialOrder: "asc",
	   debug: false,
	   indiciaSvc: "http://localhost/indicia/index.php/services/data",
	   actionColumns: 
      };
      
      this.construct = function(entity, options){
	return this.each(function(){
	  this.filter = new HashTable();
	  this.sort = new HashTable();
	  this.identifier = "idg" + Math.floor(Math.random()*10000);
	  
	  // Set the default settings object
	  this.settings = {};
	  // Extend with defaults and options
	  $.extend(this.settings, $.indiciaDataGrid.defaults, options);
	};
      };
      
      function recordCount(refresh=false){
	if (refresh || this.recordCount == null){
	  // Get a record count through calling the services.
	}
	return this.recordCount;	
      }
      
      function log(s) {
	if (typeof console != "undefined" && typeof console.debug != "undefined") {
	  console.log(s);
	} else {
	  alert(s);
	}
      }
      
      
    }
  });
  /**
  * Extend the function object.
  */
  $.fn.extend({
    indiciaDataGrid: $.indiciaDataGrid.construct
  });
})(jQuery);
