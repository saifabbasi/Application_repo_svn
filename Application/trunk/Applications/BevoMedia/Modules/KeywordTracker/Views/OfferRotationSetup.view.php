<?php echo SoapPageMenu('kwt','rotators','rotators_offer_overview',true); ?>
	<ul class="floatright">
		<li class="haskids"><a href="/BevoMedia/KeywordTracker/OfferRotationNew.html">Create New<span></span></a>
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
<!--
	<tr>
		<th>
			Label
		</th>
		<th>
			Created Date
		</th>
		<th>
			Links
		</th>
	</tr>
	-->
	<?php if(sizeof($this->OfferGroups)):?>
	<?php foreach($this->OfferGroups as $OfferGroup):?>
	<tr>
		<td class="border">&nbsp;</td>
		<td>
			<span><?php print $OfferGroup->label; ?></span>
		</td>
		<td>
			<span><?php print $OfferGroup->created; ?></span>
		</td>
		<td>
			<?php foreach($OfferGroup->Links as $Itm):?>
				<span><?php echo $Itm?></span><br/>
			<?php endforeach?>
		</td>
		<td>
			<span><a class="tbtn" href='OfferRotationEdit.html?ID=<?php print $OfferGroup->id; ?>'>
			Edit
			</a>
			&nbsp;
			<a class="tbtn" href='_OfferRotationDelete.html?ID=<?php print $OfferGroup->id; ?>'>
			Delete
			</a></span>
		</td>
		<td class="tail">&nbsp;</td>
	</tr>
	<?php endforeach?>
	<?php else:?>
	<tr>
		<td class="border"></td>
		<td colspan='4'>
			No offer rotator groups exist...  create one above.
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