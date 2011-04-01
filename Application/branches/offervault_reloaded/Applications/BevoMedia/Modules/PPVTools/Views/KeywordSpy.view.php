<?
	$soap_module = array('keywordspy','spy_kw');
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

div.progress-container {
  border: 1px solid #ccc; 
  width: 100px; 
  margin: 2px 5px 2px 0; 
  padding: 1px; 
  float: left; 
  background: white;
}

div.progress-container-green {
  background-color: #ACE97C;
  background: url('/Themes/BevoMedia/img/ppvtool_bargraph.gif') 0 0 repeat-x;
  height: 12px;
  margin: 0px;
  padding: 0px;
}


div.progress-container-red {
  background-color: #ACE97C;
  background: url('/Themes/BevoMedia/img/ppvtool_bargraph_red.gif') 0 0 repeat-x;
  height: 12px;
  margin: 0px;
  padding: 0px;
}


div.progress-container-yellow {
  background-color: #ACE97C;
  background: url('/Themes/BevoMedia/img/ppvtool_bargraph_yellow.gif') 0 0 repeat-x;
  height: 12px;
  margin: 0px;
  padding: 0px;
}

div.progress-container-grey {
  background-color: #FFFFFF;
  background: url('/Themes/BevoMedia/img/ppvtool_bargraph_nodata.gif') 0 0 repeat-x;
  height: 12px;
  margin: 0px;
  padding: 0px;
}

</style>

<style>
/*Ad variation border*/
.has-border
{
    border-right: 1px solid #ddd;
    margin-top: 10px;
}

.no-border
{
    border-right: 0;
    margin-top: 10px;
}

.ad .adTitle
{
    color: #0000FF;
    font-weight: bold;
    font-size: 15px;
    text-decoration: underline;
    border: 0px;
    padding: 0px;
    margin: 0px;
    line-height: 22px;
}
.ad .adDescription
{
    color: #000000;
    line-height: 16px;
}
.ad .adDestination
{
    color: #009900;
    line-height: 16px;
    text-decoration: underline;
}
.ad .adDestination a:link
{
    color: #009900;
}
.ad .adDestination a:visited
{
    color: #009900;
}
.ad .adDestination a:active
{
    color: #009900;
}
.ad .adDestination a:focus
{
    color: #009900;
}
.ad .adDestination a:hover
{
    color: #009900;
}
</style>



<form action="" name="ppvsniper" method="post">

    <div class="filtering">
    	<div class="col-left" style="width: 96%">
    		<div class="option">
    			<label for="ppcprovider">PPC Keyword:</label>
			<input class="formtxt" type="text" name="keywords" value="<?php echo @$_POST['keywords'] ?>" />
    		</div>
    	</div>
    	<div class="actions">
    		<input class="formsubmit ppv_go" type="submit" value="Go" />
    	</div>
    </div>

