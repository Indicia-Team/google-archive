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

class Report_Controller extends Service_Base_Controller {
  
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
  private $suppress;
  
  public function __construct($suppress = false)
  {
    $this->localReportDir = Kohana::config('indicia.localReportDir');
    $this->suppress= $suppress;
    parent::__construct();
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
  public function requestReport($report = null, $reportSource = null, $reportFormat = null,  $params = null)
  {
    
    $rep = $report ? $report : $this->input->post('report', null);
    $src = $reportSource ? $reportSource : $this->input->post('reportSource', null);
    $this->reportFormat = $reportFormat ? $reportFormat : $this->input->post('reportFormat', null);
    $this->providedParams = $params ? $params : json_decode($this->input->post('params', '{}'), true);
    
    Kohana::log('info', "Received request for report: $rep, source: $src");
    
    if ($rep == null || $src == null)
    {
      return $this->formatJSON(array
      (
      'error' => 'Either report or report source is null',
      'report' => $rep,
      'source' => $src
      ));
    }
    
    switch ($src)
    {
      case 'local':
	$this->fetchLocalReport($rep);
	break;
      case 'remote':
	$this->fetchRemoteReport($rep);
	break;
      case 'provided':
	$this->fetchProvidedReport($rep);
	break;
      default:
	// ERROR
	return $this->formatJSON(array('error' => 'Invalid report source specified'));
    }
    
    // Now we switch based on the report format.
    switch ($this->reportFormat)
    {
      case 'xml':
	$this->reportReader = new XMLReportReader($this->report);
	break;
      default:
	// No known report specified - return some error
	// TODO
    }
    
    // What parameters do we expect?
    $this->expectedParams = $this->reportReader->getParams();
    
    return $this->compileReport();
  }
  
  public function resumeReport($cacheid = null, $params = null)
  {
    // Check we have both a uid and a set of parameters given
    $uid = $cacheid ? $cacheid : $this->input->post('uid', null);
    $params = $params ? $params : json_decode($this->input->post('params', '{}'), true);
    
    if ($uid == null || $params == null)
    {
      $err = array
      (
      'error' => 'Trying to resume a report but one or more of params or uid is null',
      'uid' => $uid,
      'params' => $params
      );
      return $this->formatJSON($err);
      
    }
    
    // Retrieve the report from cache
    if (!$this->retrieveCachedReport($uid))
    {
      // Error here - the cached report has expired
      // TODO
    }
    
    // Merge the new parameters in
    $this->providedParams = array_merge($this->providedParams, $params);
    
    return $this->compileReport();
  }
  
  public function listLocalReports($detail = ReportReader::REPORT_DESCRIPTION_DEFAULT)
  {
    if (!is_int((int)$detail) || $detail < 0 || $detail > 3)
    {
      Kohana::log('info', "Invalid reporting level : $detail.");
      $detail = 2;
    }
    Kohana::log('info', "Listing reports at level $detail.");
    if ($detail == 0)
    {
      Kohana::log('info', "Listing local reports in report directory ".$this->localReportDir.".");
      $reportList = Array();
      // All we do here is return the list of tiles - don't bother interrogating the reports
      $dh = opendir($this->localReportDir);
      while ($file = readdir($dh))
      {
	if ($file != '..' && $file != '.' && is_file($this->localReportDir.'/'.$file))
	{
	  $reportList[] = array('name' => $file);
	}
      }
    }
    else
    {
      Kohana::log('info', "Listing local reports in report directory ".$this->localReportDir.".");
      
      $reportList = Array();
      $handle = opendir($this->localReportDir);
      while ($file = readdir($handle))
      {
	$a = explode('.', $file);
	$ext = $a[count($a) - 1];
	switch ($ext)
	{
	  case 'xml':
	    Kohana::log('info', "Invoking XMLReportReader to handle $file.");
	    $this->fetchLocalReport($file);
	    $this->reportReader = new XMLReportReader($this->report);
	    break;
	  default:
	    continue 2;
	}
	
	$reportList[] = $this->reportReader->describeReport($detail);
	
      }
    }
    
    return $this->formatJSON(array('reportList' => $reportList));
    
  }
  
  /**
  * Checks parameters and returns request if they're not all there, else compiles the report.
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
      return $this->formatJSON($res);
      
      
    }
    else
    {
      // Okay, all the parameters have been provided.
      $this->mergeQuery();
      $this->executeQuery();
      return $this->formatResponse();
    }
  }
  
  private function formatJSON($stuff)
  {
    if (!$this->suppress)
    {
      // Set the correct MIME type
      header("Content-Type: application/json");
      echo json_encode($stuff);
    }
    return $stuff;
  }
  
  private function fetchLocalReport($request)
  {
    if (is_dir($this->localReportDir) ||
      is_file($this->localReportDir.'/'.$request))
      {
	$this->report = $this->localReportDir.'/'.$request;
	Kohana::log('info', "Setting local report ".$this->report.".");
      }
      else
      {
	Kohana::log('info', "Unable to find report $request in ".$this->localReportDir.".");
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
    'expectedParams' => $this->expectedParams
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
    foreach ($this->providedParams as $name => $value)
    {
      $query = preg_replace("/#$name#%/", $value, $query);
    }
    
    $query .= ' ORDER BY '.$this->reportReader->getOrderClause();
    
    $this->query = $query;
  }
  
  private function executeQuery()
  {
    $db = new Database('report');
    $this->reponse = $db->query($this->query);
  }
  
  private function formatResponse()
  {
    if (!$this->suppress)
    {
      print_r($this->reponse->result_array());
    }
    return $this->reponse->result_array();
  }
}