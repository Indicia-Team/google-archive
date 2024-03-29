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


class Data_Controller extends Data_Service_Base_Controller {
  protected $model;
  protected $entity;
  protected $viewname;
  protected $foreign_keys;
  protected $view_columns;
  protected $db;
  // following are used to store the response till all finished, so we don't output anything
  // if there is an error
  protected $response;
  protected $content_type;

  // Read/Write Access to entities: there are several options:
  // 1) Standard: Restricted read and write access dependant on website id.
  //    There is a public function with the name of the entity in this file, and the entity appears in $allow_updates.
  //    The view list_<plural_entity> must exist and have a website_id column on it. If the website_id is null then
  //    the record may be accessed by all websites.
  // 2) Standard Read Only: Restricted read access dependant on website id. No write Access.
  //    There is a public function with the name of the entity in this file, and the entity does not appear in $allow_updates.
  //    The view list_<plural_entity> must exist and have a website_id column on it. If the website_id is null then
  //    the record may be accessed by all websites.
  // 3) Unrestricted Access: All records may be read and updated.
  //    There is a public function with the name of the entity in this file, and the entity appears in $allow_updates.
  //    Either the view list_<plural_entity> exists and has a website_id column on it which is forced to null, OR
  //    the entity appears in $allow_full_access.
  // 4) Unrestricted Read Only: All records may be read. No write Access.
  //    There is a public function with the name of the entity in this file, and the entity does not appear in $allow_updates.
  //    Either the view list_<plural_entity> exists and has a website_id column on it which is forced to null, OR
  //    the entity appears in $allow_full_access.
  // 5) Unrestricted Read, Restricted Write:
  //    Not currently implemented.
  // 6) No Access:
  //    There is no public function with the name of the entity in this file
  //
  // default to no updates allowed - must explicity allow updates.
  protected $allow_updates = array(
                  'location',
                  'occurrence',
                  'occurrence_comment',
                   'person',
                  'sample',
                    'survey',
                  'user'
                  );
  // Standard functionality is to use the list_<plural_entity> views to provide a mapping between entity id
  // and website_id, so that we can work out whether access to a particular record is allowed.
  // There is a potential issues with this: We may want everyone to have complete access to a particular dataset
  // So if we wish total access to a given dataset, the entity must appear in the following list.
  protected $allow_full_access = array(
                    'taxa_taxon_list'
                    );

  /**
  * Provides the /services/data/language service.
  * Retrieves details of a single language.
  */
  public function language()
  {
  $this->handle_call('language');
  }

  /**
  * Provides the /services/data/location service.
  * Retrieves details of a single survey.
  */
  public function location()
  {
    $this->handle_call('location');
  }

  /**
  * Provides the /services/data/occurrence service.
  * Retrieves details of occurrences.
  */
  public function occurrence()
  {
    $this->handle_call('occurrence');
  }

  /**
  * Provides the /service/data/occurrence_attribute service.
  * Retrieves details of occurrence attributes.
  */
  public function occurrence_attribute()
  {
  $this->handle_call('occurrence_attribute');
  }

  /**
  * Provides the /services/data/person service.
  * Retrieves details of a single person.
  */
  public function person()
  {
    $this->handle_call('person');
  }

  /**
  * Provides the /services/data/sample service.
  * Retrieves details of a sample.
  */
  public function sample()
  {
    $this->handle_call('sample');
  }

  /**
  * Provides the /services/data/survey service.
  * Retrieves details of a single survey.
  */
  public function survey()
  {
  $this->handle_call('survey');
  }

  /**
  * Provides the /services/data/taxon_group service.
  * Retrieves details of a single taxon_group.
  */
  public function taxon_group()
  {
  $this->handle_call('taxon_group');
  }

  /**
  * Provides the /services/data/taxon_list service.
  * Retrieves details of a single taxon_list.
  */
  public function taxon_list()
  {
  $this->handle_call('taxon_list');
  }

  /**
  * Provides the /services/data/taxa_taxon_list service.
  * Retrieves details of taxa on a taxon_list.
  */
  public function taxa_taxon_list()
  {
  $this->handle_call('taxa_taxon_list');
  }

  /**
  * Provides the /services/data/term service.
  * Retrieves details of a single term.
  */
  public function term()
  {
    $this->handle_call('term');
  }

