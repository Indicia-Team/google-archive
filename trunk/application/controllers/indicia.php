<?php

class Indicia_Controller extends Template_Controller {
	public function __construct() {
		parent::__construct();
		$this->db = Database::instance();

		$this->template->links = array
		(
			'Home' => 'home',
			'Term Lists' => 'termlist',
		);
	}
}
