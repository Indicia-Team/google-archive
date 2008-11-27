<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Website page controller
 *
 *
 * @package Indicia
 * @subpackage Controller
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author xxxxxxx <xxx@xxx.net> / $Author$
 * @copyright xxxx
 * @version $Rev$ / $LastChangedDate$
 */
class Website_Controller extends Gridview_Base_Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('website', 'website', 'website/index');

        $this->columns = array('title'       =>'',
                               'description' =>'',
                               'url' =>'' );

        $this->pagetitle = "Websites";
		$this->model = ORM::factory('website');
    }

    /**
     * Action for website/create page.
     * Displays a page allowing entry of a new website.
     *
     * @todo auth and permission check
     *       May you should rename this methode to "new". I find "create" a bit confusing
     *       because this methode create nothing but just loads a view with empty form fields
     */
    public function create()
    {
        $this->model = ORM::factory('website');
        $this->setView('website/website_edit', 'Website');
    }

    /**
     * Edit website data
     *
     * @todo auth and permission check
     */
    public function edit($id = null)
    {
        if ($id == null)
        {
            // we need a general error controller
            print "cannot edit website without an ID";
        }
        else
        {
            $this->model = new Website_Model($id);
            $this->setView('website/website_edit', 'Website');
        }
    }

    /**
     * Save website form data
     *
     * @todo auth and permission check
     */
    public function save_old()
    {
        // only proceed if a website id exists
        if ( isset($_POST['id']) )
        {
            $this->model = new Website_Model( $_POST['id'] );
        }
        else
        {
            // undefined website id
            // usually this should never be the case
            // we need a general error controller for such cases (exception handling?)
            url::redirect('website');
        }

        if ($this->model->saveData( $_POST ))
        {
            // on success reload the 'website' controller
            url::redirect('website');
        }
        else
        {
            // if some thing is going wrong we set the view to the same page
            $this->setView('website/website_edit', 'Website');
        }
    }
}

?>
