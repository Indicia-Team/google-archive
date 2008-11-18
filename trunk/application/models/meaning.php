<?php defined('SYSPATH') or die('No direct script access.');

class Meaning_Model extends ORM {

	protected $has_many = array(
			'terms'
		);

	public function validate(Validation $array, $save = FALSE){
		return parent::validate($array, $save);
	}

}
