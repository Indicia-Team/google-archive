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
 * Base controller class for data & reporting services.
 *
 * @package	Services
 * @subpackage Data
 */
class Data_Service_Base_Controller extends Service_Base_Controller {

  /**
   * Id of the website calling the service. Obtained when performing read authentication and used
   * to filter the response.
   */
  protected $website_id = null;

  /**
  * Before a request is accepted, this method ensures that the POST data contains the
  * correct digest token so we know the request was from the website.
  *
  * @param string $mode Whether the authentication token is required to have read or write access.
  * Possible values are 'read' and 'write'. Defaults to 'write'.
  */
  protected function authenticate($mode = 'write')
  {
    // Read calls are done using get values, so we merge the two arrays
    $array = array_merge($_POST, $_GET);
    $authentic = FALSE; // default
    if (array_key_exists('nonce', $array) && array_key_exists('auth_token',$array))
    {
      $nonce = $array['nonce'];
      $this->cache = new Cache;
      $nonces = $this->cache->find($mode);
      if (array_key_exists($nonce, $nonces))
      {
        $website_id = $nonces[$nonce];
        $website = ORM::factory('website', $website_id);
        if ($website->id) {
          $password = ORM::factory('website', $website_id)->password;
          if (sha1("$nonce:$password")==$array['auth_token'])
          {
            Kohana::log('info', "Authentication successful.");
            $authentic=TRUE;
            $this->website_id = $website_id;
          }
        }

        // Refresh the nonce. If it's a write nonce, we'll delete it later when the data has been saved
        $this->cache->delete($nonce);
        $this->cache->set($nonce, $website_id, $mode);
      }
    }

    if (!$authentic)
    {
      Kohana::log('info', "Unable to authenticate.");
      throw new ServiceError("unauthorised");
    };
  }

  /**
  * Cleanup a write once nonce from the cache. Should be called after a call to authenticate.
  * Read nonces do not need to be deleted - they are left to expire.
  */
  protected function delete_nonce()
  {
    $array = array_merge($_POST, $_GET);
    if (array_key_exists('nonce', $array))
    {
      $nonce = $array['nonce'];
      $this->cache->delete($nonce);
    }
  }

  /**
  * Generic method to handle a request for data or a report. Depends on the sub-class
  * implementing a read_records method.
  */
  protected function handle_request()
  {
    // Authenticate for a 'read' parameter
    $this->authenticate('read');
    $records=$this->read_records();
    $mode = $this->get_output_mode();
    switch ($mode)
    {
      case 'json':
        $a =  json_encode($records);
        $this->content_type = 'Content-Type: application/json';
        if (array_key_exists('callback', $_GET))
        {
          $a = $_GET['callback']."(".$a.")";
        }
        $this->response = $a;
        break;
      case 'xml':
        if (array_key_exists('xsl', $_GET))
        {
          $xsl = $_GET['xsl'];
          if (!strpos($xsl, '/'))
          // xsl is not a fully qualified path, so point it to the media folder.
          $xsl = url::base().'media/services/stylesheets/'.$xsl;
        }
        else
        {
          $xsl = '';
        }
        $this->response = $this->xml_encode($records, $xsl, TRUE);
        $this->content_type = 'Content-Type: text/xml';
        break;
      case 'csv':
        $this->response =  $this->csv_encode($records);
        $this->content_type = 'Content-Type: text/comma-separated-values';
        break;
      default:
        // Code to load from a view
        if (file_exists('views',"services/data/$entity/$mode"))
        {
          $this->response = $this->view_encode($records, View::factory("services/data/$entity/$mode"));
        }
        else
        {
          throw new ServiceError("$this->entity data cannot be output using mode $mode.");
        }
    }
  }

  /**
   * Set the content type and then issue the response.
   */
  protected function send_response()
  {
    // last thing we do is set the output
    if ($this->content_type)
    {
      header($this->content_type);
    }
    echo $this->response;
  }

  /**
  * Encode the results of a query array as a csv string
  */
  protected function csv_encode($array)
  {
    // Get the column titles in the first row
    $result = $this->get_csv(array_keys($array[0]));
    foreach ($array as $row) {
      $result .= $this->get_csv(array_values($row));
    }
    kohana::log('info', $result);
    return $result;
  }

