<?php
require(PATH.'classes/clsMarketProjects.php');
require(PATH.'classes/clsMarketProjectTerms.php');
require(PATH.'classes/clsMarketProviders.php');
require(PATH.'classes/clsUserInfo.php');

$isMarketPage = true;

global $userId, $intID, $strName, $intAcceptedID, $intLastDeposit;
global $intDeposit, $intOrderID, $strTermDate, $strTerms, $intProviderComplete, $intUserComplete;

$userId = $this->User->id;

if(isset($_GET['ID']))
	$intID = $_GET['ID'];

if (!is_numeric($intID)) {
	$intID = 0;
}

LegacyAbstraction::doAction('Create', 'CreateProject');
LegacyAbstraction::doAction('Update', 'UpdateTerms');
LegacyAbstraction::doAction('Accept', 'AcceptTerms');
LegacyAbstraction::doAction('Complete', 'MarkComplete');

LoadProject();

function CreateProject() {	
	global $userId;
	
	$intProviderIDVal = $_POST['ProviderID'];
	$strNameVal = $_POST['Name'];
	$intDepositVal = $_POST['Deposit'];
	$strTermsVal = $_POST['Terms'];
	
	$strNow = date('Y-m-d H:i:s', time());
	
	$objProject = new MarketProjects();
	$objProject->userId = $userId;
	$objProject->providerId = $intProviderIDVal;
	$objProject->name = $strNameVal;
	$objProject->date = $strNow;
	$objProject->lastPost = $strNow;
	$objProject->acceptedId = 0;
	$objProject->userComplete = 0;
	$objProject->providerComplete = 0;
	$intProjectID = $objProject->Insert();
	
	$objTerms = new MarketProjectTerms();
	$objTerms->projectId = $intProjectID;
	$objTerms->userId = $userId;
	$objTerms->providerId = 0;
	$objTerms->deposit = $intDepositVal;
	$objTerms->terms = $strTermsVal;
	$objTerms->date = $strNow;
	$objTerms->Insert();
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderIDVal;
	$objProvider->GetDetails();
	
	
	
	$strMessage = 'Bevo Media Marketplace Project Initiation Details:' . "\n\n";
	$strMessage .= 'Date:' . "\n" . $strNow . "\n\n";
	$strMessage .= 'Terms:' . "\n" . $strTermsVal . "\n\n";
	$strMessage .= 'Deposit:' . "\n" . $intDepositVal . "\n\n";
	$strMessage .= "Provider Name: {$objProvider->Name} <br /> Provider E-mail: {$objProvider->Email} <br/><br/>";
	$strSubject = 'Bevo Media Marketplace Project Initiation Details';
	
	
	$strMessage = nl2br($strMessage);
	
	$strProviderEmail = $objProvider->Email;
	
	$objUser = new User($userId);
	
	$strUserEmail = $objUser->Email;
	
	$ini = parse_ini_file(ABSPATH . 'Applications/BevoMedia/' . 'config.ini', true);
	$adminEmail =  $ini['Instance']['AdminEmail'];

	// $headers = 'From: noreply@' . $_SERVER['HTTP_HOST'] . "\r\n" . 'Reply-To: noreply@' . $_SERVER['HTTP_HOST'] . "\r\n";
	
	// mail($adminEmail, $strSubject, $strMessage, $headers);
	// mail($strProviderEmail, $strSubject, $strMessage, $headers);
	// mail($strUserEmail, $strSubject, $strMessage, $headers);
	
	
	$MailComponentObject = new MailComponent();
	$MailComponentObject->setFrom('noreply@' . $_SERVER['HTTP_HOST']);
	
	$MailComponentObject->setSubject($strSubject);
	$MailComponentObject->setHTML($strMessage);
	$MailComponentObject->send(array($strUserEmail));
	
	$MailComponentObject->setFrom($strUserEmail);
	$MailComponentObject->send(array($adminEmail, $strProviderEmail));
	
	
	
	
	
	header('Location: PublisherMarketTerms.html?ID=' . $intProjectID);
}

function UpdateTerms() {
	global $userId, $intID;
	
	$intDepositVal = $_POST['Deposit'];
	$strTermsVal = $_POST['Terms'];
		
	$strNow = date('Y-m-d H:i:s', time());
	
	$objProject = new MarketProjects();
	$objProject->id = $intID;
	$objProject->lastPost = $strNow;
	$objProject->Update();
	
	$objTerms = new MarketProjectTerms();
	$objTerms->projectId = $intID;
	$objTerms->userId = $userId;
	$objTerms->providerId = 0;
	$objTerms->deposit = $intDepositVal;
	$objTerms->terms = $strTermsVal;
	$objTerms->date = $strNow;
	$objTerms->Insert();
		
	header('Location: PublisherMarketTerms.html?ID=' . $intID);
}

