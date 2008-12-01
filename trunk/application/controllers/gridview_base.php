<?php

abstract class Gridview_Base_Controller extends Indicia_Controller {

	/* Constructor. $modelname = name of the model for the grid.
	 * $viewname = name of the view which contains the grid.
	 * $controllerpath = path the controller from the controllers folder
	 * $viewname and $controllerpath can be ommitted if the names are all the same.
	 */
	public function __construct($modelname, $gridmodelname=NULL, $viewname=NULL, $controllerpath=NULL) {
		$this->model=ORM::factory($modelname);
		$this->gridmodelname=is_null($gridmodelname) ? $modelname : $gridmodelname;
		$this->viewname=is_null($viewname) ? $modelname : $viewname;
		$this->controllerpath=is_null($controllerpath) ? $modelname : $controllerpath;
		$this->gridmodel = ORM::factory($this->gridmodelname);
		$this->pageNoUriSegment = 3;
		$this->base_filter = array();
		$this->columns = $this->gridmodel->table_columns;
		$this->actionColumns = array(
			'edit' => $this->controllerpath."/edit/£id£"
		);
		$this->pagetitle = "Abstract gridview class - override this title!";
		$this->view = new View($this->viewname);
		$upload_csv_form = new View('templates/upload_csv');
		$upload_csv_form->controllerpath = $this->controllerpath;
		$this->view->upload_csv_form = $upload_csv_form;
		parent::__construct();
	}

	public function page($page_no, $limit) {
		$grid =	Gridview_Controller::factory($this->gridmodel,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;

		// Add table to view
		$this->view->table = $grid->display();

		// Templating
		$this->template->title = $this->GetEditPageTitle($this->gridmodel, $this->pagetitle);
		$this->template->content = $this->view;
	}

	public function page_gv($page_no, $limit) {
		$this->auto_render = false;
		$grid =	Gridview_Controller::factory($this->gridmodel,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;
		return $grid->display();
	}

	public function upload_mappings() {
		$_FILES = Validation::factory($_FILES)
			->add_rules('csv_upload', 'upload::valid',
			       	'upload::required', 'upload::type[csv]', 'upload::size[1M]');
		if ($_FILES->validate()) {
			// move the file to the upload directory
			$csvTempFile = upload::save('csv_upload');
			$_SESSION['uploaded_csv'] = $csvTempFile;

			// Following helps for files from Macs
			ini_set('auto_detect_line_endings',1);
			$handle = fopen($csvTempFile, "r");
			$this->template->title = "Map CSV File columns to ".$this->pagetitle;
			$view = new View('upload_mappings');
			$view->columns = fgetcsv($handle, 1000, ",");
			fclose($handle);
			$view->model = $this->model;
			$view->controllerpath = $this->controllerpath;
			$this->template->content = $view;
		} else {
			// TODO: Display a validation error and remember current viewstate
			url::redirect($this->controllerpath);
		}


	}

	public function upload() {
		$csvTempFile = $_SESSION['uploaded_csv'];

		// make sure the file still exists
		if (file_exists($csvTempFile))
		{
			// Following helps for files from Macs
			ini_set('auto_detect_line_endings',1);
			// create the file pointer
			$handle = fopen ($csvTempFile, "r");
			// skip the title row
			fgetcsv($handle, 1000, ",");
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$index = 0;
				$saveArray = array();
				foreach ($_POST as $col=>$attr) {
					if ($attr!='<please select>') {
						// Add the data to the save array
						$saveArray[$attr] = $data[$index];
					}
					$index++;
				}
				// Save the record
				$this->model->submission = $this->wrap($saveArray, true);
				$this->model->submit();
			}
			fclose($handle);
	    	// need to flash a success message
	    	// clean up the uploaded file
	    	unlink($csvTempFile);
	    	url::redirect($this->controllerpath);
		}
	}

}
