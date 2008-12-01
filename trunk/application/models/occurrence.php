<?php
/**
 * INDICIA
 * @link http://code.google.com/p/indicia/
 * @package Indicia
 */

/**
 * Occurrence Model
 *
 *
 * @package Indicia
 * @subpackage Model
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @version $Rev$ / $LastChangedDate$
 */
class Occurrence_Model extends ORM
{
	protected $belongs_to=array(
		'determiner',
		'sample',
		'taxon',
		'created_by'=>'user',
		'updated_by'=>'user');

}
?>