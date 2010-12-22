<?
	$soap_module = array('urls','pagesniper');
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

<?php
   if(@$_POST['searchdepth'] == '') $_POST['searchdepth'] = 5;
?>
<form action="" name="ppvsniper" method="post">

    <div class="filtering">
    	<div class="col-left" style="width: 96%">
    		<div class="option">
    			<label for="ppcprovider">Keywords:</label>
			<textarea class="formtxtarea" name="keywords"><?=isset($_POST['keywords'])?$_POST['keywords']:'' ?></textarea>
    		</div>

    		<div class="option">
    			<label for="pcccampaign">Search Depth:</label>
   			<select class="formselect" name="searchdepth">
				<option value="1" <?php if($_POST['searchdepth'] == 1) echo ' selected'; ?>>1 page</option>
				<option value="2" <?php if($_POST['searchdepth'] == 2) echo ' selected'; ?>>2 pages</option>
				<option value="3" <?php if($_POST['searchdepth'] == 3) echo ' selected'; ?>>3 pages</option>
				<option value="4" <?php if($_POST['searchdepth'] == 4) echo ' selected'; ?>>4 pages</option>
				<option value="5" <?php if($_POST['searchdepth'] == 5) echo ' selected'; ?>>5 pages</option>
				<option value="10" <?php if($_POST['searchdepth'] == 10) echo ' selected'; ?>>10 pages</option>
				<option value="20" <?php if($_POST['searchdepth'] == 20) echo ' selected'; ?>>20 pages</option>
				<option value="30" <?php if($_POST['searchdepth'] == 30) echo ' selected'; ?>>30 pages</option>
				<option value="40" <?php if($_POST['searchdepth'] == 40) echo ' selected'; ?>>40 pages</option>
				<option value="50" <?php if($_POST['searchdepth'] == 50) echo ' selected'; ?>>50 pages</option>
   			</select>
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
   if ( isset($_POST['ppvfind']) && ($_POST['ppvfind'] == '1'))
   {

	   //Make sense of input keywords.
	   $keywords = explode("\n", $_POST['keywords']);
	   reset($keywords);
	   foreach($keywords as $key => $value){
		 if($value == '') unset($keywords["$key"]);
		 //Keep room for more detection processes.
	   }

      //print_r($PPVTools->ppvtool_sniper($keywords));

      //Start Table Header:
      echo '<form action="/BevoMedia/PPVTools/SendToListBuilder.html" method="post" name="ppvurls">';
      echo '<table class="btable" border="0" cellpadding="3" cellspacing="0">';
      echo '<tbody>';
      echo '<tr class="table_header">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="2">Website URL</td>';
        echo '<td>Description</td>';
        echo '<td class="hhr"></td>';
      echo '</tr>';

      $i = 0;
      $ppv_results = $PPVTools->ppvtool_sniper($keywords, $_POST['searchdepth']);
      foreach($ppv_results as $key => $value){
        echo '<tr><td>&nbsp;</td>';
        echo '<td class="GridRowHead" style="border-left: medium none; width: 24px;">';
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.base64_encode($PPVTools->post_urlpass($value['url'])).'">&nbsp;';
        echo '</td>';
	echo '<td class="GridRowHead" style="border-left: medium none;">';
	echo $value['url'];
	echo '</td>';
	echo '<td class="GridRowCol" style="width: 300px;"><span style="display: block;">';
        echo $value['titleNoFormatting'];
	echo '</span></td>';
	echo '<td class="tail">&nbsp;</td></tr>';
        $i++;
      }

      //Start Table Footer:
      echo '<tr class="table_footer">';
        echo '<td class="hhl"></td>';
        echo '<td colspan="3"></td>';
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
