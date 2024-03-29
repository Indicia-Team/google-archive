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
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */
 
/**
 * Link in other required php files.
 */
require_once('lang.php');
require_once('helper_config.php');
require_once('helper_base.php');

/**
 * A class with helper methods for handling prebuilt forms and generating complete parameters entry forms from 
 * simple input arrays.
 */
class form_helper extends helper_base {

  /**
   * Outputs a pair of linked selects, for picking a prebuilt form from the library. The first select is for picking a form 
   * category and the second select is populated by AJAX for picking the actual form.
   * @param array $options Options array with the following possibilities:<ul>
   * <li><b>form</b><br/>
   * Optional. The name of the form to select as a default value.</li>
   * <li><b>includeOutputDiv</b><br/>
   * Set to true to generate a div after the controls which will receive the form parameter
   * controls when a form is selected.</li>
   * </ul>
   */
  public static function prebuilt_form_picker($options) {
    require_once('data_entry_helper.php');
    form_helper::add_resource('jquery_ui');
    $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . self::relative_client_helper_path();
    $r = '';
    if (!$dir = opendir($path.'prebuilt_forms/'))
      throw new Exception('Cannot open path to prebuilt form library.');
    while (false !== ($file = readdir($dir))) {
      $parts=explode('.', $file);
      if ($file != "." && $file != ".." && strtolower($parts[count($parts)-1])=='php') {
        require_once $path.'prebuilt_forms/'.$file;
        $file_tokens=explode('.', $file);
        ob_start();
        if (is_callable(array('iform_'.$file_tokens[0], 'get_'.$file_tokens[0].'_definition'))) {
          $definition = call_user_func(array('iform_'.$file_tokens[0], 'get_'.$file_tokens[0].'_definition'));
          $definition['title'] = lang::get($definition['title']);
          $forms[$definition['category']][$file_tokens[0]] = $definition;
          if (isset($options['form']) && $file_tokens[0]==$options['form']) 
            $defaultCategory = $definition['category'];
        } elseif (is_callable(array('iform_'.$file_tokens[0], 'get_title'))) {
          $title = call_user_func(array('iform_'.$file_tokens[0], 'get_title'));
          $forms['Miscellaneous'][$file_tokens[0]] = array('title' => $title);
          if (isset($options['form']) && $file_tokens[0]==$options['form'])
            $defaultCategory = 'Miscellaneous';
        }
        ob_end_clean();
      }
    }
    if (isset($defaultCategory)) {
      $availableForms = array();
      foreach ($forms[$defaultCategory] as $form=>$def) 
        $availableForms[$form] = $def['title'];
    } else {
      $defaultCategory = '';
      $availableForms = array('' => '&lt;Please select a category first&gt;');
    }
    closedir($dir);
    // makes an assoc array from the categories.
    $categories = array_merge(
      array('' => '&lt;Please select&gt;'),
      array_combine(array_keys($forms), array_keys($forms))
    );
    // translate categories
    foreach ($categories as $key=>&$value) {
      $value = lang::get($value);
    }
    asort($categories);
    $r .= data_entry_helper::text_input(array(
      'label' => lang::get('Website ID'),
      'fieldname' => 'website_id',
      'helpText' => lang::get('Enter the ID of the website record on the Warehouse you are using.'),
      'default' => isset($options['website_id']) ? $options['website_id'] : ''
    ));
    $r .= data_entry_helper::text_input(array(
      'label' => lang::get('Password'),
      'fieldname' => 'password',
      'helpText' => lang::get('Enter the password for the website record on the Warehouse you are using.'),
      'default' => isset($options['password']) ? $options['password'] : ''
    ));
    $r .= data_entry_helper::select(array(
      'id' => 'form-category-picker',
      'label' => lang::get('Select Form Category'),
      'helpText' => lang::get('Select the form category pick a form from.'),
      'lookupValues' => $categories, 
      'default' => $defaultCategory
    ));
    
    $r .= data_entry_helper::select(array(
      'id' => 'form-picker',
      'fieldname' => 'iform',
      'label' => lang::get('Select Form'),
      'helpText' => lang::get('Select the Indicia form you want to use.'),
      'lookupValues' => $availableForms,
      'default' => isset($options['form']) ? $options['form'] : ''
    ));
    // div for the form instructions
    $details = '';
    if (isset($options['form'])) {
      if (isset($forms[$defaultCategory][$options['form']]['description'])) {
        $details .= '<p>'.$forms[$defaultCategory][$options['form']]['description'].'</p>';
      }
      if (isset($forms[$defaultCategory][$options['form']]['helpLink'])) {
        $details .= '<p><a href="'.$forms[$defaultCategory][$options['form']]['helpLink'].'">Find out more...</a></p>';
      }
      if ($details!=='') $details = "<div class=\"ui-state-highlight ui-corner-all page-notice\">$details</div>";
    }
    $r .= "<div id=\"form-def\">$details</div>\n";
    $r .= '<input type="button" value="'.lang::get('Load Settings Form').'" id="load-params" disabled="disabled" />';
    if (isset($options['includeOutputDivs']) && $options['includeOutputDivs']) {
      $r .= '<div id="form-params"></div>';
    }
    self::add_form_picker_js($forms);
    return $r;
  }
  
