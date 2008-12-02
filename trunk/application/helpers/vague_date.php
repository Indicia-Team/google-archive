<?php

class vague_date {

	/**
	 * List of regex strings used to try to capture date ranges. The regex key should, naturally,
	 * point to the regular expression. Start should point to the backreference for the string to
	 * be parsed for the 'start' date, 'end' to the backreference of the string to be parsed
	 * for the 'end' date. Types are not determined here. Should either 'start' or 'end' contain
	 * the string '...', this will be interpreted as one-ended range.
	 */
	private $dateRangeStrings = Array(
		array(
			'regex' => '/([\d\w\s]*)(\sto\s|\s-\s)([\d\w\s]*)/', // date to date
			'start' => '1',
			'end' => '3'
		),
		array(
			'regex' => '/([Pp]re|[Bb]efore[\.]?)([\d\w\s]*)/',
			'start' => '...',
			'end' => '2'
		),
		array(
			'regex' => '/([Ff]rom|[Aa]fter)([\d\w\s]*)/',
			'start' => '2',
			'end' => '...'
		),
		array(
			'regex' => '/([\d\w\s]*)\s-/',
			'start' => '1',
			'end' => '...'
		),
	);

	private static $parseFormats = array_merge(
		self::singleDayFormats,
		self::singleMonthInYearFormats,
		self::singleMonthFormats,
		self::singleYearFormats,
	);

	/**
	 * Array of formats used to parse a string looking for a single day with the strptime()
	 * function - see http://uk2.php.net/manual/en/function.strptime.php
	 */
	private static $singleDayFormats = Array(
		'%Y-%m-%d', // ISO 8601 date format
		'%d/%m/%y', // UK style date format
		'%d/%m/%Y', // UK style date format (full year)
		'%A %e %B %Y', // Monday 12 October 1997
		'%a %e %B %Y', // Mon 12 October 1997
		'%A %e %b %Y', // Monday 12 Oct 1997
		'%a %e %b %Y', // Mon 12 Oct 1997
		'%A %e %B %y', // Monday 12 October 97
		'%a %e %B %y', // Mon 12 October 97
		'%A %e %b %y', // Monday 12 Oct 97
		'%a %e %b %y', // Mon 12 Oct 97
		'%A %e %B', // Monday 12 October
		'%a %e %B', // Mon 12 October 
		'%A %e %b', // Monday 12 Oct 
		'%a %e %b', // Mon 12 Oct 
		'%e %B %Y', // 12 October 1997
		'%e %b %Y', // 12 Oct 1997
		'%e %B %y', // 12 October 97
		'%e %b %y', // 12 Oct 97
		'%D', // American date format
		);

	/**
	 * Array of formats used to parse a string looking for a single month in a year
	 * with the strptime() function - see http://uk2.php.net/manual/en/function.strptime.php
	 */
	private $singleMonthInYearFormats = Array(
		'%Y-%m', // ISO 8601 format - truncated to month
		'%m/%y', // British style truncated
		'%m/%Y', // British style truncated - 4 digit year
		'%B %Y', // June 1998
		'%b %Y', // Jun 1998
		'%B %y', // June 98
		'%b %y', // Jun 98
	);

	private $singleMonthFormats = Array(
		'%B', // October
		'%b', // Oct
	);

	private $singleYearFormats = Array(
		'%Y', // 1998
		'%y', // 98
	);



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
		// Our approach shall be to gradually pare down from the most complex possible
		// dates to the simplest, and match as fast as possible to try to grab the most
		// information. First we consider the potential ways that a range may be
		// represented.

		$range = false;
		$startDate = false;
		$endDate = false;
		$matched = false;
		$vagueDate = array(
			'start' => '',
			'end' => '',
			'type' => ''
		);

		foreach ($this->dateRangeStrings as $a) {
			if (ereg($a['regex'], $string, $regs) != false) {
				if ($a['start'] == '...'){
					$start = false;
				} else {
					$start = $regs[$a['start']];
				}
				if ($a['end'] == '...'){
					$end = false;
				} else {
					$end = $regs[$a['end']];
				}
				$range = true;
				break;
			}			
		}

		if (!range) {
			$a = parseSingleDate($string);
			if ($a != null) {
				$startDate = $endDate = $a;
				$matched = true;
			}
		} else {
			if ($start) {
				$a = parseSingleDate($start);
				if ($a != null) {
					$startDate = $a;
					$matched = true;
				}
			} 
			if ($end) {
				$a = parseSingleDate($end);
				if ($a != null) {
					$endDate = $a;
					$matched = true;
				}
			}
			if ($matched) {
				if ($start && !$end) {
					$endDate = $startDate;
				} else if ($end && !$start) {
					$startDate = $endDate;
				}
			}
								
		}

		if (!$matched) {
			return null;
		}

		// Okay, now we try to determine the type - we look mostly at $endDate because
		// this is more likely to contain more info e.g. 15 - 18 August 2008

		// Do we have day precision?

		if ($endDate['tm_mday'] != 0) {
			// Yay! We have day precision
			if (!$range) {
				$year = 1900 + $endDate['tm_year'];
				$month = 1 + $endDate['tm_month'];
				$day = $endDate['tm_mday'];
				$date = $year."-".$month."-".$day;

				$vagueDate = array(
					'start' => $date,
					'end' => $end,
					'type' => 'D'
				);
			} else {
				// Do we have the precision in the start date?
			}			

		}
	

	}

	/**
	 * Parses a single date from a string.
	 */
	protected static function parseSingleDate($string){
		$parsedDate = null;

		foreach (self::parseFormats as $a){
			if ($b = strptime($string, $a) != false){
				$parsedDate = $b;
				break;
			}
		}

		return $parsedDate;
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
