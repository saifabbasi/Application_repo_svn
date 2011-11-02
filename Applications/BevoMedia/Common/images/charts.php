<?php
// charts.php v4.5
// ------------------------------------------------------------------------
// Copyright (c) 2003-2005, maani.us
// ------------------------------------------------------------------------
// This file is part of "PHP/SWF Charts"
//
// PHP/SWF Charts is a shareware. See http://www.maani.us/charts/ for
// more information.
// ------------------------------------------------------------------------

	define("MAANI_LICENSE_KEY", "GTA9I-PM7Q.O.945CWK-2XOI1X0-7L");

// ------------------------------------------------------------------------

function InsertChart( $php_source, $width=400, $height=250, $bg_color="666666" )
{
	$html = "";
	$html .= "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	$html .= "<!--\n ";
	$html .= "if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {\n";
	$html .= "	alert(\"This page requires AC_RunActiveContent.js.\");\n";
	$html .= "} else {\n";
	$html .= "	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);\n";
	$html .= "	if(hasRightVersion) { \n";
	$html .= "		AC_FL_RunContent(\n";
	$html .= "			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,45,0',\n";
	$html .= "			'width', '".$width."',\n";
	$html .= "			'height', '".$height."',\n";
	$html .= "			'scale', 'noscale',\n";
	$html .= "			'salign', 'TL',\n";
	$html .= "			'bgcolor', '".$bg_color."',\n";
	$html .= "			'wmode', 'opaque',\n";
	$html .= "			'movie', '".SCRIPT_ROOT."images/charts',\n";
	$html .= "			'src', '".SCRIPT_ROOT."images/charts',\n";
	$html .= "			'FlashVars', 'library_path=".urlencode(SCRIPT_ROOT."images/charts_library")."&xml_source=".urlencode($php_source)."', \n";
	$html .= "			'id', 'my_chart',\n";
	$html .= "			'name', 'my_chart',\n";
	$html .= "			'menu', 'true',\n";
	$html .= "			'allowFullScreen', 'true',\n";
	$html .= "			'allowScriptAccess','sameDomain',\n";
	$html .= "			'quality', 'high',\n";
	$html .= "			'align', 'middle',\n";
	$html .= "			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',\n";
	$html .= "			'play', 'true',\n";
	$html .= "			'devicefont', 'false'\n";
	$html .= "			); \n";
	$html .= "	} else { \n";
	$html .= "		var alternateContent = 'This content requires the Adobe Flash Player. '\n";
	$html .= "		document.write(alternateContent); \n";
	$html .= "	}\n";
	$html .= "}\n";
	$html .= "// -->\n";
	$html .= "</script>\n";
	$html .= "<noscript>\n";
	$html .= "	<P>This content requires JavaScript.</P>\n";
	$html .= "</noscript>\n";

	return $html;
}

function SendChartData( $chart=array() ,$add='' ){
	
	$xml="<chart>\r\n";
	$xml.="\t<license>".MAANI_LICENSE_KEY."</license>\r\n";
	
	$Keys1= array_keys((array) $chart);
	
	for ($i1=0;$i1<count($Keys1);$i1++)
	{
		if(is_array($chart[$Keys1[$i1]]))
		{
			$Keys2=array_keys($chart[$Keys1[$i1]]);
			if(is_array($chart[$Keys1[$i1]][$Keys2[0]]))
			{
				$xml.="\t<".$Keys1[$i1].">\r\n";
				for($i2=0;$i2<count($Keys2);$i2++)
				{
					$Keys3=array_keys((array) $chart[$Keys1[$i1]][$Keys2[$i2]]);
					switch($Keys1[$i1])
					{
						case "chart_data":
						$xml.="\t\t<row>\r\n";
						for($i3=0;$i3<count($Keys3);$i3++)
						{
							switch(true)
							{
								case ( $chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null ):
								$xml.="\t\t\t<null/>\r\n";
								break;
								
								case ($Keys2[$i2]>0 and $Keys3[$i3]>0):
								$xml.="\t\t\t<number>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</number>\r\n";
								break;
								
								default:
								$xml.="\t\t\t<string>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</string>\r\n";
								break;
							}
						}
						$xml.="\t\t</row>\r\n";
						break;
						
						case "chart_value_text":
						$xml.="\t\t<row>\r\n";
						$count=0;
						for($i3=0;$i3<count($Keys3);$i3++){
							if($chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]===null){$xml.="\t\t\t<null/>\r\n";}
							else{$xml.="\t\t\t<string>".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."</string>\r\n";}
						}
						$xml.="\t\t</row>\r\n";
						break;
						
						case "draw":
						$text="";
						$xml.="\t\t<".$chart[$Keys1[$i1]][$Keys2[$i2]]['type'];
						for($i3=0;$i3<count($Keys3);$i3++){
							if($Keys3[$i3]!="type"){
								if($Keys3[$i3]=="text"){$text=$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]];}
								else{$xml.=" ".$Keys3[$i3]."=\"".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."\"";}
							}
						}
						if($text!=""){$xml.=">".$text."</text>\r\n";}
						else{$xml.=" />\r\n";}
						break;
						
						
						default://link, etc.
						$xml.="\t\t<value";
						for($i3=0;$i3<count($Keys3);$i3++){
							$xml.=" ".$Keys3[$i3]."=\"".$chart[$Keys1[$i1]][$Keys2[$i2]][$Keys3[$i3]]."\"";
						}
						$xml.=" />\r\n";
						break;
					}
				}
				$xml.="\t</".$Keys1[$i1].">\r\n";
			}else{
				if($Keys1[$i1]=="chart_type" or $Keys1[$i1]=="series_color" or $Keys1[$i1]=="series_image" or $Keys1[$i1]=="series_explode" or $Keys1[$i1]=="axis_value_text"){							
					$xml.="\t<".$Keys1[$i1].">\r\n";
					for($i2=0;$i2<count($Keys2);$i2++){
						if($chart[$Keys1[$i1]][$Keys2[$i2]]===null){$xml.="\t\t<null/>\r\n";}
						else{$xml.="\t\t<value>".$chart[$Keys1[$i1]][$Keys2[$i2]]."</value>\r\n";}
					}
					$xml.="\t</".$Keys1[$i1].">\r\n";
				}else{//axis_category, etc.
					$xml.="\t<".$Keys1[$i1];
					for($i2=0;$i2<count($Keys2);$i2++){
						$xml.=" ".$Keys2[$i2]."=\"".$chart[$Keys1[$i1]][$Keys2[$i2]]."\"";
					}
					$xml.=" />\r\n";
				}
			}
		}else{//chart type, etc.
			$xml.="\t<".$Keys1[$i1].">".$chart[$Keys1[$i1]]."</".$Keys1[$i1].">\r\n";
		}
	}
    $xml.=$add;
	$xml.="</chart>\r\n";
	echo $xml;
}

?>