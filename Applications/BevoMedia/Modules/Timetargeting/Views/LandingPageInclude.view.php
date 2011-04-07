<?php 
	$count = $_GET['count'];
?>

	<fieldset id="landingPageFieldset_<?=$count?>" style="padding: 5px;">
		<legend>Landing Page</legend>
		
		<label>
			<span style="width: 120px; display: inline-block;">Landing Page URL:</span>
			<input type="text" class="formtxt landingPageUrl" id="landingPageURL_<?=$count?>" name="landingPageURL_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>" value="<?=isset($this->Data->URL)?$this->Data->URL:''?>" />
		</label>
		
		<a id="addCountry" class="remove-landing-page" href="#" rowId="<?=$count?>" dbId="<?=isset($this->Data->ID)?$this->Data->ID:'0'?>">Remove Landing Page</a>
		
		<br /><br />
		<?php 
			$SelectedDay = -1;
			
			if (isset($this->Data->Times->Day))
			{
				$SelectedDay = $this->Data->Times->Day;
			}
		?>
		<fieldset style="margin-left: 120px; padding: 5px;">
		<legend>Day/Time</legend>
			<div id="dayTime">
				<label>
				<span style="width: 75px; float: left; line-height: 28px;">Day:</span>
				<span style="width: 120px; float: left;">
					<select name="day_<?=$count?>_<?=isset($this->Data->ID)?$this->Data->ID:'0'?>" class="formselect" style="width: 120px">
						<option value="-1" <?=($SelectedDay==-1)?'selected':''?>>Any Day</option>
						<option value="0" <?=($SelectedDay==0)?'selected':''?>>Monday</option>
						<option value="1" <?=($SelectedDay==1)?'selected':''?>>Tuesday</option>
						<option value="2" <?=($SelectedDay==2)?'selected':''?>>Wednesday</option>
						<option value="3" <?=($SelectedDay==3)?'selected':''?>>Thursday</option>
						<option value="4" <?=($SelectedDay==4)?'selected':''?>>Friday</option>
						<option value="5" <?=($SelectedDay==5)?'selected':''?>>Saturday</option>
						<option value="6" <?=($SelectedDay==6)?'selected':''?>>Sunday</option>
					</select>
				</span>
				</label>
				&nbsp;
				<br clear="all" />
				
				
				<span style="width: 75px; float: left; line-height: 28px;">From Hour:</span>
				<span style="width: 100px; float: left;">
					<select name="fromTime_<?=$count?>" class="formselect" style="width: 100px">
						<option value="-1">Any Time</option>
				<?php 
					for ($i=0; $i<=24; $i++)
					{
						$selected = '';
						if (isset($this->Data->Times->Start)) 
						{
							if ($i==$this->Data->Times->Start) $selected = 'selected';
						}
				?>
						<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
				<?php 
					}
				?>
					</select>
				</span>
				
				
				<span style="float: left;">
				&nbsp;&nbsp;&nbsp;
				</span>
				
				
				<span style="width: 55px; float: left; line-height: 28px;">To Hour:</span>
				<span style="width: 100px; float: left;">
					<select name="toTime_<?=$count?>" class="formselect" style="width: 100px">
						<option value="-1">Any Time</option>
				<?php 
					for ($i=0; $i<=24; $i++)
					{
						$selected = '';
						
						if (isset($this->Data->Times->End)) 
						{
							if ($i==$this->Data->Times->End) $selected = 'selected';
						}
				?>
						<option value="<?=$i?>" <?=$selected?>><?=$i?></option>
				<?php 
					}
				?>
					</select>
				</span>
				
				<br clear="all" />
			</div>
		</fieldset>
		
	</fieldset>
	
	<script type="text/javascript">

		$('#landingPageFieldset_<?=$count?> .remove-landing-page').click(function() {

			if (!confirm('Are you sure you want to remove this landing page?')) return false;

			$.get('/BevoMedia/Geotargeting/RemoveLandingPage.html?ID='+$(this).attr('dbId'));
			
			$('#landingPageFieldset_'+$(this).attr('rowId')).remove();

			return false;
		});
	
	</script>
	
	
	
	<script type="text/javascript">

	<?php 
		if (isset($this->Data->Times))
		foreach ($this->Data->Times as $Location) {
	?>
		addLocation_<?=$count?>(<?php echo $Location->ID;?>);
	<?php
		}
	?>
	
	</script>
	
	