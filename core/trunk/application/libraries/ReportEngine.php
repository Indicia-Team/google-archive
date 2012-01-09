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
 * @package Indicia
 * @subpackage Libraries
 * @author  Indicia Team
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @link    http://code.google.com/p/indicia/
 */

/**
* <h1>Report provider</h1>
* <p>The report provider allows for accession of predefined or provided reports.</p>
*
* @package Indicia
* @subpackage Controller
* @license http://www.gnu.org/licenses/gpl.html GPL
* @author Nicholas Clarke <xxx@xxx.net> / $Author$
* @copyright xxxx
* @version $Rev$ / $LastChangedDate$
*/

/**
* Class to control accession to reports generated by the Indicia core.
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

class ReportEngine {

  private $report;
  private $reportFormat;
  private $response;
  private $query;
  private $reportReader;
  // Of the form name => array('type' => type, 'display' => display, 'description' => desc)
  private $expectedParams;
  private $providedParams;
  private $localReportDir;
  private $cache;
  const rowsPerUpdate = 50;
  private $websiteIds = null;

  /**
   * @var array A list of additional columns identified from custom attribute parameters.
   */
  private $attrColumns = array();
  
  /**
   * @var array A list of the actual custom attributes, along with a link to the cols they include.
   */
  private $customAttributes = array();
  
  /**
   * @var array A list mappings from known custom attribute captions to the IDs.
   */
  private $customAttributeCaptions = array();

  public function __construct($websiteIds = null)
  {
    $this->websiteIds = $websiteIds;
    $this->localReportDir = Kohana::config('indicia.localReportDir');
    $this->reportDb = new Database('report');
  }
  
  /** 
   * Retrieve all available reports, as a nested associative array.
   */
  public function report_list() {
    $reports = $this->internal_report_list(Kohana::config('indicia.localReportDir'), '/');
    foreach (Kohana::config('config.modules') as $path) {
      if (is_dir("$path/reports")) 
        $reports = array_merge_recursive($reports, $this->internal_report_list("$path/reports", '/'));
    }
    return $reports;
  }

  private function internal_report_list($root, $path) {
    $files = array();
    $fullPath = "$root$path";
    if (!is_dir($fullPath))
      throw new Exception("Failed to open reports folder ".$fullPath);
    $dir = opendir($fullPath);
    
    while (false !== ($file = readdir($dir))) {
      if ($file != '.' && $file != '..' && $file != '.svn' && is_dir("$fullPath$file"))
        $files[$file] = array('type'=>'folder','content'=>$this->internal_report_list($root, "$path$file/"));
      elseif (substr($file, -4)=='.xml') {
        $metadata = XMLReportReader::loadMetadata("$fullPath$file");
        $file = basename($file, '.xml');
        $reportPath = ltrim("$path$file", '/');
        $files[$file] = array('type'=>'report','title'=>$metadata['title'],'description'=>$metadata['description'], 'path'=>$reportPath);
      }
    }
    closedir($dir);
    return $files;
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
  * @param string $report Name of the report file to run.
  * @param string $reportSource Source of the report, either local or remote.
  * @param string $reportFormat Format of the report file. Currently only xml report file formats are supported.
  * @param array $params Associative array of report parameters.
  */
  public function requestReport($report = null, $reportSource = 'local', $reportFormat = null,  $params = array())
  {
    $this->reportFormat = $reportFormat;
    $this->providedParams = $params;
    Kohana::log('debug', "Received request for report: $report, source: $reportSource");

    if ($reportSource == null) {
      $reportSource='local';
    }
    if ($report == null)
    {
      return array
      (
      'error' => 'Report parameter is null',
      'report' => $report,
      'source' => $reportSource
      );
    }

    switch ($reportSource)
    {
      case 'local':
        $this->fetchLocalReport($report);
        break;
      case 'remote':
        $this->fetchRemoteReport($report);
        break;
      case 'provided':
        $this->fetchProvidedReport($report);
        break;
      default:
        // ERROR
        return array('error' => 'Invalid report source specified');
    }

    // Now we switch based on the report format.
    switch ($this->reportFormat)
    {
      case 'xml':
        $this->reportReader = new XMLReportReader($this->report, $this->websiteIds);
        break;
      default:
        return array('error' => 'Unknown report format specified: '. $this->reportFormat);
        // No known report specified - return some error
        // TODO
    }

    // What parameters do we expect?
    $this->expectedParams = $this->reportReader->getParams();
    // Pull out special case params for limit and offset
    $this->limit = isset($this->providedParams['limit']) ? $this->providedParams['limit'] : null;
    $this->offset = isset($this->providedParams['offset']) ? $this->providedParams['offset'] : null;
    $this->orderby = isset($this->providedParams['orderby']) ? $this->providedParams['orderby'] : null;
    $this->sortdir = isset($this->providedParams['sortdir']) ? $this->providedParams['sortdir'] : null;
    return array(
      'description' => $this->reportReader->describeReport(ReportReader::REPORT_DESCRIPTION_BRIEF),
      'content' => $this->compileReport()
    );
  }

  public function resumeReport($uid = null, $params = array())
  {

    if ($uid == null || $params == null)
    {
      $err = array
      (
      'error' => 'Trying to resume a report but one or more of params or uid is null',
      'uid' => $uid,
      'params' => $params
      );
      return $err;

    }

    // Retrieve the report from cache
    if (!$this->retrieveCachedReport($uid))
    {
      // Error here - the cached report has expired
      // TODO
    }

    // Merge the new parameters in
    $this->providedParams = array_merge($this->providedParams, $params);
    $this->limit = isset($this->providedParams['limit']) ? $this->providedParams['limit'] : $this->limit;
    $this->offset = isset($this->providedParams['offset']) ? $this->providedParams['offset'] : $this->offset;
    $this->orderby = isset($this->providedParams['orderby']) ? $this->providedParams['orderby'] : $this->orderby;
    $this->sortdir = isset($this->providedParams['sortdir']) ? $this->providedParams['sortdir'] : $this->sortdir;

    return array(
      'description' => $this->reportReader->describeReport(ReportReader::REPORT_DESCRIPTION_BRIEF),
      'content' => $this->compileReport()
    );
  }

  /**
  * Checks parameters and returns request if they're not all there, else compiles the report.
  *
  * @return array Array containing columns and data.
  */
  private function compileReport()
  {
    // Do we need any more parameters?
    $remPars = array_diff_key($this->expectedParams, $this->providedParams);
    if (!empty($remPars))
    {
      // We need more parameters, so cache the report (and any existing parameters), get an id for
      // it and send a request for the others back to the requester.
      $uid = $this->cacheReport();

      // Send a request for further parameters back to the client
      $res = array('parameterRequest' => $remPars, 'uid' => $uid);
      return $res;

    }
    else
    {
      // Okay, all the parameters have been provided.
      $this->mergeQuery();
      $this->mergeCountQuery();
      $this->executeQuery();
      $data = $this->response->result_array(FALSE);
      $this->prepareColumns();
      $this->post_process($data);
      $r = array(
        'columns'=>$this->columns,
        'data'=>$data
      );
      return $r;
    }
  }
  
  public function record_count() {
    if ($this->countQuery!==null) {
      $count = $this->reportDb->query($this->countQuery)->result_array(FALSE);
      return $count[0]['count'];
    } else {
      return false;
    }
  }
  
  /**
   * Obtain the set of columns from the report reader on demand, so it is only called once.
   */
  private function prepareColumns() {
    if (!isset($this->columns)) {
      $this->columns = array_merge(
         $this->reportReader->getColumns(),
         $this->attrColumns
      );
    }
  }
  /**
   * Takes the data and columns lists, and carries out any post query processing.
   * This includes vague date processing, and any other defined by the
   * report reader.
   */
  private function post_process(&$data) {
    $this->merge_attribute_data($data, $this->providedParams);

    $vagueDateProcessing = $this->getVagueDateProcessing();
    $downloadProcessing = $this->getDownloadDetails();
    if($vagueDateProcessing) {
      $this->add_vague_dates($data);
    }
    if($downloadProcessing->mode == 'INITIAL' || $downloadProcessing->mode == 'CONFIRM' ||$downloadProcessing->mode == 'FINAL') {
      $this->setDownloaded($data, $downloadProcessing);
    }
  }

  private function getVagueDateProcessing() {
    $vagueDateProcessing = $this->reportReader->getVagueDateProcessing();
     foreach ($this->providedParams as $name => $value)
    {
      $vagueDateProcessing = preg_replace("/#$name#/", $value, $vagueDateProcessing);
    }
    return !($vagueDateProcessing == 'false');
  }

  private function getDownloadDetails() {
    $downloadProcessing = $this->reportReader->getDownloadDetails();
     foreach ($this->providedParams as $name => $value)
    {
        $downloadProcessing->mode = preg_replace("/#$name#/", $value, $downloadProcessing->mode);
    }
    return $downloadProcessing;
  }


  /**
   * Takes the data and columns lists, and looks for a vague date column set.
   * If one is found, inserts a new column for the processed date string.
   */
  private function add_vague_dates(&$data) {
    $col_sets=array();    
    $cols = array_keys($this->columns);
    // First find the additional plaintext columns we need to add
    for ($i=0; $i<count($cols); $i++) {
      if (substr(($cols[$i]), -10)=='date_start') {
        $prefix=substr($cols[$i], 0, strlen($cols[$i])-10);
        // check that the report includes date_end and type
        if (in_array($prefix."date_end", $cols) && in_array($prefix."date_type", $cols)) {
          array_push($col_sets, $prefix);
          if (!in_array($prefix.'date', $cols)) {
            $this->columns[$prefix.'date'] = array(
              'display'=>'',
              'class'=>'',
              'style'=>''
            );
          }
          // Hide the internal vague date columns, unless the report explicitly asks for them (in which case
          // autodef will not be true).
          if (!array_key_exists('autodef', $this->columns[$prefix.'date_start']) ||
              $this->columns[$prefix.'date_start']['autodef']==true) {
            $this->columns[$prefix.'date_start']['visible']='false';
          }
          if (!array_key_exists('autodef', $this->columns[$prefix.'date_end']) ||
              $this->columns[$prefix.'date_end']['autodef']==true) {
            $this->columns[$prefix.'date_end']['visible']='false';
          }
          if (!array_key_exists('autodef', $this->columns[$prefix.'date_type']) ||
              $this->columns[$prefix.'date_type']['autodef']==true) {
            $this->columns[$prefix.'date_type']['visible']='false';
          }
        }
      }
    }

    // Now we have identified the vague date columns to add, create data columns with the processed date
    // strings.
    $dataCount = count($data); // invariant
    for ($i=0; $i<count($col_sets); $i++) {
      for ($r=0; $r<$dataCount; $r++) {
        $row=$data[$r];
        $data[$r][$col_sets[$i].'date'] = vague_date::vague_date_to_string(array(
          $row[$col_sets[$i].'date_start'],
          $row[$col_sets[$i].'date_end'],
          $row[$col_sets[$i].'date_type']
        ));
      }
    }
  }

  public function merge_attribute_data(&$data, $providedParams)
  {
    $dataCount = count($data); // invariant
    /* attributes are extra pieces of information associated with data rows. These can have multiple values within each field,
     * so do not lend themselves to being fetched by a extended join within the original SQL query.
     */
    /* loop through each table, looking for attribute definitions */
    $attributeDefns = $this->reportReader->getAttributeDefns();
    /* Attribute definitions included the query to run, and the field names to compare between each data array for matching */
    $vagueDateProcessing = $this->getVagueDateProcessing();
    foreach($attributeDefns as $attributeDefn){
        // Build an index of the report data indexed on the attribute: nb that the attribute data has been sorted in main_id order.
        $index = array();
        for ($r=0; $r<count($data); $r++) {
          if(!isset($index[$data[$r][$attributeDefn->parentKey]])){
            $index[$data[$r][$attributeDefn->parentKey]] = array($r);
          } else
            $index[$data[$r][$attributeDefn->parentKey]][] = $r;
        }
    	$subquery = $attributeDefn->query;
        foreach ($providedParams as $name => $value)
        {
          $subquery = preg_replace("/#$name#/", $value, $subquery);
        }
        $response = $this->reportDb->query($subquery);
        $attrData = $response->result_array(FALSE);
        $newColumns = array();
        // This makes some assumptions about the way the attribute data is stored within the DB tables.
        // Note that $attributeDefn->id is actually text, which means that the order of data in $row is actually the order in which the 
        // attributes are encountered in the data set.
        // we assume that the attributes are ordered in blocks of each attribute ID, in the order that we wish them to appear in the report.
        foreach ($attrData as $row){
          // If this attribute row has not been encountered so far, make a new column for it, initialise to blank.
          if(!array_key_exists($row[$attributeDefn->id], $newColumns)){  // id is the column holding the attribute id.
            $newColName=$attributeDefn->columnPrefix.$row[$attributeDefn->id];
            $multiValue = ($row['multi_value'] == 't') && ($row["data_type"] != 'D') && ($row["data_type"] != 'V');
            $newColumns[$row[$attributeDefn->id]] = array(
                  'caption' => $row[$attributeDefn->caption],
                  'column' => $newColName,
                  'multi_value' => $multiValue);
            switch ($row["data_type"]) {
              case 'D':
              case 'V':
                $this->columns[$newColName."_date_start"] = array('display'=>$row[$attributeDefn->caption]." Start", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
                $this->columns[$newColName."_date_end"] = array('display'=>$row[$attributeDefn->caption]." End", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
                $this->columns[$newColName."_date_type"] = array('display'=>$row[$attributeDefn->caption]." Type", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
                if($vagueDateProcessing){  // if vague date processing enable for the report, add the extra column.
                  $this->columns[$newColName."_date"] = array('display'=>$row[$attributeDefn->caption]." Date", 'class'=>'', 'style'=>'');
                }
                for ($r=0; $r<$dataCount; $r++) {
                  $data[$r][$newColName.'_date_start'] = '';
                  $data[$r][$newColName.'_date_end'] = '';
                  $data[$r][$newColName.'_date_type'] = '';
                  $data[$r][$newColName.'_date'] = '';
                }
                break;
              case 'L':
                // Lookup
                if(isset($attributeDefn->meaningIdLanguage))
                  $termResponse = $this->reportDb->query("select tt.meaning_id as id, t.term from terms t, termlists_terms tt, languages l".
                  										 " where tt.termlist_id =".$row["termlist_id"].
                  										 " and tt.term_id = t.id ".
                  										 " and t.language_id = l.id ".
                  										 " and t.deleted=FALSE ".
                  										 " and tt.deleted = FALSE ".
                  										 " and l.deleted=FALSE ".
                  										 ($attributeDefn->meaningIdLanguage== "preferred" ?
                  											" and tt.preferred = true " :
                  											" and l.iso = '".$attributeDefn->meaningIdLanguage."'").
                  										 "ORDER by tt.meaning_id;");
                else
                  $termResponse = $this->reportDb->query("select t.id, t.term from terms t, termlists_terms tt where tt.termlist_id =".$row["termlist_id"]." and tt.term_id = t.id and t.deleted=FALSE and tt.deleted = FALSE ORDER by t.id;");
                $newColumns[$row[$attributeDefn->id]]['lookup'] = $termResponse->result_array(FALSE);
                // allow follow through so Lookup follows normal format of a singular field.
              default:
                $this->columns[$newColName] = array('display'=>$row[$attributeDefn->caption], 'class'=>'', 'style'=>'');
                for ($r=0; $r<$dataCount; $r++) {
                  $data[$r][$newColName] = $multiValue ? array() : '';
                }
                break;
            }
          }
          $column = $newColumns[$row[$attributeDefn->id]]['column'];
          switch ($row["data_type"]) {
            case 'L':
              $value = $row['int_value']; // default value is int value
              foreach($newColumns[$row[$attributeDefn->id]]['lookup'] as $lookup){
                if($value == $lookup["id"]){
                  $value = $lookup['term'];
                  break;
                }
              }
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r)
                  $this->mergeColumnData($data[$r][$column], $value);
              break;
            case 'I':
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r)
                  $this->mergeColumnData($data[$r][$column], $row['int_value']);
              break;
            case 'B':
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r)
                  $this->mergeColumnData($data[$r][$column], $row['int_value'] ? 'Yes' : 'No');
              break;
            case 'F':
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r)
                  $this->mergeColumnData($data[$r][$column], $row['float_value']);
              break;
            case 'T':
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r)
                  $this->mergeColumnData($data[$r][$column], $row['text_value']);
              break;
            case 'D':
            case 'V': // assume no multi values: would be far too complex to deal with...
              if(isset($index[$row[$attributeDefn->main_id]]))
                foreach($index[$row[$attributeDefn->main_id]] as $r){
                  $data[$r][$column."_date_start"] = $row['date_start_value'];
                  $data[$r][$column."_date_end"] = $row['date_end_value'];
                  $data[$r][$column."_date_type"] = $row['date_type_value'];
                }
              break;
          }
        }
        for ($r=0; $r<$dataCount; $r++) {
          foreach($newColumns as $newCol){
            $column = $newCol['column'];
            if($newCol['multi_value'] == true && is_array($data[$r][$column])){
              $data[$r][$column] = implode($attributeDefn->separator, $data[$r][$column]);
            }
          }
        }
    }
  }

  private function mergeColumnData(&$data, $value){
    if(is_array($data)){
      $data[] = $value;
    } else {
      $data = $value;
    }
  }

  /* The following function is the only method by which the reports can update the contents of the database. As a consequence
   * the following restrictions are enforced:
   * 1) the updates are not data driven. The only thing updated is the downloaded column in the occurrences table, and this
   *    is hardcoded.
   *
   */
  private function setDownloaded($data, $downloadDetails)
  {
    if($downloadDetails->mode == 'INITIAL' || $downloadDetails->mode == 'FINAL') {
      $idList = array();
      foreach($data as $row){
        if(isset($row[$downloadDetails->id])){
          $idList[] = $row[$downloadDetails->id];
          if(count($idList) >= self::rowsPerUpdate){
            $this->updateDownloaded($idList, $downloadDetails->mode);
            $idList = array();
          }
        }
      }
      $this->updateDownloaded($idList, $downloadDetails->mode);
    }
  }

  private function updateDownloaded($idList, $mode)
  {
    if(!is_array($idList) || count($idList) == 0)
      return;
    if($mode != 'INITIAL' && $mode != 'FINAL') {
      return;
    }
    $downloaded_on = date("Ymd H:i:s");
    $db = new Database(); // use default access so can update.
    $db->query('START TRANSACTION READ WRITE;');
    $response = $db->in("id", $idList)
        ->update('occurrences',
          array('downloaded_flag' => ($mode == 'FINAL'? 'F' : 'I'),
              'downloaded_on' => $downloaded_on));
    $db->query('COMMIT;');
  }

  private function fetchLocalReport($request)
  {
    $this->report = null;
    if (is_file($this->localReportDir.'/'.$request)) {
      $this->report = $this->localReportDir.'/'.$request;
      Kohana::log('debug', "Setting local report ".$this->report.".");
    } else {
      foreach (Kohana::config('config.modules') as $path) {
        if (is_file("$path/reports/$request")) {
          $this->report = "$path/reports/$request";
          break;
        }
      }
    }
    if ($this->report===null)
      throw new exception("Unable to find report $request.");    
  }

  private function fetchRemoteReport($request)
  {
    $this->report = $request;
  }

  private function fetchProvidedReport($request)
  {
    // $request here stores the report itself - we save it to a temporary place.
    $uploadDir = $this->localReportDir.'/tmp/';
    if (is_dir($uploadDir))
    {
      $fname = time();
      switch ($this->reportFormat)
      {
  case 'xml':
    $fname .= '.xml';
    break;
  default:
    // Bad stuff
    // TODO
      }
      if (file_put_contents($uploadDir.$fname, $request))
        $this->report = $uploadDir.$fname;
      else {
  // Error - unable to write to temp dir.
  // TODO
      }
    }
    else
    {
      // Unable to cache the report - could try other things, but nah.
    }
  }

  private function cacheReport()
  {
    $cachedReport = Array
    (
    'reportReader' => $this->reportReader,
    'providedParams' => $this->providedParams,
    'expectedParams' => $this->expectedParams,
    'specialParams' =>
        Array('limit' => $this->limit,
              'offset' => $this->offset,
              'orderby' => $this->orderby,
              'sortdir' => $this->sortdir)
    );

    // Set the object in the cache
    $uid = md5(time().rand());
    $this->cache = new Cache;
    $this->cache->set($uid, $cachedReport, array('report'), 3600);
    return $uid;
  }

  private function retrieveCachedReport($cacheid)
  {
    $this->cache = new Cache;
    if ($a = $this->cache->get($cacheid))
    {
      $this->reportReader = $a['reportReader'];
      $this->providedParams = $a['providedParams'];
      $this->expectedParams = $a['expectedParams'];
      $this->limit = $a['specialParams']['limit'];
      $this->offset = $a['specialParams']['offset'];
      $this->orderby = $a['specialParams']['orderby'];
      $this->sortdir = $a['specialParams']['sortdir'];
      
      return true;
    }
    else
    {
      // Cache has timed out / bad UID
      return false;
    }

  }

  private function mergeQuery()
  {
    // Grab the query from the report reader
    $query = $this->reportReader->getQuery();
    $this->query = $this->mergeQueryWithParams($query);
  }
  
  private function mergeCountQuery()
  {
    // Grab the query from the report reader
    $query = $this->reportReader->getCountQuery();
    if ($query!==null)
      $this->countQuery = $this->mergeQueryWithParams($query, true);
    else 
      $this->countQuery = null;
  }
  
  private function mergeQueryWithParams($query, $counting=false)
  {
    // Replace each parameter in place
    $paramDefs = $this->reportReader->getParams();
    // Pre-parse joins defined by parameters, so that join SQL also gets other parameter values
    // inserted
    foreach ($this->providedParams as $name => $value)
    {
      if (isset($paramDefs[$name])) {
        if (array_key_exists('joins', $paramDefs[$name]))
          $query = $this->addParamJoins($query, $paramDefs[$name], $value);
      }
    }
    // Now loop through the joins to insert the values into the query
    foreach ($this->providedParams as $name => $value)
    {
      if (isset($paramDefs[$name])) {
        if ($value==='')
          // empty integer params should be handled as 0 (null would be ideal, but we can't test for it in the same fashion as a number).
          $query = preg_replace("/#$name#/", $paramDefs[$name]['emptyvalue'], $query);
        else {
          if ($paramDefs[$name]['datatype']=='idlist')
            // idlist is a special parameter type which creates an IN (...) clause. Lets you optionally provide a list
            // of ids for a report.
            $query = preg_replace("/#$name#/", "AND ".$paramDefs[$name]['fieldname']." IN ($value)", $query);
          elseif ($paramDefs[$name]['datatype']=='smpattrs')
            $query = $this->mergeAttrListParam($query, 'sample', $value);
          elseif ($paramDefs[$name]['datatype']=='occattrs')
            $query = $this->mergeAttrListParam($query, 'occurrence', $value);
          elseif ($paramDefs[$name]['datatype']=='locattrs')
            $query = $this->mergeAttrListParam($query, 'location', $value);
          elseif ($paramDefs[$name]['datatype']=='taxattrs')
            $query = $this->mergeAttrListParam($query, 'taxa_taxon_list', $value);
          else 
            $query = preg_replace("/#$name#/", $value, $query);
        }
      }      
      elseif (isset($this->customAttributes[$name])) {
        // request includes a custom attribute column being used as a filter.
        $field=$this->customAttributes[$name]['field'];
        $query = str_replace('#filters#', "AND $field=$value\n#filters#", $query);
      }
    }
    // remove the marker left in the query to show where to insert joins
    $query = str_replace(array('#joins#','#fields#','#group_bys#','#filters#'), array('','','',''), $query);
    // allow the URL to provide a sort order override
    if (!$counting) {
      if (isset($this->orderby))
        $order_by = $this->orderby . (isset($this->sortdir) ? ' '.$this->sortdir : '');
      else
        $order_by=$this->reportReader->getOrderClause();
      if ($order_by) {
        $order_by = $this->checkOrderByForVagueDate($order_by);
        // Order by will either be appended to the end of the query, or inserted at a #order_by# marker.
        $count=0;
        $query = preg_replace("/#order_by#/",  "ORDER BY $order_by", $query, -1, $count);
        if ($count==0) {
          $query .= " ORDER BY $order_by";
        }
      } else {
        $query = preg_replace("/#order_by#/",  "", $query);
      }
      if ($this->limit)
        $query .= ' LIMIT '.$this->limit;
      if ($this->offset)
        $query .= ' OFFSET '.$this->offset;
    } else {
      $query = preg_replace("/#order_by#/",  "", $query);
    }
    return $query;
  }

  /**
   * When a parameter is found which defines a list of additional custom attributes to add to a report,
   * this method merges the parameter information into the query, adding in joins and fields to return the
   * selected attributes.
   * @param string $query SQL query to process
   * @param string $type Either occurrence, location or sample depending on the type of attributes being loaded.
   * @param string $attrList parameter value, which should be a comma separated list of attribute IDs or attribute names.
   * @return string Processed query.
   */
  private function mergeAttrListParam($query, $type, $attrList) {
    $this->reportDb
        ->select('id, data_type, caption, validation_rules')
        ->from('list_'.$type.'_attributes');
    if ($this->websiteIds)
      $this->reportDb->in('website_id', $this->websiteIds);
    $ids = array();
    $captions = array();
    $attrList = explode(',',$attrList);
    foreach($attrList as $attr) {
      if (is_numeric($attr))
        $ids[] = $attr;
      else
        $captions[] = $attr;
    }
    if (count($ids)>0) {
      $this->reportDb->in('id', $ids);
      if (count($captions)>0) 
        throw new exception('Cannot mix numeric IDs and captions in the list of requested custom attributes');
    } elseif (count($captions)>0) 
      $this->reportDb->in('caption', $captions);
    $usingCaptions=count($captions)>0;
    $attrs = $this->reportDb->get();
    foreach($attrs as $attr) {
      $id = $attr->id;
      // can only use an inner join for definitely required fields. If they are required
      // only at the per-survey level, we must use left joins as the survey could vary per record.
      $join = strpos($attr->validation_rules, 'required')===false ? 'LEFT JOIN' : 'JOIN';
      // find out what alias and field name the query uses for the table & field we need to join to
      // (samples.id, occurrences.id or locations.id).
      $rootIdAttr = inflector::plural($type).'_id_field';
      $rootId = $this->reportReader->$rootIdAttr;
      // construct a join to the attribute values table so we can get the value out.
      $query = str_replace('#joins#', "$join ".$type."_attribute_values $type$id ON $type$id.".$type."_id=$rootId AND $type$id.".$type."_attribute_id=$id AND $type$id.deleted=false\n #joins#", $query);
      // find the query column(s) required for the attribute
      switch($attr->data_type) {
        case 'F' :
          $cols = array('float_value'=>'');
          break;
        case 'T' :
          $cols = array('text_value'=>'');
          break;
        case 'D' :
          $cols = array('date_start_value'=>'');
          break;
        case 'V' :
          $cols = array('date_start_value'=>'_start','date_end_value'=>'_end','date_type_value'=>'_type');
          break;
        case 'L' :          
          $cols= array('int_value'=>'');
          // lookups will have the join inserted later
          break;
        default:
          $cols = array('int_value'=>'');
      }
      // We use the attribute ID or the attribute caption to create the column alias, depending on how it was requested.
      $uniqueId = $usingCaptions ? preg_replace('/\W/', '_', strtolower($attr->caption)) : $id;
      // create the fields required in the SQL. First the attribute ID. 
      $alias = preg_replace('/\_value$/', '', "attr_id_$type"."_$uniqueId");
      $query = str_replace('#fields#', ", $type$id.id as $alias#fields#", $query);
      // this field should also be inserted into any group by part of the query
      $query = str_replace('#group_bys#', ", $type$id.id#group_bys#", $query);
      // hide the ID column
      $this->attrColumns[$alias] = array(
        'visible' => 'false',
      );
      // then the attribute data col(s).
      foreach($cols as $col=>$suffix) {
        $alias = preg_replace('/\_value$/', '', "attr_$type"."_$uniqueId");
        // vague date cols need to distinguish the different column types.
        if ($attr->data_type=='V') 
          $alias += $col;
        // use the #fields# token in the SQL to work out where to put the field, plus #group_bys# for any grouping
        $query = str_replace('#fields#', ", $type$id.$col as $alias#fields#", $query);
        $query = str_replace('#group_bys#', ", $type$id.$col#group_bys#", $query);
        $this->attrColumns[$alias] = array(
          'display' => $attr->caption.$suffix
        );
        // the first column is normally used as the filter.
        $filterCol = $col;
      }
      // add a column to set the caption for vague date processed columns
      if ($attr->data_type=='V') {
        $this->attrColumns["attr_$type"."_$uniqueId".'date'] = array(
          'display' => $attr->caption
        );
      }
      // lookups need special processing for additional joins
      elseif ($attr->data_type=='L') {
        $query = str_replace('#joins#', "$join list_termlists_terms ltt$id ON ltt$id.id=$type$id.int_value\n #joins#", $query);
        $alias = preg_replace('/\_value$/', '', "attr_$type"."_term_$uniqueId");
        $query = str_replace('#fields#', ", ltt$id.term as $alias#fields#", $query);
        $query = str_replace('#group_bys#', ", ltt$id.term#group_bys#", $query);
        $this->attrColumns["attr_$type$id"] = array(
          'display' => $attr->caption
        );
      }
      // keep a list of the custom attribute columns with a link to the fieldname to filter against, if this column
      // gets used in a filter
      $this->customAttributes["attr_$type"."_$uniqueId"] = array(
        'field' => "$type$id.$filterCol"
      );
      // if we know an attribute caption, we want to be able to lookup the ID.
      $this->customAttributeCaptions["$type:".$attr->caption] = $id;
    }
    return $query;
  }
  
  private function addParamJoins($query, $paramDef, $value) {
    foreach($paramDef['joins'] as $joinDef) {
      if (($joinDef['operator']==='equal' && $joinDef['value']===$value) ||
          ($joinDef['operator']==='notequal' && $joinDef['value']!==$value)) {
        // Join SQL can contain the parameter value as well.
        $join = str_replace('', $value, $joinDef['sql']);
        $query = str_replace('#joins#', $join."\n #joins#", $query);
      }
    }
    return $query;
  }
  
  /**
   * If sorting on the date column (the extra column added by vague date processing) then
   * switch the sort order back to use date_start.
   */
  private function checkOrderByForVagueDate($order_by) {
    if ($this->getVagueDateProcessing()) {      
      $tokens = explode(' ', $order_by);
      $this->prepareColumns();
      if (count($tokens)>0) {
        $sortfield = $tokens[0];
        $cols = array_keys($this->columns);
        // First find the additional plaintext columns we need to add
        for ($i=0; $i<count($cols); $i++) {
          if (substr(($cols[$i]), -10)=='date_start') {
            $prefix=substr($cols[$i], 0, strlen($cols[$i])-10);
            if ($sortfield==$prefix.'date') {
              // switch sort to date start
              $tokens[0]=$cols[$i];
              $order_by=implode(' ', $tokens);
              break; // from loop
            }
          }
        }
      }
    }
    return $order_by;
  }

  private function executeQuery()
  {    
    Kohana::log('debug', "Running report query : ".$this->query);
    $this->response = $this->reportDb->query($this->query);
  }

}