<?php
/**
* INDICIA
* @link http://code.google.com/p/indicia/
* @package Indicia
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
  const rowsPerUpdate = 10;

  public function __construct()
  {
    $this->localReportDir = Kohana::config('indicia.localReportDir');
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
        $this->reportReader = new XMLReportReader($this->report);
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
    // ensure that only those expected params are passed through to the report.
    foreach($this->providedParams as $key => $value){
      if(!isset($this->expectedParams[$key])){
        unset($this->providedParams[$key]);
      }
    }
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

  public function listLocalReports($detail = ReportReader::REPORT_DESCRIPTION_DEFAULT)
  {
    if (!is_int((int)$detail) || $detail < 0 || $detail > 3)
    {
      Kohana::log('error', "Invalid reporting level : $detail.");
      $detail = 2;
    }
    Kohana::log('debug', "Listing reports at level $detail.");
    if ($detail == 0)
    {
      Kohana::log('debug', "Listing local reports in report directory ".$this->localReportDir.".");
      $reportList = Array();
      // All we do here is return the list of titles - don't bother interrogating the reports
      $dh = opendir($this->localReportDir);
      while ($file = readdir($dh))  {
        if ($file != '..' && $file != '.' && is_file($this->localReportDir.'/'.$file))
        {
          $reportList[] = array('name' => $file);
        }
      }
    }
    else
    {
      Kohana::log('debug', "Listing local reports in report directory ".$this->localReportDir.".");

      $reportList = Array();
      $handle = opendir($this->localReportDir);
      while ($file = readdir($handle))
      {
        $a = explode('.', $file);
        $ext = $a[count($a) - 1];
        switch ($ext)
        {
          case 'xml':
            Kohana::log('debug', "Invoking XMLReportReader to handle $file.");
            $this->fetchLocalReport($file);
            $this->reportReader = new XMLReportReader($this->report);
            break;
          default:
            continue 2;
        }
        $reportList[] = $this->reportReader->describeReport($detail);
      }
    }

    return array('reportList' => $reportList);

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
      $this->executeQuery();
      $data = $this->response->result_array(FALSE);
      $columns = $this->reportReader->getColumns();
      $this->post_process($data, $columns);
      return array(
        'columns'=>$columns,
        'data'=>$data
      );
    }
  }

  /**
   * Takes the data and columns lists, and carries out any post query processing.
   * This includes vague date processing, and any other defined by the
   * report reader.
   */
  private function post_process(&$data, &$columns) {
  	$this->merge_attribute_data($data, $columns, $this->providedParams);

  	$vagueDateProcessing = $this->getVagueDateProcessing();
  	$downloadProcessing = $this->getDownloadDetails();
  	if($vagueDateProcessing) {
	  	$this->add_vague_dates($data, $columns);
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
  private function add_vague_dates(&$data, &$columns) {
    $col_sets=array();
    $cols = array_keys($columns);
    // First find the additional plaintext columns we need to add
    for ($i=0; $i<count($cols); $i++) {
      if (substr(($cols[$i]), -10)=='date_start') {
        $prefix=substr($cols[$i], 0, strlen($cols[$i])-10);
        // check that the report includes date_end and type
        if (in_array($prefix."date_end", $cols) && in_array($prefix."date_type", $cols)) {
          array_push($col_sets, $prefix);
          if (!in_array($prefix.'date', $cols)) {
            $columns[$prefix.'date'] = array(
              'display'=>'',
              'class'=>'',
              'style'=>''
            );
          }
          // Hide the internal vague date columns, unless the report explicitly asks for them (in which case
          // autodef will not be true).
          if (!array_key_exists('autodef', $columns[$prefix.'date_start']) ||
              $columns[$prefix.'date_start']['autodef']==true) {
            $columns[$prefix.'date_start']['visible']='false';
          }
          if (!array_key_exists('autodef', $columns[$prefix.'date_end']) ||
              $columns[$prefix.'date_end']['autodef']==true) {
            $columns[$prefix.'date_end']['visible']='false';
          }
          if (!array_key_exists('autodef', $columns[$prefix.'date_type']) ||
              $columns[$prefix.'date_type']['autodef']==true) {
            $columns[$prefix.'date_type']['visible']='false';
          }
        }
      }
    }

    // Now we have identified the vague date columns to add, create data columns with the processed date
    // strings.
    for ($i=0; $i<count($col_sets); $i++) {
      for ($r=0; $r<count($data); $r++) {
        $row=$data[$r];
        $data[$r][$col_sets[$i].'date'] = vague_date::vague_date_to_string(array(
          $row[$col_sets[$i].'date_start'],
          $row[$col_sets[$i].'date_end'],
          $row[$col_sets[$i].'date_type']
        ));
      }
    }
  }

  public function merge_attribute_data(&$data, &$columns, $providedParams)
  {
  	/* attributes are extra pieces of information associated with data rows. These can have multiple values within each field,
  	 * so do not lend themselves to being fetched by a extended join within the original SQL query.
  	 */
  	/* loop through each table, looking for attribute definitions */
  	$attributeDefns = $this->reportReader->getAttributeDefns();
  	/* Attribute definitions included the query to run, and the field names to compare between each data array for matching */
    $db = new Database('report');
    $vagueDateProcessing = $this->getVagueDateProcessing();
    foreach($attributeDefns as $attributeDefn){
  	  	$subquery = $attributeDefn->query;
  	  	foreach ($providedParams as $name => $value)
    	{
    		$subquery = preg_replace("/#$name#/", $value, $subquery);
	    }
        $response = $db->query($subquery);
        $attrData = $response->result_array(FALSE);
        $newColumns = array();
        // initially create new columns in the the data set for each distinct attribute, and initialise them to blank.
        // This makes some assumptions about the way the attribute data is stored within the DB tables.
  	  	foreach ($attrData as $row){
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
		              	$columns[$newColName."_date_start"] = array('display'=>$row[$attributeDefn->caption]." Start", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
		              	$columns[$newColName."_date_end"] = array('display'=>$row[$attributeDefn->caption]." End", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
		              	$columns[$newColName."_date_type"] = array('display'=>$row[$attributeDefn->caption]." Type", 'class'=>'', 'style'=>'', 'autodef' => ($vagueDateProcessing && $attributeDefn->hideVagueDateFields == 'true'));
		              	if($vagueDateProcessing){  // if vague date processing enable for the report, add the extra column.
		              		$columns[$newColName."_date"] = array('display'=>$row[$attributeDefn->caption]." Date", 'class'=>'', 'style'=>'');
		              	}
			        	for ($r=0; $r<count($data); $r++) {
				        	$data[$r][$newColName.'_date_start'] = '';
				        	$data[$r][$newColName.'_date_end'] = '';
			        		$data[$r][$newColName.'_date_type'] = '';
			        		$data[$r][$newColName.'_date'] = '';
			        	}
		              	break;
              		case 'L':
              		  	// Lookup
              		  	$termResponse = $db->query("select t.id, t.term from terms t, termlists_terms tt where tt.termlist_id =".$row["termlist_id"]." and tt.term_id = t.id and t.deleted=FALSE and tt.deleted = FALSE ORDER by t.id;");
        				$newColumns[$row[$attributeDefn->id]]['lookup'] = $termResponse->result_array(FALSE);
               		  	// allow follow through so Lookup follows normal format of a singular field.
		            default:
		              	$columns[$newColName] = array('display'=>$row[$attributeDefn->caption], 'class'=>'', 'style'=>'');
			        	for ($r=0; $r<count($data); $r++) {
			        		$data[$r][$newColName] = $multiValue ? array() : '';
			        	}
		              	break;
  	  			}
   	  		}
  	  	}
  	  	// Build an index of the attribute data: nb that the attribute data has been sorted in main_id order.
  	  	// We need the index of first record for each main_id value (there may be many)
      	$index = array();
      	for ($r=0; $r<count($attrData); $r++) {
      		if(!isset($index[$attrData[$r][$attributeDefn->main_id]])){
      			$index[$attrData[$r][$attributeDefn->main_id]] = $r;
      		}
      	}
  	  	for ($r=0; $r<count($data); $r++) {
  	  		if(!isset($index[$data[$r][$attributeDefn->parentKey]])){
  	  			continue;
  	  		}
  	  		$rowIndex = $index[$data[$r][$attributeDefn->parentKey]];
  	  		while($rowIndex < count($attrData) && $attrData[$rowIndex][$attributeDefn->main_id] == $data[$r][$attributeDefn->parentKey]){
  	  			$column = $newColumns[$attrData[$rowIndex][$attributeDefn->id]]['column'];
   	  			switch ($attrData[$rowIndex]["data_type"]) {
   	  				case 'L':
   	  					$value = $attrData[$rowIndex]['int_value']; // default value is int value
   	  					foreach($newColumns[$attrData[$rowIndex][$attributeDefn->id]]['lookup'] as $lookup){
   	  						if($value == $lookup["id"]){
   	  							$value = $lookup['term'];
   	  							break;
   	  						}
   	  					}
   	  					$this->mergeColumnData($data[$r][$column], $value);
   	  					break;
   	  				case 'I':
   	  					$this->mergeColumnData($data[$r][$column], $attrData[$rowIndex]['int_value']);
		              	break;
   	  				case 'B':
   	  					$this->mergeColumnData($data[$r][$column], $attrData[$rowIndex]['int_value'] ? 'Yes' : 'No');
		              	break;
		            case 'F':
		              	$this->mergeColumnData($data[$r][$column], $attrData[$rowIndex]['float_value']);
				        break;
		            case 'T':
		              	$this->mergeColumnData($data[$r][$column], $attrData[$rowIndex]['text_value']);
				        break;
		            case 'D':
		            case 'V': // assume no multi values: would be far too complex to deal with...
		              	$data[$r][$column."_date_start"] = $attrData[$rowIndex]['date_start_value'];
		              	$data[$r][$column."_date_end"] = $attrData[$rowIndex]['date_end_value'];
		              	$data[$r][$column."_date_type"] = $attrData[$rowIndex]['date_type_value'];
				        break;
  	  			}
  	  			$rowIndex++;
  	  		}
      	}
  	  	for ($r=0; $r<count($data); $r++) {
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
    if (is_dir($this->localReportDir) ||
      is_file($this->localReportDir.'/'.$request))
      {
        $this->report = $this->localReportDir.'/'.$request;
        Kohana::log('debug', "Setting local report ".$this->report.".");
      }
      else
      {
        Kohana::log('error', "Unable to find report $request in ".$this->localReportDir.".");
        // Throw an error - something has gone wrong
        // TODO
      }
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
      {
  $this->report = $uploadDir.$fname;
      }
      else
      {
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
    // Replace each parameter in place
    $paramDefs = $this->reportReader->getParams();
    foreach ($this->providedParams as $name => $value)
    {
      if ($value==='')
        // empty integer params should be handled as 0 (null would be ideal, but we can't test for it in the same fashion as a number).
        $query = preg_replace("/#$name#/", $paramDefs[$name]['emptyvalue'], $query);
      else
        $query = preg_replace("/#$name#/", $value, $query);
    }
    // allow the URL to provide a sort order override
    if (isset($this->orderby))
      $order_by = $this->orderby . (isset($this->sortdir) ? ' '.$this->sortdir : '');
    else
      $order_by=$this->reportReader->getOrderClause();
    if ($order_by) {
      // Order by will either be appended to the end of the query, or inserted at a #order_by# marker.
      $count=0;
      $query = preg_replace("/#order_by#/",  "ORDER BY $order_by", $query, -1, $count);
      if ($count==0) {
        $query .= " ORDER BY $order_by";
      }
    }
    if ($this->limit)
      $query .= ' LIMIT '.$this->limit;
    if ($this->offset)
      $query .= ' OFFSET '.$this->offset;
    $this->query = $query;
  }

  private function executeQuery()
  {
    $db = new Database('report');
    Kohana::log('debug', "Running report query : ".$this->query);
    $this->response = $db->query($this->query);
  }

}