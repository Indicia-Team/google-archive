<?php defined('SYSPATH') or die('No direct script access.');

class valid extends valid_Core {

	/**
	 * Validate a spatial reference system is recognised, either an EPSG code or a notation.
	 *
	 * @param   string   system
	 * @return  boolean
	 * $todo Should we consider caching this?
	 */
	public static function sref_system($system)
	{
		return spatial_ref::is_valid_system($system);
	}

	/**
	 * Validate a spatial reference is a valid value for the system
	 *
	 * @param   string   sref
	 * @param   string   system
	 * @return  boolean
	 * $todo Should we consider caching the system?
	 */
	public static function sref($sref, $system)
	{
		$system = $system[0];
		return spatial_ref::is_valid($sref, $system);
	}
	
	/**
	 * Validates that a specific date string can be correctly parsed into a vague date.
	 * 
	 * @param	string	SDate
	 */
	public static function vague_date($sDate){
		if (vague_date::string_to_vague_date != false){
			return true;
		}
		return false;
	}

	/**
	 * Validates that a value is unique across a table column, NULLs are ignored.
	 * When checking a new record, just count all records in DB. When Updating, count all
	 * records excluding the one we are updating.
	 * 
	 * @param	string	column Value
	 * @param   array   table name, table column, id of current record
	 * @return  boolean
	 */
	public static function unique($column_value, $args){
		$db = new Database();
		if ($args[2] == ''){
			$number_of_records = $db->count_records($args[0], array($args[1] => $column_value));
		} else {
			$number_of_records = $db->count_records($args[0], array($args[1] => $column_value, 'id !=' => $args[2]));
		}

		return ($number_of_records == 0);
	}

	/**
	 * Service at URL services/validation/valid_term. Tests if a term can be found
	 * on the termlist identified by the supplied id in $_GET.
	 */
	public function valid_term($term, $id)
	{
		$this->valid_term_or_taxon($taxon, $id,'termlist_id', 'term', 'gv_termlists_term');
	}

	/**
	 * Service at URL services/validation/valid_taxon. Tests if a taxon can be found
	 * on the taxon list identified by the supplied id in $_GET.
	 */
	public function valid_taxon($taxon, $id)
	{
		$this->valid_term_or_taxon($taxon, $id, 'taxon_list_id', 'taxon', 'gv_taxon_lists_taxa');
	}

	/**
	 * Internal method that provides functionality for validating a term or taxon
	 * against a list.
	 */
	protected function valid_term_or_taxon($value, $list_id, $list_id_field, $search_field, $view_name)
	{
		$found=	ORM::factory($view_name)
				->where(array($list_idi_field=>$list_id))
				->like(array($search_field=>$value))
				->find_all();
		// TODO - proper handling of output XML.
		// TODO - Only accept multiple entries as valid if a single match can be determined.
		return ($found->count()>1);
	}

}
?>
