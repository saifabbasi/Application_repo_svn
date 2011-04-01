<?php echo SoapPageMenu('kwt','rotators','rotators_lp_overview',true); ?>
	<ul class="floatright">
		<li class="haskids"><a href="/BevoMedia/KeywordTracker/LandingPageRotationNew.html">Create New<span></span></a>
			<ul>
				<li><a href="/BevoMedia/KeywordTracker/LandingPageRotationNew.html">Landing Page Rotator<span></span></a></li>
				<li><a href="/BevoMedia/KeywordTracker/OfferRotationNew.html">Offer Rotator<span></span></a></li>
			</ul>
		</li>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<table width='100%' cellspacing="0" cellpadding="3" border="0" class="btable">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td>Label</td>
		<td>Date Created</td>
		<td>Links</td>
		<td>Actions</td>
		<td class="hhr">&nbsp;</td>
	</tr>
	
	<?php if(sizeof($this->LandingPageGroups)):?>
	<?php foreach($this->LandingPageGroups as $LandingPageGroup):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>
			<?php print $LandingPageGroup->label; ?>
		</td>
		<td>
			<?php print $LandingPageGroup->created; ?>
		</td>
		<td>
			<?php foreach($LandingPageGroup->Links as $Itm):?>
				<span><?php echo $Itm?></span><br/>
			<?php endforeach?>
		</td>
		<td>
			<a class="tbtn" href='LandingPageRotationEdit.html?ID=<?php print $LandingPageGroup->id; ?>'>
			Edit
			</a>
			&nbsp;
			<a class="tbtn" href='_LandingPageRotationDelete.html?ID=<?php print $LandingPageGroup->id; ?>'>
			Delete
			</a>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php endforeach?>
	<?php else:?>
	<tr>
		<td class="border"></td>
		<td colspan='4'>
			No landing page rotator groups exist...  create one above.
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php endif?>
	
	<tr class="table_footer">
		<td class="hhl"></td>
		<td style="border-left: none;" colspan="4"></td>
		<td class="hhr"></td>
	</tr>
</table>