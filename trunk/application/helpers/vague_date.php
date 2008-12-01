<?php

class vague_date {

	/**
	 * Convert a vague date in the form of array(start, end, type) to a string
	 */
	public static function vague_date_to_string($date)
	{
		$start=NULL;
		$end=NULL;
		if (!$date[0]==NULL)
			$start = $date[0];
		if (!$date[1]==NULL)
			$end = $date[1];
		$type = $date[2];
		self::validate($start, $end, $type);
		switch ($type) {
			case 'D': 	return self::vague_date_to_day($start, $end);
			case 'DD':  return self::vague_date_to_days($start, $end);
			case 'O':   return self::vague_date_to_month_in_year($start, $end);
			case 'OO':	return self::vague_date_to_months_in_year($start, $end);
			case 'P': 	return self::vague_date_to_season_in_year($start, $end);
			case 'Y':	return self::vague_date_to_year($start, $end);
			case 'YY':	return self::vague_date_to_years($start, $end);
			case 'Y-':	return self::vague_date_to_year_from($start, $end);
			case '-Y':	return self::vague_date_to_year_to($start, $end);
			case 'M':	return self::vague_date_to_month($start, $end);
			case 'S':	return self::vague_date_to_season($start, $end);
			case 'U':	return self::vague_date_to_unknown($start, $end);
			case 'C':	return self::vague_date_to_century($start, $end);
			case 'CC':	return self::vague_date_to_centuries($start, $end);
			case 'C-':	return self::vague_date_to_century_from($start, $end);
			case '-C':	return self::vague_date_to_century_to($start, $end);
		}
	}

	public static function string_to_vague_date($string)
	{

	}

	/**
	 * Convert a vague date to a string representing a fixed date.
	 */
	protected static function vague_date_to_day($start, $end)
	{
		self::check($start==$end, 'Day vague dates should have the same date for the start and end of the date range');
		return $start->format(Kohana::lang('dates.format'));
	}

	/**
	 * Convert a vague date to a string representing a range of days.
	 */
	protected static function vague_date_to_days($start, $end)
	{
		self::check($start<$end, 'Day ranges should be presented in vague dates in the correct sequence.');
		return 	$start->format(Kohana::lang('dates.format')).
				Kohana::lang('dates.range_separator').
				$end->format(Kohana::lang('dates.format'));
	}

	/**
	 * Convert a vague date to a string representing a fixed month.
	 */
	protected static function vague_date_to_month_in_year($start, $end)
	{
		self::check(self::is_month_start($start) && self::is_month_end($end) && self::is_same_month($start, $end),
				'Month dates should be represented by the first day and last day of the same month.');
		return $start->format(Kohana::lang('dates.format_m_y'));
	}

	/**
	 * Convert a vague date to a string representing a range of months.
	 */
	protected static function vague_date_to_months_in_year($start, $end)
	{
		self::check(self::is_month_start($start) && self::is_month_end($end) && $start<$end,
				'Month ranges should be represented by the first day of the first month and last day of the last month.');
		return 	$start->format(Kohana::lang('dates.format_m_y')).
				Kohana::lang('dates.range_separator').
				$end->format(Kohana::lang('dates.format_m_y'));
	}

	/*
	 * Convert a vague date to a string representing a season in a given year
	 */
	protected static function vague_date_to_season_in_year($start, $end)
	{
		return self::convert_to_season_string($start, $end).' '.$end->format('Y');
	}

	/**
	 * Convert a vague date to a string representing a year
	 */
	protected static function vague_date_to_year($start, $end)
	{
		self::check(self::is_year_start($start) && self::is_year_end($end) && self::is_same_year($start, $end),
				'Years should be represented by the first day and last day of the same year.');
		return $start->format('Y');
	}

	/**
	 * Convert a vague date to a string representing a range of years
	 */
	protected static function vague_date_to_years($start, $end)
	{
		self::check(self::is_year_start($start) && self::is_year_end($end) && $start<$end,
				'Year ranges should be represented by the first day of the first year to the last day of the last year.');
		return $start->format('Y').Kohana::lang('dates.range_separator').$end->format('Y');
	}

	/**
	 * Convert a vague date to a string representing any date after a given year
	 */
	protected static function vague_date_to_year_from($start, $end)
	{
		self::check(self::is_year_start($start) && $end===null,
				'From year date should be represented by just the first day of the first year.');
		return sprintf(Kohana::lang('dates.from_date'), $start->format('Y'));
	}

	/**
	 * Convert a vague date to a string representing any date up to and including a given year
	 */
	protected static function vague_date_to_year_to($start, $end)
	{
		self::check($start===null && self::is_year_end($end),
				'To year date should be represented by just the last day of the last year.');
		return sprintf(Kohana::lang('dates.to_date'), $end->format('Y'));
	}

	/**
	 * Convert a vague date to a string representing a month in an unkown year
	 */
	protected static function vague_date_to_month($start, $end)
	{
		self::check(self::is_month_start($start) && self::is_month_end($end) && self::is_same_month($start, $end),
				'Month dates should be represented by the start and end of the month.');
		return $start->format('F');
	}

