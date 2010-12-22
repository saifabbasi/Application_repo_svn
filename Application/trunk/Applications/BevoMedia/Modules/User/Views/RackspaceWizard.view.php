<div id="pagemenu"></div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php	//server settings: form field values
	
	//user's personal settings. map these here!
	$rack_apikey = false; //the API key, if it exists, else false
	$rack_serversize = false; //the index key of the server size value, else false
	
	//default values
	$rack_apikey_default = 'paste your RackSpace API Key here'; //the default message they see if they don't have an API key. Also used by the JS at the bottom of the page
	$rack_serversize_select = array('Baby Server - 256mb - $10/mo','Medium Server - 512mb - $20/mo','Huge Server - 1GB - $40/mo','Epic Server - 2GB - $80/mo'); //all avaliable options
?>

<div class="pagecontent rack-wrapper">
	<div class="rack-box">
		<div class="rack-boxtop"></div>
		<div class="rack-content">
		
			<p>Bevo Selfhosted requires a stable and reliable server environment. Because of the rigorous server settings required to run the application, we've been looking around for the best solution - we want our users to be able to experience the full feature set of Bevo Selfhosted, without any drawbacks.</p>
			
			<div class="soapyell">
				<div class="soapyelltop"></div>
				<div class="soapyellcontent">
					<p class="soapyell-exmark-large soapyell-centermark">
						We found the best solution for you: Bevo Media has teamed up with <strong>the world's leader in Hosting &amp; Cloud Computing</strong> to be able to offer you the best hosting environment for <em>Bevo Selfhosted</em>.
					</p>
				</div>
				<div class="soapyellbutt"></div>
			</div>
			
			<ul class="soapchecklist soapliborderbutt rack-bannertop">
				<li>We have worked with RackSpace to ensure that their servers are preconfigured to be compatible for <em>Bevo Selfhosted</em>.
				<div class="soapliborder"></div></li>
				
				<li class="rack-bannerli">Better yet, because of the cloud environment RackSpace provides, you are able to easily scale your traffic needs.
				<div class="soapliborder"></div></li>
			</ul>
			
			<a class="button rackspacebanner-rack" href="http://www.rackspacecloud.com/947.html" title="Click here to visit RackSpace" target="_blank">Rackspace Hosting starts as low as $10 per month! Click here to visit Rackspace.</a>
			
			<p>We offer two easy ways to install Bevo Selfhosted on Rackspace:</p>
			
			<div class="rack-inbox"><!-- option A -->
				
				<h3 class="rack-inboxtitle rack-inboxtitle-a">Super Easy <img src="/Themes/BevoMedia/img/rack_rackspacelogo_h3.png" alt="RackSpace" /> Installation</h3>
				
				<form method="post" onsubmit="serverSetup(); return false;">
				
					<ul class="soapnumlist soaplibordertop">
						<li class="soapnumlist-num1">
							Create a RackSpace account:
							<a class="button createrackspaceacc-rack" href="http://www.rackspacecloud.com/947.html" target="_blank">Create A Rackspace Account Now</a>
						</li>
						<li class="soapnumlist-num2">
							<div class="soapliborder"></div>
							<a href="https://manage.rackspacecloud.com/" target="_blank">Log in to your RackSpace account</a>, select <em>My Account</em> (API access is in the sidebar navigation)
							<a class="rack-shadowbox" href="/Themes/BevoMedia/img/rack_api.jpg" rel="shadowbox"><img src="/Themes/BevoMedia/img/rack_api_thumb.jpg" alt="RackSpace API Access Screenshot" /></a>
						</li>
						<li class="soapnumlist-num3">
							<div class="soapliborder"></div>
							Click on <em>Show API Key</em>, then copy &amp; paste here:
							
							<div class="formtxtwrap">
								<input type="text" class="formtxt rack-listen" id="rack_apikey" name="rack_apikey" value="<?php echo $rack_apikey_default; ?>" />
							</div>
							<br />
							Enter your Rackspace Username:
							
							<div class="formtxtwrap">
								<input type="text" class="formtxt rack-listen" id="rack_username" name="rack_username" value="" />
							</div>
						</li>
						<li class="soapnumlist-num4">
							<div class="soapliborder"></div>
							Select your server size:
							
							
							<select class="formselect rack-listen<?php if($rack_serversize) echo ' formfield-disabled'; ?>" name="rack_serversize" id="rack_serversize"<?php if($rack_serversize) echo ' disabled="disabled"'; ?>>
							<?php	foreach($rack_serversize_select as $k => $v) {
									$rackO .= '<option value="'.$k.'"';
									$rackO .= $rack_serversize === $k ? ' selected="selected"' : ''; //if this is the one they have, select it
									$rackO .= '>'.$v.'</option>'; 
								} 
								
								//prepend empty value and select it if user hasnt saved any value yet
								$rackO = $rack_serversize 	? '<option value="">-- Select your server size --</option>'.$rackO 
												: '<option value="" selected="selected">-- Select your server size --</option>'.$rackO;
								
								echo $rackO; ?>								
							</select>
							
							<?php if($rack_serversize) echo '<a class="button edittheabove-rack" href="#rack_serversize">Edit your Server Size</a>
									<a class="rack-editcancel rack-hide" href="#rack_serversize">Cancel</a>'; ?>
						</li>
						<li class="soapnumlist-num5">
							<div class="soapliborder"></div>
							<div class="rack-formaction rack-formaction-launch<?php if($rack_apikey && $rack_serversize) echo ' rack-hide'; ?>">
								
								<?php 	//if they've entered only 1 of the 2 fields above, throw a notice
									if(($rack_apikey && !$rack_serversize) || (!$rack_apikey && $rack_serversize))
										echo '<strong>Important:</strong> Your RackSpace server isn\'t running yet! Please make sure that you have entered your API key and selected your server size above, then click here:';
									
									//if they havent entered anything yet
									else echo 'Save these settings:'; ?>
									
								<input type="submit" class="formsubmit launchnewserver-rack" value="Launch New Server" />
								<small>Launch a new server with these settings. Old servers will remain running. You'll be emailed the details of your new RackSpace server once it's completely started.</small>
							</div>
							
						</li>
						<li class="soapnumlist-num6">
							<div class="soapliborder"></div>
							<img src="/Themes/BevoMedia/img/txticon_optional.gif" alt="Optional: " /> <b>Add a domain to your server.</b> Go to your domain's registrar and point the nameservers to <code>dns1.stabletransit.com</code> and <code>dns2.stabletransit.com</code>. Log into your RackSpace account, click "Hosting", then "Cloud Servers", select your newly created server, and click the "DNS" tab at the top. Add your domain by clicking the "Add" button, then click the domain name to edit it. On this page, click "Add" and create a DNS record, type <b>A</b>. The <b>Name</b> should be your domain name (no www.), and the <b>Content</b> is the IP of your new server. <b>TTL</b> should be set to 300.
							
							<a href="/Themes/BevoMedia/img/rack_dns.jpg" rel="shadowbox"><img src="/Themes/BevoMedia/img/rack_dns_thumb.jpg" alt="RackSpace DNS Settings Screenshot" /></a>
						</li>
					</ul>					
				</form>
				
				<?php 	//only show this if they dont already have a running server
					if(!$rack_apikey || !$rack_serversize) echo '<p>That\'s it! Bevo Media should now be successfully installed on your server.</p>'; ?>
			
			</div><!--close option A-->			
			<div class="rack-inbox"><!-- option B -->
			
				<h3 class="rack-inboxtitle rack-inboxtitle-b">Manual <img src="/Themes/BevoMedia/img/rack_rackspacelogo_h3.png" alt="RackSpace" /> Installation</h3>
			
				<p>Use the following directions to manually install <em>Bevo Selfhosted</em> step by step via RackSpace, or any other Ubuntu server.</p>
				
				<a class="button stepbystepdirections-rack" href="ServerScript.html">Click Here for Step-by-Step Directions</a>
				<?php if($this->User->membershipType == 'basic') { ?><a class="button needhelpgetpremium-rack" href="Premium.html">Need Help with this? Get Premium</a><?php } ?>
				<div class="clear"></div>
			
			</div><!--close option B-->			
		
		</div>
	</div><!--close rack-box-->
	
	<div class="rack-boxbutt">
		<p><strong>No thanks, I'd rather try to configure <em>Bevo Selfhosted</em> myself, on my current server.</strong>
			<?php if($this->User->membershipType == 'basic') { ?><small>Warning: We cannot offer installation help or technical support for non-premium users! <a href="Premium.html">Get Premium</a></small><?php } ?>			
		</p>
			
		<a class="button doitmyself-onred-rack" href="SelfHostedLoginDownload.html">Do It Myself</a>
		<div class="clear"></div>
	</div>
	
	<p><small>Because of the wide variety of servers available in the market today, Bevo Media cannot offer installation help or technical support for non-premium members who aren't using RackSpace. However, feel free to <a href="/BevoMedia/User/SelfHostedLoginDownload.html">download our Auto-Installer</a> and attempt to install Bevo Selfhosted yourself.</small></p>
	
