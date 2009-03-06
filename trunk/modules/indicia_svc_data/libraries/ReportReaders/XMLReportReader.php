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
  private $columns;
  
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
	     $this->inferFromQuery();
	     break;
	   case 'order_by':
	     $reader->read();
	     $this->order_by[] = $reader->getValue();
	     break;
	   case 'param':
	     $this->mergeParam($reader->getAttribute('name'), $reader->getAttribute('display'), $reader->getAttribute('datatype'), $reader->getAttribute('description'));
	     break;
	     
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
  public function getDescription()
  {
    return $this->description;
  }
  
  /**
   * <p> Returns the query specified. </p>
   */
  public function getQuery()
  {
    return $this->query;
  }
  
  /**
   * <p> Uses source-specific validation methods to check whether the report query is valid. </p>
   */
  public function isValid(){}
  
  /**
   * <p> Gets a list of parameters (name => type) </p>
   */
  public function getParams()
  {
    return $this->params;
  }
  
  /**
  * <p> Returns a description of the report appropriate to the level specified. </p>
  */
  public function describeReport($descLevel){}
  
  private function mergeParam($name, $display = '', $type = '', $description = '')
  {
    if (array_key_exists($name, $this->params))
    {
      if ($display != '') $this->params[$name]['display'] = $display;
      if ($type != '') $this->params[$name]['datatype'] = $type;
      if ($description != '') $this->params[$name]['description'] = $description;
    }
    else
    {
      $this->params[$name] = array('datatype'=>$type, 'display'=>$display, 'description'=>$description);
    }
  }
  
  private function mergeColumn($name, $display, $style)
  {
    if (array_key_exists($name, $this->columns))
    {
      if ($display != '') $this->columns[$name]['display'] = $display;
      if ($style != '') $this->columns[$name]['style'] = $style;
    }
    else
    {
      $this->columns[$name] = array('display' => $display, 'style' => $style);
    }
  }
  
  /**
  * Infers parameters such as column names and parameters from the query string.
  */
  private function inferFromQuery()
  {
    
  }
  
}