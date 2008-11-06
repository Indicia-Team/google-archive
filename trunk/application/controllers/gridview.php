<?php 
/**
 * Generates a gridview control.
 */

class Gridview_Controller extends Controller {
	public static function factory($model,$page,$limit){
		$gridview = new Gridview_Controller();
		$gridview->model = $model;
		$gridview->page = $page;
		$gridview->limit = $limit;
		return $gridview;
	}
	function display() {
		/**
		 * Renders the grid with whatever parameters are supplied
		 */
		$gridview = new View('gridview');
		$gridview->columns = $this->model->table_columns;
		$gridview_body = new View('gridview_body');
		$total_records = $this->model->count_all();

		$pagination = Pagination::factory(array(
			'items_per_page' => $this->limit,
			'uri_segment' => 'page',
			'total_items' => $total_records
		));

		if(request::is_ajax()){
			# 2 things we could be up to here - filtering or table sort.
			// Get all the parameters
			$filtercol = $this->input->get('columns',null);
			$filters = $this->input->get('filters',null);
			$orderby = $this->input->get('orderby','id');
			$direction = $this->input->get('direction','asc');

			$arrorder = explode(',',$orderby);
			$arrdirect = explode(',',$direction);
			if (count($arrorder)==count($arrdirect)){
				$orderclause = array_combine($arrorder,$arrdirect);
			} else {
				$orderclause = array('id' => 'asc');
			}
			$lists = $this->model->orderby($orderclause);

			// Are we doing filtering?
			if ($filtercol!=null){
				$arrcols = explode(',',$filtercol);
				$arrfilters = explode(',',$filters);
				if (count($arrcols)==count($arrfilters)){
					$filter = array_combine($arrcols,$arrfilters);
					$lists = $lists->like($filter);
				}
			} 

			$pagination->total_items = $lists->count_all();
			$offset = $pagination->sql_offset();
			$lists = $lists->find_all($this->limit, $offset);
			
			$gridview_body->table = $lists;
			$gridview->body = $gridview_body;
			$this->auto_render=false;
			$gridview->render(true);
		} else {
			$offset = $pagination->sql_offset();
			$lists = $this->model->orderby('id','asc')
				->find_all($this->limit, $offset);
			$gridview_body->table = $lists;
			$gridview->body = $gridview_body;
			$gridview->pagination = $pagination;
			return $gridview->render();
		}
	}
}
?>
