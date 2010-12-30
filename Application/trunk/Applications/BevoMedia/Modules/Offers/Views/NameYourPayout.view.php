<div id="pagemenu">
	<ul>
		<li><a href="/BevoMedia/Offers/Index.html">Offers<span></span></a></li>
		<li><a class="active" href="/BevoMedia/Offers/NameYourPayout.html">Name Your Payout<span></span></a></li>
	</ul>
</div>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false, false, false, 'nypdesc'); //disable toggle, custom css class
?>
<div id="nypbg"></div>
<div class="pagecontent nyp">
	<form class="appform" method="post" id="nypform">
		<input type="hidden" name="SubmitAjax" value="1" />
		<div class="box box3 n1">
			<div class="boxtitle">
				<h3>1. Offer</h3>
				<p>Select the niche for which you'd like Bevo to find the best payout.</p>
			</div>
			
			<label for="Niche">
				<span class="label">Niche:*</span>
				<select name="Niche" id="Niche" class="required formselect" rel="Niche">
					<option value="0">Select...</option>
					<?php 
						foreach ($this->Niches as $Niche) {
							$selected = '';
							if ($Niche->ID==$_GET['NicheID']) $selected = 'selected';
					?>
								<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo $Niche->Name?></option>
					<?php 
						}
					?>
				</select>
			</label>
			
			<label for="SuggestedOffer">
				<span class="label">Offer Name:</span>
				<input type="text" name="SuggestedOffer" value="" id="SuggestedOffer" class="formtxt" />
			</label>
			
			<label for="TrafficSource">
				<span class="label">Traffic Source:</span>
				<select name="TrafficSource" id="TrafficSource" class="formselect">
					<option value="Not Available">Not Specified</option>
					<option>Search</option>
					<option>Social</option>
					<option>Display</option>
					<option>PPV</option>
					<option>Email</option>
				</select>
			</label>
			
			<p><em>Optional: If you have your eyes on a specific offer, you can enter that too. You can also specify the desired traffic source for more accurate results.</em></p>
		</div><!--close box-->
		
		<div class="box box3 n2">
			<div class="boxtitle">
				<h3>2. Current Payout</h3>
				<p>Enter your current payout and EPC for this offer.</p>
			</div>
			
			<label for="CurrentPayout">
				<span class="label">Payout:</span>
				$<input type="text" name="CurrentPayout" value="" id="CurrentPayout" class="formtxt wide_number" autocomplete="off" />
			</label>
			
			<label for="CurrentEPC">
				<span class="label">EPC:</span>
				$<input type="text" name="CurrentEPC" value="" id="CurrentEPC" class="formtxt wide_number" autocomplete="off" />
			</label>
			
			<p><em>Optional step. For a more efficient search, we recommend that you enter your current payout and EPC for the niche/offer you're looking to find a better payout for.</em></p>			
		</div><!--close box-->
		
		<div class="box box3 nomargin n3">
			<div class="boxtitle">
				<h3>3. Desired Payout</h3>
			</div>
			
			<label for="DesiredBidPayout">
				<span class="label">Payout:*</span>
				$<input type="text" name="DesiredBidPayout" value="" id="DesiredBidPayout" class="required formtxt wide_number" autocomplete="off" rel="Desired Payout" />
			</label>
			
			<label for="DesiredEPC">
				<span class="label">EPC:</span>
				$<input type="text" name="DesiredEPC" value="" id="DesiredEPC" class="formtxt wide_number" autocomplete="off" />
			</label>
		
		</div><!--close box-->
		<div class="clear"></div>
		
		<div class="box boxfull bordertop aligncenter">
			<h3>Submit Query</h3>
			<p>Bevo's powerful Offer Search engine will now process your request. This will take a few seconds.</p>
			<input class="formsubmit btn_off_submit" type="submit" name="Submit" value="Submit" />
			<p>If a match is found, it will be displayed on the next screen.</p>
		</div>
	</form>
	<div class="working aligncenter">
		<h3>Working...</h3>
		<p>Stand by while we calculate your results. This should only take a few seconds.</p>
		<div class="icon icon_working_48"></div>
	</div>
</div><!--close pagecontent-->

<script type="text/javascript">
$(document).ready(function() {
	var payoutfound = false;	
	$('#nypbg').delay(500).fadeIn(2000);	
	$('#nypform').submit(function() {
		if(payoutfound)
			return true;
		else {
			var 	m = '',
				data;				
			$('.required', $(this)).each(function() {
				if($(this).val() == '' || $(this).val() == 0) {
					m += 'Please enter a '+$(this).attr('rel')+"!\n"; 
				}
			});
			if(m != '')
				alert(m);
			else {
				$('#nypform, #nypbg').slideUp(400);
				$('.nyp .working').slideDown(400, function() {
					$('#pagedesc.nypdesc').animate({width:960},100);
					$('#pagedesc.nypdesc p.nypnote').html('Please note: the highest bidding network may not match your desired payout and/or EPC <em>exactly</em>, but it\'s the winner among all bidders.');
					data = $('#nypform').serialize();
					$.post('/BevoMedia/Offers/NameYourPayoutResult.html', data, soap_nypDisplayResults);
				});	
			}
			return false;
		}
	});	
	function soap_nypDisplayResults(r) {
		r = eval('('+r+')');		
		window.location = '/BevoMedia/Offers/NameYourPayoutResult.html?ID='+r.ID;		
	}
});
</script>