</div><!-- rackspace end -->

<script type="text/javascript"><!--
$(document).ready(function() {
	/*rack*/
	var 	rack_edit_isactive = 0, //the number of activated field edits
		rack_fieldval_orig = new Array(); //the original field value, before editing
	
	//edit
	$('.rack-wrapper a.edittheabove-rack').click(function() {
		target = $(this).attr('href');
		rack_fieldval_orig[target] = $(target).val();
		
		$(target).removeAttr('disabled').removeClass('formfield-disabled');
		$(this).fadeOut(200);
		$(this).next().delay(200).fadeIn(200);
				
		return false;
	});
	
	//cancel edit
	$('.rack-wrapper a.rack-editcancel').live('click', function() {
		target = $(this).attr('href');
		
		$(target).val(rack_fieldval_orig[target]);
		$(target).attr({'disabled':'disabled'});
		$(target).removeClass('rack-edited').addClass('formfield-disabled');
		
		$(this).fadeOut(200);
		$(this).prev().delay(200).fadeIn(200);
		
		rack_edit_isactive--;
		
		if(rack_edit_isactive == 0) { //if we dont have another edit active, restore the original step 5
			$('.rack-formaction-saveedits').delay(600).slideUp(200);
			$('.rack-formaction-original').delay(800).fadeIn(200).removeClass('rack-formaction-original');
		}
		
		return false;
	});
	
	//default textfield value
	$('.rack-wrapper input#rack_apikey').focus(function() {
		if($(this).val() == "<?php echo $rack_apikey_default; ?>")
			$(this).val('');
	});
	$('.rack-wrapper input#rack_apikey').blur(function() {
		if($(this).val() == '')
			$(this).val("<?php echo $rack_apikey_default; ?>");
	});
});

