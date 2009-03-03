<?php

/**
* INDICIA
* @link http://code.google.com/p/indicia/
* @package Indicia
*/

/**
* <h1>Report reader</h1>
* <p>The report reader encapsulates logic for reading reports from a number of sources, and opens up * report methods in a transparent way to the report controller.</p>
*
* @package Indicia
* @subpackage Controller
* @license http://www.gnu.org/licenses/gpl.html GPL
* @author Nicholas Clarke <xxx@xxx.net> / $Author$
* @copyright xxxx
* @version $Rev$ / $LastChangedDate$
*/
interface ReportReader_Core 
{
  const REPORT_DESCRIPTION_NAME = 0;
  const REPORT_DESCRIPTION_BRIEF = 1;
  const REPORT_DESCRIPTION_DEFAULT = 2;
  const REPORT_DESCRIPTION_FULL = 3;
  
  /**
  * <p> Constructs a reader for the specified report. </p>
  */
  public function __construct($report){}

  /**
   * <p> Returns the title of the report. </p>
   */
  public function getTitle(){}
  
  /**
   * <p> Returns the description of the report. </p>
   */
  public function getDescription(){}
  
  /**
   * <p> Returns the query specified. </p>
   */
  public function getQuery(){}
  
  /**
   * <p> Uses source-specific validation methods to check whether the report query is valid. </p>
   */
  public function isValid(){}
  
  /**
   * <p> Gets a list of parameters (name => type) </p>
   */
  public function getParams(){}
  
  
}