<?php
/**
 * Class to parse a string containing a single date and extract the date.
 */
public class DateParser_Core {
	
	private $timeStamp;
	private $format;
	private $locale;
	
	// Set everything to null so we know what has actually been parsed.
	private $aResult = Array(
	        'tm_sec'   => null,
            'tm_min'   => null,
            'tm_hour'  => null,
            'tm_mday'  => null,
            'tm_mon'   => null,
            'tm_year'  => null,
            'tm_wday'  => null,
            'tm_yday'  => null,
			'tm_season' => null,
			'tm_century' => null,
            'unparsed' => null
        );
	
   /**
    * Constructs a date parser for a specific format. 
    */
    public function __construct($format){
    	$this->format = $format;
    	parent::__construct();    	
    }
    
    public function strptime($string){
    	$sFormat = $this->format;
    	  while($sFormat != "") {
            // ===== Search a %x element, Check the static string before the %x =====
            $nIdxFound = strpos($sFormat, '%');
            if($nIdxFound === false)
            {
                
                // There is no more format. Check the last static string.
                $this->aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
                break;
            }
            
            $sFormatBefore = substr($sFormat, 0, $nIdxFound);
            $sDateBefore   = substr($sDate,   0, $nIdxFound);
            
            if($sFormatBefore != $sDateBefore) break;
            
            // ===== Read the value of the %x found =====
            $sFormat = substr($sFormat, $nIdxFound);
            $sDate   = substr($sDate,   $nIdxFound);
            
            $this->aResult['unparsed'] = $sDate;
            
            $sFormatCurrent = substr($sFormat, 0, 2);
            $sFormatAfter   = substr($sFormat, 2);
            
            $nValue = -1;
            $sDateAfter = "";
            switch($sFormatCurrent)
            {
                case '%S': // Seconds after the minute (0-59)
                    
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                    
                    if(($nValue < 0) || ($nValue > 59)) return false;
                    
                    $this->aResult['tm_sec']  = $nValue;
                    break;
                
                // ----------
                case '%M': // Minutes after the hour (0-59)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                    
                    if(($nValue < 0) || ($nValue > 59)) return false;
                
                    $this->aResult['tm_min']  = $nValue;
                    break;
                
                // ----------
                case '%H': // Hour since midnight (0-23)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                    
                    if(($nValue < 0) || ($nValue > 23)) return false;
                
                    $this->aResult['tm_hour']  = $nValue;
                    break;
                
                // ----------
                case '%d': // Day of the month (1-31)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                    
                    if(($nValue < 1) || ($nValue > 31)) return false;
                
                    $this->aResult['tm_mday']  = $nValue;
                    break;
                
                // ----------
                case '%m': // Months since January (0-11)
                    sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                    
                    if(($nValue < 1) || ($nValue > 12)) return false;
                
                    $this->aResult['tm_mon']  = ($nValue - 1);
                    break;
                
                // ----------
                case '%Y': // Year
                    sscanf($sDate, "%4d%[^\\n]", $nValue, $sDateAfter);
                                  
                    $this->aResult['tm_year']  = ($nValue);
                    break;
                // ----------
                case '%y': // 2digit year
                	sscanf($dDate, "%2d%[^\\n]", $nValue, $sDateAfter);
                	
                	if ($nValue <= strftime("%y") {
                		// This century.
                		$nValue = strftime("%C").$nValue;
                	} else {
                		// Last century.
                		$nValue = (strftime("%C") - 1).$nValue;
                		
                	}
                	
                	$this->aResult['tm_year'] = $nValue;
                	break;                
                // ----------
                case '%A': // Full weekday
                	// sscanf isn't powerful enough for this.
                	// Get locale specific day names
                	for ($i = 0; $i < 7, $i++){
                		$weekdays[strtolower(kohana::lang('days')[i])] = i;
                		$dayStr .= ($i == 0) ? kohana::lang('days')[i] : "|".kohana::lang('days')[i];
                	}
                	$a = eregi("/(".$dayStr.")(.*)/",$sDate,$refs);
                	if ($a){
                		$nValue = $weekdays[strtolower($refs[1])];                		
                		$this->aResult['tm_wday'] = $nValue;
                		$dateAfter = $refs[2];
                	} else { 
                		return false;
                	}
                	break;
                case '%a': // Abbreviated weekday according to current locale
                            	// sscanf isn't powerful enough for this.
                	// Get locale specific day names
                	for ($i = 0; $i < 7, $i++){
                		$weekdays[strtolower(Kohana::lang('abbrDays')[i])] = i;
                		$dayStr .= ($i == 0) ? Kohana::lang('abbrDays')[i] : "|".Kohana::lang('abbrDays')[i];
                	}
                	$a = eregi("/(".$dayStr.")(.*)/",$sDate,$refs);
                	if ($a){
                		$nValue = $weekdays[strtolower($refs[1])];                		
                		$this->aResult['tm_wday'] = $nValue;
                		$dateAfter = $refs[2];
                	} else { 
                		return false;
                	}
                	break;
                case '$e': // Day of the month as decimal number, single digit preceeded by a space.
                	sscanf($sDate, "%d%[^\\n]", $nValue, $dateAfter);
                	
                	if(($nValue < 1) || ($nValue > 31)) return false;
					$this->aResult['tm_mday']  = $nValue;
					break;
                case '%B': // Full month according to current locale.
                                      	// sscanf isn't powerful enough for this.
                	// Get locale specific day names
                	for ($i = 0; $i < 12, $i++){
                		$weekdays[strtolower(Kohana::lang('months')[i])] = i;
                		$dayStr .= ($i == 0) ? Kohana::lang('dates.months')[i] : "|".Kohana::lang('dates.months')[i];
                	}
                	$a = eregi("/(".$dayStr.")(.*)/",$sDate,$refs);
                	if ($a){
                		$nValue = $weekdays[strtolower($refs[1])];                		
                		$this->aResult['tm_month'] = $nValue;
                		$dateAfter = $refs[2];
                	} else { 
                		return false;
                	}
                	break
                case '%b': // Abbreviated month according to current locale.
                               	// sscanf isn't powerful enough for this.
                	// Get locale specific day names
                	for ($i = 0; $i < 12, $i++){
                		$weekdays[strtolower(Kohana::lang('dates.abbrMonths')[i])] = i;
                		$dayStr .= ($i == 0) ? Kohana::lang('dates.abbrMonths')[i] : "|".Kohana::lang('dates.abbrMonths')[i];
                	}
                	$a = eregi("/(".$dayStr.")(.*)/",$sDate,$refs);
                	if ($a){
                		$nValue = $weekdays[strtolower($refs[1])];                		
                		$this->aResult['tm_month'] = $nValue;
                		$dateAfter = $refs[2];
                	} else { 
                		return false;
                	}
                	break;
                case '%K': // Season
                	// Get locale specific season names
                	$first = true;
                	foreach (Kohana::lang('dates.seasons') as $season) {
                		$sRegex .= ($first) ? $season : "|".$season;
                		$first = false; 
                	}
                	$a = eregi("/(".$sRegex.")(.*)/", $sDate, $refs);
                	if ($a){
                		$nValue = strtolower($refs[1]);
                		$this->aResult['tm_season'] = $nValue;
                		$dateAfter = $refs[2];
                	} else {
                		return false;
                	}
                	break;
                case '%k': // Season (short form) in year
                	break;
                case '%C': // Century
                	//Use a regex for this
                	$a = eregi("/c?(\d{1,2})(th|st|nd)?(.*)/", $sDate, $refs);
                	if ($a) {
                		$nValue = $refs[1];
                		$this->aResult['tm_century'] = $nValue;
                		$dateAfter = $refs[3];
                	} else {
                		return false;
                	}
                default: break 2; // Break Switch and while
            }
            
            // ===== Next please =====
            $sFormat = $sFormatAfter;
            $sDate   = $sDateAfter;
            
            $this->aResult['unparsed'] = $sDate;
            
        } // END while($sFormat != "") 
    }
    
    public function getIsoDate(){
    	if ($this->aResult['tm_year'] == null) return null;
    	$y = $this->aResult['tm_year'];
    	$ret = $y;
   		if ($m = $this->aResult['tm_month'] != null){
   				$m += 1;
   				$ret .= "-".$m;
   				if ($d = $this->aResult['tm_day'] != null){
   					$ret .= "-".$d;
   				}
   		}
		return $ret;
    }
	
	
}
?>