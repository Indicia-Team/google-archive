<?php
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
 * @package	Client
 * @subpackage PrebuiltForms
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

/**
 * Prebuilt Indicia data entry form.
 * NB has Drupal specific code.
 *
 * @package	Client
 * @subpackage PrebuiltForms
 */

require_once('includes/language_utils.php');
require_once('includes/user.php');

class iform_pollenator_gallery {

	/* TODO
	 * TODO photoreel: validate insect -> success posts, adds to photoreel, clears insect.
	 * 					clicking on photo -> validates existing insect (as above), sets insects
	 * 		occurrence attributes
	 * 		floral station.
	 * TODO L2 validation rules for radio buttons.
	 * TODO L4 convert uploads to flash to give progress bar.
	 * TODO nsp on floral station - "do not know"
	 * TODO convert ajaxsubmits to ajaxforms.
	 */
  /**
   * Get the list of parameters for this form.
   * @return array List of parameters that this form requires.
   */
  public static function get_parameters() {
    $retVal = array(
     array(
      	'name'=>'survey_id',
        'caption'=>'Survey ID',
        'description'=>'The Indicia ID of the survey that data will be posted into.',
        'type'=>'int'
        ),
      array(
          'name'=>'start_time_attr_id',
          'caption'=>'Start Time Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the start time.',
          'type'=>'int',
          'group'=>'Session Attributes'
            ),
      array(
          'name'=>'end_time_attr_id',
          'caption'=>'End Time Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the end time.',
          'type'=>'int',
          'group'=>'Session Attributes'
            ),
      array(
          'name'=>'sky_state_attr_id',
          'caption'=>'Sky State Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the state of the sky.',
          'type'=>'int',
          'group'=>'Session Attributes'
            ),
      array(
          'name'=>'temperature_attr_id',
          'caption'=>'Temperature Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the temperature.',
          'type'=>'int',
          'group'=>'Session Attributes'
            ),
      array(
          'name'=>'wind_attr_id',
          'caption'=>'Wind Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the Wind.',
          'type'=>'int',
          'group'=>'Session Attributes'
            ),
      array(
          'name'=>'shade_attr_id',
          'caption'=>'Shade Attribute ID',      
          'description'=>'Indicia ID for the sample attribute that stores the shade.',
          'type'=>'int',
          'group'=>'Session Attributes'
      ) ,
      array(
          'name'=>'insect_list_id',
          'caption'=>'Insect Species List ID',
          'description'=>'The Indicia ID for the species list that insects can be selected from.',
          'type'=>'int',
          'group'=>'Insect Attributes'
      )
    );
    return $retVal;
  	
  }

