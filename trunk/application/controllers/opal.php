<?php

class Opal_Controller extends Template_Controller {
	public function __construct() {
		parent::__construct();
		$this->db = Database::instance();
	}
}