  /**
  * Provides the /services/data/termlist service.
  * Retrieves details of a single termlist.
  */
  public function termlist()
  {
    $this->handle_call('termlist');
  }

  /**
  * Provides the /services/data/termlists_term service.
  * Retrieves details of a single termlists_term.
  */
  public function termlists_term()
  {
    $this->handle_call('termlists_term');
  }

  /**
  * Provides the /services/data/user service.
  * Retrieves details of a single user.
  */
  public function user()
  {
    $this->handle_call('user');
  }

  /**
  * Provides the /services/data/website service.
  * Retrieves details of a single website.
  */
  public function website()
  {
    $this->handle_call('website');
  }

  /**
  * Provides the /services/data/occurrence_comments service.
  */
  public function occurrence_comment()
  {
    $this->handle_call('occurrence_comment');
  }

  /**
  * Internal method to handle calls - decides if it's a request for data or a submission.
  * @todo include exception getTrace() in the error response?
  */
  protected function handle_call($entity)
  {
    try {
      $this->entity = $entity;

      if (array_key_exists('submission', $_POST))
      {
        $this->handle_submit();
      }
      else
      {
        $this->handle_request();
      }
      kohana::log('debug', 'Sending reponse size '.count($this->response));
      $this->send_response();
    }
    catch (Exception $e)
    {
      $this->handle_error($e);
    }
  }

  /**
  * Internal method for handling a generic submission to a particular model.
  */
  protected function handle_submit()
  {
    $this->authenticate();
    $mode = $this->get_input_mode();
    switch ($mode)
    {
      case 'json':
        $s = json_decode($_POST['submission'], true);
    }

    if (array_key_exists('submission', $s))
    {
      $id = $this->submit($s);
      // TODO: proper handling of result checking
      $result = TRUE;
    }
    else
    {
      $this->check_update_access($this->entity, $s);
      $model = ORM::factory($this->entity);
      $model->submission = $s;
      $result = $model->submit();
      $id = $model->id;
    }
    if ($result)
    {
      $this->response=json_encode(array('success'=>$id));
      $this->delete_nonce();
    }
    else if (isset($model))
      Throw new ArrayException($model->getAllErrors());
    else
      Throw new Exception('Unknown error on submission (to do - get correct error info)');

  }

  /**
   * Retrieve the records for a read request. Also sets the list of columns into $this->columns.
   *
   * @return Array Query results array.
   */
  protected function read_records() {
    // Store the entity in class member, so less recursion overhead when building XML
    $this->viewname = $this->get_view_name();
    $this->model=ORM::factory($this->entity);
    $this->db = new Database();
    $this->view_columns=$this->db->list_fields($this->viewname);
    $result=$this->build_query_results();
    kohana::log('debug', 'Query ran for service call: '.$this->db->last_query());
    return $result;
  }

  public function handle_media()
  {
    syslog(LOG_DEBUG, "Attempting to handle media submission.");
    // Ensure we have write permissions.
    $this->authenticate();
    syslog(LOG_DEBUG, "Authentication for media successful.");
    // We will be using a POST array to send data, and presumably a FILES array for the
    // media.
    // Upload size
    $ups = Kohana::config('indicia.maxUploadSize');
    syslog(LOG_DEBUG, "Maximum upload size is $ups.");
    $_FILES = Validation::factory($_FILES)->add_rules(
      'media_upload', 'upload::valid', 'upload::required',
      'upload::type[png,gif,jpg]', "upload::size[$ups]"
    );
    if ($_FILES->validate())
    {
      $fTmp = upload::save('media_upload');
      syslog(LOG_DEBUG, "Media validated and saved as $fTmp.");
    }
    else
    {
      syslog(LOG_DEBUG, "Media did not validate.");
      //TODO better error message
      echo "Some sort of problem!";
    }

  }

