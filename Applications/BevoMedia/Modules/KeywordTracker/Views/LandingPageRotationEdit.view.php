<style type='text/css'>
fieldset {padding: 5px;}
legend {font-size: 12px; font-weight: bold; font-family: Tahoma;}
p.descript {float: right; width: 60%;}

.colN {width: 30%; float: left; margin: 0px;}
.colS {width: 15%; float: left; margin: 0px;}

</style>

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

<div class="pagecontent track_offerrotation_new">
	
	<div id='landing-page-link-clone' style='display:none;'>
		<div class="sepa"></div>
		<div class='colN'>
			<input class="formtxt wide_200" type='text' name='link[]' autocomplete="off" />
		</div>
		<div class='colS'>
			<input class="formtxt wide_80" type='text' value='1' onkeydown='javascript: updatePcts();' onkeyup='javascript: updatePcts();' name='ratio[]' autocomplete="off"/>
		</div>
		<div class='colN'>
			<input class="formtxt wide_80 disabled" type='text' name='pct[]' disabled='disabled' value='100%'/>
		</div>
		<div>
			<input class="formsubmit tbtn small" type="submit" onclick="removeLink(this); return false;" name='delete[]' value="Delete" />
		</div>
	</div>

	<form method='post'>
		<input type='hidden' name='id' value='<?php print $this->LandingPageGroup->id; ?>'/>

		<div class="box boxfull">
			<h3>Landing Page Group</h3>
			<p>Set a label for the group of links you are going to create so that you can easily reference it later on.</p>
			
			<label>
				<span>Landing Page Group Label</span>
				<input class="formtxt" name='groupLabel' type='text' value='<?php print $this->LandingPageGroup->label; ?>'>			
			</label>
		</div>
		<div class="box boxfull bordertop">
			<h3>Landing Page Links</h3>
			<p>Add links to this landing page rotator group below. Set a ratio for how much each link should be displayed. The <b>ratio</b> will determine what percentage of the time a visitor will see that specific landing page. The <b>percentage</b> will update automatically as you change your ratio values so that it is easy to tell which landing pages will be shown the most.</p>
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
			
			<div id='landing-page-link-container'>
				<?php foreach($this->LandingPageGroup->LandingPages as $LandingPage):?>
				<div id="LandingPage_<?php echo $LandingPage->id?>" class="landingPageRow">
					<div class="sepa"></div>
					<div class='colN'>
						<input class="formtxt wide_200" type='text' landingPageId="<?php echo $LandingPage->id?>" name='link[]' value='<?php print $LandingPage->link; ?>' autocomplete="off" />
					</div>
					<div class='colS'>
						<input class="formtxt wide_80 landingPageRadio" type='text' landingPageId="<?php echo $LandingPage->id?>" id="ratio_<?php echo $LandingPage->id?>" value='<?php print $LandingPage->ratio; ?>' onkeydown='javascript: updatePercents();' onkeyup='javascript: updatePercents();' name='ratio[]' autocomplete="off" />
					</div>
					<div class='colN'>
						<input class="formtxt wide_80 disabled landingPagePercent" type='text' landingPageId="<?php echo $LandingPage->id?>" id="percent_<?php echo $LandingPage->id?>" name='pct[]' disabled='disabled' value='100%'/>
					</div>
					<div>
						<input class="formsubmit tbtn small delete" type="submit" landingPageId="<?php echo $LandingPage->id?>" id="delete_<?php echo $LandingPage->id?>" name='delete[]' value="Delete" />
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
//<![CDATA[
	function updatePercents() {

		var totalRadios = 0;
		
		$('.landingPageRadio').each(function(key, item) {
			totalRadios += parseInt(item.value);
		});

		
		$('.landingPagePercent').each(function(key, item) { 
			var item = $('#'+item.id);
			var landingPageRatio = $('#ratio_'+item.attr('landingPageId'));

			item.val(Math.round((landingPageRatio.val() / totalRadios)*100) + "%");
		});
	}

	function addItem() {

		var totalRows = $('.landingPageRow').length;

		var landingPageRowNum = (totalRows+1);
		var landingPageRowId = 'LandingPage_'+(totalRows+1);
		$('#landing-page-link-container').append('<div id="'+landingPageRowId+'" class="landingPageRow"></div>');

		var landingPageRow = $('#'+landingPageRowId);
		landingPageRow.append('<div class="sepa"></div>');

		var linkDiv = $(document.createElement('div')).addClass('colN');
		landingPageRow.append(linkDiv);
		linkDiv.append("<input class='formtxt wide_200' type='text' landingPageId='"+landingPageRowNum+"' name='link[]' value='' autocomplete=\"off\" />");


		var ratioDiv = $(document.createElement('div')).addClass('colS');
		landingPageRow.append(ratioDiv);
		ratioDiv.append("<input class=\"formtxt wide_80 landingPageRadio\" type='text' landingPageId=\""+landingPageRowNum+"\" id=\"ratio_"+landingPageRowNum+"\" value='1' onkeydown='javascript: updatePercents();' onkeyup='javascript: updatePercents();' name='ratio[]' autocomplete=\"off\" />");


		var percentageDiv = $(document.createElement('div')).addClass('colN');
		landingPageRow.append(percentageDiv);
		percentageDiv.append("<input id=\"percent_"+landingPageRowNum+"\" class=\"formtxt wide_80 disabled landingPagePercent\" type='text' name='pct[]' disabled='disabled' value='100%' landingPageId='"+landingPageRowNum+"'/>");


		var deleteDiv = $(document.createElement('div'));
		landingPageRow.append(deleteDiv);
		deleteDiv.append("<input class=\"formsubmit tbtn small delete\" type=\"submit\" landingPageId=\""+landingPageRowNum+"\" id=\"delete_"+landingPageRowNum+"\" name='delete[]' value=\"Delete\" />");
		
		updatePercents();

		$('.delete').click(function() {
			$('#LandingPage_'+$(this).attr('landingPageId')).remove();
			updatePercents();
			return false;
		});
		

		return false;
	}

	$(document).ready(function() {

		$('.delete').click(function() {
			$('#LandingPage_'+$(this).attr('landingPageId')).remove();
			updatePercents();
			return false;
		});

		updatePercents();
	});
//]]>
</script>




<script language='javascript'>
//function removeLink(s)
//{
//	var r = s.parentNode.parentNode;
//	r.parentNode.removeChild(r);
//}
//function addLink()
//{
//	var s = document.getElementById('landing-page-link-clone');
//	var c = document.getElementById('landing-page-link-container');
//	var add = document.createElement('div');
//	add.innerHTML = s.innerHTML;
//	c.appendChild(add);
//	updatePcts();
//}
//
//function updatePcts()
//{
//	var s = document.forms[0];
//	var total = 0;
//	for(var i in s["ratio[]"])
//	{
//		var itm = s["ratio[]"][i];
//		if(itm.name !== "ratio[]")
//			continue;
//
//		var val = itm.value;
//		if(val == '')
//			val = 0;	
//		
//		total += parseInt(val);
//	}
//
//	for(var i in s["ratio[]"])
//	{
//		var itm = s["ratio[]"][i];
//		if(itm.name !== "ratio[]")
//			continue;
//
//		s["pct[]"][i].value = Math.round((itm.value / total)*100) + "%";
//	}
//}
//
//updatePcts();

</script>