	/*
	 * Convert a vague date to a string representing a season in an unknown year
	 */
	protected static function vague_date_to_season($start, $end)
	{
		return self::convert_to_season_string($start, $end);
	}

	/*
	 * Convert a vague date to a string representing an unknown date
	 */
	protected static function vague_date_to_unknown($start, $end)
	{
		self::check($start===null && $end===null,
				'Unknown dates should not have a start or end specified');
		return Kohana::lang('dates.unknown');
	}

	/*
	 * Convert a vague date to a string representing a century
	 */
	protected static function vague_date_to_century($start, $end)
	{
		self::check(self::is_century_start($start) && self::is_century_end($end) && self::is_same_century($start, $end),
				'Century dates should be represented by the first day and the last day of the century');
		return sprintf(Kohana::lang('dates.century', ($start->format('Y')-1)/100+1));
	}

	/*
	 * Convert a vague date to a string representing a century
	 */
	protected static function vague_date_to_centuries($start, $end)
	{
		self::check(self::is_century_start($start) && self::is_century_end($end) && $start<$end,
				'Century ranges should be represented by the first day of the first century and the last day of the last century');
		return 	sprintf(Kohana::lang('dates.century', ($start->format('Y')-1)/100+1)).
				Kohana::lang('dates.range_separator').
				sprintf(Kohana::lang('dates.century', ($end->format('Y')-1)/100+1));
	}

	/*
	 * Convert a vague date to a string representing a date during or after a specified century
	 */
	protected static function vague_date_to_century_from($start, $end)
	{
		self::check(self::is_century_start($start) && $end===null,
				'From Century dates should be represented by the first day of the century only');
		return sprintf(Kohana::lang('dates.from_date'), sprintf(Kohana::lang('dates.century', ($start->format('Y')-1)/100+1)));
	}

	/*
	 * Convert a vague date to a string representing a date before or during a specified century
	 */
	protected static function vague_date_to_century_to($start, $end)
	{
		self::check($start===null && self::is_century_end($end),
				'To Century dates should be represented by the last day of the century only');
		return sprintf(Kohana::lang('dates.to_date'), sprintf(Kohana::lang('dates.century', ($end->format('Y')-1)/100+1)));
	}


	/**
	 * Returns true if the supplied date is the first day of the month
	 */
	protected static function is_month_start($date)
	{
		return ($date->format('j')==1);
	}

	/**
	 * Returns true if the supplied date is the last day of the month
	 */
	protected static function is_month_end($date)
	{
		// format t gives us the last day of the given date's month
		return ($date->format('j')==$date->format('t'));
	}

	/**
	 * Returns true if the supplied dates are in the same month
	 */
	protected static function is_same_month($date1, $date2)
	{
		return ($date1->format('m')==$date2->format('m'));
	}

	/**
	 * Returns true if the supplied date is the first day of the year
	 */
	protected static function is_year_start($date)
	{
		return ($date->format('j')==1 && $date->format('m')==1);
	}

	/**
	 * Returns true if the supplied date is the last day of the year
	 */
	protected static function is_year_end($date)
	{
		return ($date->format('j')==31 && $date->format('m')==12);
	}

	/**
	 * Returns true if the supplied dates are in the same year
	 */
	protected static function is_same_year($date1, $date2)
	{
		return ($date1->format('y')==$date2->format('y'));
	}

	/**
	 * Returns true if the supplied date is the first day of the century (starts in year nn01!)
	 */
	protected static function is_century_start($date)
	{
		return ($date->format('j')==1 && $date->format('m')==1 && $date->format('y')==1);
	}

	/**
	 * Returns true if the supplied date is the last day of the century
	 */
	protected static function is_century_end($date)
	{
		return ($date->format('j')==31 && $date->format('m')==12 && $date->format('y')==0);
	}

	/**
	 * Returns true if the supplied dates are in the same century
	 */
	protected static function is_same_century($date1, $date2)
	{
		return floor(($date1->format('Y')-1)/100)==floor(($date2->format('Y')-1)/100);
	}

	/**
	 * Retrieve the string that describes a season (spring, summer, autumn, winter)
	 * for a start and end date.
	 */
	protected static function convert_to_season_string($start, $end)
	{
		self::check(self::is_month_start($start) && self::is_month_end($end),
			'Seasons should be represented by the start of the first month of the season, to the end of the last month.');
		// ensure the season spans 3 months.
		self::check( ($start->format('Y')*12 + $start->format('m') + 2)
					 ==
					 ($end->format('Y')*12 + $end->format('m')),
					 'Seasons should be 3 months long');
		switch ($start->format('m'))
		{
			case 3:
				return Kohana::lang('dates.seasons.spring');
			case 6:
				return Kohana::lang('dates.seasons.summer');
			case 9:
				return Kohana::lang('dates.seasons.autumn');
			case 12:
				return Kohana::lang('dates.seasons.winter');
			default:
				throw new Exception('Season date does not start on the month a known season starts on.');
		}
	}


	/**
	 * Ensure a vague date array is well-formed.
	 */
	protected static function validate($start, $end, $type)
	{

	}

	protected static function check($pass, $message)
	{
		if (!$pass)
			throw new Exception($message);
	}

}
?>
