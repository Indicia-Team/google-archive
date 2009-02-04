/**
* jQuery datagrid that hooks up to Indicia data services.
* We apply this to some sort of container - all elements will be dropped into this.
* @requires jQuery v1.2.3
*/

(function($) {
  $.extend({
    indiciaDataGrid: new function() {
      this.recordCount = null;
      
      this.defaults = {
	cssHeader: "header",
	   cssSortable: "headerSort",
	   cssAsc: "headerSortUp",
	   cssDesc: "headerSortDown",
	   sortInitialOrder: "asc",
	   debug: false,
	   indiciaSvc: "http://localhost/indicia/index.php/services/data",
	   actionColumns: {"edit" : "?id=£id£"},
	   itemsPerPage: 10
      };
      
      this.construct = function(entity, options){
	// Set the default settings object
	this.settings = {};
	// Extend with defaults and options
	$.extend(this.settings, $.indiciaDataGrid.defaults, options);
	return this.each(function(){
	  this.filter = new HashArray();
	  this.sort = new HashArray();
	  this.identifier = "idg" + Math.floor(Math.random()*10000);
	  
	  // Build the html to drop in the container
	  
	  var table = "<table class='idg' id='" + this.identifier + "' >";
	  table += "<thead>";
	  table += "</thead>";
	  table += "</table>";
	  
	  // Drop the table into the container
	  
	  $(this).html(table);
	});
      };
      
      function recordCount(refresh){
	if (typeof refresh == "undefined"){
	  refresh = false;
	}
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
