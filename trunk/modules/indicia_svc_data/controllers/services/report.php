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
  private $query;
  private $reportReader;
  // Of the form name => array('type' => type, 'display' => display, 'description' => desc)
  private $expectedParams;
  private $providedParams;
  private $localReportDir;
  private $cache;
  
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
  */
  public function requestReport()
  {
    // First we determine how we're fetching the report
    if (($a = $this->input->get('uri', null)) != null)
    {
      $this->fetchRemoteReport($a);
    }
    else if (($a = $this->input->post('localReport', null)) != null)
    {
     $this->fetchLocalReport($a); 
    }
    else if (($a = $this->input->post('remoteReport', null)) != null)
    {
      $this->fetchRemoteReport($a);
    }
    else if (($a = $this->input->post('providedReport', null)) != null)
    {
      $this->report = $a;
    }
    else
    {
      // No report provided - die
      // TODO
    }
    
    $reportFormat = $this->input->get('reportFormat') || $this->input->post('reportFormat');
    // Now we switch based on the report format.
    switch ($reportFormat)
    {
      case 'xml':
	$this->reportReader = new XMLReportReader($this->report);
	break;
      default:
	// No known report specified - return some error
	// TODO
    }
    
    // Have any parameters been provided?
    $this->providedParams = json_decode($this->input->post('params', array()), true);
    // What parameters do we expect?
    $this->expectedParams = $this->reportReader->getParams();
    
    // Do we need any more parameters?
    $remPars = array_diff_key($this->expectedParams, $this->providedParams);
    if (!empty($remPars))
    {
      // We need more parameters, so cache the report (and any existing parameters), get an id for
      // it and send a request for the others back to the requester.
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
      
      // Send a request for further parameters back to the client
      return json_encode(array('parameterRequest' => $remPars));
      
    }
    else
    {
      // Okay, all the parameters have been provided.
      $this->mergeParameters();
    }
    
	
    
    
  }
  
  public function resumeReport($cacheid)
  {}
  
  public function listLocalReports($detail = 1)
  {
    if (typeof($detail) != int || $detail < 0 || $detail > 2)
    {
      $detail = 1;
    }
    
    $reportList = Array();
    $handle = opendir($this->localReportDir);
    while ($file = readdir($handle))
    {
      $a = explode('.', $file);
      $ext = $a[count($a) - 1];
      switch ($ext)
      {
	case 'xml':
	  $this->reportReader = new XMLReportReader($this->fetchLocalReport($file));
	  break;
	default:
	  continue 2;
      }
      
      $reportList[] = $this->reportReader->describeReport($detail);
      
    }
    
    return json_encode(array('reportList' => $reportList));
    
  }
  
  private function fetchLocalReport($request)
  {}
  
  private function fetchRemoteReport($request)
  {}
  
  private function cacheReport()
  {}
  
  private function retrieveCachedReport($cacheid)
  {}
  
  private function mergeParameters()
  {}
}