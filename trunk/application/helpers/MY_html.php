<?php defined('SYSPATH') or die('No direct script access.');

class html extends html_Core {

 	/* Outputs an error message in a span, but only if there is something to output */
	public static function error_message($message)
	{
		if ($message) echo '<span class="form_error">'.$message.'</span>';
	}

}
?>