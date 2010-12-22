<?php	$SelfHosted = false;
	if ( $this->{'Application/Mode'} == 'SelfHosted' )
	{
		$SelfHosted = true;
	} 
?>
<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Publisher/Index.html">&laquo; Back to all networks<span></span></a></li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<table class="btable" width="100%" cellpadding="0" cellspacing="0">
<tbody>
	<tr class="table_header">
		<td class="hhl"></td>
		<td>Network</td>
		<td>Rating</td>
		<td>Comment</td>
		<td class="hhr"></td>
	</tr>
	
<?
	foreach ($this->Reviews as $Review)
	{
?>
	<tr>
		<td style="border-top: 0px;">&nbsp;</td>
		<td style="border: 0px;" width="110">
			<img src="/Themes/BevoMedia/img/networklogos/<?=$Review->network__id?>.png" alt="" />
		</td>
		<td style="border: 0px;" width="110" class="rating">
			
			<div id="div_<?php print $Review->network__id; ; ?>" style="white-space: nowrap;">
			<?php for($i=1; $i<6; $i++):?>
				<?php if($Review->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
					<?
						if (!$SelfHosted)
						{
					?>
						<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $Review->network__id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $Review->network__id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $Review->network__id; ; ?>', <?php print $Review->rating; ?>)" style="" align="absbottom" border="0" />
					<?
						} else {
					?>
						<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $Review->network__id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
					<?
						}
					?>
					
				<?php endfor?>
			</div>
		
		</td>
		<td style="border: 0px;"><?=$Review->userComment?></td>
		<td class="tail" style="border-top: 0px;">&nbsp;</td>
	</tr>
<?	
	}
?>

<?
	if (count($this->Reviews)==0)
	{
?>
	<tr>
		<td style="border-top: 0px;">&nbsp;</td>
		<td style="border: 0px; text-align: center;" colspan="3" >
			There are no reviews for this network.
		</td>
		<td class="tail" style="border-top: 0px;">&nbsp;</td>
	</tr>
<?
	}
?>
	
	<tr class="table_footer">
		<td class="hhl"></td>
		<td colspan="3"></td>
		<td class="hhr"></td>
	</tr>
</tbody>	
</table>