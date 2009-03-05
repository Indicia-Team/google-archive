<?php

/**
* INDICIA
* @link http://code.google.com/p/indicia/
* @package Indicia
*/

/**
* <h1>XML Report reader</h1>
* <p>The report reader encapsulates logic for reading reports from a number of sources, and opens up * report methods in a transparent way to the report controller.</p>
*
* @package Indicia
* @subpackage Controller
* @license http://www.gnu.org/licenses/gpl.html GPL
* @author Nicholas Clarke <xxx@xxx.net> / $Author$
* @copyright xxxx
* @version $Rev$ / $LastChangedDate$
*/

class XMLReportReader_Core implements ReportReader
{
  private $title;
  private $description;
  private $query;
  private $order_by;
  private $params;
  
  /**
  * <p> Constructs a reader for the specified report. </p>
  */
  public function __construct($report)
  {
    $reader = new XMLReader();
    $reader->open($report);
    while($reader->read())
    {
     switch($reader->nodeType)
     {
       case (XMLREADER::ELEMENT):
	 switch ($reader->name)
	 {
	   case 'report':
	     $this->title = $reader->getAttribute('title');
	     $this->description = $reader->getAttribute('description');
	     break;
	   case 'query':
	     $reader->read();
	     $this->query = $reader->getValue();
	     break;
	   case 'order_by':
	     $reader->read();
	     $this->order_by[] = $reader->getValue();
	     break;
	   case 'param':
	     
	     
	     
	 }
	 break;
     }
    }
  }

  /**
   * <p> Returns the title of the report. </p>
   */
  public function getTitle()
  {
    return $this->title;
  }
  
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
  
  /**
  * <p> Returns a description of the report appropriate to the level specified. </p>
  */
  public function describeReport($descLevel){}
  
}