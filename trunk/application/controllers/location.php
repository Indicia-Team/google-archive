<?php

class Location_Controller extends Gridview_Base_Controller {

	public function __construct()
	{
		parent::__construct('location', 'location', 'location/index');
		$this->columns = array(
                        'name'=>'',
                        'code'=>'',
                        'entered_sref'=>'');
        $this->pagetitle = "Locations";
	}

	/**
	 * Action for location/create page.
	 * Displays a page allowing entry of a new location.
	 */
	public function create() {
		$this->setView('location/location_edit', 'Location');
	}

	/**
	 * Action for location/edit page.
	 * Displays a page allowing editing of an existing location.
     *
     * @todo auth and permission check
     */
    public function edit($id = null)
    {
        if ($id == null)
        {
            // we need a general error controller
            print "cannot edit location without an ID";
        }
        else
        {
            $this->model = new Location_Model($id);
            $this->setView('location/location_edit', 'Location');
        }
    }

}

?>