  /**
   * Return the form title.
   * @return string The title of the form.
   */
  public static function get_title() {
    return 'Pollenators: Gallery Filter and Focus on Collection and Insect';
  }

/**
   * Return the generated form output.
   * @return Form HTML.
   */
  public static function get_form($args, $node) {
  	global $user;
  	// There is a language entry in the args parameter list: this is derived from the $language DRUPAL global.
  	// It holds the 2 letter code, used to pick the language file from the lang subdirectory of prebuilt_forms.
  	// There should be no explicitly output text in this file.
  	// We must translate any field names and ensure that the termlists and taxonlists use the correct language.
  	// For attributes, the caption is automatically translated by data_entry_helper.
    $logged_in = $user->uid>0;
    $uid = $user->uid;
    $email = $user->mail;
    $username = $user->name;

  	$r = '';

    // Get authorisation tokens to update and read from the Warehouse.
    $readAuth = data_entry_helper::get_read_auth($args['website_id'], $args['password']);
	$svcUrl = data_entry_helper::$base_url.'/index.php/services';

	drupal_add_js(drupal_get_path('module', 'iform') .'/media/js/jquery.form.js', 'module');
	data_entry_helper::link_default_stylesheet();
	data_entry_helper::add_resource('jquery_ui');
	data_entry_helper::enable_validation('new-comments-form'); // don't care about ID itself, just want resources
	
	
	if (array_key_exists('occurrence_id', $_GET)){
        $occID = $_GET['occurrence_id'];
	} else {
		return "<strong>".lang::get('LANG_Invocation_Error')."</strong>";
	}
	
//	data_entry_helper::enable_validation('cc-1-collection-details'); // don't care about ID itself, just want resources
	
	// The only things that will be editable after the collection is saved will be the identifiaction of the flower/insects.
	// no id - just getting the attributes, rest will be filled in using AJAX
	$sample_attributes = data_entry_helper::getAttributes(array(
        'valuetable'=>'sample_attribute_value'
       ,'attrtable'=>'sample_attribute'
       ,'key'=>'sample_id'
       ,'fieldprefix'=>'smpAttr'
       ,'extraParams'=>$readAuth
       ,'survey_id'=>$args['survey_id']
    ));
    $occurrence_attributes = data_entry_helper::getAttributes(array(
        'valuetable'=>'occurrence_attribute_value'
       ,'attrtable'=>'occurrence_attribute'
       ,'key'=>'occurrence_id'
       ,'fieldprefix'=>'occAttr'
       ,'extraParams'=>$readAuth
       ,'survey_id'=>$args['survey_id']
    ));
    $location_attributes = data_entry_helper::getAttributes(array(
        'valuetable'=>'location_attribute_value'
       ,'attrtable'=>'location_attribute'
       ,'key'=>'location_id'
       ,'fieldprefix'=>'locAttr'
       ,'extraParams'=>$readAuth
       ,'survey_id'=>$args['survey_id']
    ));
    $defAttrOptions = array('extraParams'=>$readAuth,
    				'lookUpListCtrl' => 'radio_group',
    				'validation' => array('required'),
    				'language' => iform_lang_iso_639_2($args['language']));
	// note we have to proxy the post. Every time a write transaction is carried out, the write nonce is trashed.
	// For security reasons we don't want to give the user the ability to generate their own nonce, so we use
	// the fact that the user is logged in to drupal as the main authentication/authorisation/identification
	// process for the user. The proxy packages the post into the correct format	

 	$defAttrOptions = array('extraParams'=>$readAuth, 'readonly' => 'readonly'); 	
	$species_ctrl_args=array(
    	    'label'=>lang::get('LANG_Insect_Species'),
        	'fieldname'=>'determination:taxa_taxon_list_id',
	        'table'=>'taxa_taxon_list',
    	    'captionField'=>'taxon',
        	'valueField'=>'id',
	        'columns'=>2,
    		'blankText'=>lang::get('LANG_Choose_Taxon'),
    	    'extraParams'=>$readAuth + array('taxon_list_id' => $args['insect_list_id'])
	);
	$flower_ctrl_args=array(
    	    'label'=>lang::get('LANG_Flower_Species'),
        	'fieldname'=>'flower:taxa_taxon_list_id',
	        'table'=>'taxa_taxon_list',
    	    'captionField'=>'taxon',
        	'valueField'=>'id',
	        'columns'=>2,
    		'blankText'=>lang::get('LANG_Choose_Taxon'),
    	    'extraParams'=>$readAuth + array('taxon_list_id' => $args['flower_list_id'])
	);
	$insect_ctrl_args=array(
    	    'label'=>lang::get('LANG_Insect_Species'),
        	'fieldname'=>'insect:taxa_taxon_list_id',
	        'table'=>'taxa_taxon_list',
    	    'captionField'=>'taxon',
        	'valueField'=>'id',
	        'columns'=>2,
    		'blankText'=>lang::get('LANG_Choose_Taxon'),
    	    'extraParams'=>$readAuth + array('taxon_list_id' => $args['insect_list_id'])
	);
 	$r .= '
<div id="filter" class="ui-accordion ui-widget ui-helper-reset">
	<div id="filter-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-accordion-content-active ui-corner-top">
	  	<div id="results-collections-title">
	  		<span>TBD Filter Collections</span>
    	</div>
	</div>
	<div id="filter-spec" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active">
	  <div class="ui-accordion ui-widget ui-helper-reset">
		<div id="general-filter-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  		<div id="reset-general-button" class="right ui-state-default ui-corner-all reset-general-button">'.lang::get('LANG_Reset_Filter').'</div>
			<div id="general-filter-title">
		  		<span>TBD General</span>
      		</div>
		</div>
	    <div id="general-filter-body" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active ui-corner-bottom">
    	</div>
		<div id="flower-filter-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  		<div id="reset-flower-button" class="right ui-state-default ui-corner-all reset-flower-button">'.lang::get('LANG_Reset_Filter').'</div>
			<div id="flower-filter-title">
		  		<span>TBD Flowers</span>
      		</div>
		</div>
		<div id="flower-filter-body" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active ui-corner-bottom">
		'.data_entry_helper::select($flower_ctrl_args).'
    	</div>
		<div id="insect-filter-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  		<div id="reset-insect-button" class="right ui-state-default ui-corner-all reset-insect-button">'.lang::get('LANG_Reset_Filter').'</div>
			<div id="insect-filter-title">
		  		<span>TBD Insects</span>
      		</div>
		</div>
		<div id="insect-filter-body" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active ui-corner-bottom">
		'.data_entry_helper::select($insect_ctrl_args).'
		</div>
		<div id="location-filter-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  		<div id="reset-location-button" class="right ui-state-default ui-corner-all reset-location-button">'.lang::get('LANG_Reset_Filter').'</div>
			<div id="location-filter-title">
		  		<span>TBD Location</span>
      		</div>
		</div>
		<div id="location-filter-body" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active ui-corner-bottom">
    	</div>
      </div>
    </div>
    <div id="filter-footer" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active ui-corner-bottom">
	  <div id="search-insects-button" class="right ui-state-default ui-corner-all search-insects-button">'.lang::get('LANG_Search_Insects').'</div>
      <div id="search-collections-button" class="right ui-state-default ui-corner-all search-collections-button">'.lang::get('LANG_Search_Collections').'</div>
    </div>
	<div id="results-collections-header" class="ui-accordion-header ui-helper-reset ui-corner-top">
	  <div id="results-collections-title">
	  	<span>TBD Collections Filter results</span>
      </div>
	</div>
	<div id="results-collections-results" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
    </div>
	<div id="results-insects-header" class="ui-accordion-header ui-helper-reset ui-corner-top">
	  <div id="results-insects-title">
	  	<span>TBD Insect Filter results</span>
      </div>
	</div>
	<div id="results-insects-results" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
    </div>
</div>
<div id="focus-collection" class="ui-accordion ui-widget ui-helper-reset">
	<div id="collection-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  <div id="collection-title">
	  	<span>TBD Breadcrumb</span>
      </div>
	</div>
	<div id="collection-details" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active">
	  <div id="flower-image">
      </div>
      <div id="map">
      </div>
	  <div id="collection-description">
      </div>
	  <div id="environment-image">
      </div>
    </div>
	<div id="collection-insects" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active">
    </div>
	<div id="collection-comments" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
    </div>
</div>
<div id="focus-insect" class="ui-accordion ui-widget ui-helper-reset">
	<div id="insect-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  <div class="right">
 	      <span id="collection-button" class="ui-state-default ui-corner-all collection-button">'.lang::get('LANG_Collection').'</span>
	      <span id="previous-button" class="ui-state-default ui-corner-all previous-button">'.lang::get('LANG_Previous').'</span>
	      <span id="next-button" class="ui-state-default ui-corner-all next-button">'.lang::get('LANG_Next').'</span>
	  </div>
	  <div id="insect-title">
	  	<span>TBD Breadcrumb</span>
      </div>
	</div>
	<div id="insect-picture" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
	  <div id="insect-image">
      </div>
	  <div class="right">
	      <span id="preferred-insect-button" class="ui-state-default ui-corner-all next-button">'.lang::get('LANG_Add_Preferred_Insect').'</span>
	  </div>
    </div>
	<div id="insect-identification" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  <div id="new-id-button" class="right ui-state-default ui-corner-all new-id-button">'.lang::get('LANG_New_ID').'</div>
	  <div id="doubt-button" class="right ui-state-default ui-corner-all doubt-button">'.lang::get('LANG_Doubt').'</div>
	  <div id="id-title">
	  	<span>'.lang::get('LANG_Indentification_Title').'</span>
      </div>
    </div>
	<div id="current-id" class="ui-accordion-content ui-helper-reset ui-widget-content ui-accordion-content-active">
	</div>
	<div id="new-id" class="ui-accordion-content ui-helper-reset ui-widget-content">
	  <form id="new-id-form" action="'.iform_ajaxproxy_url($node, 'determination').'" method="POST">
		<input type="hidden" name="website_id" value="'.$args['website_id'].'" />
    	<input type="hidden" name="determination:occurrence_id" value="" />
		<input type="hidden" name="determination:cms_ref" value="'.$uid.'" />  
    	<input type="hidden" name="determination:person_name" value="'.$username.'" />  
		<input type="hidden" name="determination:email_address" value="'.$email.'" />
        <p>TBD '.lang::get('LANG_Launch_ID_Key').'</p>
        '.data_entry_helper::select($species_ctrl_args).'
    	<input type="submit" id="id_submit_button" class="ui-state-default ui-corner-all" value="'.lang::get('LANG_Validate').'" />
      </form>
	</div>
	<div id="id-history" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
	</div>
	
	<div id="additional-information-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	  <div id="id-title">
	  	<span>'.lang::get('LANG_Additional_Info_Title').'</span>
      </div>
	</div>
	<div id="additional-information" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
		<label for="sample_date">'.lang::get('LANG_Date').'</label>
		<input type="text" id="sample_date" readonly="readonly">
		<label for="sample_start_time">'.lang::get('LANG_Time').'</label>
		<input type="text" id="sample_start_time" readonly="readonly">
		'.lang::get('LANG_To').'
		<input type="text" id="sample_end_time" readonly="readonly"><br />
		<label for="sample_sky">'.$sample_attributes[$args['sky_state_attr_id']]['caption'].'</label>
		<input type="text" id="sample_sky" readonly="readonly">
		<label for="sample_temp">'.$sample_attributes[$args['temperature_attr_id']]['caption'].'</label>
		<input type="text" id="sample_temp" readonly="readonly">
		<label for="sample_wind">'.$sample_attributes[$args['wind_attr_id']]['caption'].'</label>
		<input type="text" id="sample_wind" readonly="readonly"><br />
		<label for="sample_shade">'.$sample_attributes[$args['shade_attr_id']]['caption'].'</label>
		<input type="text" id="sample_wind" readonly="readonly"><br />
	</div>
	<div id="comments-header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top">
	    <div id="new-comment-button" class="right ui-state-default ui-corner-all new-comment-button">'.lang::get('LANG_New_Comment').'</div>
		<span>'.lang::get('LANG_Comments_Title').'</span>
	</div>
	<div id="new-comments" class="ui-accordion-content ui-helper-reset ui-widget-content">
		<form id="new-comments-form" action="'.iform_ajaxproxy_url($node, 'occ-comment').'" method="POST">
		    <input type="hidden" name="website_id" value="'.$args['website_id'].'" />
    		<input type="hidden" name="occurrence_comment:occurrence_id" value="" />
    		<label for="occurrence_comment:person_name">'.lang::get('LANG_Username').':</label>
		    <input type="text" name="occurrence_comment:person_name" value="'.$username.'" readonly="readonly" /><br />  
    		<label for="occurrence_comment:email_address">'.lang::get('LANG_Email').':</label>
		    <input type="text" name="occurrence_comment:email_address" value="'.$email.'" readonly="readonly" /><br />
		    '.data_entry_helper::textarea(array('label'=>lang::get('LANG_Comment'), 'fieldname'=>'occurrence_comment:comment', 'class'=>'required')).'
    		<input type="submit" id="comment_submit_button" class="ui-state-default ui-corner-all" value="'.lang::get('LANG_Submit_Comment').'" />
    	</form>
	</div>
	<div id="comments-block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active">
	</div>
</div>
';

    data_entry_helper::$javascript .= "

previous_insect = '';
next_insect = '';
collection = '';

jQuery('form#new-id-form').ajaxForm({ 
	// dataType identifies the expected content type of the server response 
	dataType:  'json', 
	// success identifies the function to invoke when the server response 
	// has been received 
	beforeSubmit:   function(data, obj, options){
		if (!jQuery('form#new-id-form').valid()) { return false; }
		return true;
	},
	success:   function(data){
		if(data.error == undefined){
			jQuery('[name=determination\\:taxa_taxon_list_id]').val('');
			jQuery('#new-id').removeClass('ui-accordion-content-active');
			loadDeterminations(jQuery('[name=determination\\:occurrence_id]').val());
		} else {
			alert(data.error);
		}
	} 
});

jQuery('#new-comments-form').ajaxForm({ 
	// dataType identifies the expected content type of the server response 
	dataType:  'json', 
	// success identifies the function to invoke when the server response 
	// has been received 
	beforeSubmit:   function(data, obj, options){
		if (!jQuery('form#new-comments-form').valid()) { return false; }
		return true;
	},
	success:   function(data){
		if(data.error == undefined){
			jQuery('[name=occurrence_comment\\:comment]').val('');
			jQuery('#new-comments').removeClass('ui-accordion-content-active');
			loadComments(jQuery('[name=occurrence_comment\\:occurrence_id]').val());
  		} else {
			alert(data.error);
		}
	} 
});

loadSampleAttributes = function(keyValue){
	$.getJSON(\"".$svcUrl."/data/sample_attribute_value\"  +
   			\"?mode=json&view=list&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&sample_id=\" + keyValue + \"&callback=?\", function(attrdata) {
		if (attrdata.length>0) {
			for(i=0; i< attrdata.length; i++){
				if (attrdata[i].id){
					switch(parseInt(attrdata[i].sample_attribute_id)){
						case ".$args['start_time_attr_id'].":
							jQuery('#sample_start_time').val(attrdata[i].value);
							break;
						case ".$args['end_time_attr_id'].":
							jQuery('#sample_end_time').val(attrdata[i].value);
							break;
  						case ".$args['sky_state_attr_id'].":
							jQuery('#sample_sky').val(attrdata[i].value);
							break;
  						case ".$args['temperature_attr_id'].":
							jQuery('#sample_temp').val(attrdata[i].value);
							break;
  						case ".$args['wind_attr_id'].":
							jQuery('#sample_wind').val(attrdata[i].value);
							break;
  						case ".$args['shade_attr_id'].":
							jQuery('#sample_shade').val(attrdata[i].value);
							break;
  					}
				}
			}
		}
	});
}
		
loadImage = function(imageTable, key, keyValue, target){
	jQuery(target).empty();
	$.getJSON(\"".$svcUrl."/data/\" + imageTable +
   			\"?mode=json&view=list&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&\" + key + \"=\" + keyValue + \"&callback=?\", function(imageData) {
		if (imageData.length>0) {
			var img = new Image();
			jQuery(img)
        		.load(function () {
        			jQuery(target).empty().append(this);
			    })
			    .attr('src', '".(data_entry_helper::$base_url).(data_entry_helper::$indicia_upload_path)."'+imageData[0].path)
			    .attr('width', $(target).width());
			    ;
		}
	});
}