function LoadProject() {
	global $intID, $strName, $intUserID, $intProviderID, $strDate, $strLastPost, $intAcceptedID, $intOrderID, $strUserName, $strProviderName, $intDeposit, $strTerms, $strTermDate, $intUserComplete, $intProviderComplete;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();

	$strName = $objProject->Name;
	$intUserID = $objProject->user__id;
	$intProviderID = $objProject->providerId;
	$strDate = $objProject->Date;
	$strLastPost = $objProject->LastPost;
	$intAcceptedID = $objProject->AcceptedID;
	$intOrderID = $objProject->OrderID;
	$intUserComplete = $objProject->UserComplete;
	$intProviderComplete = $objProject->ProviderComplete;
	
	// Load Provider Name
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strProviderName = $objProvider->Name;
	
	// Load User Name
	$objUser = new UserInfo();
	$objUser->ID = $intUserID;
	$objUser->GetDetails();
	
	$strUserName = $objUser->FirstName . ' ' . $objUser->LastName;

	// Load Accepted Terms
	if ($intAcceptedID == 0) {
		return false;
	}
	
	$objTerms = new MarketProjectTerms();
	$objTerms->ID = $intAcceptedID;
	$objTerms->GetDetails();
	
	$intDeposit = $objTerms->Deposit;
	$strTerms = $objTerms->Terms;
	$strTermDate = $objTerms->Date;
}

function AcceptTerms() {
	global $intID, $userId;
	
	$intTermsID = $_GET['TermID'];
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->AcceptedID = $intTermsID;
	$objProject->LastPost = date('Y-m-d H:i:s', time());
	$objProject->Update();
	
	// Email Provider
	$objProject->GetDetails();
	$strProjectName = $objProject->Name;
	$intProviderID = $objProject->ProviderID;
	
	// Load Provider Name/Email
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strProviderName = $objProvider->Name;
	$strProviderEmail = $objProvider->Email;
	
	// Load User Name
	$objUser = new UserInfo();
	$objUser->ID = $userId;
	$objUser->GetDetails();
	$strUserName = $objUser->FirstName . ' ' . $objUser->LastName;
	
	// Send Email
	$strMessage = "Hello " . $strProviderName . ",\n" . $strUserName . " has accepted the terms for the project " . $strProjectName . ". Once a deposit is made, you should begin work.\n\n
					https://www.bevomedia.com/freelancers/terms.php?ID=" . $intID;
	$header = "From: Bevo Media Marketplace <market@bevomedia.com>\r\n"; //optional headerfields
	//mail($strProviderEmail, 'Bevo Media Marketplace', $strMessage, $header);
	
	$MailComponentObject = new MailComponent();
	$MailComponentObject->setFrom('market@bevomedia.com');
	
	$MailComponentObject->setSubject('Bevo Media Marketplace');
	$MailComponentObject->setHTML($strMessage);
	$MailComponentObject->send(array($strProviderEmail));	
	
	
	header('Location: PublisherMarketTerms.html?ID=' . $intID);
}

function MarkComplete() {
	global $intID, $userId;
	
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->UserComplete = 1;
	$objProject->LastPost = date('Y-m-d H:i:s', time());
	$objProject->Update();
	unset($objProject);
	
	// Email Provider
	$objProject = new MarketProjects();
	$objProject->ID = $intID;
	$objProject->GetDetails();
	$strProjectName = $objProject->Name;
	$intProviderID = $objProject->ProviderID;
	
	// Load Provider Name/Email
	$objProvider = new MarketProviders();
	$objProvider->ID = $intProviderID;
	$objProvider->GetDetails();
	
	$strProviderName = $objProvider->Name;
	$strProviderEmail = $objProvider->Email;
	
	// Load User Name
	$objUser = new UserInfo();
	$objUser->ID = $userId;
	$objUser->GetDetails();
	$strUserName = $objUser->FirstName . ' ' . $objUser->LastName;
	
	// Send Email
	$strMessage = "Hello " . $strProviderName . ",\n" . $strUserName . " has marked the project " . $strProjectName . " as completed. Please visit the project page and confirm completion.\n\n
					https://www.bevomedia.com/freelancers/terms.php?ID=" . $intID;
	$header = "From: Bevo Media Marketplace <market@bevomedia.com>\r\n"; //optional headerfields
	//mail($strProviderEmail, 'Bevo Media Marketplace', $strMessage, $header);
	
	
	$MailComponentObject = new MailComponent();
	$MailComponentObject->setFrom('market@bevomedia.com');
	
	$MailComponentObject->setSubject('Bevo Media Marketplace');
	$MailComponentObject->setHTML($strMessage);
	$MailComponentObject->send(array($strProviderEmail));	
	
	
	header('Location: PublisherMarketTerms.html?ID=' . $intID);
}

