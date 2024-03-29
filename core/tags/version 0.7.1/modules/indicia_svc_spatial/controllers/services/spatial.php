<?php
class Spatial_Controller extends Service_Base_Controller {

  /**
   * Handle a service request to convert a spatial reference into WKT representing the reference
   * using the internal SRID (normally spherical mercator since it is compatible with Google Maps).
   * The response is in JSON. Provide a callback in the GET request to use JSONP.
   */
  public function sref_to_wkt()
  {
    try
    {
      $r = json_encode(array('wkt'=>spatial_ref::sref_to_internal_wkt($_GET['sref'], $_GET['system'])));
      // enable a JSONP request
      if (array_key_exists('callback', $_GET)){
        $r = $_GET['callback']."(".$r.")";
      }
      echo $r;
    }
    catch (Exception $e)
    {
      $this->handle_error($e);
    }
  }

  /**
   * Handle a service request to convert a WKT representing the reference
   * using the internal SRID (normally spherical mercator since it is compatible with Google Maps)
   * into a spatial reference. Returns the sref, plus a new WKT representing the returned
   * sref. Note that if you pass in a point and convert it to a grid square, then the returned
   * wkt will reflect the grid square not the point. GET parameters allowed are wkt, system, precision
   * and callback (for JSONP).
   */
  public function wkt_to_sref()
  {
    try
    {
      if (array_key_exists('precision',$_GET))
        $precision = $_GET['precision'];
      else
        $precision = null;
      if (array_key_exists('metresAccuracy',$_GET))
        $metresAccuracy = $_GET['metresAccuracy'];
      else
        $metresAccuracy = null;
      if (array_key_exists('output',$_GET))
        $output = $_GET['output'];
      else
        $output = null;
      $sref = spatial_ref::internal_wkt_to_sref($_GET['wkt'], $_GET['system'], $precision, $output, $metresAccuracy);
      // Note we also need to return the wkt of the actual sref, which may be a square now.
      $wkt = spatial_ref::sref_to_internal_wkt($sref, $_GET['system']);
      $r = json_encode(array('sref'=>$sref,'wkt'=>$wkt));
      // enable a JSONP request
      if (array_key_exists('callback', $_GET)){
        $r = $_GET['callback']."(".$r.")";
      }
      echo $r;
    }
    catch (Exception $e)
    {
      $this->handle_error($e);
    }
  }

  /**
   * Allow a service request to triangulate between 2 systems. GET parameters are:
   * 	from_sref
   * 	from_system
   * 	to_system
   *  to_precision (optional)
   */
  public function convert_sref()
  {
    try
    {
      $wkt = spatial_ref::sref_to_internal_wkt($_GET['from_sref'], $_GET['from_system']);
      if (array_key_exists('precision',$_GET))
        $precision = $_GET['precision'];
      else
        $precision = null;
      if (array_key_exists('metresAccuracy',$_GET))
        $metresAccuracy = $_GET['metresAccuracy'];
      else
        $metresAccuracy = null;
      echo spatial_ref::internal_wkt_to_sref($wkt, $_GET['to_system'], $precision, null, $metresAccuracy);
    }
    catch (Exception $e)
    {
      $this->handle_error($e);
    }
  }

  public function buffer()
  {
    if (array_key_exists('wkt', $_GET) && array_key_exists('buffer', $_GET)) {
      if ($_GET['buffer']==0)
        // no need to buffer if width set to zero
        echo $_GET['wkt'];
      else {
        $db = new Database;
        $wkt = $_GET['wkt'];
        $buffer = $_GET['buffer'];
        kohana::log('debug', "SELECT st_astext(st_buffer(st_geomfromtext('$wkt'),$buffer)) AS wkt;");
        $result = $db->query("SELECT st_astext(st_buffer(st_geomfromtext('$wkt'),$buffer)) AS wkt;")->current();
        echo $result->wkt;
      }
    } else {
     echo 'No wkt or buffer to process';
    }
    
  }


}
?>