loadDeterminations = function(keyValue){
	jQuery('#id-history').empty().append('<strong>".lang::get('LANG_History_Title')."</strong>');
	jQuery('#current-id').empty();
	$.getJSON(\"".$svcUrl."/data/determination\" +
   			\"?mode=json&view=list&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&occurrence_id=\" + keyValue + \"&callback=?\", function(detData) {
   		if (detData.length>0) {
			var i = detData.length-1;
			var string = '';
			if(detData[i].taxon != '' && detData[i].taxon != null){
				string = string + detData[i].taxon + ', ';
			}
			if(detData[i].taxon_text_description != '' && detData[i].taxon_text_description != null){
				string = string + detData[i].taxon_text_description + ', ';
			}
			if(detData[i].taxon_extra_info != '' && detData[i].taxon_extra_info != null){
				string = string + detData[i].taxon_text_description + ', ';
			}
			jQuery('<p><strong>'+string+ '</strong>".lang::get('LANG_Comment_By')."' + detData[i].person_name + ' ' + detData[i].updated_on + '</p>').appendTo('#current-id')
   			for(i=detData.length - 2; i >= 0; i--){ // deliberately miss last one, in reverse order
				var string = detData[i].updated_on + ' : ';
				if(detData[i].taxon != '' && detData[i].taxon != null){
					string = string + detData[i].taxon + ', ';
				}
				if(detData[i].taxon_text_description != '' && detData[i].taxon_text_description != null){
					string = string + detData[i].taxon_text_description + ', ';
				}
				if(detData[i].taxon_extra_info != '' && detData[i].taxon_extra_info != null){
					string = string + detData[i].taxon_text_description + ', ';
				}
				jQuery('<p>'+string+ '".lang::get('LANG_Comment_By')."' + detData[i].person_name+'</p>').appendTo('#id-history')
			}
		} else {
			jQuery('<p>".lang::get('LANG_No_Determinations')."</p>')
					.appendTo('#id-history');
			jQuery('<p>".lang::get('LANG_No_Determinations')."</p>')
					.appendTo('#current-id');
  }
	});
};
loadComments = function(keyValue){
					// location_image, location_id, location:id, 1, #cc-4-insect-image
	jQuery('#comments-block').empty();
	$.getJSON(\"".$svcUrl."/data/occurrence_comment\" +
   			\"?mode=json&view=list&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&occurrence_id=\" + keyValue + \"&callback=?\", function(commentData) {
   		if (commentData.length>0) {
   			for(i=commentData.length - 1; i >= 0; i--){
	   			var newCommentDetails = jQuery('<div class=\"insect-comment-details\"/>')
					.appendTo('#comments-block');
				jQuery('<span>".lang::get('LANG_Comment_By')."' + commentData[i].person_name + ' ' + commentData[i].updated_on + '</span>')
					.appendTo(newCommentDetails);
	   			var newComment = jQuery('<div class=\"insect-comment-body\"/>')
					.appendTo('#comments-block');
				jQuery('<p>' + commentData[i].comment + '</p>')
					.appendTo(newComment);
			}
		} else {
			jQuery('<p>".lang::get('LANG_No_Comments')."</p>')
					.appendTo('#comments-block');
		}
	});
};

