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
 * @package Client
 * @author  Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link    http://code.google.com/p/indicia/
 */
 
var loadFilter, loadFilterUser, applyFilterToReports;

jQuery(document).ready(function($) {
  "use strict";
  indiciaData.filter = {"def":{},"id":null,"title":null};
  var saving=false, filterOverride={};
  
  function disableIfPresent(context, fields, ctrlIds) {
    var disable=false;
    $.each(fields, function(idx, field) {
      if (context && context[field]) {
        disable=true;
      }
    });
    $.each(ctrlIds, function(idx, ctrlId) {
      if (disable) {
        $(ctrlId).attr('disabled', true);
      } else {
        $(ctrlId).removeAttr('disabled');
      }
    });
  }
  
  function disableSourceControlsForContext(type, context) {
    if (context && context[type+'_list_op'] && context[type+'_list']) {
      $('#filter-'+type+'s-mode').attr('disabled', true);
      if (context[type+'_list_op']==='not in') {
        // website, survey or form must not be in a list, so disable the list so that they can't be unchecked
        $('#'+type+'-list-checklist input').removeAttr('disabled');
        $.each(context[type+'_list'].split(','), function(idx, website_id) {
          $('#check-'+type+'_id').attr('disabled', true);
        });
      } else {
        // website, survey or form must be in a list, so may as well disable the others
        $('#'+type+'-list-checklist input').attr('disabled', true);
        $.each(context[type+'_list'].split(','), function(idx, website_id) {
          $('#check-'+type+'_id').removeAttr('disabled');
        });
      }
    } else {
      $('#filter-'+type+'s-mode').removeAttr('disabled');
      $('#'+type+'-list-checklist input').removeAttr('disabled');
    }
  }
  
  function limitSourceToContext (type, filterDef, context) {
    var context_idlist, idlist;
    if (context[type + '_list']) {
      if (!filterDef[type + '_list']) {
        filterDef[type + '_list']=context[type + '_list'];
      } else if (context[type + '_list_op']==='not in') {
        // if excluding these websites, then we combine the selected filter list with the context list            
        filterDef[type + '_list']=filterDef[type + '_list'] + ',' + context[type + '_list'];
      } else {
        // including context websites, so we want an intersection
        context_idlist=context[type + '_list'].split(',');
        idlist=$.grep(filterDef[type + '_list'].split(','), function(elem) {
          return $.inArray(elem, context_idlist)!==-1;
        });
        filterDef[type + '_list']=idlist.join(',');
      }
    }
  }
  
  // functions that drive each of the filter panes, e.g. to obtain the description from the controls.
  var paneObjList = {
    what:{
      loadFilter: function() {
        // if list of ids defined but not group names, this is a taxon group list loaded from the user profile.
        // Hijack the names from indiciaData.myGroups.
        if (typeof indiciaData.filter.def.taxon_group_list!=="undefined" && typeof indiciaData.filter.def.taxon_group_names==="undefined") {
          indiciaData.filter.def.taxon_group_names = [];
          var foundIds = [], foundNames = [];
          // Loop the group IDs we are expected to load
          $.each(indiciaData.filter.def.taxon_group_list, function(idx, groupId) {
            // Use the myGroups list to look them up
            $.each(indiciaData.myGroups, function() {
              if (this[0]===parseInt(groupId)) {
                foundIds.push(this[0]);
                foundNames.push(this[1]);
              }
            });
          });
          indiciaData.filter.def.taxon_group_names = foundNames;
          indiciaData.filter.def.taxon_group_list = foundIds;
        }
      },
      getDescription:function() {
        var groups=[], taxa=[], r=[];
        if (typeof indiciaData.filter.def.taxon_group_list!=="undefined") {
          $.each(indiciaData.filter.def.taxon_group_names, function(idx, group) {
            groups.push(group);
          });
        }
        if (typeof indiciaData.filter.def.higher_taxa_taxon_list_names!=="undefined") {
          $.each(indiciaData.filter.def.higher_taxa_taxon_list_names, function(idx, taxon) {
            taxa.push(taxon);
          });
        }
        if (typeof indiciaData.filter.def.taxa_taxon_list_names!=="undefined") {
          $.each(indiciaData.filter.def.taxa_taxon_list_names, function(idx, taxon) {
            taxa.push(taxon);
          });
        }
        if (groups.length>0) {
          r.push(groups.join(', '));
        }
        if (taxa.length>0) {
          r.push(taxa.join(', '));
        }
        if (typeof indiciaData.filter.def.taxon_rank_sort_order_combined!=="undefined" && 
            indiciaData.filter.def.taxon_rank_sort_order_combined!=="") {
          r.push($("#level-label").text() + ' ' + $("#taxon_rank_sort_order_op option:selected").text() + ' ' + $("#taxon_rank_sort_order_combined option:selected").text());
        }
        if (typeof indiciaData.filter.def.marine_flag!=="undefined" && indiciaData.filter.def.marine_flag!=="all") {
          r.push($("#marine_flag option[value="+indiciaData.filter.def.marine_flag+"]").text());
        }
        return r.join('<br/>');
      },
      applyFormToDefinition:function() {
        // don't send unnecessary stuff
        delete indiciaData.filter.def['taxon_group_list:search'];
        delete indiciaData.filter.def['taxon_group_list:search:title'];
        delete indiciaData.filter.def['higher_taxa_taxon_list_list:search'];
        delete indiciaData.filter.def['higher_taxa_taxon_list_list:search:taxon'];
        delete indiciaData.filter.def['taxa_taxon_list_list:search'];
        delete indiciaData.filter.def['taxa_taxon_list_list:search:taxon'];
        // reset the list of group names and species
        indiciaData.filter.def.taxon_group_names={};
        indiciaData.filter.def.higher_taxa_taxon_list_names={};
        indiciaData.filter.def.taxa_taxon_list_names={};
        // if nothing selected, clean up the def
        if ($('input[name="taxon_group_list\\[\\]"]').length===0) {
          indiciaData.filter.def.taxon_group_list='';
        } else {
          // store the list of names in the def, though not used for the report they save web service hits later
          $.each($('input[name="taxon_group_list\\[\\]"]'), function(idx, ctrl) {
            indiciaData.filter.def.taxon_group_names[$(ctrl).val()] = $.trim($(ctrl).parent().text());
          });
        }
        if ($('input[name="higher_taxa_taxon_list_list\\[\\]"]').length===0) {
          indiciaData.filter.def.higher_taxa_taxon_list_list='';
        } else {
          // store the list of names in the def, though not used for the report they save web service hits later
          $.each($('input[name="higher_taxa_taxon_list_list\\[\\]"]'), function(idx, ctrl) {
            indiciaData.filter.def.higher_taxa_taxon_list_names[$(ctrl).val()] = $.trim($(ctrl).parent().text());
          });
        }
        if ($('input[name="taxa_taxon_list_list\\[\\]"]').length===0) {
          indiciaData.filter.def.taxa_taxon_list_list='';
        } else {
          // store the list of names in the def, though not used for the report they save web service hits later
          $.each($('input[name="taxa_taxon_list_list\\[\\]"]'), function(idx, ctrl) {
            indiciaData.filter.def.taxa_taxon_list_names[$(ctrl).val()] = $.trim($(ctrl).parent().text());
          });
        }
        // because the rank sort order key includes both the sort order and rank ID, clean this up for the actual filter
        if (typeof indiciaData.filter.def.taxon_rank_sort_order_combined!=="undefined") {
          indiciaData.filter.def.taxon_rank_sort_order = indiciaData.filter.def.taxon_rank_sort_order_combined.split(':')[0];
        }
      },
      loadForm:function(context) {
        var firstTab = 0, i, disabled = [];
        // got a families or species level context. So may as well disable the less specific tabs as they won't be useful.
        if (context && context.higher_taxa_taxon_list_list) {
          firstTab = 1;
          disabled = [0];
          $('#families-tab .context-instruct').show();
        }
        else if (context && context.taxa_taxon_list_list) {
          firstTab = 2;
          disabled = [0, 1];
          $('#species-tab .context-instruct').show();
        }
        if (context && context.marine_flag && context.marine_flag!=='all') {
          $('#marine_flag option[value='+context.marine_flag+']').attr('selected', 'selected');
          $('#marine_flag').attr('disabled', 'disabled');
          $('#flags-tab .context-instruct').show();
        } else {
          $('#marine_flag').removeAttr('disabled');
          $('#flags-tab .context-instruct').hide();
        }
        $("#what-tabs").tabs("option", "disabled", disabled);
        indiciaFns.activeTab($( "#what-tabs" ), firstTab);
        if (context && context.taxon_group_list) {
          $('input#taxon_group_list\\:search\\:q').setExtraParams({"idlist":context.taxon_group_list});
          $('#species-group-tab .context-instruct').show();
        }
        else {
          $('input#taxon_group_list\\:search\\:q').unsetExtraParams('query');
        }
        // need to load the sub list control for taxon groups.
        $('#taxon_group_list\\:sublist').children().remove();
        if (typeof indiciaData.filter.def.taxon_group_names!=="undefined") {
          $.each(indiciaData.filter.def.taxon_group_names, function(id, name) {
            $('#taxon_group_list\\:sublist').append('<li class="ui-widget-content ui-corner-all"><span class="ind-delete-icon"> </span>' + name + 
                '<input type="hidden" value="' + id + '" name="taxon_group_list[]"/></li>');
          });
        }
        $('#higher_taxa_taxon_list_list\\:sublist').children().remove();
        if (typeof indiciaData.filter.def.higher_taxa_taxon_list_names!=="undefined") {
          $.each(indiciaData.filter.def.higher_taxa_taxon_list_names, function(id, name) {
            $('#higher_taxa_taxon_list_list\\:sublist').append('<li class="ui-widget-content ui-corner-all"><span class="ind-delete-icon"> </span>' + name + 
                '<input type="hidden" value="' + id + '" name="higher_taxa_taxon_list_list[]"/></li>');
          });
        }
        $('#taxa_taxon_list_list\\:sublist').children().remove();
        if (typeof indiciaData.filter.def.taxa_taxon_list_names!=="undefined") {
          $.each(indiciaData.filter.def.taxa_taxon_list_names, function(id, name) {
            $('#taxa_taxon_list_list\\:sublist').append('<li class="ui-widget-content ui-corner-all"><span class="ind-delete-icon"> </span>' + name + 
                '<input type="hidden" value="' + id + '" name="taxa_taxon_list_list[]"/></li>');
          });
        }
        // these auto-disable on form submission
        $('#taxon_group_list\\:search\\:title').removeAttr('disabled');
        $('#higher_taxa_taxon_list_list\\:search\\:taxon').removeAttr('disabled');
        $('#taxa_taxon_list_list\\:search\\:taxon').removeAttr('disabled');
      }
    },
    when:{
      getDescription:function() {
        var r=[], dateType='recorded', dateFromField='date_from', dateToField='date_to', dateAgeField='date_age';
        if (typeof indiciaData.filter.def.date_type!=="undefined") {
          dateType = indiciaData.filter.def.date_type;
          if (dateType!=='recorded') {
            dateFromField = dateType + '_date_from';
            dateToField = dateType + '_date_to';
            dateAgeField = dateType + '_date_age';
          }
        }
        if (indiciaData.filter.def[dateFromField] && indiciaData.filter.def[dateToField]) {
          r.push('Records '+dateType + ' between ' + indiciaData.filter.def[dateFromField] + ' and ' + indiciaData.filter.def[dateToField]);
        } else if (indiciaData.filter.def[dateFromField]) {
          r.push('Records '+dateType + ' on or after ' + indiciaData.filter.def[dateFromField]);
        } else if (indiciaData.filter.def[dateToField]) {
          r.push('Records '+dateType + ' on or before ' + indiciaData.filter[dateToField]);
        }
        if (indiciaData.filter.def[dateAgeField]) {
          r.push('Records '+dateType + ' in last ' + indiciaData.filter.def[dateAgeField]);
        }
        return r.join('<br/>');
      },
      loadForm:function(context) {
        var dateTypePrefix='';
        if (typeof indiciaData.filter.def.date_type!=="undefined" && indiciaData.filter.def.date_type!=="recorded") {
          dateTypePrefix = indiciaData.filter.def.date_type + '_';
        }
        if (context && (context.date_from || context.date_to || context.date_age || 
            context.input_date_from || context.input_date_to || context.input_date_age ||
            context.edited_date_from || context.edited_date_to || context.edited_date_age ||
            context.verified_date_from || context.verified_date_to || context.verified_date_age)) {
          $('#controls-filter_when .context-instruct').show();
        }
        if (dateTypePrefix) {
          // We need to load the default values for each control, as if prefixed then they won't autoload
          if (typeof indiciaData.filter.def[dateTypePrefix + 'date_from']!=="undefined") {
            $('#date_from').val(indiciaData.filter.def[dateTypePrefix + 'date_from']);
          }
          if (typeof indiciaData.filter.def[dateTypePrefix + 'date_age']!=="undefined") {
            $('#date_to').val(indiciaData.filter.def[dateTypePrefix + 'date_to']);
          }
          if (typeof indiciaData.filter.def[dateTypePrefix + 'date_age']!=="undefined") {
            $('#date_age').val(indiciaData.filter.def[dateTypePrefix + 'date_age']);
          }
        }
      },
      applyFormToDefinition:function() {
        var dateTypePrefix='';
        if (typeof indiciaData.filter.def.date_type!=="undefined" && indiciaData.filter.def.date_type!=="recorded") {
          dateTypePrefix = indiciaData.filter.def.date_type + '_';
        }
        // make sure we clean up, especially if switching date filter type
        delete indiciaData.filter.def.input_date_from;
        delete indiciaData.filter.def.input_date_to;
        delete indiciaData.filter.def.input_date_age;
        delete indiciaData.filter.def.edited_date_from;
        delete indiciaData.filter.def.edited_date_to;
        delete indiciaData.filter.def.edited_date_age;
        delete indiciaData.filter.def.verified_date_from;
        delete indiciaData.filter.def.verified_date_to;
        delete indiciaData.filter.def.verified_date_age;
        // if the date filter type needs a prefix on the parameter field names, then copy the values from the
        // date controls into the proper parameter field names
        if (dateTypePrefix) {
          indiciaData.filter.def[dateTypePrefix + 'date_from'] = indiciaData.filter.def.date_from;
          indiciaData.filter.def[dateTypePrefix + 'date_to'] = indiciaData.filter.def.date_to;
          indiciaData.filter.def[dateTypePrefix + 'date_age'] = indiciaData.filter.def.date_age;
          // the date control values must NOT apply to the field record date in this case - we are doing a different
          // type filter.
          delete indiciaData.filter.def.date_from;
          delete indiciaData.filter.def.date_to;
          delete indiciaData.filter.def.date_age;
        }
      }
    },
    where:{
      getDescription:function() {
        if (indiciaData.filter.def.remembered_location_name) {
          return 'Records in ' + indiciaData.filter.def.remembered_location_name;
        } else if (indiciaData.filter.def['imp-location:name']) { // legacy
          return 'Records in ' + indiciaData.filter.def['imp-location:name'];
        } else if (indiciaData.filter.def.indexed_location_id) { 
          // legacy location ID for the user's locality. In this case we need to hijack the site type drop down shortcuts to get the locality name
          return $('#site-type option[value=loc\\:' + indiciaData.filter.def.indexed_location_id + ']').text();
        } else if (indiciaData.filter.def.location_name) {
          return 'Records in places containing "' + indiciaData.filter.def.location_name + '"';
        } else if (indiciaData.filter.def.sref) {
          return 'Records in square ' + indiciaData.filter.def.sref;
        } else if (indiciaData.filter.def.searchArea) {
          return 'Records within a freehand boundary';
        } else {
          return '';
        }        
      },
      applyFormToDefinition:function() {
        var geoms=[], geom;
        indiciaData.filter.def.location_id='';
        indiciaData.filter.def.indexed_location_id='';
        delete indiciaData.filter.def.remembered_location_name;
        delete indiciaData.filter.def.searchArea;
        delete indiciaData.filter.def['imp-location:name'];
        // if we've got a location name to search for, no need to do anything else as the where filters are exclusive.
        if (indiciaData.filter.def.location_name) {
          return;
        }
        if ($('#site-type').val()!=='') {
          if ($('#site-type').val().match(/^loc:[0-9]+$/)) {
            indiciaData.filter.def.indexed_location_id=$('#site-type').val().replace(/^loc:/, '');
            indiciaData.filter.def.remembered_location_name = $('#site-type :selected').text();
            return;
          } else if ($('#imp-location').val()) {
            if ($.inArray(parseInt($('#site-type').val()), indiciaData.indexedLocationTypeIds)!==-1) {
              indiciaData.filter.def.indexed_location_id=$('#imp-location').val();
            } else {
              indiciaData.filter.def.location_id=$('#imp-location').val();
            }
            indiciaData.filter.def.remembered_location_name = $('#imp-location :selected').text();
            return;
          }
        }
        
        $.each(indiciaData.mapdiv.map.editLayer.features, function(i, feature) {
          // ignore features with a special purpose, e.g. the selected record when verifying
          if (typeof feature.tag==="undefined" && (typeof feature.attributes.type==="undefined" || feature.attributes.type!=="boundary")) {
            if (feature.geometry.CLASS_NAME.indexOf('Multi')!==-1) {
              geoms = geoms.concat(feature.geometry.components);
            } else {
              geoms.push(feature.geometry);
            }
          }
        });
        if (geoms.length>0) {
          if (geoms[0].CLASS_NAME === 'OpenLayers.Geometry.Polygon') {
            geom = new OpenLayers.Geometry.MultiPolygon(geoms);
          } else if (geoms[0].CLASS_NAME === 'OpenLayers.Geometry.LineString') {
            geom = new OpenLayers.Geometry.MultiLineString(geoms);
          } else if (geoms[0].CLASS_NAME === 'OpenLayers.Geometry.Point') {
            geom = new OpenLayers.Geometry.MultiPoint(geoms);
          }
          if (indiciaData.mapdiv.map.projection.getCode() !== 'EPSG:3857') {
            geom.transform(indiciaData.mapdiv.map.projection, new OpenLayers.Projection('EPSG:3857'));
          }
          if (indiciaData.filter.def.searchArea !== geom.toString()) {
            indiciaData.filter.def.searchArea = geom.toString();
            filterParamsChanged();
          }
        }
      },
      preloadForm:function() {
        // max size the map
        $('#filter-map-container').css('width', $(window).width()-160);
        $('#filter-map-container').css('height', $(window).height()-380);
      },
      loadForm:function(context) {
        indiciaData.disableMapDataLoading=true;
        indiciaData.mapOrigCentre=indiciaData.mapdiv.map.getCenter();
        indiciaData.mapOrigZoom=indiciaData.mapdiv.map.getZoom();
        if (indiciaData.filter.def.indexed_location_id && 
            $("#site-type option[value='loc:"+indiciaData.filter.def.indexed_location_id+"']").length > 0) {
          $('#site-type').val('loc:'+indiciaData.filter.def.indexed_location_id);
        } else if (indiciaData.filter.def.indexed_location_id || indiciaData.filter.def.location_id) {
          var locationToLoad=indiciaData.filter.def.indexed_location_id ? indiciaData.filter.def.indexed_location_id : indiciaData.filter.def.location_id;
          if (indiciaData.filter.def['site-type']) {
            $('#site-type').val(indiciaData.filter.def['site-type']);
          } else {
            // legacy
            $('#site-type').val('my');
          }
          changeSiteType(locationToLoad);
        }
        if (typeof indiciaData.linkToMapDiv!=="undefined") {
          // move the map div to our container so it appears on the popup
          var element=$('#' + indiciaData.linkToMapDiv);
          indiciaData.origMapParent = element.parent();
          indiciaData.origMapSize = {"width": $(indiciaData.mapdiv).css('width'), "height": $(indiciaData.mapdiv).css('height')};
          $(indiciaData.mapdiv).css('width', '100%');
          $(indiciaData.mapdiv).css('height', '100%');
          $('#filter-map-container').append(element);
        }
        // select the first draw... tool if allowed to draw on the map by permissions, else select navigate
        $.each(indiciaData.mapdiv.map.controls, function(idx, ctrl) {        
          if (context && (((context.sref || context.searchArea) && ctrl.CLASS_NAME.indexOf('Control.Navigate')>-1) ||
              ((!context.sref && !context.searchArea) && ctrl.CLASS_NAME.indexOf('Control.Draw')>-1))) {
            ctrl.activate();
            return false;
          }
        });
        if (context && (context.location_id || context.indexed_location_id || context.location_name || context.searchArea)) {
          $('#controls-filter_where .context-instruct').show();
        }        
        disableIfPresent(context, ['location_id','location_name'], ['#imp-location\\:name','#location_name']);
        disableIfPresent(context, ['indexed_location_id'], ['#indexed_location_id']);
        disableIfPresent(context, ['sref','searchArea'], ['#imp-sref']);
        if (context && (context.sref || context.searchArea)) {
          $('#controls-filter_where legend').hide();
          $('.olControlDrawFeaturePolygonItemInactive').addClass('disabled');
          $('.olControlDrawFeaturePathItemInactive').addClass('disabled');
          $('.olControlDrawFeaturePointItemInactive').addClass('disabled');
        } else {
          $('#controls-filter_where legend').show();
        }
        
      },
      loadFilter: function() {
        if (typeof indiciaData.mapdiv!=="undefined") {
          if (indiciaData.filter.def.searchArea) {
            var parser = new OpenLayers.Format.WKT(), feature = parser.read(indiciaData.filter.def.searchArea);
            if (indiciaData.mapdiv.map.projection.getCode() !== indiciaData.mapdiv.indiciaProjection.getCode()) {
              feature.geometry.transform(indiciaData.mapdiv.indiciaProjection, indiciaData.mapdiv.map.projection);
            }
            indiciaData.mapdiv.map.editLayer.addFeatures([feature]);
          } else if (indiciaData.filter.def.location_id) {
            indiciaData.mapdiv.locationSelectedInInput(indiciaData.mapdiv, indiciaData.filter.def.location_id);
          } else if (indiciaData.filter.def.indexed_location_id) {
            indiciaData.mapdiv.locationSelectedInInput(indiciaData.mapdiv, indiciaData.filter.def.indexed_location_id);
          }
        }
      }
    },
    who:{
      getDescription:function() {
        if (indiciaData.filter.def.my_records) {
          return indiciaData.lang.MyRecords;
        } else {
          return '';
        }
      },
      loadForm:function(context) {
        if (context && context.my_records) {
          $('#my_records').attr('disabled', true);
          $('#controls-filter_who .context-instruct').show();
          $('#controls-filter_who button').hide();
        } else {
          $('#my_records').removeAttr('disabled');
          $('#controls-filter_who button').show();
        }
      }
    },
    occurrence_id:{
      getDescription:function() {
        if (indiciaData.filter.def.occurrence_id) {
          var op = typeof indiciaData.filter.def.occurrence_id_op==="undefined" ? '=' : indiciaData.filter.def.occurrence_id_op.replace(/[<=>]/g, "\\$&");
          return $('#occurrence_id_op option[value='+op+']').html()
              + ' ' + indiciaData.filter.def.occurrence_id;
        } else {
          return '';
        }
      },
      loadForm:function(context) {
      }
    },
    quality:{
      getDescription:function() {
        var r=[];
        if (indiciaData.filter.def.quality!=='all') {
          r.push($('#quality-filter option[value='+indiciaData.filter.def.quality.replace('!','\\!')+']').html());
        }
        if (indiciaData.filter.def.autochecks==='F') {
          r.push(indiciaData.lang.AutochecksFailed);
        } else if (indiciaData.filter.def.autochecks==='P') {
          r.push(indiciaData.lang.AutochecksPassed);
        }
        if (indiciaData.filter.def.has_photos) {
          r.push(indiciaData.lang.HasPhotos);
        }
        return r.join('<br/>');
      },
      getDefaults:function() {
        return {
          "quality" : "!R"
        };
      },
      loadForm:function(context) {
        if (context && context.quality && context.quality!=='all') {
          $('#quality-filter').attr('disabled', true);
        } else {
          $('#quality-filter').removeAttr('disabled');
        }
        if (context && context.autochecks) {
          $('#autochecks').attr('disabled', true);
        } else {
          $('#autochecks').removeAttr('disabled');
        }
        if (context && context.has_photos) {
          $('#has_photos').attr('disabled', true);
        } else {
          $('#has_photos').removeAttr('disabled');
        }
        if (context && ((context.quality && context.quality!=='all') || context.autochecks || context.has_photos)) {
          $('#controls-filter_quality .context-instruct').show();
        }
      }
    },
    source:{
      getDescription:function() {
        var r=[], list=[];
        if (indiciaData.filter.def.website_list) {
          $.each(indiciaData.filter.def.website_list.split(','), function(idx, id) {
            list.push($('#check-'+id).next('label').html());
          });
          r.push((indiciaData.filter.def.website_list_op==='not in' ? 'Exclude ' : '') + list.join(', '));
        }
        if (indiciaData.filter.def.survey_list) {
          $.each(indiciaData.filter.def.survey_list.split(','), function(idx, id) {
            list.push($('#check-survey-'+id).next('label').html());
          });
          r.push((indiciaData.filter.def.survey_list_op==='not in' ? 'Exclude ' : '') + list.join(', '));
        }
        if (indiciaData.filter.def.input_form_list) {
          $.each(indiciaData.filter.def.input_form_list.split(','), function(idx, id) {
            list.push($('#check-input_form-'+id).next('label').html());
          });
          r.push((indiciaData.filter.def.input_form_list_op==='not in' ? 'Exclude ' : '') + list.join(', '));
        }
        return r.join('<br/>');
      },
      loadForm: function(context) {
        if (context && ((context.website_list && context.website_list_op) || 
            (context.survey_list && context.survey_list_op) || (context.input_form_list && context.input_form_list_op))) {
          $('#controls-filter_source .context-instruct').show();
        }
        if (indiciaData.filter.def.website_list) {
          $('#website-list-checklist input').attr('checked', false);
          $.each(indiciaData.filter.def.website_list.split(','), function(idx, id) {
            $('#check-'+id).attr('checked', true);
          });
          updateWebsiteSelection();
        }
        if (indiciaData.filter.def.survey_list) {
          $('#survey_list input').attr('checked', false);
          $.each(indiciaData.filter.def.survey_list.split(','), function(idx, id) {
            $('#check-survey-'+id).attr('checked', true);
          });
        }
        if (indiciaData.filter.def.input_form_list) {
          $('#input_form_list input').attr('checked', false);
          $.each(indiciaData.filter.def.input_form_list.split(','), function(idx, form) {
            $('#check-form-'+indiciaData.formsList[form]).attr('checked', true);
          });
        }
        disableSourceControlsForContext('website', context);
        disableSourceControlsForContext('survey', context);
        disableSourceControlsForContext('input_form', context);
      },
      applyFormToDefinition:function() {
        var website_ids = [], survey_ids=[], input_forms=[];
        $.each($('#filter-websites input:checked').filter(":visible"), function(idx, ctrl) {
          website_ids.push($(ctrl).val());
        });
        indiciaData.filter.def.website_list = website_ids.join(',');
        $.each($('#filter-surveys input:checked').filter(":visible"), function(idx, ctrl) {
          survey_ids.push($(ctrl).val());
        });
        indiciaData.filter.def.survey_list = survey_ids.join(',');
        $.each($('#filter-input_forms input:checked').filter(":visible"), function(idx, ctrl) {
          input_forms.push("'" + $(ctrl).val() + "'");
        });
        indiciaData.filter.def.input_form_list = input_forms.join(',');
      }
    }
  };
  
  // Event handler for a draw tool boundary being added which clears the other controls on the map pane.
  function addedFeature() {
    $('#controls-filter_where').find(':input').not('#imp-sref-system,:checkbox,[type=button]').val('');
    $('#controls-filter_where').find(':checkbox').attr('checked', false);
  }
  
  // Hook the addedFeature handler up to the draw controls on the map
  mapInitialisationHooks.push(function(mapdiv) {
    $.each(mapdiv.map.controls, function(idx, ctrl) {
      if (ctrl.CLASS_NAME.indexOf('Control.Draw')>-1) {
        ctrl.events.register('featureadded', ctrl, addedFeature);
      }
    });
    // ensures that if part of a loaded filter description is a boundary, it gets loaded onto the map only when the map is ready
    updateFilterDescriptions();
  });
  
  // Ensure that pane controls that are exclusive of others are only filled in one at a time
  $('.filter-controls fieldset :input').change(function(e) {
    var form=$(e.currentTarget).parents('.filter-controls'),
      thisFieldset=$(e.currentTarget).parents('fieldset')[0];
    if ($(form)[0].id==='controls-filter_where') {      
      indiciaData.mapdiv.map.editLayer.removeAllFeatures();
    }
    $.each($(form).find('fieldset.exclusive'), function(idx, fieldset) {
      if (fieldset!==thisFieldset) {
        $(fieldset).find(':input').not('#imp-sref-system,:checkbox,[type=button]').val('');
        $(fieldset).find(':checkbox').attr('checked', false);
      }
    });
  });
  
  // Ensure that only one of families, species and species groups are picked on the what filter
  var taxonSelectionMethods=['higher_taxa_taxon_list_list', 'taxa_taxon_list_list', 'taxon_group_list'];
  var keep = function(toKeep) {    
    $.each(taxonSelectionMethods, function() {
      if (this!==toKeep) {
        $('#'+this+'\\:sublist').children().remove();
      }
    });
  };
  $.each(taxonSelectionMethods, function(idx, method) {
    $('#'+this+'\\:search\\:taxon').keypress(function(e) {
      if (e.which===13) {
        keep(method);
      }
    });
    $('#'+this+'\\:add').click(function() {
      keep(method);
    });
  });
  
  function loadSites(filter, idToSelect) {
    $.ajax({
      dataType: "json",
      url: indiciaData.read.url + 'index.php/services/data/location',
      data: 'mode=json&view=list&orderby=name&auth_token='+indiciaData.read.auth_token+'&nonce='+indiciaData.read.nonce+'&'+filter+'&callback=?',
      success: function(data) {
        $.each(data, function(idx, loc) {
          $('#imp-location').append('<option value="'+loc.id+'">' + loc.name + '</option>');
        });
        if (typeof idToSelect !== "undefined") {
          $('#imp-location').val(idToSelect);
        }
      }
    });
  }
  
  function changeSiteType(idToSelect) {
    $('#imp-location').children().remove();
    $('#imp-location').append('<option value="">'+indiciaData.lang.pleaseSelect+'</option>');
    if ($('#site-type').val()==='my') {
      // my sites
      $('#imp-location').show();
      if (indiciaData.includeSitesCreatedByUser) {
        loadSites('view=detail&created_by_id='+indiciaData.user_id, idToSelect);
      }
    } else if ($('#site-type').val().match(/^[0-9]+$/)) {
      // a location_type_id selected
      $('#imp-location').show();      
      loadSites('location_type_id='+$('#site-type').val(), idToSelect);
    } else {
      // a shortcut site from the site-types list
      $('#imp-location').hide();
      if ($('#site-type').val().match(/^loc:[0-9]+$/)) {
        // add a dummy location entry and pick it, so we can fire change to redraw the map
        $('#imp-location').children().append('<option selected="selected" value="'+$('#site-type').val().replace(/^loc:/, '')+'">x</option>');
        indiciaData.mapdiv.locationSelectedInInput(indiciaData.mapdiv, $('#site-type').val().replace(/^loc:/, ''));
      }
    }
  }
  
  $('#site-type').change(function(e) {
    changeSiteType();
  });
  
  function updateSurveySelection() {
    var surveys=[];
    $.each($('#filter-surveys input:checked'), function(idx, checkbox) {
      surveys.push('.vis-survey-' + $(checkbox).val());
    });
    if (surveys.length===0) {
      // no websites picked, so can pick any survey
      $('#filter-input_forms li').show();
    } 
    else if ($('#filter-surveys-mode').val()==='in') {
      // list only the forms that can be picked
      $('#filter-input_forms li').filter(surveys.join(',')).removeClass('survey-hide');
      $('#filter-input_forms li').not(surveys.join(',')).addClass('survey-hide');
      $('#filter-input_forms li').not(surveys.join(',')).find('input').attr('checked', false);
    } else {
      // list only the forms that can be picked - based on an exclusion of surveys
      $('#filter-input_forms li').filter(surveys.join(',')).addClass('survey-hide');
      $('#filter-input_forms li').not(surveys.join(',')).removeClass('survey-hide');
      $('#filter-input_forms li').filter(surveys.join(',')).find('input').attr('checked', false);
    }
  }
  
  function updateWebsiteSelection() {
    var websites=[], lis=$('#filter-surveys li, #filter-input_forms li');
    $.each($('#filter-websites input:checked'), function(idx, checkbox) {
      websites.push('.vis-website-' + $(checkbox).val());
    });
    
    if (websites.length===0) {
      // no websites picked, so can pick any survey
      lis.removeClass('website-hide');
    } 
    else if ($('#filter-websites-mode').val()==='in') {
      // list only the surveys that can be picked
      lis.filter(websites.join(',')).removeClass('website-hide');
      lis.not(websites.join(',')).addClass('website-hide');
      lis.not(websites.join(',')).find('input').attr('checked', false);
    } else {
      // list only the surveys that can be picked - based on an exclusion of websites
      lis.filter(websites.join(',')).addClass('website-hide');
      lis.not(websites.join(',')).removeClass('website-hide');
      lis.filter(websites.join(',')).find('input').attr('checked', false);
    }
  }
  
  $('#filter-websites :input').change(updateWebsiteSelection);
  
  $('#filter-surveys :input').change(updateSurveySelection);
  
  $('#my_groups').click(function() {
    $.each(indiciaData.myGroups, function(idx, group) {
      if ($('#taxon_group_list\\:sublist input[value=' + group[0] + ']').length===0) {
        $('#taxon_group_list\\:sublist').append('<li><span class="ind-delete-icon"> </span>' + group[1] + 
            '<input type="hidden" value="' + group[0] + '" name="taxon_group_list[]"></li>');
      }
    });
  });
  
  // Event handler for selecting a filter from the drop down. Enables the apply filter button when appropriate.
  var filterChange=function() {
    if ($('#select-filter').val()) {
      $('#filter-apply').removeClass('disabled');
    } else {
      $('#filter-apply').addClass('disabled');
    }
  };
  
  // Hook the above event handler to the select filter dropdown.
  $('#select-filter').change(filterChange);
  
  function dateStrIsGreater(d1, d2) {
    var format=$("#date_from").datepicker( "option", "dateFormat");
    d1 = jQuery.datepicker.parseDate(format, d1);
    d2 = jQuery.datepicker.parseDate(format, d2);
    return d1>d2;
  }
  
  function ageStrIsGreater(d1, d2) {
    var days1, days2;
    d1 = d1.split(/\s/);
    switch ($.trim(d1[1]).substr(0,3).toUpperCase()) {
      case 'WEE': days1=d1[0]*7; break;
      case 'MON': days1=d1[0]*30; break;
      case 'YEA': days1=d1[0]*365; break;
      default: days1=d1[0];
    }
    d2 = d2.split(/\s/);
    switch ($.trim(d2[1]).substr(0,3).toUpperCase()) {
      case 'WEE': days2=d2[0]*7; break;
      case 'MON': days2=d2[0]*30; break;
      case 'YEA': days2=d2[0]*365; break;
      default: days2=d2[0];
    }
    return days1>days2;
  }
  
  function applyContextLimits() {
    // if a context is loaded, need to limit the filter to the records in the context
    if ($('#context-filter').length) {
      var context = indiciaData.filterContextDefs[$('#context-filter').val()];
      $.each(context, function (param, value) {
        if (value!=='') {
          indiciaData.filter.def[param+'_context']=value;
        }
      });
    }
  }
  
  applyFilterToReports = function(reload) {
    if (typeof reload==="undefined") {
      reload=true;
    }
    applyContextLimits();
    var filterDef = $.extend({}, indiciaData.filter.def);
    delete filterDef.taxon_group_names;
    delete filterDef.taxa_taxon_list_names;
    delete filterDef.taxon_group_names_context;
    delete filterDef.taxa_taxon_list_names_context;
    if (indiciaData.reports) {
      // apply the filter to any reports on the page
      $.each(indiciaData.reports, function(i, group) {
        $.each(group, function(j, grid) {
          // store a copy of the original params before any reset, so we can revert.
          if (typeof grid[0].settings.origParams==="undefined") {
            grid[0].settings.origParams = $.extend({}, grid[0].settings.extraParams);
          }
          // merge in the filter
          grid[0].settings.extraParams = $.extend({}, grid[0].settings.origParams, filterDef);
          if ($('#filter\\:sharing').length>0) {
            grid[0].settings.extraParams.sharing = codeToSharingTerm($('#filter\\:sharing').val()).replace(' ', '_');
          }
          if (reload) {
            // reload the report grid
            grid.ajaxload();
            if (grid[0].settings.linkFilterToMap && typeof indiciaData.mapdiv!=="undefined") {
              grid.mapRecords(grid[0].settings.mapDataSource, grid[0].settings.mapDataSourceLoRes);
            }
          }
        });
      });
    }
  };
  
  function applyDefaults() {
    $.each(paneObjList, function(name, obj) {
      if (typeof obj.getDefaults!=="undefined") {
        $.extend(indiciaData.filter.def, obj.getDefaults());
      }
    });
  }
  
  function resetFilter() {
    indiciaData.filter.def={};
    applyDefaults();
    if (typeof indiciaData.filter.orig!=="undefined") {
      indiciaData.filter.def = $.extend(indiciaData.filter.def, indiciaData.filter.orig);
    }
    indiciaData.filter.id = null;
    $('#filter\\:title').val('');
    $('#select-filter').val('');
    $.each(indiciaData.reports, function(i, group) {
      $.each(group, function(j, grid) {
        // revert any stored original params for the grid.
        if (typeof grid[0].settings.origParams!=="undefined") {
          grid[0].settings.extraParams = $.extend({}, grid[0].settings.origParams);
        }
      });
    });
    applyFilterToReports();
    $.each(indiciaData.reports, function(i, group) {
      $.each(group, function(j, grid) {
        // reload the report grid
        grid.ajaxload();
        if (grid[0].settings.linkFilterToMap) {
          grid.mapRecords(grid[0].settings.mapDataSource);
        }
      });
    });
    // clear map edit layer
    if (indiciaData.mapdiv) {
      indiciaData.mapdiv.map.editLayer.removeAllFeatures();
    }
    updateFilterDescriptions();
    $('#filter-build').html(indiciaData.lang.CreateAFilter);
    $('#filter-reset').addClass('disabled');
    $('#filter-delete').addClass('disabled');
    $('#filter-apply').addClass('disabled');
    // reset the filter label
    $('#active-filter-label').html(indiciaData.lang.FilterReport);
    $('#standard-params .header span.changed').hide();
  }
  
  function updateFilterDescriptions() {
    var description, name;
    $.each($('#filter-panes .pane'), function(idx, pane) {
      name=pane.id.replace(/^pane-filter_/,'');
      if (paneObjList[name].loadFilter) {
        paneObjList[name].loadFilter();
      }
      description = paneObjList[name].getDescription();
      if (description==='') {
        description=indiciaData.lang['NoDescription' + name];
      }
      $(pane).find('span.filter-desc').html(description);
    });
  }
  
  function filterLoaded(data) {
    indiciaData.filter.def = $.extend(JSON.parse(data[0].definition), filterOverride);
    indiciaData.filter.id = data[0].id;
    delete indiciaData.filter.filters_user_id;
    indiciaData.filter.title = data[0].title;
    $('#filter\\:title').val(data[0].title);
    applyFilterToReports();
    $('#filter-reset').removeClass('disabled');
    $('#filter-delete').removeClass('disabled');
    $('#active-filter-label').html('Active filter: '+data[0].title);
    updateFilterDescriptions();
    $('#filter-build').html(indiciaData.lang.ModifyFilter);
    $('#standard-params .header span.changed').hide();
    // can't delete a filter you didn't create.
    if (data[0].created_by_id===indiciaData.user_id) {
      $('#filter-delete').show();
    } else {
      $('#filter-delete').hide();
    }      
  }
  
  loadFilter = function(id, getParams) {
    filterOverride = getParams;
    if ($('#standard-params .header span.changed:visible').length===0 || confirm(indiciaData.lang.ConfirmFilterChangedLoad)) {
      var def=false;
      switch (id) {
        case 'my-records':
          def = "{\"quality\":\"all\",\"my_records\":1}";
          break;
        case 'my-queried-rejected-records':
          def = "{\"quality\":\"DR\",\"my_records\":1}";
          break;
        case 'my-pending-records':
          def = "{\"quality\":\"P\",\"my_records\":1}";
          break;
        case 'my-verified-records':
          def = "{\"quality\":\"V\",\"my_records\":1}";
          break;
        case 'my-groups':
          def = "{\"quality\":\"all\",\"my_records\":0,\"taxon_group_list\":"+indiciaData.userPrefsTaxonGroups+"}";
          break;
        case 'my-locality':
          def = "{\"quality\":\"all\",\"my_records\":0,\"indexed_location_id\":"+indiciaData.userPrefsLocation+"}";
          break;
        case 'my-groups-locality':
          def = "{\"quality\":\"all\",\"my_records\":0,\"taxon_group_list\":"+indiciaData.userPrefsTaxonGroups+",\"indexed_location_id\":"+indiciaData.userPrefsLocation+"}";
          break;
      }
      if (def) {
        filterLoaded([{"id":id,"title":$('#select-filter option:selected').html(),"definition":def}]);
      } else {
        $.ajax({
          dataType: "json",
          url: indiciaData.read.url + 'index.php/services/data/filter/' + id,
          data: 'mode=json&view=list&auth_token='+indiciaData.read.auth_token+'&nonce='+indiciaData.read.nonce+'&callback=?',
          success: filterLoaded
        });
      }
    }
  };
  
  function codeToSharingTerm(code) {
    switch (code) {
      case 'R': return 'reporting';
      case 'V': return 'verification';
      case 'P': return 'peer review';
      case 'D': return 'data flow';
      case 'M': return 'moderation';
    }
    return code;
  }
  
  loadFilterUser = function(fu, getParams) {
    indiciaData.filter.def = $.extend(JSON.parse(fu.filter_definition), getParams);
    indiciaData.filter.id = fu.filter_id;
    indiciaData.filter.filters_user_id = fu.id;
    indiciaData.filter.title = fu.filter_title;
    $('#filter\\:title').val(fu.filter_title);
    $('#filter\\:description').val(fu.filter_description);
    $('#filter\\:sharing').val(fu.filter_sharing);
    $('#sharing-type-label').html(codeToSharingTerm(fu.filter_sharing));
    $('#filters_user\\:user_id\\:person_name').val(fu.person_name);
    $('#filters_user\\:user_id').val(fu.user_id);
    applyFilterToReports();
    $('#filter-reset').removeClass('disabled');
    $('#filter-delete').removeClass('disabled');
    $('#active-filter-label').html('Active filter: '+fu.filter_title);
    updateFilterDescriptions();
    $('#standard-params .header span.changed').hide();
    // can't delete a filter you didn't create.
    if (fu.filter_created_by_id===indiciaData.user_id) {
      $('#filter-delete').show();
    } else {
      $('#filter-delete').hide();
    }
  };
  
  function filterParamsChanged() {
    $('#standard-params .header span.changed').show();
    $('#filter-reset').removeClass('disabled');
  }
  
  // Applies the current loaded filter to the controls within the pane.
  function updateControlValuesToReflectCurrentFilter(pane) {
    // regexp extracts the pane ID from the href. Loop through the controls in the pane      
    $.each(pane.find(':input').not(':checkbox,[type=button]'), function(idx, ctrl) {
      // set control value to the stored filter setting
      $(ctrl).val(indiciaData.filter.def[$(ctrl).attr('name')]);        
    });
    $.each(pane.find(':checkbox'), function(idx, ctrl) {
      $(ctrl).attr('checked', typeof indiciaData.filter.def[$(ctrl).attr('name')]!=="undefined" && indiciaData.filter.def[$(ctrl).attr('name')]==$(ctrl).val());
    });
  }
  
  $('.fb-filter-link').fancybox({
    onStart: function(e) {
      var pane=$(e[0].href.replace(/^[^#]+/, '')),
          paneName=$(pane).attr('id').replace('controls-filter_', '');
      if (typeof paneObjList[paneName].preloadForm!=="undefined") {
        paneObjList[paneName].preloadForm();
      }
      // reset
      pane.find('.fb-apply').data('clicked', false);
      updateControlValuesToReflectCurrentFilter(pane);
    },
    onComplete: function(e) {
      var pane=$(e[0].href.replace(/^[^#]+/, '')), context, 
          paneName=$(pane).attr('id').replace('controls-filter_', '');
      // if a context is loaded, need to let the forms configure themselves on this basis
      context = $('#context-filter').length ? indiciaData.filterContextDefs[$('#context-filter').val()] : null;
      $('.context-instruct').hide();
      // Does the pane have any special code for loading the definition into the form?
      if (typeof paneObjList[paneName].loadForm!=="undefined") {
        paneObjList[paneName].loadForm(context);
      }
      if (pane[0].id==='controls-filter_where') {
        indiciaData.mapdiv.map.updateSize();
        indiciaData.mapdiv.settings.drawObjectType='queryPolygon';
      }
    },
    onClosed: function(e) {
      var pane=$(e[0].href.replace(/^[^#]+/, ''));
      if (pane[0].id==='controls-filter_where' && typeof indiciaData.linkToMapDiv!=="undefined") {
        var element=$('#' + indiciaData.linkToMapDiv);
        $(indiciaData.mapdiv).css('width', indiciaData.origMapSize.width);
        $(indiciaData.mapdiv).css('height', indiciaData.origMapSize.height);
        $(indiciaData.origMapParent).append(element);
        indiciaData.mapdiv.map.setCenter(indiciaData.mapOrigCentre, indiciaData.mapOrigZoom);
        indiciaData.mapdiv.map.updateSize();
        indiciaData.mapdiv.settings.drawObjectType='boundary';
        indiciaData.disableMapDataLoading=false;
      }
    }
  }); 
  
  $('form.filter-controls :input').change(function(){
    filterParamsChanged();
  });

  $('#filter-apply').click(function() {
    loadFilter($('#select-filter').val(), {});
  });  
  
  $('#filter-reset').click(function() {
    resetFilter();
  }); 
  
  $('#filter-build').click(function() {
    var desc;
    $.each(paneObjList, function(name, obj) {
      desc=obj.getDescription();
      if (desc==='') {
        desc=indiciaData.lang['NoDescription' + name];
      }
      $('#pane-filter_'+name+' span.filter-desc').html(desc);
    });    
    $('#filter-details').slideDown();
    $('#filter-build').addClass('disabled');
  });
  
  $('#filter-delete').click(function(e) {
    if ($(e.currentTarget).hasClass('disabled')) {
      return;
    }
    if (confirm(indiciaData.lang.ConfirmFilterDelete.replace('{title}', indiciaData.filter.title))) {
      var filter = {"id": indiciaData.filter.id, "website_id":indiciaData.website_id, "user_id":indiciaData.user_id, "deleted":"t"};
      $.post(indiciaData.filterPostUrl, 
        filter,
        function (data) {
          if (typeof data.error === 'undefined') {
            alert(indiciaData.lang.FilterDeleted);
            $('#select-filter').val('');
            $('#select-filter').find('option[value="'+indiciaData.filter.id+'"]').remove();
            resetFilter();
          } else {
            alert(data.error);
          }
        }
      );
    }
  });
  
  $('#filter-done').click(function() {
    $('#filter-details').slideUp();
    $('#filter-build').removeClass('disabled');
  });
  
  $('.fb-close').click(function() {
    $.fancybox.close();
  });  
  
  $('form.filter-controls').submit(function(e){
    e.preventDefault();
    if (!$(e.currentTarget).valid() || $(e.currentTarget).find('.fb-apply').data('clicked')) {
      return false;
    }
    $(e.currentTarget).find('.fb-apply').data('clicked', true);
    var arrays={}, arrayName;
    // persist each control value into the stored settings
    $.each($(e.currentTarget).find(':input[name]'), function(idx, ctrl) {
      if (!$(ctrl).hasClass('olButton')) { // skip open layers switcher
        if ($(ctrl).attr('type')!=='checkbox' || $(ctrl).attr('checked')) {
          // array control?
          if ($(ctrl).attr('name').match(/\[\]$/)) {
            // store array control data to handle later
            arrayName=$(ctrl).attr('name').substring(0, $(ctrl).attr('name').length-2);
            if (typeof arrays[arrayName]==="undefined") {
              arrays[arrayName] = [];
            }
            arrays[arrayName].push($(ctrl).val());
          } else {
            // normal control
            indiciaData.filter.def[$(ctrl).attr('name')]=$(ctrl).val();
          }
        }
        else {
          // an unchecked checkbox so clear it's value
          indiciaData.filter.def[$(ctrl).attr('name')]='';
        }
      }
    });
    // convert array values to comma lists
    $.each(arrays, function(name, arr) {
      indiciaData.filter.def[name] = arr.join(',');
    });
    var pane=e.currentTarget.parentNode.id.replace('controls-filter_', '');
    // Does the pane have any special code for applying it's settings to the definition?
    if (typeof paneObjList[pane].applyFormToDefinition!=="undefined") {
      paneObjList[pane].applyFormToDefinition();
    }
    applyFilterToReports();
    updateFilterDescriptions();
    $.fancybox.close();
  });
  
  var saveFilter = function() {
    if (saving) {
      return;
    }
    if ($.trim($('#filter\\:title').val())==='') {
      alert('Please provide a name for your filter.');
      $('#filter\\:title').focus();
      return;
    }
    if ($('#filters_user\\:user_id').length && $('#filters_user\\:user_id').val()==='') {
      alert('Please fill in who this filter is for.');
      $('#filters_user\\:user_id\\:person_name').focus();
      return;
    }
    saving=true;
    // TODO: Validate user control
    
    var adminMode = $('#filters_user\\:user_id').length===1,
        user_id=adminMode ? $('#filters_user\\:user_id').val() : indiciaData.user_id,
        sharing=adminMode ? $('#filter\\:sharing').val() : indiciaData.filterSharing,
        url, 
        filter = {"website_id":indiciaData.website_id, "user_id":indiciaData.user_id,
          "filters_user:user_id":user_id, "filter:title":$('#filter\\:title').val(),
          "filter:description":$('#filter\\:description').val(),
          "filter:definition":JSON.stringify(indiciaData.filter.def), "filter:sharing":sharing,
          "filter:defines_permissions":adminMode?'t':'f'};
    // if existing filter and the title has not changed, or in admin mode, overwrite
    if (indiciaData.filter.id && ($('#filter\\:title').val()===indiciaData.filter.title || adminMode)) {
      filter['filter:id'] = indiciaData.filter.id;
    }
    // if existing filters_users then hook to same record
    if (typeof indiciaData.filter.filters_user_id!=="undefined") {
      filter['filters_user:id']=indiciaData.filter.filters_user_id;
    }
    // If a new filter or admin mode, then also need to create a filters_users record.
    url = (typeof indiciaData.filter.id==="undefined" || indiciaData.filter.id===null || adminMode) ? indiciaData.filterAndUserPostUrl : indiciaData.filterPostUrl;
    $.post(url, filter,
      function (data) {
        if (typeof data.error === 'undefined') {
          alert(indiciaData.lang.FilterSaved);
          indiciaData.filter.id = data.outer_id;
          indiciaData.filter.title = $('#filter\\:title').val();
          indiciaData.filter.filters_user_id=data.struct.children[0].id;
          $('#active-filter-label').html('Active filter: '+$('#filter\\:title').val());
          $('#standard-params .header span.changed').hide();
          $('#select-filter').val(indiciaData.filter.id);
          if ($('#select-filter').val()==='') {
            // this is a new filter, so add to the select list
            $('#select-filter').append('<option value="' + indiciaData.filter.id + '" selected="selected">' + indiciaData.filter.title + '</option>');
          }
          if (indiciaData.redirectOnSuccess!=='') {
            window.location=indiciaData.redirectOnSuccess;
          }
        } else {
          var handled = false;
          if (typeof data.errors!=="undefined") {
            $.each(data.errors, function(key, msg) {
              if (msg.indexOf('duplicate')>-1) {
                if (confirm(indiciaData.lang.FilterExistsOverwrite)) {
                  // need to load the existing filter to get it's ID, then resave
                  $.getJSON(indiciaData.read.url + 'index.php/services/data/filter?created_by_id='+indiciaData.user_id+'&title='+
                      encodeURIComponent($('#filter\\:title').val())+'&sharing='+indiciaData.filterSharing+
                      '&mode=json&view=list&auth_token='+indiciaData.read.auth_token+'&nonce='+indiciaData.read.nonce+'&callback=?', function(data) {
                    indiciaData.filter.id = data[0].id;
                    indiciaData.filter.title = $('#filter\\:title').val();
                    saveFilter();
                  });
                }
                handled = true;
              }
            });
          }
          if (!handled) {
            alert(data.error);
          }
        }
        saving=false;
        $('#filter-build').html(indiciaData.lang.ModifyFilter);
        $('#filter-reset').removeClass('disabled');
      },
      'json'
    );
  };
  
  $('#imp-location').hide();
  $('#filter-save').click(saveFilter);
  $('#context-filter').change(resetFilter);
  
  filterChange();
  applyDefaults();
  $('form.filter-controls').validate();
 
});
