<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Language page controller
 *
 *
 * @package Indicia
 * @subpackage Controller
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author xxxxxxx <xxx@xxx.net> / $Author$
 * @copyright xxxx
 * @version $Rev$ / $LastChangedDate$
 */
class Language_Controller extends Gridview_Base_Controller {

	/**
     * Constructor
     */
	public function __construct()
	{
		parent::__construct('language', 'language', 'language/index');
		$this->columns = array(
			'iso'=>'',
			'language'=>'');
		$this->pagetitle = "Languages";
		$this->model = ORM::factory('language');
	}

	/**
	 * Action for language/create page/
	 * Displays a page allowing entry of a new language.
	 */
	public function create()
	{
		$this->model = ORM::factory('language');
        $this->setView('language/language_edit', 'Website');
	}

    /**
     * Action for language/edit page
     * Edit website data
     */
	public function edit($id) {
		if ($id == null)
        {
            // we need a general error controller
            print "cannot edit language without an ID";
        }
        else
        {
            $this->model = new Language_Model($id);
            $this->setView('language/language_edit', 'Website');
        }
	}
}
?>
