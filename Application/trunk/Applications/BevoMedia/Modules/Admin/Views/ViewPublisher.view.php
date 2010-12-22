<h2 class='adminPageHeading'>View Publisher</h2>

<div class='floatRight'>
<br/>
<table cellspacing=0 cellpadding=0 class='adminPublisherTable publisherFunctionsBorder textAlignCenter'>
	<tr class='lightBlueRow'>
		<th>
			Admin Functions
		</th>
	</tr>
	<tr>
		<td>
			<a href='GivePremium.html?user=<?php print $this->User->id; ?>' title='Give a month of premium to <?php print $this->User->getUserName(); ?>'>
				Give a month of premium
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<a href='AddAPICreditToUser.html?id=<?php print $this->User->id; ?>' title='Add API Credit to <?php print $this->User->getUserName(); ?>' rel='shadowbox;width=300;height=120;player=iframe'>
				Add API Credit
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<a href='EditPublisher.html?id=<?php print $this->User->id; ?>'>
				Edit Publisher
			</a>
		</td>
	</tr>
	<tr>
		<td>
			<a href='LoginAsPublisher.html?id=<?php print $this->User->id; ?>'>
				Login as Publisher
			</a>
		</td>
	</tr>
	
	<tr>
		<td>
			
		</td>
	</tr>
	
	
	<tr>
		<td>
			<a href='AddDemoData.html?id=<?php print $this->User->id; ?>'>
				Add Demo Data
			</a>
		</td>
	</tr>
</table>
<br/>

<table cellspacing=0 cellpadding=0 class='adminPublisherTable publisherFunctionsBorder textAlignLeft'>
	<tr class='lightBlueRow'>
		<th colspan='3' class='textAlignCenter'>
			Publisher Stats
		</th>
	</tr>
	<tr>
		<td>
		
			<a href='APIUsageDetails.html?id=<?php print $this->User->id; ?>'>
				Details
			</a>
		</td>
		<td class='nameCell textAlignRight'>
			API Balance:
		</td>
		<td>
			<span style='color:<?php echo($this->User->apiCalls < 0)?'#880000':'#008800'?>;'>
				<?= $this->User->apiCalls?>	</span>
		</td>
	</tr>
	<tr>
		<td>
			<a href='ViewNotes.html?id=<?php print $this->User->id; ?>'>
				View
			</a>
		</td>
		<td class='nameCell textAlignRight'>
			Notes:
		</td>
		<td class='textAlignCenter'>
			<?php print $this->User->GetNoteCount(); ?>
		</td>
	</tr>
	<tr>
		<td class='nameCell textAlignRight' colspan='2'>
			Ticket Submissions:
		</td>
		<td class='textAlignCenter'>
			<?php print $this->User->GetNoteCount(); ?>
		</td>
	</tr>
	<tr>
		<td class='nameCell textAlignRight' colspan='2'>
			PPC Accounts:
		</td>
		
		<td class='textAlignCenter'>
			<a href='PublisherPPCAccounts.html?id=<?php print $this->User->id; ?>' title='<?php print sizeOf($this->User->GetDailyAccountsAdwords()); ?> Adwords accounts scheduled for daily updates out of <?php print sizeOf($this->User->GetAllAccounts()); ?> total accounts.'>
				<?php print sizeOf($this->User->GetDailyAccountsAdwords()); ?>/<?php print sizeOf($this->User->GetAllAccounts()); ?>
			</a>
		</td>
	</tr>
	<tr>
		<td class='nameCell textAlignRight' colspan='2'>
			Total Income Generated:
		</td>
		
		<td class='textAlignCenter'>
			$<?php echo number_format($this->User->GetTotalIncomeGenerated(), 2)?>
		</td>
	</tr>
	
</table>
</div>

<h1><?php print $this->User->firstName; ?> <?php print $this->User->lastName; ?></h1>

<b>User ID:</b> <?php print $this->User->id; ?> <br/>
<b>Last Login On:</b> <?php

	if ($this->User->lastLogin!='0000-00-00 00:00:00')
		echo date('m/d/Y', strtotime($this->User->lastLogin)); else
		echo 'Never logged in';
	
?><br/>
<b>Joined On:</b> <?php print date('m/d/Y', strtotime($this->User->created)); ?><br/>
<b>Email Address:</b> <?php print $this->User->email; ?><br/>
<br/>
<b>Company Name:</b> <?php print $this->User->companyName; ?><br/>
<br/>

<b>Address:</b>
<br/>
<?php print $this->User->address; ?><br/>
<?php print $this->User->city; ?> <?php print $this->User->state; ?>, <?php print $this->User->zip; ?><br/>
<?php print $this->User->country; ?>

<br/><br/>

<b>Website:</b> <?php print $this->User->website; ?><br/>
<br/>

<b>Phone:</b> <?php print $this->User->phone; ?><br/>
<br/>

<b>Messenger:</b> <?php print $this->User->messenger; ?><br/>
<b>Messenger Handle:</b> <?php print $this->User->messengerHandle; ?><br/>
<br/>

<b>Marketing Method:</b> <?php print $this->User->marketingMethod; ?><br/>
<b>Other Marketing Method:</b> <?php print $this->User->marketingMethodOther; ?><br/>
<br/>


<b>Comments:</b> <?php print $this->User->comments; ?><br/>
<b>How did you hear about us:</b> <?php print $this->User->howHeard; ?><br/>


<br/>
<?php $TZ = new TimezoneHelper()?>
<b>Timezone:</b> <?php print $TZ->getTimezoneByPHPTimezone($this->User->timezone)->GMTLabel; ?><br/>

<br /><?= $this->pwChanged ? 'Password updated' : ''?>
<br />Reset password: <input type="button" onclick="$(this).hide();$('#changepw').show();" value="Click here">
<div id="changepw" style="display:none">
  <form method="POST">
  <input type="text" name="changepw" value="" /><input type="submit" name="submit" value="submit" />
  </form>
</div>

<br /><br />

<?php 
	$Referrer = $this->User->ReferredBy();
	if ($Referrer!=null) {
?>
		<b>Referred by</b>: <a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $Referrer->id?>"><?php  echo $Referrer->firstName.' '.$Referrer->lastName; ?></a>
<?php 
	} else {
?>
		<b>Referred by</b>: N/A
<?php 
	}
?>

	

<br /><br />

<div>
	<table width="100%">
	<tr>
		<th width="200">Product</th>
		<th width="75">Price</th>
		<th>&nbsp;</th>
	</tr>
	<?php 
		$User = $this->User;
		
		/* @var $User User */
		foreach ($User->GetProducts() as $Product)
		{
	?>
		<tr>
			<td><?php echo round($Product->Quantity)?> <?php echo $Product->ProductName?></td>
			<td>$<?php echo number_format($Product->Price, 2)?></td>
			<td>
			<?php 
				if (!$User->IsSubscribed($Product->ProductName))
				{
			?>
				<a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $_GET['id']?>&Subscribe=1&ProductName=<?php echo $Product->ProductName?>" onclick="return confirm('Are you sure you want to SUBSCRIBE this user?');">Subscribe</a>
			<?php 
				} else { 					
			?>
				<a href="/BevoMedia/Admin/ViewPublisher.html?id=<?php echo $_GET['id']?>&Unsubscribe=1&ProductName=<?php echo $Product->ProductName?>" onclick="return confirm('Are you sure you want to UNsubscribe this user?');">Unsubscribe</a>
			<?php 
				}
			?>
			</td>
		</tr>
	<?php 
		}
	?>
	</table>
	
</div>

