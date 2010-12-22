<?php
class TimezoneHelper
{
	private $readableTimezones = array(
		"(GMT-12:00) International Date Line West",
		"(GMT-11:00) Midway Island, Samoa",
		"(GMT-10:00) Hawaii",
		"(GMT-09:00) Alaska",
		"(GMT-08:00) Pacific Time (US & Canada)",
		"(GMT-07:00) Mountain Time (US & Canada)",
		"(GMT-06:00) Central Time (US & Canada)",
		"(GMT-05:00) Eastern Time (US & Canada)",
		"(GMT-04:30) Caracas",
		"(GMT-04:00) Santiago",
		"(GMT-03:30) Newfoundland",
		"(GMT-03:00) Buenos Aires",
		"(GMT-02:00) Mid-Atlantic",
		"(GMT-01:00) Cape Verde Is.",
		"(GMT+00:00) Greenwich Mean Time: London, Dublin",
		"(GMT+01:00) Amsterdam, Berlin, Rome, Vienna",
		"(GMT+02:00) Athens, Bucharest, Istanbul",
		"(GMT+03:00) Baghdad",
		"(GMT+03:30) Tehran",
		"(GMT+04:00) Caucasus Standard Time",
		"(GMT+04:30) Kabul",
		"(GMT+05:00) Islamabad, Karachi",
		"(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi",
		"(GMT+05:45) Kathmandu",
		"(GMT+06:00) Almaty, Novosibirsk",
		"(GMT+06:30) Yangon (Rangoon)",
		"(GMT+07:00) Bangkok, Hanoi, Jakarta",
		"(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
		"(GMT+09:00) Osaka, Sapporo, Tokyo",
		"(GMT+09:30) Darwin",
		"(GMT+10:00) Brisbane",
		"(GMT+11:00) Magadan, Solomon Is., New Caledonia",
		"(GMT+12:00) Fiji, Kamchatka, Marshall Is.",
		"(GMT+13:00) Nuku'alofa"
	);
	
	private $processedTimezones = array();
	
	function __construct()
	{
		
	}
	
	function processTimezones()
	{
		$tzAbbr = DateTimeZone::listAbbreviations();
	    $tempList = array();
	    foreach($tzAbbr as $tz)
	    {
	    	foreach($tz as $tza)
	    	{
	    		if($tza['timezone_id'] != '' && !in_array($tza['offset']/3600, $tempList))
	    		{
			    	$tempList[$tza['timezone_id']] = $tza['offset']/3600;
	    		}
	    	}
	    }
	    
		$listed = array();
		foreach($this->readableTimezones as $timezone)
		{
			$temp = explode(") ", $timezone);
			$num = str_replace('(GMT', '', $temp[0]);
			$tempIn = array($num, $temp[1], $this->timeToSeconds($num)/3600);
			foreach($tempList as $key=>$listB)
			{
				if($listB == $tempIn[2])
				{
					$tempIn[3] = $key;
					$tempIn[4] = $timezone;
					$listed[] = $tempIn;
				}else{
					continue;
				}
			}
		}
		
		$output = array();
		foreach($listed as $item)
		{
			$temp = $this->createBaseObject();
			$temp->GMTDiff = $item[0];
			$temp->GMTLabel = $item[4];
			$temp->Label = $item[1];
			$temp->PHPTimezone = $item[3];
			$temp->HourIntOffset = $item[2];
			$output[] = $temp;
		}
		
		$this->processedTimezones = $output;
	}
	
	function getTimezones()
	{
		$this->processTimezones();
		return $this->processedTimezones;
	}
	
	function createBaseObject()
	{
		$temp = new stdClass();
		$temp->GMTDiff = '+00:00';
		$temp->GMTLabel = '(GMT+00:00) [Timezone Not Set]';
		$temp->Label = '[Timezone Not Set]';
		$temp->PHPTimezone = 'America/Scoresbysund';
		$temp->HourIntOffset = '0';
		return $temp;
	}
	
	function getTimezoneByPHPTimezone($PHPTimezone)
	{
		foreach($this->getTimezones() as $tz)
		{
			if($tz->PHPTimezone == $PHPTimezone)
			{
				return $tz;
			}
		}
		return $this->createBaseObject();
	}
	
	function timeToSeconds($time='00:00'){
		$neg = 1;
		if(strpos($time, '-') !== false)
		{
			$neg = -1;
			$time = substr($time, 1);
		}
		list($hr,$m) = explode(':', $time);
		
		$output = ((int)$hr*3600 ) + ((int)$m*60);
		
		return $output * $neg;
	}
	
}
?>