  /**
  * Returns some information about the table - at least list of columns and
  * number of records. This is required for the external datagrid control.
  */
  public function info_table($tablename)
  {
    $this->authenticate('read'); // populates $this->website_id
    $this->entity = $tablename;
    $this->db = new Database();
    $this->viewname = $this->get_view_name();
    $this->view_columns = $this->db->list_fields($this->viewname);
    $mode = $this->get_output_mode();
    if(!in_array ($this->entity, $this->allow_full_access)) {
        if(array_key_exists ('website_id', $this->view_columns))
        {
          $this->db->in('website_id', array(null, $this->website_id));
        } else {
          Kohana::log('info', $this->viewname.' does not have a website_id - access denied');
            throw new ServiceError('No access to '.$this->viewname.' allowed.');
        }
    }

    $return = Array(
      'record_count' => $this->db->count_records($this->viewname),
      'columns' => array_keys($this->db->list_fields($this->viewname))
    );
    switch ($mode)
    {
      case 'json':
        $a = json_encode($return);
        if (array_key_exists('callback', $_GET))
        {
          $a = $_GET['callback']."(".$a.")";
        }
        echo $a;
        break;
      default:
        echo json_encode($return);
    }
  }


  /**
  * Builds a query to extract data from the requested entity, and also
  * include relationships to foreign key tables and the caption fields from those tables.
  *
  * @todo Review this code for SQL Injection attack!
  * @todo Basic website filter done, but not clever enough.
  */
  protected function build_query_results()
  {
    $this->foreign_keys = array();
    $this->db->from($this->viewname);
    // Select all the table columns from the view
    $select = implode(', ', array_keys($this->db->list_fields($this->viewname)));
    $this->db->select($select);
    // Make sure that we're only showing items appropriate to the logged-in website
    if(!in_array ($this->entity, $this->allow_full_access)) {
      if(array_key_exists ('website_id', $this->view_columns))
      {
        $this->db->in('website_id', array(null, $this->website_id));
      } else {
        Kohana::log('info', $this->viewname.' does not have a website_id - access denied');
        throw new ServiceError('No access to entity '.$this->entity.' allowed through view '.$this->viewname);
      }
    }
     // if requesting a single item in the segment, filter for it, otherwise use GET parameters to control the list returned
    if ($this->uri->total_arguments()==0)
      $this->apply_get_parameters_to_db();
    else {
     if (!$this->check_record_access($this->entity, $this->uri->argument(1), $this->website_id))
      {
      Kohana::log('info', 'Attempt to access existing record failed - website_id '.$this->website_id.' does not match website for '.$this->entity.' id '.$this->uri->argument(1));
          throw new ServiceError('Attempt to access existing record failed - website_id '.$this->website_id.' does not match website for '.$this->entity.' id '.$this->uri->argument(1));
      }
        $this->db->where($this->viewname.'.id', $this->uri->argument(1));
    }
    return $this->db->get()->result_array(FALSE);
  }

  /**
  * Returns the name of the view for the request. This is a view
  * associated with the entity, but prefixed by either list, gv or max depending
  * on the GET view parameter.
  */
  protected function get_view_name()
  {
    $table = inflector::plural($this->entity);
    $prefix='';
    if (array_key_exists('view', $_GET))
    {
      $prefix = $_GET['view'];
    }
    // Check for allowed view prefixes, and use 'list' as the default
    if ($prefix!='gv' && $prefix!='detail')
    $prefix='list';
    return $prefix.'_'.$table;
  }


  /**
  * Works out what filter and other options to set on the db object according to the
  * $_GET parameters currently available, when retrieving a list of items.
  */
  protected function apply_get_parameters_to_db()
  {
    $sortdir='ASC';
    $orderby='';
    $like=array();
    $where=array();
    foreach ($_GET as $param => $value)
    {
      switch ($param)
      {
        case 'sortdir':
          $sortdir=strtoupper($value);
          if ($sortdir != 'ASC' && $sortdir != 'DESC')
          {
            $sortdir='ASC';
          }
          break;
        case 'orderby':
          if (array_key_exists(strtolower($value), $this->view_columns))
            $orderby=strtolower($value);
          break;
        case 'limit':
          if (is_numeric($value))
          $this->db->limit($value);
          break;
        case 'offset':
          if (is_numeric($value))
          $this->db->offset($value);
          break;
        case 'qfield':
          if (array_key_exists(strtolower($value), $this->view_columns))
          {
            $qfield = strtolower($value);
          }
          break;
        case 'q':
          $q = strtolower($value);
          break;
        case 'attrs':
          // Check that we're dealing with 'occurrence' or 'sample' here
          switch($this->entity)
          {
            case 'sample':
              Kohana::log('info', "Fetching attributes $value for sample");
              $attrs = explode(',', $value);
              break;
            case 'occurrence':
              Kohana::log('info', "Fetching attributes $value for occurrence");
              $attrs = explode(',', $value);
              break;
            default:
              Kohana::log('info', 'Trying to fetch attributes for non sample/occurrence table. Ignoring.');
          }
          break;
      default:
        if (array_key_exists(strtolower($param), $this->view_columns))
        {
          // A parameter has been supplied which specifies the field name of a filter field
          if ($value == 'NULL')
            $value = NULL;
          if ($this->view_columns[$param]['type']=='int' || $this->view_columns[$param]['type']=='bool') {
            $where[$param]=$value;
          } else {
            $like[$param]=$value;
          }

        }
      }
    }
    if (isset($qfield) && isset($q))
    {
      if ($this->view_columns[$qfield]['type']=='int' || $this->view_columns[$qfield]['type']=='bool')
      {
        $where[$qfield]=$q;
      }
      else
      {
        $like[$qfield]=$q;
      }
    }
    if ($orderby)
      $this->db->orderby($orderby, $sortdir);
    if (count($like))
      $this->db->like($like);
    if (count($where))
      $this->db->where($where);
  }

