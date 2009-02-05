/**
* jQuery datagrid that hooks up to Indicia data services.
* We apply this to some sort of container - all elements will be dropped into this.
* @requires jQuery v1.2.3
*/

(function($) {
  $.extend({
    indiciaDataGrid: new function() {
      this.defaults = {
	cssHeader: "header",
	   cssSortHeader: "headerSort",
	   cssAsc: "headerSortUp",
	   cssDesc: "headerSortDown",
	   sortInitialOrder: "asc",
	   debug: false,
	   indiciaSvc: "http://localhost/indicia/index.php/services/data",
	   dataColumns: null,
	   actionColumns: Array(Array("edit", "?id=�id�")),
	   itemsPerPage: 10,
	   multiSort: false
      };
      
      this.construct = function(entity, options){
	// Set the default settings object
	var settings = {};
	// Extend with defaults and options
	$.extend(settings, $.indiciaDataGrid.defaults, options);
	return this.each(function(){
	  this.page = 1;
	  this.entity = entity;
	  this.settings = settings;
	  this.filter = new HashArray();
	  this.sort = new HashArray();
	  this.identifier = "idg" + Math.floor(Math.random()*10000);
	  this.recordCount = 0;
	  
	  // Build the basic html to drop in the container
	  
	  var table = "<table class='idg tablesorter' id='" + this.identifier + "' >";
	  table += "<thead><tr>";
	  table += "</tr></thead>";
	  table += "<tbody>";
	  table += "</tbody>";
	  table += "</table>";
	  
	  // Drop the table into the container
	  
	  $(this).html(table);
	  
	  generateHeader(this);
	  generateBody(this, 1);
	  
	});
      };
      /**
      * Write the correct html into the header section of the table. Also generates the paginator.
      */
      function generateHeader(div){
	var url = div.settings.indiciaSvc;
	var headers = "";
	url += "/info_table/" + div.entity + "?mode=json&callback=?";
	$.getJSON(url, function(data){
	  div.recordCount = data.record_count;
	  $.each(data.columns, function(i, item){
	    if (div.settings.dataColumns == null || div.settings.dataColumns.indexOf(item) != -1){
	      headers += "<th class='" + div.settings.cssHeader + " " +div.settings.cssSortHeader + "'>"+item+"</th>";
	    }
	  });
	  $.each(div.settings.actionColumns, function(i, item){
	    headers += "<th class='" + div.settings.cssHeader + "'>";
	    headers += item[0];
	    headers += "</th>";
	  });
	  $("thead tr", div).html(headers);
	  $("th."+div.settings.cssSortHeader, div).each(function(i){
	    $(this).click(function(e){
	     sort(div, this);
	    });
	  });
	});
      }
      
      function sort(div, header){
	var multiSort = div.settings.multiSort;
	var h = $(header).html().toLowerCase();
	var a = div.sort.get(h);
	var cssAsc = div.settings.cssAsc;
	var cssDesc = div.settings.cssDesc;
	if (a != undefined) {
	  if (a == 'asc') {
	    if (multiSort){
	      $(header).removeClass(cssAsc);
	    } else {
	      div.sort.clear();
	      $("th."+div.settings.cssSortHeader, div).removeClass(cssDesc + " " + cssAsc);
	    }
	    div.sort.unshift(h,'desc');
	    $(header).addClass(cssDesc);
	  } else {
	    if (multiSort){
	      $(header).removeClass(cssAsc);
	    } else {
	      div.sort.clear();
	      $("th."+div.settings.cssSortHeader, div).removeClass(cssDesc + " " + cssAsc);
	    }
	  }
	} else {
	  if (!multiSort){
	    div.sort.clear();
	    $("th."+div.settings.cssSortHeader, div).removeClass(cssDesc + " " + cssAsc);
	  }
	  div.sort.unshift(h, 'asc');
	  $(header).addClass(cssAsc);
	}
	generateBody(div);
      }
      
      function generateBody(div){
	var body = "";
	var url = getUrl(div);
	$.getJSON(url, function(data){
	  $.each(data, function(r, record){
	    body += "<tr>";
	    $.each(record, function(i, item){
	      if (div.settings.dataColumns == null || div.settings.dataColumns.indexOf(item) != -1){
		body += "<td>"+item+"</td>";
	      }
	    });
	    $.each(div.settings.actionColumns, function(i, item){
	      body += "<td>";
	      body += "<a href='" + item[1].replace(/�([a-zA-Z_\-]+)�/g, record["$1"]) +
	      "'>"+item[0]+"</a>";
	      body += "</td>";
	    });
	    body += "</tr>";
	  });
	  $("tbody", div).html(body);
	});
      }
      
      function getUrl(div){
	var page = div.page;
	var url = div.settings.indiciaSvc;
	var offset = (page - 1)*div.settings.itemsPerPage;
	var sortCols = div.sort.getKeys().join(",");
	var sortDirs = div.sort.getValues().join(",");
	var filterCols = div.filter.getKeys().join(",");
	var filterVals = div.filter.getValues().join(",");
	url += "/" + div.entity + "?mode=json&callback=?&limit=" + div.settings.itemsPerPage + "&offset=" + offset;
	if (sortCols.length > 0){
	  url += "&orderby="+sortCols+"&sortdir="+sortDirs;
	}
	if (filterCols.length > 0){
	  url += "&qfield="+filterCols+"&q="+filterVals;
	}
	return url;
	
      }
      
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