<input type="hidden" name="ppvfind" value="1">
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
     $url = trim(@$_POST['keywords']);

		
		$alexa_raw = array();
     //$alexa_raw = $ppvtools->ppvtool_alexa_ranking($url);
        $ppv_report['alexa_ranking'] = @$alexa_raw['SD']['1']['POPULARITY']['@attributes']['TEXT'];
        $ppv_report['alexa_host'] = @$alexa_raw['SD']['0']['@attributes']['HOST'];
        $ppv_report['alexa_rating'] = @$alexa_raw['SD']['0']['REVIEWS']['@attributes']['AVG'];
        $ppv_report['alexa_raw'] = $alexa_raw;


            $test_array['Title'] = 'Criminal Laws';
            $test_array['Description1'] = 'Why do Innocent People Confess?';
            $test_array['Description2'] = 'Learn More and Get Involved Today!';
            $test_array['Display URL'] = 'InnocenceProject.org';
            $test_array['Destination URL'] = 'http://www.innocenceproject.org/understand/False-Confessions.php';
            $test_array['ROI'] = '57';
            $test_array['Affiliate'] = 'N/A';
            $test_array['Current Position'] = '3';
            $test_array['Average Position'] = '2.53';
            $test_array['Days Seen'] = '133/312 days';
            $test_array['Date Last Seen'] = '2010/07/01 03:16:00';
            $test_array['Date First Seen'] = '2009/08/24 10:46:00';




      //$keyword_stats = $ppvtools->ppvtool_kwspy_keywordstats($url);
      //print_r($keyword_stats);
      //print_r($ppvtools->ppvtool_kwspy_ads($url));

      $cpc_compition = $PPVTools->ppvtool_kwspy_ads($url);
      $cpc_keyword = $PPVTools->ppvtool_kwspy_keywordstats($url);
        $keyword_stats = $PPVTools->ppvtool_alexa_ratio(trim($url));
        $google_stats = $PPVTools->google_search(trim($url));
         $search_results = $google_stats['responseData']['cursor']['estimatedResultCount'];
      $cpc_compition2 = $PPVTools->ppvtool_kwspy_ads($cpc_keyword['related']['0']['keyword'], 1);

      $cpc_compition = @array_merge($cpc_compition, $cpc_compition2);

      if($cpc_compition == -1){
         echo 'No data found for '.$url.'.<br />';
      }else{
?>
<br /><br />

<table class="btable" cellspacing="0" cellpadding="5" border="0" style="float: left; width: 270px;">
   <tr class="table_header_small">
	<td class="hhls" style="border: none;"></td>
	<td style="border: none;" colspan="2">&nbsp;<?php echo $url; ?></td>
	<td class="hhrs" style="border: none;"></td>
  </tr>
<tr><td class="border">&nbsp;</td><td width="130px">Cost Per Click:</td><td><span><?php echo $cpc_keyword['cpc']; ?></span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Search Volume:</td><td><span><?php echo $cpc_keyword['volume']; ?></span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Search Results:</td><td><span><?php echo number_format($search_results); ?> pages</span></td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Popularity:</td><td><span style="display: block;">
<?php
         echo '<div class="progress-container">';
           if($keyword_stats['popularity'] == '') echo '<div class="progress-container-grey" ';
           elseif($keyword_stats['popularity'] < 35) echo '<div class="progress-container-red" ';
           elseif($keyword_stats['popularity'] < 60) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         if($keyword_stats['popularity'] == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         else echo $keyword_stats['popularity'].'%"></div></div>';
	echo '</span>';
?>
</td><td class="tail">&nbsp;</td></tr>
<tr><td class="border">&nbsp;</td><td>Competition:</td><td><span style="display: block;">
<?php
        echo '<div class="progress-container">';
           if($keyword_stats['qci'] == '') echo '<div class="progress-container-grey" ';
           elseif($keyword_stats['qci'] > 60) echo '<div class="progress-container-red" ';
           elseif($keyword_stats['qci'] > 40) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         if($keyword_stats['qci'] == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         else echo $keyword_stats['qci'].'%"></div></div>';
	echo '</span>';
?>
</td><td class="tail">&nbsp;</td></tr>

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
        echo '<td colspan="2">Website</td>';
        echo '<td>Banner</td>';
        echo '<td>ROI Rating</td>';
        echo '<td>Site Rating</td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';

      $i = 0;

	  if (is_array($cpc_compition))
      foreach($cpc_compition as $key => $value){

           //$detail_stats = $ppvtools->ppvtool_kwspy_urlstats($value['Competitors']);
           usleep(200000); //Give google API some time between each fetch.

        echo '<tr><td>&nbsp;</td>';
        echo '<td class="GridRowHead" style="border-left: medium none; width: 24px;">';
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.base64_encode($PPVTools->post_urlpass($value['Destination URL'])).'">&nbsp;';
        echo '</td>';

	echo '<td class="GridRowCol" style="border-left: medium none;"><span style="display: block;">';
        echo '<img src="ppvtools_site_thumbnails.php?url='.$value['Display URL'].'" width="111" height="82" border="0" />';
	echo '</span></td>';

	echo '<td class="GridRowHead" style="width: 220px;"><span style="display: block;">';
echo '
                <div class="ad">
                    <a href="'.$value['Destination URL'].'" target="popup" rel="nofollow"><div class="adTitle">'.$value['Title'].'<br></div></a>
                    <div class="adDescription">
                        '.$value['Description1'].'<br>
                        '.$value['Description2'].'</div>
                    <div class="adDestination">
                    <a href="?module=kwsurl&url='.$value['Display URL'].'" target="" rel="nofollow">'.$value['Display URL'].'</a>
                    </div>
                 </div>
';
	echo '</span></td>';



        $alexa_raw = $PPVTools->ppvtool_alexa_ranking($value['Display URL']);
           $ppv_report['alexa_ranking'] = @$alexa_raw['SD']['1']['POPULARITY']['@attributes']['TEXT'];
           $ppv_report['alexa_host'] = @$alexa_raw['SD']['0']['@attributes']['HOST'];
           $ppv_report['alexa_rating'] = @$alexa_raw['SD']['0']['REVIEWS']['@attributes']['AVG'];
           $ppv_report['alexa_raw'] = $alexa_raw;

        //echo '<td class="GridRowCol"><span style="display: block;">#'.number_format($ppv_report['alexa_ranking'])..'</span></td>';

	echo '<td class="GridRowCol"><span style="display: block;">';

	 $roi = $value['ROI']*2;
         if($roi > 100) $roi = 100;

         echo '<div class="progress-container">';
           if($roi == '') echo '<div class="progress-container-grey" ';
           elseif($roi < 35) echo '<div class="progress-container-red" ';
           elseif($roi < 60) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         //if($roi == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         //else echo $roi.'%"></div></div>';
	echo $roi.'%"></div></div>';

         echo 'Current Position: '.$value['Current Position'].'<br />';
         echo 'Average Position: '.$value['Average Position'].'<br />';

        echo '</span></td>';


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