loadAddnInfo = function(keyValue){
	// TODO convert buttons into thumbnails
	previous_insect = '';
	next_insect = '';
	collection = '';
	jQuery('#previous-button').hide();
	jQuery('#next-button').hide();
	
	// fetch occurrence details first to get the sample_id.
	// Get the sample to get the parent_id.
	// get all the samples (sessions) with the same parent_id;
	// fetch all the occurrences of the sessions.
	$.getJSON(\"".$svcUrl."/data/occurrence/\" + keyValue +
   			\"?mode=json&view=detail&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   			\"&callback=?\", function(occData) {
   		if (occData.length > 0) {
			$.getJSON(\"".$svcUrl."/data/sample/\" + occData[0].sample_id +
   					\"?mode=json&view=detail&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   					\"&callback=?\", function(smpData) {
   				if (smpData.length > 0) {
   					collection = smpData[0].parent_id;
					jQuery('#sample_date').val(smpData[0].date_start);
					loadSampleAttributes(smpData[0].id);
					$.getJSON(\"".$svcUrl."/data/sample/\" +
   							\"?mode=json&view=detail&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   							\"&parent_id=\" + smpData[0].parent_id + \"&callback=?\", function(smpList) {
   						if (smpList.length > 0) {
   							for(j=0; j< smpList.length; j++){
		   						$.getJSON(\"".$svcUrl."/data/occurrence\" +
   										\"?mode=json&view=detail&nonce=".$readAuth['nonce']."&auth_token=".$readAuth['auth_token']."\" +
   										\"&sample_id=\" + smpList[j].id + \"&callback=?\", function(occList) {
	   								if(occList.length > 0){
   										for(i=0; i< occList.length; i++){
   											if(parseInt(occList[i].id) < parseInt(keyValue) && (previous_insect == '' || parseInt(occList[i].id) > parseInt(previous_insect))){
   												previous_insect = occList[i].id;
												jQuery('#previous-button').show();
											}
   											if(parseInt(occList[i].id) > parseInt(keyValue) && (next_insect == '' || parseInt(occList[i].id) > parseInt(next_insect))){
   												next_insect = occList[i].id;
												jQuery('#next-button').show();
  											}
   										}
   									}
   								});
   							}
   						}
  					});
  				}
   		   	});
   		}
   	});
}

