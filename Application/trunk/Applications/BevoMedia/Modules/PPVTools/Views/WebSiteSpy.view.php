<?
	$soap_module = array('keywordspy','spy_website');
	include "Header.view.php";
	
	if (isset($_GET['basicRequired']))
	{
		include_once "Applications/BevoMedia/Modules/Marketplace/Views/Premium.view.php";	
		return;
	}
	
	require_once(PATH . "Legacy.Abstraction.class.php");
	require(PATH . 'PPVTools/class.ppvtools.php');
	
	$PPVTools = new PPVToolsModule();
?>




<style>
.rating_bar {
  width: 55px;
  background: url('/Themes/BevoMedia/img/ppvtools_star_grey.gif') 0 0 repeat-x;
}

.rating_bar div {
  height: 12px;
  background: url('/Themes/BevoMedia/img/ppvtools_star_orange.gif') 0 0 repeat-x;
}

</style>

<?php

   if(@$_GET['url'] != ''){
      $_POST['keywords'] = @$_GET['url'];
      $_POST['ppvfind'] = 1;
   }
   if(@$_POST['keywords'] == '') $_POST['keywords'] = 'website.com';

?>

<form action="" name="ppvsniper" method="post">

    <div class="filtering">
    	<div class="col-left" style="width: 96%">
    		<div class="option">
    			<label for="ppcprovider">URL:</label>
			<input class="formtxt" type="text" name="keywords" value="<?php echo @$_POST['keywords'] ?>" />
    		</div>
    	</div>
    	<div class="actions">
    		<input class="formsubmit ppv_go" type="submit" value="Go" />
    	</div>
    </div>

<input type="hidden" name="ppvfind" value="1" />
</form>

<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function checkAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = true ;
}

function uncheckAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = false ;
}
//  End -->
</script>


