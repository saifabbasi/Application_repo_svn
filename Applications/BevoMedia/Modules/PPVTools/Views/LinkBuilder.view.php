<?
	if (isset($_SESSION['PPVPost']))
	{
		$_POST = $_SESSION['PPVPost'];
		unset($_SESSION['PPVPost']);
	}
	
	$soap_module = array('linkbuilder');
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
//print_r($_POST);

   //Fetch List
   unset($tagslist);
   foreach($_POST as $key => $value){
      if(substr($key, 0, 4) == 'list'){
         $tagslist["$key"] = base64_decode($value);
      }
   }

?>
<?php

//function check_listsize($input){
//
//   if(strlen($input) > 8*1024*1024)
//
//
//}

   //Make sense of input keywords.
   $listwords = explode("\n", isset($_POST['liswords'])?$_POST['liswords']:'');
   reset($listwords);
   foreach($listwords as $key => $value){
     if($value == '') unset($listwords["$key"]);
     //Keep room for more detection processes.
   }

   $tagchange = 0;
   $new_tags = '';
   $size_error = 0;

   //Add Exact
   if(isset($_POST['addexact'])){
     $tagchange = 1;
     foreach($listwords as $key => $value){
        $new_tags .= '['.trim($value).']'."\n";
     }
   }
   //Add Phrase
   if(isset($_POST['addphrase'])){
     $tagchange = 1;
     foreach($listwords as $key => $value){
        $new_tags .= '"'.trim($value).'"'."\n";
     }
   }
   //Add States
   if(isset($_POST['addstates'])){
     $tagchange = 1;
     $arr_us_states = ppvarray_us_states();
     foreach($listwords as $key => $value){
      if(strlen($new_tags) > 1*1024*1024){
         $size_error = 1;
         break;
      }
        foreach($arr_us_states as $value2){
           $new_tags .= trim($value).' '.$value2."\n";
        }
     }
   }
   //Add Countries
   if(isset($_POST['addcountr'])){
     $tagchange = 1;
     $arr_countries = ppvarray_countries();
     foreach($listwords as $key => $value){
      if(strlen($new_tags) > 1*1024*1024){
         $size_error = 1;
         break;
      }
        foreach($arr_countries as $value2){
           $new_tags .= trim($value).' '.$value2."\n";
        }
     }
   }
   //Add Names
   if(isset($_POST['addnames'])){
     $tagchange = 1;
     $arr_names = ppvarray_namesmale();
     foreach($listwords as $key => $value){
      if(strlen($new_tags) > 1*1024*1024){
         $size_error = 1;
         break;
      }
        foreach($arr_names as $value2){
           $new_tags .= trim($value).' '.$value2."\n";
        }
     }
     $arr_names = ppvarray_namesfemale();
     foreach($listwords as $key => $value){
      if(strlen($new_tags) > 1*1024*1024){
         $size_error = 1;
         break;
      }
        foreach($arr_names as $value2){
           $new_tags .= trim($value).' '.$value2."\n";
        }
     }
   }
   //Add Common Keywords
   if(isset($_POST['addkeywords'])){
     $tagchange = 1;
     $arr_keywords = ppvarray_keywords();
     foreach($listwords as $key => $value){
      if(strlen($new_tags) > 1*1024*1024){
         $size_error = 1;
         break;
      }
        foreach($arr_keywords as $value2){
           $new_tags .= trim($value).' '.$value2."\n";
        }
     }
   }

   //Shrink to 40 Max.
   if(isset($_POST['shrinkto40'])){
     $tagchange = 1;
     foreach($listwords as $key => $value){
        $new_tags .= substr(trim($value), 0 , 40)."\n";
     }
   }
   //Domains Only.
   unset($domains);
   if(isset($_POST['urlroots'])){
     $tagchange = 1;
     foreach($listwords as $key => $value){
        $i = strpos(trim($value), '/');
        if($i == 0) $i = 256;

        $i2 = substr(trim($value), 0 , $i);
        if (isset($domains) && ($domains["$i2"] != '1'))
		{
           $new_tags .= $i2."\n";
           $domains["$i2"] = 1;
        }
     }
   }


   if($tagchange == 1){
      $_POST['liswords'] = $new_tags;
   }

   if($size_error == 1) echo '<div align="center">Your keyword list has became too large for a single edit. The end of the list has been cropped.</div>';

?>


<form action="" name="ppvlistb" method="post" class="ppvresearchform">

    <div class="filtering">
    	
    		<div class="option">
    			<?php //<label for="ppcprovider">List:</label> 
    			?>
			<textarea class="formtxtarea hugetxtarea floatleft" name="liswords" wrap="off"><?php
//Keys Output
   if(isset($tagslist)){
     foreach($tagslist as $value){
      echo $value."\r\n";
     }
   }else
   {
		if (isset($_POST['liswords']))
		{
			echo $_POST['liswords'];
		}
   }
?></textarea>
    		</div><!--close option-->

    		<div class="box checkoptions floatleft">
			<input type="checkbox" name="addexact" value="1" /> Add Exact Match<br />
			<input type="checkbox" name="addphrase" value="1" /> Add Phrase Match<br />
			<input type="checkbox" name="addstates" value="1" /> Add States<br />
			<input type="checkbox" name="addcountr" value="1" /> Add Countries<br />
			<input type="checkbox" name="addnames" value="1" /> Add Common First Names<br />
			<input type="checkbox" name="addkeywords" value="1" /> Add Common Keywords
		</div>
    	
		<div class="clear"></div>
    		
		<div class="option">
    			<input type="checkbox" name="shrinkto40" value="1" /> Shrink To Max Length of 40 Characters
    		</div>
    		<div class="option">
    			<input type="checkbox" name="urlroots" value="1" /> Root URLs Only
    		</div>

    	<div class="actions">
    		<input class="formsubmit ppv_go" type="submit" value="Go" />
    	</div>
    </div>

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


      //print_r($ppvtools->ppvtool_sniper($keywords));

      //Start Table Header:
      echo '<form action="" method="post" name="ppvurls">';
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
        echo '<input type="checkbox" id="list" name="list'.$i.'" value="'.$value['url'].'">&nbsp;';
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
      ';
      echo '<br /><br /><br /><input type="submit" name="test" value="Download TEST">';
      echo '<input type="hidden" name="download" value="1">';
      echo '</form>';
   }
?>
<div class="clear"></div>