function soap_rackHandleSaveEdits(e, rack_edit_isactive) {
	if(rack_edit_isactive == 0) { //if saveedits isnt showing already, show it
		li = $('.soapnumlist-num5');
		lih = $(li).outerHeight(false);
		$(li).css({'min-height':lih+'px'});
		
		$('.rack-formaction').each(function() {
			if(!$(this).hasClass('rack-hide') && !$(this).hasClass('rack-formaction-saveedits'))
				$(this).fadeOut(200).addClass('rack-formaction-original');
		});
		$('.rack-formaction-saveedits').delay(200).slideDown(400);
	}
	
	if(!$(e).hasClass('rack-edited')) {
		$(e).addClass('rack-edited');
		rack_edit_isactive++;
	}
	
	return rack_edit_isactive;
}
function serverSetup()
{
	key = $('#rack_apikey').val();
	user = $('#rack_username').val();
	size = $('#rack_serversize').val();
	if(!key || key == "" || key == "<?= $rack_apikey_default ?>")
	{
		alert("Please enter an API key");
		return;
	}
	if(!user || user == "")
	{
		alert("Please enter your Rackspace username");
		return;
	}
	if(!size)
	{
		alert("Please pick a server size");
		return;
	}
	Shadowbox.open({
		content:    'RackspaceLaunch.html?user='+user+'&size='+size+'&key='+key,
		player:     "iframe",
		height:     640,
		width:      640,
		modal: false
	});
}
--></script>