  /**
  * Return a line of CSV from an array. This is instead of PHP's fputcsv because that
  * function only writes straight to a file, whereas we need a string.
  */
  function get_csv($data,$delimiter=',',$enclose='"')
  {
    $newline="\n";
    $output = '';
    foreach ($data as $cell)
    {
      //Test if numeric
      if (!is_numeric($cell))
      {
        //Escape the enclose
        $cell = str_replace($enclose,$enclose.$enclose,$cell);
        //Not numeric enclose
        $cell = $enclose . $cell . $enclose;
      }
      if ($output=='')
      {
        $output = $cell;
      }
      else
      {
        $output.=  $delimiter . $cell;
      }
    }
    $output.=$newline;
    return $output;
  }


  /**
  * Get the results of the query using the supplied view to render each row.
  */
  protected function view_encode($array, $view)
  {
    $output = '';
    foreach ($array as $row)
    {
      $view->row= $row;
      $output .= $view->render();
    }
  }

  /**
  * Encodes an array as xml. Uses $this->entity to decide the name of the root element.
  * Recurses into the array where array values are themselves arrays. Also inserts
  * xlink paths to any foreign keys, and gets the caption of the foreign entity.
  */
  protected function xml_encode($array, $xsl, $indent=false, $recursion=0)
  {
    // Keep an array to track any elements that must be skipped. For example if an array contains
    // {person_id=>1, person=>James Brown} then the xml output for the id is <person id="1">James Brown</person>.
    // There is no need to output the person separately so it gets flagged in this array for skipping.
    $to_skip=array();

    if (!$recursion)
    {
      // if we are outputting a specific record, root is singular
      if ($this->uri->total_arguments())
      {
        $root = $this->entity;
        // We don't need to repeat the element for each record, as there is only 1.
        $array = $array[0];
      }
      else
      {
        $root = inflector::plural($this->entity);
      }
      $data = '<?xml version="1.0"?>';
      if ($xsl)
        $data .= '<?xml-stylesheet type="text/xsl" href="'.$xsl.'"?>';
      $data .= ($indent?"\r\n":'').
      "<$root xmlns:xlink=\"http://www.w3.org/1999/xlink\">".
      ($indent?"\r\n":'');
    }
    else
    {
      $data = '';
    }

    foreach ($array as $element => $value)
    {
      if (!in_array($element, $to_skip))
      {
        if ($value)
        {
          if (is_numeric($element))
          {
            $element = $this->entity;
          }
          if ((substr($element, -3)=='_id') && (array_key_exists(substr($element, 0, -3), $array)))
          {
            $element = substr($element, 0, -3);
            // This is a foreign key described by another field, so create an xlink path
            if (array_key_exists($element, $this->model->belongs_to))
            {
              // Belongs_to specifies a fk table that does not match the attribute name
              $fk_entity=$this->model->belongs_to[$element];
            }
            elseif ($element=='parent')
            {
              $fk_entity=$this->entity;
            } else {
              // Belongs_to specifies a fk table that matches the attribute name
              $fk_entity=$element;
            }
            $data .= ($indent?str_repeat("\t", $recursion):'');
            $data .= "<$element id=\"$value\" xlink:href=\"".url::base(TRUE)."services/data/$fk_entity/$value\">";
            $data .= $array[$element];
            // We output the associated caption element already, so add it to the list to skip
            $to_skip[count($to_skip)-1]=$element;
          }
          else
          {
            $data .= ($indent?str_repeat("\t", $recursion):'').'<'.$element.'>';
            if (is_array($value)) {
              $data .= ($indent?"\r\n":'').$this->xml_encode($value, NULL, $indent, ($recursion + 1)).($indent?str_repeat("\t", $recursion):'');
            }
            else
            {
              $data .= $value;
            }
          }
          $data .= '</'.$element.'>'.($indent?"\r\n":'');
        }
      }
    }
    if (!$recursion)
    {
      $data .= "</$root>";
    }
    return $data;
  }


}

?>
