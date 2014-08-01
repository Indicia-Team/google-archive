<?php

/**
 * @file
 * Default theme implementation to display the basic html structure of a single
 * Drupal page.
 *
 * Variables:
 * - $css: An array of CSS files for the current page.
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation.
 *   $language->dir contains the language direction. It will either be 'ltr' or 'rtl'.
 * - $rdf_namespaces: All the RDF namespace prefixes used in the HTML document.
 * - $grddl_profile: A GRDDL profile allowing agents to extract the RDF data.
 * - $head_title: A modified version of the page title, for use in the TITLE
 *   tag.
 * - $head_title_array: (array) An associative array containing the string parts
 *   that were used to generate the $head_title variable, already prepared to be
 *   output as TITLE tag. The key/value pairs may contain one or more of the
 *   following, depending on conditions:
 *   - title: The title of the current page, if any.
 *   - name: The name of the site.
 *   - slogan: The slogan of the site, if any, and if there is no title.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $page_top: Initial markup from any modules that have altered the
 *   page. This variable should always be output first, before all other dynamic
 *   content.
 * - $page: The rendered page content.
 * - $page_bottom: Final closing markup from any modules that have altered the
 *   page. This variable should always be output last, after all other dynamic
 *   content.
 * - $classes String of classes that can be used to style contextually through
 *   CSS.
 *
 * @see template_preprocess()
 * @see template_preprocess_html()
 * @see template_process()
 */
?>
<!DOCTYPE html>
<html> 
	<head>
	  <meta name="mobile-web-app-capable" content="yes">
	  <meta name="apple-mobile-web-app-capable" content="yes">
	  
	  <!-- ICONS -->
	  <link rel="shortcut icon" sizes="196x196" href="">
	  <link rel="shortcut icon" sizes="196x196" href="">
	  <link href="" sizes="57x57" rel="apple-touch-icon">
	  <link href="" sizes="72x72" rel="apple-touch-icon">
	  <link href="" sizes="114x114" rel="apple-touch-icon">
	  <link href="" sizes="144x144" rel="apple-touch-icon">

	  <!-- CSS -->
	  <?php print $styles; ?>
	  
	  <!-- JavaScript -->
	  <?php print $scripts; ?>
	  <?php 
		// Get settings to pass to JavaScript
		$start_path = base_path() . mobile_jquery_theme_get_setting('app_home');
		// Get path to JavaScript
		$theme_path = base_path() . $GLOBALS['theme_path']; 
		?>
		
	  <!-- APP libs-->
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/app.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/helper.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/appcache_back_button_fix.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/form.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/io.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/storage.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/geoloc.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/navigation.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/image.js"></script>
	  
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/vector3d.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/geo.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/latlon-ellipsoid.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/osgridref.js"></script>
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/klass.min.js"></script> 
	  <link href="/drupal/sites/all/libraries/photoswipe-1.0.11/photoswipe.css" type="text/css" rel="stylesheet" />	  
	  <script type="text/javascript" src="/drupal/sites/all/modules/iform/media/js/mobile/libs/code.photoswipe.jquery-3.0.4.min.js"></script>  
	  
	  <!-- APP specific scripts -->

	 </head>
	 

	<body class="<?php print $classes; ?>" <?php print $attributes; ?>>
	  <?php print $page_top; ?>
	  <?php print $page; ?>
	  <?php print $page_bottom; ?>
	</body>
</html>
