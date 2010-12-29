<style type='text/css'>
fieldset {padding: 5px;}
legend {font-size: 12px; font-weight: bold; font-family: Tahoma;}
p.descript {float: right; width: 60%;}

.colN {width: 30%; float: left; margin: 0px;}
.colS {width: 15%; float: left; margin: 0px;}

</style>

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

<div class="pagecontent track_offerrotation_new">
	
	<div id='offer-link-clone' style='display:none;'>
		<div class="sepa"></div>
		<div class='colN'>
			<input class="formtxt wide_200" type='text' name='link[]' autocomplete="off" />
		</div>
		<div class='colS'>
			<input class="formtxt wide_80" type='text' value='1' onkeydown='javascript: updatePercents();' onkeyup='javascript: updatePercents();' name='ratio[]' autocomplete="off"/>
		</div>
		<div class='colN'>
			<input class="formtxt wide_80 disabled" type='text' name='pct[]' disabled='disabled' value='100%'/>
		</div>
		<div>
			<input class="formsubmit tbtn small" type="submit" onclick="removeLink(this); return false;" name='delete[]' value="Delete" />
		</div>
	</div>

	<form method='post'>
		<input type='hidden' name='id' value='<?php print $this->OfferGroup->id; ?>'/>

		<div class="box boxfull">
			<h3>Offer Group</h3>
			<p>Set a label for the group of links you are going to create so that you can easily reference it later on.</p>
			
			<label>
				<span>Offer Group Label</span>
				<input class="formtxt" name='groupLabel' type='text' value='<?php print $this->OfferGroup->label; ?>'>			
			</label>
		</div>
		<div class="box boxfull bordertop">
			<h3>Offer Links</h3>
			<p>Add links to this offer rotator group below. Set a ratio for how much each link should be displayed. The <b>ratio</b> will determine what percentage of the time a visitor will see that specific offer. The <b>percentage</b> will update automatically as you change your ratio values so that it is easy to tell which offers will be shown the most.</p>
			<div class='colN'>
				<b>Link</b>
			</div>
			<div class='colS'>
				<b>Ratio</b>
			</div>
			<div class='colN'>
				<b>Percentage Shown</b>
			</div>
			<div class='colS'>
				<b>Remove</b>
			</div>			
			<div class="clear"></div>
			
			<div id='offer-link-container'>
				<?php foreach($this->OfferGroup->Offers as $Offer):?>
					<div id="Offer_<?php echo $Offer->id?>" class="offerRow">
						<div class="sepa"></div>
						<div class='colN'>
							<input class="formtxt wide_200" type='text' offerId="<?php echo $Offer->id?>" name='link[]' value='<?php print $Offer->link; ?>' autocomplete="off" />
						</div>
						<div class='colS'>
							<input class="formtxt wide_80 offerRadio" type='text' offerId="<?php echo $Offer->id?>" id="ratio_<?php echo $Offer->id?>" value='<?php print $Offer->ratio; ?>' onkeydown='javascript: updatePercents();' onkeyup='javascript: updatePercents();' name='ratio[]' autocomplete="off" />
						</div>
						<div class='colN'>
							<input class="formtxt wide_80 disabled offerPercent" type='text' offerId="<?php echo $Offer->id?>" id="percent_<?php echo $Offer->id?>" name='pct[]' disabled='disabled' value='100%'/>
						</div>
						<div>
							<input class="formsubmit tbtn small delete" type="submit" offerId="<?php echo $Offer->id?>" id="delete_<?php echo $Offer->id?>" name='delete[]' value="Delete" />
						</div>
					</div>
				<?php endforeach?>
			</div>
			<div class="clear"></div>
				
		</div><!--close box-->
		<div class="box boxfull bordertop">
			<input class="formsubmit track_addlink floatleft" type="submit" onclick="addItem(); return false;" value="Add Link" />
			<input class="formsubmit track_submit floatright" type='submit' name="edit" />
			<div class="clear"></div>
		</div>
	</form>
</div>

<script type="text/javascript">

	function updatePercents() {

		var totalRadios = 0;
		
		$('.offerRadio').each(function(key, item) {
			totalRadios += parseInt(item.value);
		});

		
		$('.offerPercent').each(function(key, item) {
			var item = $('#'+item.id);
			var offerRatio = $('#ratio_'+item.attr('offerId'));

			item.val(Math.round((offerRatio.val() / totalRadios)*100) + "%");
		});
	}

	function addItem() {

		var totalRows = $('.offerRow').length;

		var offerRowNum = (totalRows+1);
		var offerRowId = 'Offer_'+(totalRows+1);
		$('#offer-link-container').append('<div id="'+offerRowId+'" class="offerRow"></div>');

		var offerRow = $('#'+offerRowId);
		offerRow.append('<div class="sepa"></div>');

		var linkDiv = $(document.createElement('div')).addClass('colN');
		offerRow.append(linkDiv);
		linkDiv.append("<input class='formtxt wide_200' type='text' offerId='"+offerRowNum+"' name='link[]' value='' autocomplete=\"off\" />");


		var ratioDiv = $(document.createElement('div')).addClass('colS');
		offerRow.append(ratioDiv);
		ratioDiv.append("<input class=\"formtxt wide_80 offerRadio\" type='text' offerId=\""+offerRowNum+"\" id=\"ratio_"+offerRowNum+"\" value='1' onkeydown='javascript: updatePercents();' onkeyup='javascript: updatePercents();' name='ratio[]' autocomplete=\"off\" />");


		var percentageDiv = $(document.createElement('div')).addClass('colN');
		offerRow.append(percentageDiv);
		percentageDiv.append("<input id=\"percent_"+offerRowNum+"\" class=\"formtxt wide_80 disabled offerPercent\" type='text' name='pct[]' disabled='disabled' value='100%' offerId='"+offerRowNum+"'/>");


		var deleteDiv = $(document.createElement('div'));
		offerRow.append(deleteDiv);
		deleteDiv.append("<input class=\"formsubmit tbtn small delete\" type=\"submit\" offerId=\""+offerRowNum+"\" id=\"delete_"+offerRowNum+"\" name='delete[]' value=\"Delete\" />");
		
		updatePercents();

		$('.delete').click(function() {
			$('#Offer_'+$(this).attr('offerId')).remove();
			updatePercents();
			return false;
		});
		

		return false;
	}

	$(document).ready(function() {

		$('.delete').click(function() {
			$('#Offer_'+$(this).attr('offerId')).remove();
			updatePercents();
			return false;
		});

		updatePercents();

	});
</script>

