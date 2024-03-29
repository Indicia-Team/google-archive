/**
 * Indicia, the OPAL Online Recording Toolkit.
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
 *
 * @package	Media
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

/**
* jQuery datagrid that hooks up to Indicia data services.
* We apply this to some sort of container - all elements will be dropped into this.
* @requires jQuery v1.2.3
*/

(function($) {
  $.extend({
    indiciaDataGrid: new function() {
      this.defaults = {
        cssTable: "ui-widget ui-widget-content",
        cssHeader: "header",
        cssSortHeader: "headerSort",
        cssAsc: "headerSortUp",
        cssDesc: "headerSortDown",
        indiciaSvc: "http://localhost/indicia/index.php/services",
        dataColumns: null,
        actionColumns: {},
        itemsPerPage: 10,
        multiSort: false,
        auth: {},
        parameters: {},
        formatPager: formatPager
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
          var table = "<table class=\""+this.settings.cssTable+"\" id=\"" + this.identifier + "\" >";
          table += "<thead class=\"ui-widget-header\"><tr>";
          table += "</tr></thead>";
          table += "<tbody>";
          table += "</tbody>";
          table += "</table>";

          // Build the basic pagination div
          var paginationDiv = "<div class='pager' />";

          // Build the basic filter div
          var filterDiv = "<div class='filter' />";

          // Drop the table into the container
          $(this).html(table + paginationDiv + filterDiv);

          if (this.entity.substring(0,4)!='rpt:') {
            generateTableHeader(this);
          }
          apply_page(this, 1);

        });
      };

    /**
    * Write the correct html into the header section of the table. Also generates the paginator.
    * This only applies when the grid is in "table based" mode.
    */
    function generateTableHeader(div){
      var headers = "";
      var filter = "<form name='Filter' action='' method='get'>Filter for <input type='text' name='filters' class='filter' /> in <select type='text' name='columns' class='filterCol'>";
      var url = div.settings.indiciaSvc + "/data";
      url += "/info_table/" + div.entity + "?mode=json&callback=?";
      $.each(div.settings.auth, function(key, value){
        url += "&" + key + "=" + value;
      });
      $.getJSON(url, function(data){
        div.recordCount = data.record_count;
        $.each(data.columns, function(i, item){
          if (div.settings.dataColumns == null || div.settings.dataColumns.indexOf(item) != -1){
            headers += "<th class='" + div.settings.cssHeader + " " +div.settings.cssSortHeader + "'>"+item+"</th>";
            filter += "<option value='"+item+"'>"+item+"</option>";
          }
        });
        filter += "</select> <input class='filterButton' type='submit' value='Filter' /></form>";
        $.each(div.settings.actionColumns, function(key, value){
          headers += "<th class='" + div.settings.cssHeader + "'>";
          headers += key;
          headers += "</th>";
        });
        $("thead tr", div).html(headers);
        // TODO enable filtering once this is sensible
        // $("div.filter", div).html(filter);
        $("th."+div.settings.cssSortHeader, div).each(function(i){
          $(this).click(function(e){
            apply_sort(div, this);
          });
        });
        $("input.filterButton", div).each(function(i){
          $(this).click(function(e){
            e.preventDefault();
            apply_filter(div);
          });
        });
      });
    }

    /**
    * Write the correct html into the header section of the table. This is done directly from
    * the Json in the report data for reports.
    */
    function generateReportHeader(record, div) {
      var headers = "";
      $.each(record, function(key, value){
        headers += "<th class='" + div.settings.cssHeader + " " +div.settings.cssSortHeader + "'>"+key+"</th>";
      });

      $("thead tr", div).html(headers);
    }

    function apply_filter(div){
      div.filter.clear();
      div.filter.unshift($("select.filterCol", div).val(), $("input.filter", div).val());
      generateBody(div);
    }

    function apply_page(div, page){
      div.page = page;
      generateBody(div);
      $("div.pager", div).each(function(i){
        generatePager(div, this);
      });
    }

    function apply_sort(div, header){
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
          if (r==0 && div.entity.substring(0,4)=='rpt:') {
            generateReportHeader(record, div);
          }
          if (r%2==0) {
            body += "<tr>";
          } else {
            body += "<tr class='odd'>";
          }
          $.each(record, function(i, item){
            if (div.settings.dataColumns == null || div.settings.dataColumns.indexOf(item) != -1){
              body += "<td>"+item+"</td>";
            }
          });
          $.each(div.settings.actionColumns, function(key, value){
            body += "<td>";
            body += "<a href='" + value.replace(/�([a-zA-Z_\-]+)�/g, function($0, $1){ return record[$1]; }) + "'>"+key+"</a>";
            body += "</td>";
          });
          body += "</tr>";
        });
        $("tbody", div).html(body);
      });
    }

    function formatPager(div){
      var pageNo = div.page;
      var totalPages = Math.ceil(div.recordCount / div.settings.itemsPerPage);
      var pagerString = (pageNo == 1) ? "<< | < | " : "<a href='' class='first'>&lt;&lt;</a> | <a href='' class='previous'>&lt;</a> | ";
      pagerString += (pageNo == totalPages) ? pageNo + " | > | >>" : pageNo + " | <a href='' class='next'>&gt;</a> | <a href='' class='last'>&gt;&gt;</a>";
      return pagerString;
    }

    function generatePager(div, pagerDiv){
      var pageNo = div.page;
      var totalPages = Math.ceil(div.recordCount / div.settings.itemsPerPage);
      $(pagerDiv).html(div.settings.formatPager(div));
      $(".first", pagerDiv).each(function(i){
        $(this).click(function(e){
          e.preventDefault();
          apply_page(div, 1);
        });
      });
      $(".previous", pagerDiv).each(function(i){
        $(this).click(function(e){
          e.preventDefault();
          apply_page(div, pageNo - 1);
        });
      });
      $(".next", pagerDiv).each(function(i){
        $(this).click(function(e){
          e.preventDefault();
          apply_page(div, pageNo + 1);
        });
      });
      $(".last", pagerDiv).each(function(i){
        $(this).click(function(e){
          e.preventDefault();
          apply_page(div, totalPages);
        });
      });
    }

    function getUrl(div){
      var page = div.page;
      if (div.entity.substring(0,4)=='rpt:') {
        url = div.settings.indiciaSvc + "/report/requestReport?report=" + div.entity.substring(4) + ".xml&reportSource=local&mode=json&callback=?";
      } else {
        var url = div.settings.indiciaSvc + "/data";
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
      }
      $.each(div.settings.parameters, function(key, value){
        url += "&" + key + "=" + value;
      });
      $.each(div.settings.auth, function(key, value){
        url += "&" + key + "=" + value;
      });
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

  }
  });

  /**
  * Extend the function object.
  */
  $.fn.extend({
    indiciaDataGrid: $.indiciaDataGrid.construct
  });
})(jQuery);
