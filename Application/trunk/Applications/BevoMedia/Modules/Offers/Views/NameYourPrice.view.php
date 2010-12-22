<div id="pagemenu">
	
</div>

<?php echo $this->PageDesc->ShowDesc($this->PageHelper, false); //2nd param to hide toggle btn, as there is nothing else on this page ?>



	<form class="appform registerForm" method="post">
		
		<label for="SuggestedOffer">
			<span class="label">Suggested Offer:</span>
			<input type="text" name="SuggestedOffer" value="" id="SuggestedOffer" class="formtxt" />
		</label>
		
		<label for="Niche">
			<span class="label">Niche:*</span>
			<select name="Niche" id="Niche" class="required formselect">
				<option value="0"></option>
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
		
		<label for="CurrentPayout">
			<span class="label">Current Payout:</span>
			<input type="text" name="CurrentPayout" value="" id="CurrentPayout" class="formtxt" />
		</label>
		
		<label for="DesiredBidPayout">
			<span class="label">Desired Bid Payout:*</span>
			<input type="text" name="DesiredBidPayout" value="" id="DesiredBidPayout" class="required formtxt" />
		</label>
		
		<label for="CurrentEPC">
			<span class="label">Current EPC:</span>
			<input type="text" name="CurrentEPC" value="" id="CurrentEPC" class="formtxt" />
		</label>
		
		<label for="DesiredEPC">
			<span class="label">Desired EPC:</span>
			<input type="text" name="DesiredEPC" value="" id="DesiredEPC" class="formtxt" />
		</label>
		
		<label for="TrafficSource">
			<span class="label">Traffic Source:</span>
			<select name="TrafficSource" id="TrafficSource" class="required formselect">
				<option>Not Available</option>
				<option>Search</option>
				<option>Social</option>
				<option>Display</option>
				<option>PPV</option>
				<option>Email</option>
			</select>
		</label>
		
		<input type="submit" name="Submit" value="Submit" />
		
	</form>
	
