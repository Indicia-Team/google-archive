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
 * @package	Services
 * @subpackage Data
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 * @link 	http://code.google.com/p/indicia/
 */

/**
* Class to provide access to reports generated by the Indicia core.
*
* A report will have a number of parameters that need to be completed by the requester. Because
* this interface is designed to be used by both the core module and the site module, we cannot
* directly request this information. As such, we do the following:
* <ol>
* <li> Grab the report and parse it for parameters. </li>
* <li> Cache the report (if it didn't exist on the server already) and assign it a unique id,
* which we store temporarily in the Kohana cache. </li>
* <li> Send a response back to the requester, inviting them to fill in the parameters. This
* reponse will include the id generated in step 3. </li>
* <li> The requester sends back the requested parameters, which are checked against the cache to
* ensure they're all there. If not, repeat these steps. </li>
* <li> The core retrieves the report from cache, merges the parameters in and executes the query
* against the core database. Results are formatted and returned to the requester. </li>
* </ol>
*
* We should also allow submission of parameters with the report, or a combination of this and
* requesting them as we go.
*
* We use XML reports roughly in keeping with the standard defined in Recorder (though with limited
* complexity compared to recorder's options). However, this module is written to easily allow
* reports written in other formats, and in keeping with the rest of the project we use JSON as our
* principal language for network communication - e.g. for parameter requests, delivery, and other
* messages.
*/

class Report_Controller extends Data_Service_Base_Controller {

  private $reportEngine;
  
  private function setup() {
    $this->authenticate('read');
    $websites = $this->website_id ? array($this->website_id) : null;
    $this->reportEngine = new ReportEngine($websites, $this->user_id);
  }

  /**
  * Access the report - probably we will use routing to direct /report directly to /report/access
  * We can specify a request in a number of ways:
  * <ul>
  * <li> Predefined report on the core module. </li>
  * <li> Predefined report elsewhere (URI given). </li>
  * <li> Report passed with the query. </li>
  * </ul>
  * We also need to perform authentication at a read level for the data we're trying to access
  * (this might be fun, given the low level that the reports run at).
  *
  */
  public function requestReport()
  { 
    try {
      $this->setup();
      $this->entity = 'record';
      $this->handle_request();
      $mode = $this->get_output_mode();
      switch($mode) {
        case 'json' :
      	case 'csv' :
        case 'tsv' :
        case 'xml' :
        case 'gpx' :
        case 'kml' :
          $extension=$mode;
          break;
        default : $extension='txt';
      }
      if (array_key_exists('filename', $_REQUEST))
        $downloadfilename = $_REQUEST['filename'];
      else
        $downloadfilename='download';
      header('Content-Disposition: attachment; filename="'.$downloadfilename.'.'.$extension.'"');
      if ($mode=='csv') {
        // prepend a byte order marker, so Excel recognises the CSV file as UTF8
        if (!empty($this->response))
          echo chr(hexdec('EF')) . chr(hexdec('BB')) . chr(hexdec('BF'));
      }
      $this->send_response();
    }
    catch (Exception $e) {
      $this->handle_error($e);
    }
  }

  /**
   * Method called via report services to return a JSON encoded nested array of the available reporting directory structure.
   */
  public function report_list() {
    try {
      $this->setup();
      echo json_encode($this->reportEngine->report_list());
    }
    catch (Exception $e) {
      $this->handle_error($e);
    }
  }

  /**
   * Actually perform the task of reading the records. Called by the base class handle_read
   * method when it is ready to receive the data. As well as the returned records array, sets
   * $this->view_columns to the list of columns.
   *
   * @return array Array of records.
   */
  protected function read_data() {
    $src = $_REQUEST['reportSource'];
    $rep = $_REQUEST['report'];
    $params = json_decode($this->input->post('params', '{}'), true);
    // NB that for JSON requests (eg from datagrids) the parameters do not get posted, but appear in the url.
    if(empty($params)){
      // no params posted so look on URL
      $params = $this->getRawGET();
    }
    $data=$this->reportEngine->requestReport($rep, $src, 'xml', $params);
    if (isset($data['content']['columns'])) {
      $this->view_columns = $data['content']['columns'];
      unset($data['content']['columns']);
    }
    return $data['content'];
  }
  
  /**
   * Report parameters can contain spaces in the names, for example smpattr:CMS User ID=3, which means filter on the attribute
   * called CMS User ID for value 3. Unfortunately PHP mangles incoming $_GET key names, replacing spaces and dots with underscores. So
   * rather than use $_GET we have to try the raw input from the $_SERVER variable.
   * @return array Assoc array matching $_GET without the name mangling.
   */
  private function getRawGET() {
    $vars = array();
    if (!empty($_SERVER['QUERY_STRING'])) {
      $pairs = explode('&', $_SERVER['QUERY_STRING']);
      foreach ($pairs as $pair) {
        if (!empty($pair)) {
          // limit explode to 2 in case there is an = in the value itself
          $nv = explode("=", $pair, 2);
          $name = urldecode($nv[0]);
          $value = urldecode($nv[1]);
          $vars[$name] = $value;
        }
      }
    }
    return $vars;
}

  /**
   * When a report was requested, but the report needed parameter inputs which were requested,
   * this action allows the caller to restart the report having obtained the parameters.
   *
   * @param int $cacheid Id of the report, returned by the original call to requestReport.
   */
  public function resumeReport($cacheid = null)
  {
    try {
      $this->setup();
      // Check we have both a uid and a set of parameters given
      $uid = $cacheid ? $cacheid : $this->input->post('uid', null);
      $params = json_decode($this->input->post('params', '{}'), true);

      return $this->formatJSON($this->reportEngine->resumeReport($uid, $params));
    }
    catch (Exception $e) {
      $this->handle_error($e);
    }
  }

  private function formatJSON($stuff)
  {
    // Set the correct MIME type
    header("Content-Type: application/json");
    echo json_encode($stuff);
  }
  
  protected function record_count() {
    return $this->reportEngine->record_count();
  }
}