  /**
   * Adds the JavaScript required to drive the prebuilt form picker.
   */
  private function add_form_picker_js($forms) {
    self::$javascript .= "prebuilt_forms = ".json_encode($forms).";

$('#form-category-picker').change(function(evt) {
  var opts = '<option value=\"\">".lang::get('&lt;Please select&gt;')."</option>';
  $.each(prebuilt_forms[evt.currentTarget.value], function(form, def) {
    opts += '<option value=\"'+form+'\">'+def.title+'</option>';
  });
  $('#form-picker').html(opts);
  $('#form-picker').change();
});

$('#form-picker').change(function(evt) {
  $('#load-params').attr('disabled','');
  $('#form-params').html('');
  var details='';
  if ($('#form-picker').val()!=='') {
    def = prebuilt_forms[$('#form-category-picker').val()][$('#form-picker').val()];
    if (typeof def.description !== 'undefined') {
      details += '<p>'+def.description+'</p>';
    }
    if (typeof def.helpLink !== 'undefined') {
      details += '<p><a href=\"'+def.helpLink+'\" target=\"_blank\">".lang::get('Find out more...')."</a></p>';
    }
    if (details!=='') {
      details = '<div class=\"ui-state-highlight ui-corner-all page-notice\">' + details + '</div>';
    }
  }
  $('#form-def').hide().html(details).fadeIn();
});

$('#load-params').click(function(evt) {
  if ($('#form-picker').val()==='' || $('#website_id').val()==='' || $('#form-picker').val()==='') {
    alert('".lang::get('Please specify a website ID, password and select a form before proceeding.')."');
  } else {
    if (typeof prebuilt_forms[$('#form-category-picker').val()][$('#form-picker').val()] !== \"undefined\") {
      // now use an Ajax request to get the form params
      $.post(
        '".self::getRootFolder() . self::relative_client_helper_path()."prebuilt_forms_ajax.php',
        {form: $('#form-picker').val(),
            website_id: $('#website_id').val(),
            password: $('#password').val()},
        function(data) {
          $('#form-params').hide().html(data).fadeIn();
          Drupal.attachBehaviors();
        }
      );
    } else {
      $('#form-params').hide();
    }
  }
});\n";
  }
  
  /**
   * Generates the parameters form required for configuring a prebuilt form.
   * Fieldsets are given classes which define that they are collapsible and normally initially
   * collapsed, though the css for handling this must be defined elsewhere. For Drupal usage this
   * css is normally handled by default in the template.
   * @param array $options Options array with the following possibilities:<ul>
   * <li><b>form</b>
   * Name of the form file without the .php extension, e.g. mnhnl_dynamic_1.</li>
   * <li><b>currentSettings</b>
   * Associative array of default values to load into the form controls.</li>
   * <li><b>expandFirst</b>
   * Optional. If set to true, then the first fieldset on the form is initially expanded.</li>
   * </ul>
   */
  public static function prebuilt_form_params_form($options) {
    require_once('data_entry_helper.php');
    $formparams = self::get_form_parameters($options['form']);
    $fieldsets = array();
    $r = '';
    foreach ($formparams as $control) {
      $fieldset = isset($control['group']) ? $control['group'] : 'Other IForm Parameters';
      // apply default options to the control
      $ctrlOptions = array_merge(array(
        'sep' => '<br/>',
        'class' => '',
        'blankText'=>'<'.lang::get('please select').'>',
        'extraParams' => array()
      ), $control);
      $type = self::map_type($control);

      // current form settings will overwrite the default
      if (isset($options['currentSettings']) && isset($options['currentSettings'][$control['fieldname']]))
        $ctrlOptions['default'] = $options['currentSettings'][$control['fieldname']];

      $ctrlOptions['extraParams'] = array_merge($ctrlOptions['extraParams'], $options['readAuth']);
      // standardise the control width unless specified already in the control options
      if (strpos($ctrlOptions['class'], 'control-width')==false && $type != 'checkbox')
        $ctrlOptions['class'] .= ' control-width-6';
      if (!isset($fieldsets[$fieldset])) 
        $fieldsets[$fieldset]='';
      $fieldsets[$fieldset] .= data_entry_helper::$type($ctrlOptions);
    }
    $class=(isset($options['expandFirst']) && $options['expandFirst']) ? 'collapsible' : 'collapsible collapsed';
    foreach($fieldsets as $fieldset=>$content) { 
      $r .= "<fieldset class=\"$class\"><legend>$fieldset</legend>\n";
      $r .= $fieldsets[$fieldset];
      $r .= "\n</fieldset>\n";
      // any subsequent fieldset should be collapsed
      if (isset($options['expandFirst']) && $options['expandFirst'])
        $class .= ' collapsed';
    }
    return $r;
  }

