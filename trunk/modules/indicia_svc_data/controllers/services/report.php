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
  * We can specify a request in a number of ways:
  * <ul>
  * <li> Predefined report on the core module. </li>
  * <li> Predefined report elsewhere (URI given). </li>
  * <li> Report passed with the query. </li>
  * </ul>
  * We also need to perform authentication at a read level for the data we're trying to access
  * (this might be fun, given the low level that the reports run at).
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
  */
  public function access()
  {
    
  }
  
  private function fetchLocalReport($request)
  {}
  
  private function fetchRemoteReport($request)
  {}
}