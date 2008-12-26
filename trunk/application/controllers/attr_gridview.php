<?php
/**
 * Generates a gridview control.
 */

class Attr_Gridview_Controller extends Controller {
	public static function factory($model,$page,$limit,$uri_segment,$createpath,$createbutton){
		$gridview = new Attr_Gridview_Controller();
		$gridview->model = $model;
		$gridview->columns = $model->table_columns;
		$gridview->page = $page;
		$gridview->limit = $limit;
		$gridview->createpath = $createpath;
		$gridview->createbutton = $createbutton;
		$gridview->uri_segment = $uri_segment;
		$gridview->base_filter = null;
		$gridview->auth_filter = null;
		$gridview->actionColumns = array();
		return $gridview;
	}
	function display() {
		/**
		 * Renders the grid with whatever parameters are supplied
		 */
		$gridview = new View('attr_gridview');
		$gridview_body = new View('gridview_body');

		# 2 things we could be up to here - filtering or table sort.
		// Get all the parameters
		$filter_website = $this->input->get('website_id',null);
		$filter_survey = $this->input->get('survey_id',null);

		$orderby = $this->input->get('orderby','id');
		$direction = $this->input->get('direction','asc');

//		if ($filter_website==null)
//			return;
		
		$arrorder = explode(',',$orderby);
		$arrdirect = explode(',',$direction);
		if (count($arrorder)==count($arrdirect)){
			$orderclause = array_combine($arrorder,$arrdirect);
		} else {
			$orderclause = array('id' => 'asc');
		}
		$lists = $this->model->orderby($orderclause);

		// If we are logged on as a site controller, then need to restrict access to those
		// records on websites we are site controller for.
		// Core Admins get access to everything - no filter applied.
		if ($this->auth_filter != null){
			$filter = $this->auth_filter;
			$lists = $lists->in($filter['field'], $filter['values']);
		}
		// Are we doing server-side filtering?
		if ($this->base_filter != null){
			$filter = $this->base_filter;
			$lists = $lists->where($filter);
		}
		// Are we doing client-side filtering?
		if ($filter_website != null AND is_numeric($filter_website) AND $filter_website >= 0){
			$lists = $lists->where(array('website_id' => $filter_website));
		} else
			$filter_website = null;
			
		if ($filter_survey != null AND is_numeric($filter_survey) AND $filter_survey >= 0 ){
			$lists = $lists->where(array('survey_id' => $filter_survey));
		} else 
			$filter_survey = null;
			
		$offset = ($this->page -1) * $this->limit;
		$table = $lists->find_all($this->limit, $offset);

		$pagination = Pagination::factory(array(
			'style' => 'extended',
			'items_per_page' => $this->limit,
			'uri_segment' => $this->uri_segment,
			'total_items' => $lists->count_last_query(),
			'auto_hide' => true
		));

		$gridview_body->table = $table;
		$gridview->body = $gridview_body;
		$gridview->pagination = $pagination;
		$gridview->columns = $this->columns;
		$gridview->actionColumns = $this->actionColumns;
		$gridview->createpath = $this->createpath;
		$gridview->createbuttonname = $this->createbutton;
		$gridview_body->columns = $this->columns;
		$gridview_body->actionColumns = $this->actionColumns;
		
		if (!is_null($filter_website)) {
			$gridview->website_id = $filter_website;
			$website= new Website_Model($filter_website);
			$gridview->filter_summary = 'Filter applied: Website = "'.$website->title.'"';
			if (!is_null($filter_survey)) {
				$gridview->survey_id = $filter_survey;
				$survey= new Survey_Model($filter_survey);
				$gridview->filter_summary = $gridview->filter_summary.' : Survey = "'.$survey->title.'"';
			} else{
				$gridview->filter_summary = $gridview->filter_summary.' : Attributes Common to all surveys on the website';
			}
		} else {
			$gridview->filter_summary = "A filter must be applied in order to display or create records.";
			$gridview->disable_new_button = true;
		}
					
		if(request::is_ajax()){
			if ($this->input->get('type',null) == 'pager'){
				echo $pagination;
			} else {
				$this->auto_render=false;
				$gridview_body->render(true);
			}

		} else {
			return $gridview->render();
		}
	}
}
?>