  /**
   * Version 0.6 of Indicia converted from using a specific format for defining
   * prebuilt form parameters forms to arrays which map directly onto the options
   * for controls defined in the data entry helper. This makes the forms much more
   * powerful with built in AJAX support etc. However, old forms need to have the
   * control options mapped to the newer option names.
   * @param array $controlList List of controls as defined by the prebuilt form.
   * @return array List of modified controls.
   */
  private static function map_control_options($controlList) {
    $mappings = array(
        'name'=>'fieldname',
        'caption'=>'label',
        'options'=>'lookupValues',
        'description'=>'helpText'
    );
    foreach ($controlList as &$options) {
      foreach ($options as $option => $value) {
        if (isset($mappings[$option])) {
          $options[$mappings[$option]] = $value;
          unset($options[$option]);
        }
      }
      if (!isset($options['required']) || $options['required']===true) {
        $options['class'] .= ' required';
        $options['suffixTemplate'] = 'requiredsuffix';
      }
    }
    return $controlList;
  }
  
  /**
   * Maps control types in simple form definition arrays (e.g. parameter forms for prebuilt forms or reports)
   * to their constituent controls.
   * @param string $type Type name given for the control.
   * @return string Data_entry_helper control name.
   */
  private static function map_type($control) {
    $mapping = array(
        'textfield'=>'text_input', // in case there is any Drupal hangover code
        'string'=>'text_input',
        'int'=>'text_input',
        'smpAttr'=>'text_input',
        'occAttr'=>'text_input',
        'termlist'=>'text_input',
        'boolean'=>'checkbox',
        'list'=>'checkbox_group'
      );
    return array_key_exists($control['type'], $mapping) ? $mapping[$control['type']] : $control['type'];
  }
  
  /** 
   * retrieve the parameters for an iform. this is defined by each iform individually.
   * @param object $node the node that the iform is linked to. 
   * @return array list of parameter definitions.
   */
  public static function get_form_parameters($form) {
    $path = dirname($_SERVER['SCRIPT_FILENAME']) . '/' . self::relative_client_helper_path();
    require_once $path."prebuilt_forms/$form.php";
    // first some parameters that are always required to configure the website
    $params = array(
        array(
          'fieldname'=>'view_access_control',
          'label'=>'View access control',
          'helpText'=>'If ticked, then a Drupal permission is created for this form to allow you to specify which '.
              'roles are able to view the form.',
          'type'=>'checkbox',
          'required'=>false
        )
    );
    // now get the specific parameters from the form
    if (!is_callable(array('iform_'.$form, 'get_parameters'))) 
      throw new Exception('Form does not implement the get_parameters method.');
    $formParams = self::map_control_options(call_user_func(array('iform_'.$form, 'get_parameters')));
    $params = array_merge($params, $formParams);
    // add in a standard parameter for specifying a redirection.
    array_push($params, 
      array(
        'fieldname'=>'redirect_on_success',
        'label'=>'Redirect to page after successful data entry',
        'helpText'=>'The url of the page that will be navigated to after a successful data entry. '.
            'leave blank to just display a success message on the same page so further records can be entered. if the site is internationalised, '.
            'make sure that the page you want to go to has a url specified that is the same for all language versions. also ensure your site uses '.
            'a path prefix for the language negotiation (administer > site configuration > languages > configure). then, specify the url that you attached to the node '.
            'so that the language prefix is not included.',
        'type'=>'text_input',
        'required'=>false
      )
    );
    return $params;
  }

}
 
 ?>