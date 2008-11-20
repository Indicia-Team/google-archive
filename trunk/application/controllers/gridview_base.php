<?php

abstract class Gridview_Base_Controller extends Indicia_Controller {

	/* Constructor. $modelname = name of the model for the grid.
	 * $viewname = name of the view which contains the grid.
	 * $controllerpath = path the controller from the controllers folder
	 * $viewname and $controllerpath can be ommitted if the names are all the same.
	 */
	public function __construct($modelname, $gridmodelname=NULL, $viewname=NULL, $controllerpath=NULL) {
		$this->modelname=$modelname;
		$this->gridmodelname=is_null($gridmodelname) ? $modelname : $gridmodelname;
		$this->viewname=is_null($viewname) ? $modelname : $viewname;
		$this->controllerpath=is_null($controllerpath) ? $modelname : $controllerpath;
		$this->model = ORM::factory($this->gridmodelname);
		$this->pageNoUriSegment = 3;
		$this->base_filter = array();
		$this->columns = $this->model->table_columns;
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
		$grid =	Gridview_Controller::factory($this->model,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;

		// Add table to view
		$this->view->table = $grid->display();

		// Templating
		$this->template->title = $this->GetEditPageTitle($this->model, $this->pagetitle);
		$this->template->content = $this->view;
	}

	public function page_gv($page_no, $limit) {
		$this->auto_render = false;
		$grid =	Gridview_Controller::factory($this->model,
			$page_no,
			$limit,
			$this->pageNoUriSegment);
		$grid->base_filter = $this->base_filter;
		$grid->columns = array_intersect_key($grid->columns, $this->columns);
		$grid->actionColumns = $this->actionColumns;
		return $grid->display();
	}

	/* Upload action. Accepts a CSV file in the csv_upload FILE post
	 */
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
				$model = ORM::factory($this->modelname);
				$index = 0;
				foreach ($_POST as $col=>$attr) {
					if ($attr!='<please select>') {
						if (substr($attr, -3)!='_id') {
							$model->__set($attr, $data[$index]);
						} else {
							// This is a foreign key, so need to lookup the id based on the string
							if (array_key_exists(substr($attr,0,-3), $model->belongs_to)) {
								// Belongs_to specifies a fk table that does not match the attribute name
								$fk_model=ORM::factory($model->belongs_to[substr($attr,0,-3)]);
							} else {
								// Belongs_to specifies a fk table that matches the attribute name
								$fk_model=ORM::factory(substr($attr,0,-3));
							}
							// TODO: Don't hard code the title as the search field
							// TODO: Consider filtering so it only picks up sub-items relevant to this website.
							$fk_record = $fk_model->lookup($data[$index]);
							$model->__set($attr, $fk_record->id);
						}
					}
					$index++;
				}
				$model->set_metadata();
				$model->save();
			}
			fclose($handle);
	    	// need to flash a success message
	    	// clean up the uploaded file
	    	unlink($csvTempFile);
	    	url::redirect($this->controllerpath);
		}
	}

	public function upload_mappings() {
		$_FILES = Validation::factory($_FILES)
			->add_rules('csv_upload', 'upload::valid', 'upload::required', 'upload::type[csv]', 'upload::size[1M]');
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
			$view->model = ORM::factory($this->modelname);
			$view->controllerpath = $this->controllerpath;
			$this->template->content = $view;
		} else {
			// TODO: Display a validation error and remember current viewstate
			url::redirect($this->controllerpath);
		}
	}

}
