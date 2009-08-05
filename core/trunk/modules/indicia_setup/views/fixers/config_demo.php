<?php
if ($error!=null) {
  echo html::page_error('Demo pages configuration problem', $error);
}
?>
<p> The following options configure the demonstration pages provided with this Warehouse installation.</p>
<form class="cmxform widelabels" name="setup" action="config_demo_save" method="post">
<fieldset>
  <legend><?php echo Kohana::lang('setup.demo_configuration'); ?></legend>
  <ol>
    <li class="ui-widget-content ui-corner-all" style="margin: 1em;">
      <p>If you have installed GeoServer to provide spatial access to the data in this Indicia Warehouse, enter the
      URL to the GeoServer instance here, for example http://www.mysite.com:8080/geoserver/.</p>
      <label for="geoserver_url">GeoServer URL:</label>
      <input name="geoserver_url" type="text"/>
    </li>
    <li class="ui-widget-content ui-corner-all" style="margin: 1em;">
      <p>The GeoPlanet API key allows Indicia to lookup place names entered during data entry. You can obtain a key by
      following the Application ID link from <a href="http://developer.yahoo.com/geo/geoplanet/">Yahoo! GeoPlanet</a>.</p>
      <label for="geoplanet_api_key">GeoPlanet API Key:</label>
      <input name="geoplanet_api_key" type="text"/>
    </li>
    <li class="ui-widget-content ui-corner-all" style="margin: 1em;">
      <p>The Google Search API key allows Indicia to resolve postcodes to places on a map. You can
      <a href="http://code.google.com/apis/ajaxsearch/signup.html">sign up for an AJAX Search API Key</a>.</p>
      <label for="google_search_api_key">Google Search API Key:</label>
      <input name="google_search_api_key" type="text"/>
    </li>
    <li class="ui-widget-content ui-corner-all" style="margin: 1em;">
      <p>The Google Maps API key allows Indicia to use Google Map data in pages that are accessible to the public.
      You can <a href="http://code.google.com/apis/maps/signup.html">sign Up for the Google Maps API</a>.</p>
      <label for="google_api_key">Google API Key:</label>
      <input name="google_api_key" type="text"/>
    </li>
    <li class="ui-widget-content ui-corner-all" style="margin: 1em;">
      <p>The MultiMap Open API key allows Indicia to use MultiMap Map data in pages that are accessible to the public.
      You can <a href="https://www.multimap.com/my/register/?openapi_create=1">sign Up for the MultiMap Open API</a>.</p>
      <label for="multimap_api_key">MultiMap API Key:</label>
      <input name="multimap_api_key" type="text"/>
    </li>
  </ol>
</fieldset>

<input type="submit" role="button" value="<?php echo html::specialchars(Kohana::lang('setup.submit')); ?>"
    class="narrow" />

</form>