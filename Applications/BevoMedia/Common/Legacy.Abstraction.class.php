<?php
if(!defined('PATH'))
{
    define('PATH', dirname(__FILE__));
}


require_once(str_replace('\\', '/', PATH). '../../../Components/Mail/Mail.Component.php');
require_once('constants.php');
ini_set('register_globals', '0');
define('SCRIPT_WEBMASTER',			'ryan@bevomedia.com');
define('SCRIPT_DOMAIN',			'http://beta.bevomedia.com/');
define('SCRIPT_ROOT',				'/Themes/BevoMedia/');
define('SCRIPT_DOLLAR',			'$');
define('SCRIPT_DOLLAR_NAME',		'USD');
define('SCRIPT_TIMEZONE',			-5);
define('SCRIPT_TIMEZONE_NAME',		'EST');

$abs_ini = parse_ini_file(PATH . '..' . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.ini', true);


$Mode = $abs_ini['Application']['Mode'];

define('DATABASE_URL', $abs_ini['Database/'.$Mode]['Host']);
define('DATABASE_USERID', $abs_ini['Database/'.$Mode]['User']);
define('DATABASE_PASSWORD', $abs_ini['Database/'.$Mode]['Pass']);
define('DATABASE_NAME', $abs_ini['Database/'.$Mode]['Name']);

define('PREFIX',					'adpalace_');
require_once(PATH . 'clsDB.php');
require_once(PATH . 'clsDBObject.php');
class LegacyAbstraction {
    public static $strDateRangeVal;
    public static $strStartDateVal;
    public static $strEndDateVal;
    public static $connection;
    public static function ParseDateRange()
    {
        if(empty(self::$strDateRangeVal)) {
            self::$strStartDateVal = date('n/d/Y', time() - (60*60*24));
            self::$strEndDateVal = date('n/d/Y', time());
            self::$strDateRangeVal = self::$strStartDateVal.' - '.self::$strEndDateVal;
            return self::$strDateRangeVal;
        }
        if(strpos(self::$strDateRangeVal, '-') === false)
        {
            self::$strStartDateVal = self::$strDateRangeVal;
            self::$strEndDateVal = self::$strDateRangeVal;
            return self::$strDateRangeVal;
        }
        else
        {
            $arrDate = explode('-', self::$strDateRangeVal);
            self::$strStartDateVal = trim($arrDate[0]);
            self::$strEndDateVal = trim($arrDate[1]);
            return self::$strDateRangeVal;
        }
    }
    public static function doAction($strInAction, $strInFunc) {
		if(!isset($_GET['Action']))
			return false;
			
		$strAction = $_GET['Action'];
		
		if (strtoupper($strAction) == strtoupper($strInAction)) {
			if (is_callable($strInFunc)) {
				call_user_func($strInFunc);
			}
		}
	}
    public static function FriendlyDateDiff($strInDate) {
		$strNow = date('Y-m-d H:i:s');
		$intDiff = strtotime($strNow) - strtotime($strInDate);
		
		$intDiffInDays = $intDiff/(60*60*24);
		$intDiffInHours = $intDiff/(60*60);
		$intDiffInMins = $intDiff/(60);
		
		$strDiff = $intDiff . ' Seconds';
		
		if ($intDiffInMins > 1) {
			$strDiff = floor($intDiffInMins) . ' Minutes';
		}
		
		if ($intDiffInHours > 1) {
			$strDiff = floor($intDiffInHours) . ' Hours';
		}
		
		if ($intDiffInDays > 1) {
			$strDiff = floor($intDiffInDays) . ' Days';
		}
		
		if ($intDiffInDays > 30) {
			return date('j M Y h:ia', strtotime($strInDate));
		}
		return $strDiff . ' Ago';
	}
    
    public static function addDays($date, $increment=30)
    {
        if ( trim($date) == "" || $date == "0000-00-00" || $date == "0000-00-00 00:00:00" )
            return 0;


        // In Format YYYY-MM-DD
        $year	= substr($date,0,4);
        $month	= substr($date,5,2);
        $day	= substr($date,8,2);

        // Out Format YYYY-MM-DD
        return date("Y-m-d", mktime(0, 0, 0, $month, $day + ($increment), $year));
    }

    public static function executeQuery($qry, $isEcho=0, $isCritical=1)
    {
        // Used to time each query
        if ( DO_TIME_EACH_QUERY == 1 )
            $mySqlStart = getMicroTime();

        // Execute Query
        if ( $isCritical == 1 )
        {
            $sqlResult = mysql_query($qry)
                or die ("<font face='verdana' color='red' size='1'>Could not execute query</font><br><br>".(DO_SHOW_ERRORS==1?" [".$qry."] - ".mysql_error():""));
        }
        else
        {
            $sqlResult  = mysql_query($qry)
                or $err = ("<font face='verdana' color='red' size='1'>Could not execute query</font><br><br>".(DO_SHOW_ERRORS==1?" [".$qry."] - ".mysql_error():""));
        }

        if ( isset($err) && $err != '' )
            echo $err;

        if ( $isEcho == 1 )
            echo $qry;


        if ( DO_TIME_EACH_QUERY == 1 )
        {
            $mySqlEnd	= getMicroTime();
            $tDiff		= ($mySqlEnd - $mySqlStart);
            printf("\n\n<!--\n\n %0.6f seconds \n %s \n\n-->\n\n", $tDiff, $qry);
        }

        // Return Result
        return $sqlResult;
    }

   	public static function makePagesEx($pageName, $locJS='')
	{
		global $t, $x, $pageSize;

		$numOfPages = ceil($t/$pageSize);

		if ( $numOfPages == 0 )
			$numOfPages = 1;

		$curPage    = (ceil($x/$pageSize)+1);

		//$page = "<center><font class='small'><b>".VIEW_PAGE.":</b> ";
		$page = "<center><font class='small'><b>". "View Page" .":</b> ";
		
		$startPage = $curPage-($curPage>$numOfPages-6?6-($numOfPages-$curPage+1):0)-5;
		$startPage = $startPage<1?1:$startPage;
		$endPage = $curPage+($curPage<6?6-$curPage:0)+5;
		$endPage = $endPage>$numOfPages?$numOfPages:$endPage;

		if ( $numOfPages >= 1 )
		{
			$y=($startPage-1)*$pageSize;
			for ( $i=$startPage; $i<=$endPage; $i++ )
			{
				if ( $i == $curPage )
					$page .= $i.($i==$endPage?"":" | ");
				else
					$page .= "<a href='#' onclick=\"location.href='".$pageName."t=".$t."&x=".($y)."'".($locJS==""?"":"+".$locJS)."\">".$i."</a>".($i==$endPage?"":" | ");

				$y+=$pageSize;
			}
		} // if ends here //

		$page .= "</font></center>";

		return $page;
	}

	public static function removeQueryStringVar($query, $var)
	{
		$ret			= '';
		$qsVars			= explode('&', urldecode($query));
		foreach ( $qsVars as $qsVar )
		{
			$couple		= explode('=', $qsVar);

			if ( trim($couple[0]) == $var || trim($couple[0]) == '' ) continue;

			$ret	.= $couple[0].'='.$couple[1].'&';
		}

		return $ret;
	}


    public static function openConnection()
    {
        // Create Connection
        self::$connection = mysql_connect(DATABASE_URL, DATABASE_USERID, DATABASE_PASSWORD)
            or die ("Could not connect to database (".$qry.") - ".mysql_error());

        // Select Database
        $db = mysql_select_db(DATABASE_NAME, self::connection)
            or die ("Could not select database (".$qry.") - ".mysql_error());

        return $connection;
    }

    public static function getRow($result)
    {
        @$row = mysql_fetch_assoc($result);
        return $row;
    }
    public static function getColumn($table, $id, $colRet="TITLE", $col="ID")
    {
        $result = self::executeQuery("Select ".$colRet." from ".'bevomedia_'."$table WHERE $col = '$id'");
        if ( $row = self::getRow($result) )
        {
            return $row[$colRet];
        }
        return 0;
    }
    public static function getRecord($table,$id)
    {
    	$col = 'id';
        if ( $id == '' )
            return;
        $result = self::executeQuery("Select * from bevomedia_$table WHERE $col = '$id'");
        if ( $row = self::getRow($result) )
        {
            return $row;
        }
        return 0;
    }
    public static function free($result)
    {
        @mysql_free_result($result);
    }
    public static function getTotalRows($result)
    {
        $total = mysql_num_rows($result);
        return $total;
    }
    public static function makeGetArray($arr, $name)
    {
        $ret = '';

        if ( !is_array($arr) || count($arr) == 0 )
            return '';

        else if ( $name == '' )
            return '';

        else
        {
            foreach ( $arr as $val )
            {
                if ( $val != '' )
                    $ret .= '&'.urlencode($name).'='.$val;
            }
        }

        return $ret;
    }
    public static function divideEx($op1, $op2)
    {
        if ( $op2 != 0 )
            return $op1 / $op2;
        return $op1;
    }
    public static function getcountrylist($id)
    {
        $string='';
        $sql="SELECT * FROM bevomedia_mcountry_offers WHERE offer__id=".$id;
        $res = self::executeQuery($sql);
        while($row = self::getRow($res) )
        {
            $countryid=$row['mCountry__id'];
            $sql2="SELECT * FROM bevomedia_mcountry WHERE id=".$countryid;
            $res2 = self::executeQuery($sql2);
            if ( $row = self::getRow($res2) )
            {
                if( $row['mCountry']!='N/A' )
                {
                    $string.=$row['mCountry'];
                    $string.=',';
                }
            }
        }
        return $string;
    }
    public static function getcategorylist($id)
    {
        $string='';
        $sql="SELECT * FROM bevomedia_mcategorie_offers WHERE offer__id=".$id;
        $res = self::executeQuery($sql);
        while($row = self::getRow($res) )
        {
            $countryid=$row['mCategorie__id'];
            $sql2="SELECT * FROM bevomedia_mcategorie WHERE id=".$countryid;
            $res2 = self::executeQuery($sql2);
            if ( $row = self::getRow($res2) )
            {
                if($row['mCategorie'] !='N/A' )
                {
                    $string.=$row['mCategorie'];
                    $string.=',';
                }
            }
        }
        return $string;
    }
    public static function getPartialString($str, $max=150, $link='')
    {
        $retVal = "";

        if ( strlen($str) > ($max+3) )
        {
            $sPos = strpos($str, " ", $max);

            if ( $sPos )
                $retVal .= substr($str, 0, $sPos )."...";
            else
                $retVal .= substr($str, 0, 150 )."...";

            if ( $link != '' )
                $retVal .= " <a href=\"".$link."\" style='font-size:10px'>".READ_MORE."</a>";
        }
        else
            $retVal .= $str ;

        return $retVal;
    }
    public static function breakPages($qry, $qryTotal='')
    {
        global $t, $x, $pageSize;
        if(!$pageSize)
            $pageSize = 20;

        @$t	= $_GET["t"];
        @$x	= $_GET["x"];

        // Do it for ORDER BY
        if ( @$_GET["sk"] != "" || @$_GET["so"] != "" )
        {
            $orderPos = strpos($qry, "ORDER BY");
            if ( $orderPos !== false )
                $qry = substr($qry, 0, $orderPos)." ORDER BY ".$_GET["sk"]." ".$_GET["so"];
            else
                $qry = $qry." ORDER BY ".$_GET["sk"]." ".$_GET["so"];
        }

        if ( $t == "" )
        {
            if ( $qryTotal == '' )
            {
                $result = self::executeQuery($qry);
                $t = self::getTotalRows($result);
                self::free($result);
            }
            else
            {
                $result = self::executeQuery($qryTotal);
                if ( $r = self::getRow($result) )
                    $t	= $r['TOTAL'];
                self::free($result);
            }
        }

        if ( $x == '' )
            $x = '0';
        if ( $x <= 0 )
            $x = 0;

        $qry	= $qry." Limit ".$x.", ".$pageSize;

        $result = self::executeQuery($qry);
        return $result;
    }
    public static function getComboBox($comboName, $table, $value, $title, $selected="", $where="", $orderBy="", $firstOption="", $javascript="")
    {
        $Q = "SELECT ".$value." AS ID, ".$title." AS TITLE FROM bevomedia_".$table;
        if ($where<>"")
            $Q .= " WHERE ".$where;
        if ($orderBy<>"")
            $Q .= " ORDER BY ".$orderBy;
        $CBresult = self::executeQuery($Q);
        $CBout = "<SELECT NAME=\"$comboName\" CLASS=\"effect\" $javascript>";
        if ($firstOption<>"")
            $CBout .= "<OPTION VALUE=\"\">$firstOption</OPTION>";
        while ( $CBrow = self::getRow($CBresult))
        {
            $CBout .= "<OPTION VALUE=\"".$CBrow["ID"]."\"";
            if ($CBrow["ID"]==$selected)
                $CBout .= " SELECTED";
            $CBout .= ">".$CBrow["TITLE"]."</OPTION>";
        }
        $CBout .= "</SELECT>";
        return $CBout;
    }
    public static function getFormattedPrice($price, $decimals=2)
    {
        global $currencySymbol, $currencyRate;
        if(!isset($currencyRate))
        {
        	$currencyRate = 1;
        }
        if ( IS_CURRENCY_AFTER_PRICE )
            return number_format($currencyRate*$price, $decimals).''.$currencySymbol;
        else
            return $currencySymbol.''.number_format($currencyRate*$price, $decimals);
    }
    public static function handleSingleQuote($str)
    {
        if ( strlen($str) > 0 )
        {
            // First remove already escaped
            $str = str_replace("\\'",	"'",  $str);

            $str = str_replace("'",		"''", $str);
            return trim($str);
        }
        else
            return $str;
    }
    public static function makePages($pageName)
    {
        global $t, $x, $pageSize;
        $numOfPages = ceil($t/$pageSize);
        if ( $numOfPages == 0 )
            $numOfPages = 1;
        $curPage    = (ceil($x/$pageSize)+1);
        $page = "<center><font class='small'>";
        $startPage = $curPage-($curPage>$numOfPages-6?6-($numOfPages-$curPage+1):0)-5;
        $startPage = $startPage<1?1:$startPage;
        $endPage = $curPage+($curPage<6?6-$curPage:0)+5;
        $endPage = $endPage>$numOfPages?$numOfPages:$endPage;
        if ( $numOfPages > 1 )
        {
            $page .= "<br>";

            if ( $curPage == 1 )
                $page .= "Prev | ";
            else
                $page .= "<a href='".$pageName."t=".$t."&x=".($x-$pageSize)."'>Prev</a> | ";

            $y=($startPage-1)*$pageSize;
            for ( $i=$startPage; $i<=$endPage; $i++ )
            {
                if ( $i == $curPage )
                    $page .= $i." | ";
                else
                    $page .= "<a href='".$pageName."t=".$t."&x=".($y)."'>".$i."</a> | ";

                $y+=$pageSize;
            }

            if ( $curPage == $numOfPages )
                $page .= "Next";
            else
                $page .= "<a href='".$pageName."t=".$t."&x=".($x+$pageSize)."'>Next</a>";

        }
        $page .= "<br><input type='text' name='pgno' size='3' value='".$curPage."' class='effect' style='text-align:center' onkeypress=\"if(event.keyCode==13){location.href='".$pageName."t=".$t."&x='+((pgno.value-1)*".$pageSize.")}\">";
        $page .= "<br><br>Page ".$curPage." of ".$numOfPages."<br><b>Total Record(s) : $t</b><br><br></font></center>";
        return $page;
    }
    public static function getFormattedPriceEx($price, $decimals=2)
    {
        global $DOLLAR;
        if ( IS_CURRENCY_AFTER_PRICE )
            return number_format($price, $decimals).''.$DOLLAR;
        else
            return $DOLLAR.''.number_format($price, $decimals);
    }
}
if(isset($_GET['DateRange']))
    LegacyAbstraction::$strDateRangeVal = $_GET['DateRange'];
LegacyAbstraction::ParseDateRange();
header('x-http-start-date: ' . LegacyAbstraction::$strStartDateVal);
header('x-http-end-date: ' . LegacyAbstraction::$strEndDateVal);
?>