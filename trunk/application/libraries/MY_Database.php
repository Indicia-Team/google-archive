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
		$null_value = false;
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
					$null_value = true;
				}
				else
				{
					$escaped_values[] = "'".$this->driver->escape_str($v)."'";
				}
			}
			$values = implode(",", $escaped_values);
		}
		if ($not === FALSE){
			if (!$values =='')
				// there is a valid list, and a possible null value.
				$this->where('('.$this->driver->escape_column($field).' IN ('.$values.')'.($null_value ? ' OR '.$this->driver->escape_column($field).' IS NULL)' : ')'));
			else
				// List is empty (originally values contained an array of nothing or just entry "null")
				// If original list was completely empty, need to force nothing returned.
				$this->where($null_value ? $this->driver->escape_column($field).' IS NULL' : "'t' = 'f'");
		} else {
			// not is true
			if (!$values =='')
				// there is a valid list, and a possible null value. Note need to use AND rather than OR
				$this->where('('.$this->driver->escape_column($field).' NOT IN ('.$values.')'.($null_value ? ' AND '.$this->driver->escape_column($field).' IS NOT NULL)' : ')'));
			else if ($null_value)
				// Originally values only contained an array of just the entry "null"
				$this->where($this->driver->escape_column($field).' IS NOT NULL');
			// If original values list was completely empty, then don't need to add any where clause for NOT IN
		}
		
		return $this;
	}
}
