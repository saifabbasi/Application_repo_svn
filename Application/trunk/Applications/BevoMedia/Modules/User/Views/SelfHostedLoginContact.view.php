<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/bob.style.css" rel="stylesheet" type="text/css" />

<?
	if (isset($_POST['Submit']))
	{
		if ($_GET['MembershipType']=='Premier Membership')
		{
?>
		<div style="text-align: center;">
			Thank you for your request to use the beta self hosted version of Bevo Media. <br />
			A representative will be contacting you shortly to explain to you the Beta Process and to provide your designated download link.
		</div>

		<br/>
		You will be redirected to the download location when this window closes...
		<br/>
		<a href='#' onClick='javascript:parent.Shadowbox.close();'>Close Window</a>
		<br/><br/>
		<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
		<script language="Javascript">
			function onclose()
			{
				LayoutAssist.parentLocation('/BevoMedia/User/SelfHostedLoginDownload.html');
			}
			window.onunload = onclose;
			LayoutAssist.shadowboxCloseTimer(10);
		</script>
<?
			return;
		} else
		if ($_GET['MembershipType']=='Deluxe Membership')
		{
?>
		<div style="text-align: center;">
			Thank you for your submission. A Bevo Media representative will be in contact with you shortly.
		</div>
		
		<br/>
		You will be redirected to the download location when this window closes...
		<br/>
		<a href='#' onClick='javascript:parent.Shadowbox.close();'>Close Window</a>
		<br/><br/>
		<div id='LayoutAssist_Shadowbox_Close_Timer'></div>
		<script language="Javascript">
			function onclose()
			{
				LayoutAssist.parentLocation('/BevoMedia/User/SelfHostedLoginDownload.html');
			}
			window.onunload = onclose;
			LayoutAssist.shadowboxCloseTimer(10);
		</script>	
<?
			return;
		}
	}
?>

<?
	if ($_GET['MembershipType']=='Premier Membership')
	{
?>
		Before you download Bevo Media Premier, The Self Hosted Beta of Bevo Media - Please provide your contact information.
<?
	} else
	if ($_GET['MembershipType']=='Deluxe Membership')
	{
?>
		Thank you for your interest in Bevo Media Deluxe, the customizable version of Bevo Media. 
		<br />
		Please provide the following information and a representative will contact you shortly.
<?
	}
?>


<br /><br />

<form method="post">
<input type="hidden" name="MembershipType" value="<?=$_GET['MembershipType']?>" />
<fieldset style="width: 550px; margin-left: auto; margin-right: auto; text-align: left;">
	<legend>Contact Us</legend>
	<table style="margin-left: auto; margin-right: auto; text-align: left;" width="500">
		<tr>
			<td>Name:</td>
			<td>
				<input type="text" name="Name" value="" />
			</td>
		</tr>
		<tr>
			<td>E-mail:</td>
			<td>
				<input type="text" name="Email" value="" />
			</td>
		</tr>
		
<?
	if ($_GET['MembershipType']=='Deluxe Membership')
	{
?>
		<tr>
			<td>Company:</td>
			<td>
				<input type="text" name="Company" value="" />
			</td>
		</tr>
<?
	}
?>
		<tr>
			<td>Address:</td>
			<td>
				<input type="text" name="Address" value="" />
			</td>
		</tr>
		<tr>
			<td>Phone Number:</td>
			<td>
				<input type="text" name="PhoneNumber" value="" />
			</td>
		</tr>
		
<?
	if ($_GET['MembershipType']=='Premier Membership')
	{
?>
		<tr>
			<td>AIM Screen Name:</td>
			<td>
				<input type="text" name="AIM" value="" />
			</td>
		</tr>
<?
	}
?>
		<tr>
			<td>Are you an:</td>
			<td>
				<label><input type="radio" name="UserType" value="Affiliate Marketer" /> Affiliate Marketer</label> <br />
				<label><input type="radio" name="UserType" value="PPC Firm" /> PPC Firm</label> <br />
				<label><input type="radio" name="UserType" value="Business" /> Business Owner</label> <br />
<?
	if ($_GET['MembershipType']=='Deluxe Membership')
	{
?>
				<label><input type="radio" name="UserType" value="Other" /> Other</label> <br />
<?
	}
?>
			</td>
		</tr>
		
<?
	if ($_GET['MembershipType']=='Premier Membership')
	{
?>
		<tr>
			<td>What type of marketing methods do you do <br />(check all that apply):</td>
			<td>
				<label><input type="checkbox" name="MarketingMethods[]" value="Search Marketing" /> Search Marketing</label> <br />
				<label><input type="checkbox" name="MarketingMethods[]" value="Media Buy Marketing" /> Media Buy Marketing</label> <br />
				<label><input type="checkbox" name="MarketingMethods[]" value="Other" /> Other</label> <br />
			</td>
		</tr>
<?
	}
?>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" name="Submit" value="Submit" class="formSubmit" />
			</td>
		</tr>
	</table>
</fieldset>
</form>


