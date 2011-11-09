<?
	$soap_module = array('keywordspy','spy_comp');
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


<form action="" name="ppvsniper" method="post">

    <div class="filtering">
    	<div class="col-left" style="width: 96%">
    		<div class="option">
    			<label for="ppcprovider">Keyword List:</label>
			<textarea class="formtxtarea" name="keywords"><?=isset($_POST['keywords'])?$_POST['keywords']:''?></textarea>
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

	   //Make sense of input keywords.
	   $keywords = explode("\n", $_POST['keywords']);
	   reset($keywords);
	   foreach($keywords as $key => $value){
		 if($value == '') unset($keywords["$key"]);
		 //Keep room for more detection processes.
	   }


      //Start Table Header:
      echo '<form action="/BevoMedia/PPVTools/LinkBuilder.html" method="post" name="ppvurls">';
      echo '<table class="btable" border="0" cellpadding="3" cellspacing="0">';
      echo '<tbody>';
      echo '<tr class="table_header">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="2">Keyword</td>';
        echo '<td>Popularity</td>';
        echo '<td>Competition</td>';
        echo '<td>Search Results</td>';
        echo '<td>Overall Effectiveness</td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';

      $i = 0;
      foreach($keywords as $key => $value){
        $keyword_stats = $PPVTools->ppvtool_alexa_ratio(trim($value));
        $google_stats = $PPVTools->google_search(trim($value));

        usleep(80000); //Give Alexa API some time between each fetch.
        $search_results = $google_stats['responseData']['cursor']['estimatedResultCount'];


        echo '<tr><td>&nbsp;</td>';
        echo '<td class="GridRowHead" style="border-left: medium none; width: 24px;">';
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.base64_encode(trim($value)).'">&nbsp;';
        echo '</td>';
	echo '<td class="GridRowHead" style="border-left: medium none;">';
	echo $value.'&nbsp;';
	echo '</td>';


	echo '<td class="GridRowCol" style="width: 100px;"><span style="display: block;">';

         echo '<div class="progress-container">';
           if($keyword_stats['popularity'] == '') echo '<div class="progress-container-grey" ';
           elseif($keyword_stats['popularity'] < 35) echo '<div class="progress-container-red" ';
           elseif($keyword_stats['popularity'] < 60) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         if($keyword_stats['popularity'] == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         else echo $keyword_stats['popularity'].'%"></div></div>';
	echo '</span></td>';

      /////

	echo '<td class="GridRowCol" style="width: 100px;"><span style="display: block;">';
        echo '<div class="progress-container">';
           if($keyword_stats['qci'] == '') echo '<div class="progress-container-grey" ';
           elseif($keyword_stats['qci'] > 60) echo '<div class="progress-container-red" ';
           elseif($keyword_stats['qci'] > 40) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         if($keyword_stats['qci'] == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         else echo $keyword_stats['qci'].'%"></div></div>';
	echo '</span></td>';

      /////

	echo '<td class="GridRowHead" style="border-left: medium none;">';
	echo number_format($search_results);
	echo '</td>';

      ///// OVERALL STAT

        //if($keyword_stats['qci'] == '') $keyword_stats['qci'] = 50;
        //if($keyword_stats['popularity'] == '') $keyword_stats['popularity'] = 50;

        if($keyword_stats['qci'] == '') $overall = '';
        else $overall = ((100-$keyword_stats['qci'])+$keyword_stats['popularity'])/2;

	echo '<td class="GridRowCol" style="width: 100px;"><span style="display: block;">';
        echo '<div class="progress-container">';
           if($overall == '') echo '<div class="progress-container-grey" ';
           elseif($overall < 35) echo '<div class="progress-container-red" ';
           elseif($overall < 60) echo '<div class="progress-container-yellow" ';
           else echo '<div class="progress-container-green" ';

         echo 'style="width: ';
         if($overall == '') echo '100%"><span style="font-size: 90%">No data</span></div></div>';
         else echo $overall.'%"></div></div>';
	echo '</span></td>';



	echo '<td class="tail">&nbsp;</td></tr>';
        $i++;
      }

      //Start Table Footer:
      echo '<tr class="table_footer">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="6"></td>';
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
?>
<div class="clear"></div>
