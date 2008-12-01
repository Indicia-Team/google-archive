<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Sample Model
 *
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @version $Rev$ / $LastChangedDate$
 */
class Sample_Model extends ORM
{
	protected $has_many=array('occurrences');
	protected $belongs_to=array(
		'survey', 
		'location',
		'created_by'=>'user', 
		'updated_by'=>'user');	
	
}
?>