function ListTerms() {
	global $intID, $intAcceptedID, $intLastDeposit;
	
	$objTerms = new MarketProjectTerms();
	$objTerms->GetListByProjectID($intID);
	
	if ($objTerms->RowCount == 0) {
		return false;
	}
	
	$arrRows = $objTerms->GetRows();
	
	$intCount = count($arrRows);
	for ($intX = 0; $intX < $intCount; $intX++) {
		$arrThisRow = $arrRows[$intX];

		// Don't List Accepted Terms with Other Terms
		if ($arrThisRow['id'] == $intAcceptedID) {
			continue;
		}
		
		$intLastDeposit = $arrThisRow['deposit'];
?>
		<li>
			<ul>
<?php if ($arrThisRow['userId'] != 0) { ?>
				<li class="num1">Affiliate: <span class="txtdark"><?php echo $arrThisRow['UserName']; ?></span></li>
<?php } else { ?>
				<li class="num1">Freelancer: <span class="txtdark"><?php echo $arrThisRow['ProviderName']; ?></span></li>
<?php } ?>
				<li class="num2">Date: <span class="txtdark"><?php echo $arrThisRow['date']; ?></span></li>
				<li class="num3">Price $: <span class="txtdark"><?php echo $arrThisRow['deposit']; ?></span></li>
			</ul>			
			<ul>
				<li class="full">Project Agreement: 
					<span class="desc"><?php echo $arrThisRow['terms']; ?>
					
<?php if ($intAcceptedID == 0 && $arrThisRow['userId'] == 0 && ($intX+1) == $intCount) { ?>					
					<a class="button acceptterms" href="PublisherMarketTerms.html?ID=<?php echo $intID; ?>&TermID=<?php echo $arrThisRow['ID']; ?>&Action=Accept">Accept Terms</a>
<?php } ?>
					</span>
				</li>
			</ul>
		</li>
<?php
	}
}

?>

<?
$strPageHead = '<script language="Javascript" src="js/jquery-1.3.1.min.js"></script>';
?>
 
<div class="SkyBox"><div class="SkyBoxTopLeft"><div class="SkyBoxTopRight"><div class="SkyBoxBotLeft"><div class="SkyBoxBotRight">
		<table width="550" cellspacing="0" cellpadding="5" border="0">
			<tr valign="top">
				<td width="127"><img src="/Themes/BevoMedia/img/marketicon.gif" width="118" height="127" border=0 alt=""></td>

				<td class="main">
				<center>
					<h4>Marketplace</h4>
					<br>
	Looking for a design, programmer or content writer? The Bevo Marketplace consists of handpicked service providers specializing in their service within the internet marketing realm. To get started, send a message to your desired service provider. After communication initiate a project, agree to terms and get started. All services are satisfaction guaranteed.
					
<!-- 
	Easily and securely purchase online commonly needed services from Bevo Media. We offer content creation, graphic design, programming jobs and search engine optimization – BeVo can connect you with whatever online service you need. Services are delivered quickly and with high quality.  Buy with confidence from BeVo Media!
 -->      
</center>
</td>
			</tr>
		</table>
	</div></div></div></div></div>
	
<!-- BEGIN Newbies Package -->
<div class="mpnewb-wrapper">
	<a id="mpnewb_button" class="button isclosed" href="#" title="Click to view">Newbie Package</a>
	
	<div id="mpnewb">
		<div class="mpnewb-box">
			<div class="mpnewb-txtlarge">45</div>
			<div class="mpnewb-txtsmall">min.</div>
			<div class="mpnewb-txtnormal">Consulting Session</div>
		</div>
		<div class="mpnewb-box">
			<div class="mpnewb-txtlarge">1</div>
			<div class="mpnewb-txtsmall">premium</div>
			<div class="mpnewb-txtnormal">Landing Page</div>
		</div>
		<div class="mpnewb-box mpnewb-third">
			<div class="mpnewb-txtlarge">5</div>
			<div class="mpnewb-txtsmall">keyword-rich</div>
			<div class="mpnewb-txtnormal">Articles</div>
		</div>
		<div class="mpnewb-box mpnewb-rightmost">
			<div class="mpnewb-oldprice">
				799.00 <span class="mpnewb-txttiny">Value</span>
			</div>
			<div class="mpnewb-price">499</div>
			<a class="button getinfo" href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Marketplace/NewbiePackage.html">Get Info</a>
		</div>
	</div>