<?php
   if (isset($_POST['ppvfind']) && ($_POST['ppvfind'] == '1'))
   {

	// get host name from URL
	 preg_match('@^(?:http://)?([^/]+)@i', @$_POST['keywords'], $matches);
     $host = $matches[1];
     // get last two segments of host name
     preg_match('/[^.]+\.[^.]+$/', $host, $matches);
     $url = $matches[0];


     $alexa_raw = $PPVTools->ppvtool_alexa_ranking($url);
        $ppv_report['alexa_ranking'] = @$alexa_raw['SD']['1']['POPULARITY']['@attributes']['TEXT'];
        $ppv_report['alexa_host'] = @$alexa_raw['SD']['0']['@attributes']['HOST'];
        $ppv_report['alexa_rating'] = @$alexa_raw['SD']['0']['REVIEWS']['@attributes']['AVG'];
        $ppv_report['alexa_raw'] = $alexa_raw;

//$cpc_compition['daily_budget'] = '20,333';
//$cpc_compition['clicks_day'] = '58,111';
//$cpc_compition['average_position'] = '5.55';
//$cpc_compition['average_cost'] = '$0.35';

      $cpc_compition = $PPVTools->ppvtool_kwspy_urlstats($url);
      if($cpc_compition == -1){
         //echo '<br />';
         echo 'No data found for '.$url.'.<br />';
      }else{
?>
<br /><br />
<img src="ppvtools_site_thumbnails.php?url=<?php echo $url; ?>" width="111" height="82" /><br /><br />

<table class="btable" cellspacing="0" cellpadding="5" border="0" style="float: left; width: 270px;">
   <tr class="table_header_small">
	<td class="hhls" style="border: none;"></td>
	<td style="border: none;" colspan="2">&nbsp;<?php echo $url; ?></td>
	<td class="hhrs" style="border: none;"></td>
  </tr>
<tr><td class="border">&nbsp;</td><td width="130px">Advertising Budget:</td><td><span><?php echo $cpc_compition['daily_budget']; ?> per day</span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Advertising Clicks:</td><td><span><?php echo $cpc_compition['clicks_day']; ?> per day</span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Average Ad Position:</td><td><span><?php echo $cpc_compition['average_position']; ?></span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Average Cost Per Click:</td><td><span><?php echo $cpc_compition['average_cost']; ?></span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Site Rating:</td><td><span>
<?php
          if($ppv_report['alexa_ranking'] != ''){
             $stars = $ppv_report['alexa_rating']*20;
             echo '<div class="rating_bar">';
             echo '<div style="width:'.$stars.'%; margin-left: 0px;"></div>';
             echo '</div>';
          }else{
             echo '<img src="/Themes/BevoMedia/img/ppvtools_star_norating.gif" border="0">';
          }
?>
</span></td><td class="tail">&nbsp;</td></tr>
   <tr class="table_footer">
	<td class="hhl"></td>
	<td colspan="2">&nbsp;</td>
	<td class="hhr"></td>
   </tr>
</table>
<br style="clear: both;" />

<?php


      //Start Table Header:
      echo '<form action="/BevoMedia/PPVTools/LinkBuilder.html" method="post" name="ppvurls">';
      echo '<table class="btable" border="0" cellpadding="3" cellspacing="0">';
      echo '<tbody>';
      echo '<tr class="table_header">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="2">Website Domain</td>';
        echo '<td>Search Keywords</td>';
        //echo '<td>Ad Budget</td>';
        //echo '<td>Ad Clicks</td>';
        //echo '<td>Average Ad Position</td>';
        //echo '<td>Average CPC</td>';
        echo '<td>Alexa Ranking</td>';
        echo '<td>Site Rating</td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';


      //$ppv_results = $ppvtools->ppvtool_sniper($keywords, $_POST['searchdepth']);
      //foreach($ppv_results as $key => $value){
      //  $alexa_raw = $ppvtools->ppvtool_alexa_ranking($value['url']);
      //  $alexa_ranking = $alexa_raw['SD']['1']['POPULARITY']['@attributes']['TEXT'];

      //  $ppv_report["$alexa_ranking"] = $value;
      //  $ppv_report["$alexa_ranking"]['alexa_ranking'] = $alexa_ranking;
      //  $ppv_report["$alexa_ranking"]['alexa_host'] = $alexa_raw['SD']['0']['@attributes']['HOST'];
      //  $ppv_report["$alexa_ranking"]['alexa_rating'] = $alexa_raw['SD']['0']['REVIEWS']['@attributes']['AVG'];
      //  $ppv_report["$alexa_ranking"]['alexa_raw'] = $alexa_raw;
      //}
      //ksort($ppv_report);

      $i = 0;

      //$cpc_compition = $ppvtools->ppvtool_kwspy_urlstats($_POST['keywords']);
//print_r($cpc_compition);
      foreach($cpc_compition['ads'] as $key => $value){

           //$detail_stats = $ppvtools->ppvtool_kwspy_urlstats($value['Competitors']);
           //usleep(400000); //Give google API some time between each fetch.

        echo '<tr><td>&nbsp;</td>';
        echo '<td class="GridRowHead" style="border-left: medium none; width: 24px;">';
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.base64_encode($PPVTools->post_urlpass($value['Competitors'])).'">&nbsp;';
        echo '</td>';
	echo '<td class="GridRowHead" style="border-left: medium none;">';
	echo '<a href="/BevoMedia/PPVTools/WebSiteSpy.html?url='.$value['Competitors'].'">'.$value['Competitors'].'</a>&nbsp;'; //$value['alexa_host']; //$value['url'];
	echo '</td>';
	echo '<td class="GridRowCol" style="width: 300px;"><span style="display: block;">';
        echo $value['Keywords'];
	echo '</span></td>';




        //echo '<td class="GridRowCol"><span style="display: block;">';
        //   echo $detail_stats['daily_budget'];
        //echo '</td>';

        //echo '<td class="GridRowCol"><span style="display: block;">';
        //   echo $detail_stats['clicks_day'];
        //echo '</td>';

        //echo '<td class="GridRowCol"><span style="display: block;">';
        //   echo $detail_stats['average_position'];
        //echo '</td>';

        //echo '<td class="GridRowCol"><span style="display: block;">';
        //   echo $detail_stats['average_cost'];
        //echo '</td>';




        $alexa_raw = $PPVTools->ppvtool_alexa_ranking($value['Competitors']);
           $ppv_report['alexa_ranking'] = @$alexa_raw['SD']['1']['POPULARITY']['@attributes']['TEXT'];
           $ppv_report['alexa_host'] = @$alexa_raw['SD']['0']['@attributes']['HOST'];
           $ppv_report['alexa_rating'] = @$alexa_raw['SD']['0']['REVIEWS']['@attributes']['AVG'];
           $ppv_report['alexa_raw'] = $alexa_raw;

        echo '<td class="GridRowCol" style="width: 100px;"><span style="display: block;">#'.number_format($ppv_report['alexa_ranking']).'</span></td>';

        echo '<td class="GridRowCol"><span style="display: block;">';
          if($ppv_report['alexa_ranking'] != ''){
             $stars = $ppv_report['alexa_rating']*20;
             echo '<div class="rating_bar">';
             echo '<div style="width:'.$stars.'%; margin-left: 0px;"></div>';
             echo '</div>';
          }else{
             echo '<img src="/Themes/BevoMedia/img/ppvtools_star_norating.gif" border="0">';
          }
        echo '</span></td>';



	echo '<td class="tail">&nbsp;</td></tr>';
        $i++;
      }

      //Start Table Footer:
      echo '<tr class="table_footer">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="5"></td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';
      echo '</tbody></table>';

      echo '
        <a class="btn checkall inarow" href="#" onclick="checkAll(document.ppvurls.list); return false;" title="Check all found URLs">Check All</a>
	<a class="btn uncheckall inarow" href="#" onclick="uncheckAll(document.ppvurls.list); return false;" title="UN-Check all URLs">Uncheck All</a>
	<a class="btn ppv_sendtolistbuilder inarow" href="#" onclick="document.ppvurls.submit()" title="Send checked URLs to the List Builder">Send to List Builder</a>
	<div class="clear"></div>
      ';
      echo '<input type="hidden" name="ltype" value="urls">';
      echo '</form>';
   }
 }

?>
<div class="clear"></div>