loadInsect = function(insectID){
	jQuery('[name=determination\\:occurrence_id]').val(insectID);
	jQuery('[name=occurrence_comment\\:occurrence_id]').val(insectID);
	loadImage('occurrence_image', 'occurrence_id', insectID, '#insect-image');
	loadDeterminations(insectID);
	loadAddnInfo(insectID);
	loadComments(insectID);
}

jQuery('#new-comment-button').click(function(){ 
	jQuery('#new-comments').toggleClass('ui-accordion-content-active');
});
jQuery('#new-id-button').click(function(){ 
	jQuery('#new-id').toggleClass('ui-accordion-content-active');
});
jQuery('#collection-button').click(function(){
	alert('TBD');
//	loadCollection('ui-accordion-content-active');
});
jQuery('#previous-button').click(function(){
	if(previous_insect != '') {
		loadInsect(previous_insect);
	}
});
jQuery('#next-button').click(function(){
	if(next_insect != '') {
		loadInsect(next_insect);
	}
});

loadInsect(".$occID.");

  ";
    return $r;
  }

    /**
   * Handles the construction of a submission array from a set of form values.
   * @param array $values Associative array of form data values.
   * @param array $args iform parameters.
   * @return array Submission structure.
   */
  public static function get_submission($values, $args) {
  	// Submission is AJAX based.
  	return false;
  }

  /**
   * Retrieves a list of the css files that this form requires in addition to the standard
   * Drupal, theme or Indicia ones.
   *
   * @return array List of css files to include for this form.
   */
  public static function get_css() {
    return array('pollenator_gallery.css');
  }
}