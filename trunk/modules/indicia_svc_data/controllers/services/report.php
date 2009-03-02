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

class Report_Controller extends Service_Base_Controller {
  
  /**
   * Access the report - probably we will use routing to direct /report directly to /report/access
   */
  public function access()
  {
    /*
     * We can specify a request in a number of ways:
     * <ol>
     * <li> Predefined report on the core module. </li>
     * <li> Predefined report elsewhere (URI given). </li>
     * <li> Report passed with the query. </li>
     * </ol>
     * We also need to perform authentication at a read level for the data we're trying to access
     * (this might be fun, given the low level that the reports run at).
     */
    
  }
  
  private function fetchLocalRequest($request)
  {}
  
  private function fetchRemoteRequest($request)
  {}
}