  /**
  * Accepts a submission from POST data and attempts to save to the database.
  */
  public function save()
  {
    try
    {
      $this->authenticate();
      if (array_key_exists('submission', $_POST))
      {
        $mode = $this->get_input_mode();
        switch ($mode)
        {
          case 'json':
            $s = json_decode($_POST['submission'], true);
        }
        $this->submit($s);
      }
      // return a success message
      echo json_encode(array('success'=>'multiple records'));
      $this->delete_nonce();
    }
    catch (Exception $e)
    {
      $this->handle_error($e);
    }
  }

  /**
  * Takes a submission array and attempts to save to the database.
  */
  protected function submit($s)
  {
    kohana::log('info', 'submit');
    foreach ($s['submission']['entries'] as $m)
    {
      $m = $m['model'];
      $model = ORM::factory($m['id']); // id is the entity.
      $this->check_update_access($m['id'], $m);
      $model->submission = $m;
      $result = $model->submit();
      $id = $model->id;
      if (!$result)
      {
        Throw new ArrayException('Validation error', $model->getAllErrors());
      }
      // return the first model
      if (!isset($this->model))
        $id=$model->id;
    }
    return $id;
  }

 /**
  * Checks that we have update access to a given entity for a given submission array.
  * The submission array is checked to see if there is a primary key ('id').
  * Returns true if access OK, otherwise throws an exception.
  */
  protected function check_update_access($entity, $s)
  {
      if (!in_array($entity, $this->allow_updates)) {
      Kohana::log('info', 'Attempt to write to entity '.$entity.' by website '.$this->website_id.': no write access allowed through services.');
          throw new ServiceError('Attempt to write to entity '.$entity.' failed: no write access allowed through services.');
    }

      if(array_key_exists('id', $s['fields']))
        if (is_numeric($s['fields']['id']['value']))
          // there is an numeric id field so modifying an existing record
          if (!$this->check_record_access($entity, $s['fields']['id']['value'], $this->website_id))
          {
        Kohana::log('info', 'Attempt to update existing record failed - website_id '.$this->website_id.' does not match website for '.$entity.' id '.$s['fields']['id']['value']);
              throw new ServiceError('Attempt to update existing record failed - website_id '.$this->website_id.' does not match website for '.$entity.' id '.$s['fields']['id']['value']);
          }
    return true;
  }

  protected function check_record_access($entity, $id, $website_id)
  {
    // if $id is null, then we have a new record, so no need to check if we have access to the record
    if (is_null($id))
      return true;
    $table = inflector::plural($entity);
      $viewname='list_'.$table;
      $db = new Database;
      $fields=$db->list_fields($viewname);
      if(empty($fields)) {
      Kohana::log('info', $viewname.' not present - access denied');
         throw new ServiceError('Access to entity '.$entity.' denied.');
      }
      $db->from($viewname);
      $db->where(array('id' => $id));

      if(!in_array ($this->entity, $this->allow_full_access)) {
            if(array_key_exists ('website_id', $this->view_columns))
            {
                $db->in('website_id', array(null, $this->website_id));
            } else {
                Kohana::log('info', $viewname.' does not have a website_id - access denied');
                throw new ServiceError('No access to entity '.$entity.' allowed.');
            }
      }
    $number_rec = $db->count_records();
    return ($number_rec > 0 ? true : false);
  }
}

?>