</div>
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/mpnewb.style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
$(document).ready(function() {
$.fn.pause = function(duration){$(this).animate({dummy:1},duration);return this;};
$('a#mpnewb_button').click(function(){
	if($(this).hasClass('isclosed')) {$('div#mpnewb').show(function(){
		$('a#mpnewb_button').removeClass('isclosed').addClass('isopen');$(this).slideDown(400);
		$('div.mpnewb-rightmost').pause(1000).fadeIn(400);$('div.mpnewb-price').pause(1400).slideDown(400);
	});
	} else {
		$('div#mpnewb').hide(function(){$('a#mpnewb_button').removeClass('isopen').addClass('isclosed');$(this).slideUp(100);});
	}
});
});
</script>
<!-- ENDOF Newbies Package -->

<br/>

<div class="prsv-wrapper"><!-- project single view wrapper -->

	<h2>Project <span class="txtblue"><?php echo $strName; ?></span></h2>

<?php if ($intAcceptedID != 0) { ?>
	<ul class="prsv-list darkblue">
		<li class="prsv-header">
			Accepted Project Agreement

<?php if($intOrderID == 0) { ?>
				<div class="prsv-pay">
					<span>You must make a deposit for the work to begin</span>
					
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input name="item_number" type="hidden" value="<?php echo $intID; ?>"/> 
<input name="cmd" type="hidden" value="_xclick" /> 
<input name="business" type="hidden" value="payment@bevomedia.com" /> 
<input name="lc" type="hidden" value="US" />
<input name="item_name" type="hidden" value="<?php echo $strName; ?>"/>  
<input name="amount" type="hidden" value="<?php echo $intDeposit; ?>"/> 
<input name="currency_code" type="hidden" value="USD"/> 
<input name="no_note" type="hidden" value="1"/> 
<input name="no_shipping" type="hidden" value="1"/> 
<input name="rm" type="hidden" value="1"/> 
<input name="return" type="hidden" value="https://www.bevomedia.com/PublisherMarketTerms.html?ID=<?php echo $intID; ?>"/> 
<input name="weight_unit" type="hidden" value="lbs"/> 
<input name="bn" type="hidden" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHosted"/> 
<input name="notify_url" type="hidden" value="https://www.bevomedia.com/market-ipn.php"/> 

<input name="submit" src="<?=SCRIPT_ROOT?>img/button_makepayment_small.gif" type="image" /></p>

</form> 

				</div>
<?php } else { ?>

<?php if ($intProviderComplete != 0) { ?>
<!--<p>
The freelancer has marked this project as complete.
</p> -->
<?php } ?>

<?php if ($intUserComplete != 0) { ?>
				<div class="prsv-pay2">
					<span>You have marked this project as complete.</span>
				</div>
<?php } else { ?>
				<div class="prsv-pay">
					<span>Click here to confirm when a project is complete</span>
					<a class="button completeproject_small" href="PublisherMarketTerms.html?ID=<?php echo $intID; ?>&Action=Complete">Complete Project</a>
				</div>
<?php } ?>

<?php } ?>
		</li>
		<li>
			<ul>
				<li class="num1">Date: <span class="txtblue strong"><?php echo $strTermDate; ?></span></li>
				<li class="num2">Project Price $: <span class="txtblue strong"><?php echo $intDeposit; ?> <?php if ($intOrderID != 0) { ?>(Deposited)<?php } ?></span></li>
			</ul>
			<ul>
				<li class="full">Project Agreement: 
					<span class="desc"><?php echo $strTerms; ?></span>
				</li>
			</ul>
		</li>
		<li class="prsv-footer"></li>
	</ul>
<?php } ?>


	<ul class="prsv-list gray">
		<li class="prsv-header">
			Proposed Project Agreements
		</li>
<?php ListTerms(); ?>
		<li class="prsv-footer"></li>
	</ul>
</div>

<?php if ($intAcceptedID == 0) { ?>

<h3>Edit Proposed Agreement</h3>

<form method="post" action="PublisherMarketTerms.html?ID=<?php echo $intID; ?>&Action=Update">
<table>
  <tr id="ShowPrice">
    <td><label for="Deposit">Project Price $:</label></td>
	<td><?php echo $intLastDeposit; ?> <a href="#ShowPrice" onClick="$('#EditPrice').show(); $('#ShowPrice').hide(); ">Edit</a></td>
  </tr>
  <tr id="EditPrice" style="display: none;">
    <td><label for="Deposit">Project Price $:</label></td>
	<td><input type="text" name="Deposit" id="Deposit" value="<?php echo $intLastDeposit; ?>"/></td>
  </tr>
  <tr>
    <td><label for="Terms">Adjusted Agreement:</label></td>
	<td><textarea name="Terms" id="Terms" cols="40" rows="4"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Submit"/></td>
  </tr>
</table>
</form>

<?php } ?>
