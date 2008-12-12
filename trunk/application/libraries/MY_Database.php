<?php 
class Database extends Database_Core {
	/**
	 * Adds an "IN" condition to the where clause
	 *
	 * @param   string  Name of the column being examined
	 * @param   mixed   An array or string to match against
	 * @param   bool    Generate a NOT IN clause instead
	 * @return  Database_Core  This Database object.
	 */
	public function in($field, $values, $not = FALSE)
	{
		if (is_array($values))
		{
			$escaped_values = array();
			foreach ($values as $v)
			{
				if (is_numeric($v))
				{
					$escaped_values[] = $v;
				}
				else if ($v == null) {
					$escaped_values[] = 'null';
				}
				else
				{
					$escaped_values[] = "'".$this->driver->escape_str($v)."'";
				}
			}
			$values = implode(",", $escaped_values);
		}
		$this->where($this->driver->escape_column($field).' '.($not === TRUE ? 'NOT ' : '').'IN ('.$values.')');

		return $this;
	}

}

?>
