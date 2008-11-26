<?php

class Validation_Controller extends Service_Base_Controller {

	/**
	 * Service at URL services/validation/valid_term. Tests if a term can be found
	 * on the termlist identified by the supplied id in $_GET.
	 */
	public function valid_term()
	{
		$this->valid_term_or_taxon('termlist_id', 'term', 'gv_termlists_term');
	}

	/**
	 * Service at URL services/validation/valid_taxon. Tests if a taxon can be found
	 * on the taxon list identified by the supplied id in $_GET.
	 */
	public function valid_taxon()
	{
		$this->valid_term_or_taxon('taxon_list_id', 'taxon', 'gv_taxon_lists_taxon');
	}

	/**
	 * Internal method that provides functionality for validating a term or taxon
	 * against a list.
	 */
	protected function valid_term_or_taxon($list_id, $search_field, $view_name)
	{
		if (array_key_exists($list_id, $_GET))
			$termlist_id = $_GET[$list_id];
		else
			$this->error("No $list_id supplied to validate the term against");
		if (array_key_exists($search_field, $_GET))
			$term = $_GET[$search_field];
		else
			$this->error("No $search_field supplied to validate");
		$mode = $this->get_output_mode();
		$found=	ORM::factory($view_name)
				->where(array($list_id=>$termlist_id))
				->like(array($search_field=>$term))
				->find_all();
		// TODO - proper handling of output XML.
		// TODO - Only accept multiple entries as valid if a single match can be determined.
		if ($found->count()>1)
			$id['id'] = $found[0]->id; // Found a valid term, so return it's ID
		else
			$id['id'] = "not found"; // Return information that the term wasn't found
		switch ($mode) {
			case 'json':
				echo json_encode($id);
				break;
			case 'xml':
				//echo $this->xml_encode($id, TRUE);
				break;
		}
	}

}
?>
