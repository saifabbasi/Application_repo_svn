<?
	$soap_module = array('keywords','site_kws');
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



<form action="" name="ppvsniper" method="post">

    <div class="filtering">
    	<div class="col-left" style="width: 96%">
    		<div class="option">
    			<label for="ppcprovider">Urls:</label>
			<textarea class="formtxtarea" name="urls"><?=isset($_POST['urls'])?$_POST['urls']:'' ?></textarea>
    		</div>
<?php
	$kw_check1 = 0;
	$kw_check2 = 0;
	$kw_check3 = 0;

	if (isset($_POST['keyw1']) && ($_POST['keyw1'] == 1))
	   $kw_check1 = 1;
	   
	if (isset($_POST['keyw2']) && ($_POST['keyw2'] == 1))
	   $kw_check2 = 1;
	   
	if (isset($_POST['keyw3']) && ($_POST['keyw3'] == 1))
	   $kw_check3 = 1;

	if (isset($_POST['ppvfind']) && ($_POST['ppvfind'] != 1))
	{
	   $kw_check1 = 1;
	   $kw_check2 = 1;
	   $kw_check3 = 1;
	}

?>
    		<div class="option">
    			<label for="pcccampaign">Keyword Types:</label><div style="display:inline-block; vertical-align: text-top;">
			<input type="checkbox" name="keyw1" value="1" <?php if($kw_check1 == 1) echo 'CHECKED'; ?> /> Single Word Keywords<br />
			<input type="checkbox" name="keyw2" value="1" <?php if($kw_check2 == 1) echo 'CHECKED'; ?> /> Two Word Keywords<br />
			<input type="checkbox" name="keyw3" value="1" <?php if($kw_check3 == 1) echo 'CHECKED'; ?> /> Three Word Keywords</div>
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
   if ( isset($_POST['ppvfind']) && ($_POST['ppvfind'] == '1'))
   {

	   //Make sense of input keywords.
	   $keywords = explode("\n", $_POST['urls']);
	   reset($keywords);
	   foreach($keywords as $key => $value){
		 if($value == '') unset($keywords["$key"]);
		 //Keep room for more detection processes.
	   }

      //print_r($ppvtools->ppvtool_sniper($keywords));

      //Start Table Header:
      echo '<form action="/BevoMedia/PPVTools/LinkBuilder.html" method="post" name="ppvurls">';
      echo '<table class="btable" border="0" cellpadding="3" cellspacing="0">';
      echo '<tbody>';
      echo '<tr class="table_header">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="2">Keyword</td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';

	
	  $ppv_keywords = '';
      $keyword_results = '';
      foreach($keywords as $key => $value){
         $ppv_results = $PPVTools->ppvtool_sitekeywords(trim($value));
          if(@$_POST['keyw1'] == 1)
            $ppv_keywords .= $ppv_results['one_word'].', ';
          if(@$_POST['keyw2'] == 1)
            $ppv_keywords .= $ppv_results['two_word'].', ';
          if(@$_POST['keyw3'] == 1)
            $ppv_keywords .= $ppv_results['three_word'].', ';
      }

      $keyword_results = explode(', ', $ppv_keywords);

      $i = 0;
      foreach($keyword_results as $key => $value){
        if($value == '') continue;
        echo '<tr><td>&nbsp;</td>';
        echo '<td class="GridRowHead" style="border-left: medium none; width: 24px;">';
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.base64_encode($value).'">&nbsp;';
        echo '</td>';
	echo '<td class="GridRowHead" style="border-left: medium none;">';
	echo $value;
	echo '</td>';
	echo '<td class="tail">&nbsp;</td></tr>';
        $i++;
      }

      //Start Table Footer:
      echo '<tr class="table_footer">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="2"></td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';
      echo '</tbody></table>';

      echo '
      	<a class="btn checkall inarow" href="#" onclick="checkAll(document.ppvurls.list); return false;" title="Check all found URLs">Check All</a>
	<a class="btn uncheckall inarow" href="#" onclick="uncheckAll(document.ppvurls.list); return false;" title="UN-Check all URLs">Uncheck All</a>
	<a class="btn ppv_sendtolistbuilder inarow" href="#" onclick="document.ppvurls.submit()" title="Send checked URLs to the List Builder">Send to List Builder</a>
	<div class="clear"></div>
      ';
      echo '<input type="hidden" name="ltype" value="keywords">';
      echo '</form>';
   }
?>
<div class="clear"></div>
