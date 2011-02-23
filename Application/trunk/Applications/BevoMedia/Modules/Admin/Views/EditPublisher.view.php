<h2 class='adminPageHeading'>Edit Publisher</h2>

<h1><?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?></h1>

<br/>

<b>User ID:</b> <?php print $this->User->id; ?> <br/>
<b>Joined On:</b> <?php print date('m/d/Y', strtotime($this->User->created)); ?><br/>
<b>User Email:</b> <?php print $this->User->email; ?> <br/>

<br/><br/>

<form method='post'>
	<b>First Name:</b>
	<br/>
	<input type='text' name='firstName' value='<?php print $this->User->firstName; ?>'/>
	<br/>
	<b>Last Name:</b>
	<br/>
	<input type='text' name='lastName' value='<?php print $this->User->lastName; ?>'/>
	<br/>

	<br/><br/>
	
	<b>Company Name:</b>
	<br/>
	<input type='text' name='companyName' value='<?php print $this->User->companyName; ?>'/>
	<br/>

	<br/><br/>
	
	<b>Address:</b>
	<br/>
	<input type='text' name='address' value='<?php print $this->User->address; ?>'/>
	<br/>
	
	<b>City:</b>
	<br/>
	<input type='text' name='city' value='<?php print $this->User->city; ?>'/>
	<br/>
	
	<b>State:</b>
	<br/>
	<input type='text' name='state' value='<?php print $this->User->state; ?>'/>
	<br/>
	
	<b>Zipcode:</b>
	<br/>
	<input type='text' name='zip' value='<?php print $this->User->zip; ?>'/>
	<br/>
	
	<b>Country:</b>
	<br/>
	<input type='text' name='country' value='<?php print $this->User->country; ?>'/>
	<br/>

	
	<br/><br/>

	<b>Website:</b>
	<br/>
	<input type='text' name='website' value='<?php print $this->User->website; ?>'/>
	<br/>
	
	
		<br/><br/>

	<b>Phone:</b>
	<br/>
	<input type='text' name='phone' value='<?php print $this->User->phone; ?>'/>
	<br/>
	
		
	<br/><br/>

	<b>Messenger:</b>
	<br/>
	<?php foreach($this->PageHelper->GetMessengers() as $Messenger):?>
		<input <?php echo($this->User->messenger == $Messenger)?'checked="CHECKED"':''?>type='radio' name='Messenger' value='<?php echo $Messenger?>'/>
		<?php echo $Messenger?>
		<br/>
	<?php endforeach?>
	
	<b>Messenger Handle:</b>
	<br/>
	<input type='text' name='MessengerHandle' value='<?php print $this->User->messengerHandle; ?>'/>
	<br/>
	
	<br/><br/>
	
	

	<b>Marketing Method:</b>
	<br/>
	<?php foreach($this->PageHelper->GetMarketingMethods() as $Method):?>
		<input <?php echo($this->User->marketingMethod == $Method)?'checked="CHECKED"':''?>type='radio' name='MarketingMethod' value='<?php echo $Method?>'/>
		<?php echo $Method?>
		<br/>
	<?php endforeach?>
	
	<b>Marketing Method Other:</b>
	<br/>
	<input type='text' name='MarketingMethodOther' value='<?php print $this->User->marketingMethodOther; ?>'/>
	<br/>
	
	<br/><br/>
	
	
	<b>Comments:</b>
	<br/>
	<textarea name='Comments'><?php print $this->User->comments; ?></textarea>
	
	<br/><br/>
	
	
	<b>Timezone:</b>
	<br/>
	<select name="Timezone" id="timezone_id">
		<?php $tz = new TimezoneHelper()?>
		<?php foreach($tz->getTimezones() as $timezone):?>
			<option <?php echo($timezone->PHPTimezone == $this->User->timezone)?'selected="SELECTED"':''?> value="<?php print $timezone->PHPTimezone; ?>"><?php print $timezone->GMTLabel; ?></option>
		<?php endforeach?>
	</select>

	<br/><br/>
	
	<b>Bevo Performance Connector</b>
	<br/>
	<select name="niche[]" size="10" id="niche" class="required formselect" rel="Niche" multiple="multiple">
		<?php 
			foreach ($this->Niches as $Niche) {
				$selected = '';
				if (in_array($Niche->ID, $this->UserNicheIDs)) {
					$selected = 'selected="selected"';
				}
		?>
				<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo $Niche->Name?></option>
		<?php 
			}
		?>
	</select>
	
	<br/><br/>
	
	<input type='hidden' name='ID' value='<?php print $this->User->id; ?>'/>
	<input type='submit' name='editPublisherSubmit' />
	
</form>

