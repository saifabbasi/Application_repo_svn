<?php
require_once(PATH . "Legacy.Abstraction.class.php");

global $userId;
$userId = $this->User->id;
if($this->{'Application/Mode'} == 'SelfHosted')
{
	$webHost = 'http://' . $_SERVER['HTTP_HOST'] . '/';
	$trackHost = 'http://' . $_SERVER['HTTP_HOST'] . '/track/';
	$trackHostSecure = 'https://' . $_SERVER['HTTP_HOST'] . '/track/';
	$trackHostBase = $_SERVER['HTTP_HOST'] . '/track/';
}else{
	$webHost = 'http://beta.bevomedia.com/';
	$trackHost = 'http://track.bevomedia.com/';
	$trackHostSecure = "https://track.bevomedia.com/";
	$trackHostBase = 'track.bevomedia.com/';
}

$isTrackerPage    = true;
//*************************************************************************************************

        $models			= array('CPA', 'CPM', 'CPC');
        $arrNetworks	= array();
        $arrNewNetworks	= array();
        $codeNet = array("Azoogle"=>"sub","Copeac"=>"subid1","CPAStorm"=>"subid1","Maxbounty"=>"s1","Neverblueads"=>"subid");
        
        foreach ( $models as $model )
        {
            $arrNetworks[$model]    = array();
            $arrNewNetworks[$model]    = array();
        }
        $res = LegacyAbstraction::executeQuery("SELECT N.ID, N.TITLE, N.signupUrl, N.w9Required, N.paymentInfo, N.paymentOptions, N.model, N.userIdLabel, N.msgB4Apply, N.otherIdLabel, N.networkRequirements, UN.status, UN.loginId, UN.otherId FROM bevomedia_aff_network N LEFT OUTER JOIN bevomedia_user_aff_network UN ON UN.user__id = '".$userId."' AND UN.network__id = N.id WHERE N.isValid = 'Y' ORDER BY N.model, N.title");
        while ( $row = LegacyAbstraction::getRow($res) )
        {
            $arrNetworks[$row['model']][]    = $row;
        }
        LegacyAbstraction::free($res);
        
        
        $fdb_landingpage = $fdb_offerurl = "";
        if(isset($_GET['Load']))
        {
        	$Sql = "SELECT * FROM bevomedia_tracker_getcodes WHERE id = {$_GET['Load']}";
        	$Row = mysql_fetch_assoc(mysql_query($Sql));
        	$fdb_landingpage = $Row['landingPage'];
        	$fdb_offerurl = $Row['offerUrl'];
        }
?>

<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','codes','new'); 
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
		
<div class="pagecontent">
	<form method="post" id="fTs" class="appform">
		<input type="hidden" name="init" value="1" />
		<table class="dataentry">
			<tr><td colspan=2><h3>Traffic Source</h3></td></tr>
			<tr>
				<th><label for="se"></label></th>
				<td>
					<select id="se" name="se" class="required formselect">
						<option value="google">Google Adwords</option>
						<option value="yahoo">Yahoo Search Marketing</option>
						<option value="msn">Microsoft adCenter</option>
						<option value="trafficvance">TrafficVance</option>
						<option value="adon">AdOn Network</option>
						<option value="mediatraffic">Media Traffic</option>
						<option value="dircpv">DirectCPV</option>
						<option value="leadimpact">LeadImpact</option>
						<option selected value="other">Other/Media buy</option>
					</select>
				</td>
			</tr>
			
			
			<!-- BEGIN Additional PPV Fields -->
			<tr id='ppv_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='ppv_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="ppv_campaign_input_id" name='ppv_campaign'>
					<input type='hidden' id='ppv_campaign_input_id-id' name='ppv_campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='ppv_cpm_id' style='display:none;'>
				<th>
					<label>Average CPC:</label>
				</th>
				<td>
					<input class="formtxt wide_number" autocomplete='off' type='text' size='5' id="ppv_cpm_input_id" name='ppv_cpm'>
					<input type='hidden' id='ppv_cpm_input_id-id' name='ppv_cpm_input_id-id' value=''/>
				</td>
			</tr>
			<!-- ENDOF Additional PPV Fields -->
			
			
			<!-- BEGIN Additional AdOn Fields -->
			<tr id='adon_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='adon_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="adon_campaign_input_id" name='adon_campaign'>
					<input type='hidden' id='adon_campaign_input_id-id' name='adon_campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='adon_cpm_id' style='display:none;'>
				<th>
					<label>Average CPC:</label>
				</th>
				<td>
					<input class="formtxt wide_number" autocomplete='off' type='text' size='5' id="adon_cpm_input_id" name='adon_cpm'>
					<input type='hidden' id='adon_cpm_input_id-id' name='adon_cpm_input_id-id' value=''/>
				</td>
			</tr>
			<!-- ENDOF Additional AdOn Fields -->
			
			
			
			
			
			<!-- BEGIN Additional MediaTraffic Fields -->
			<tr id='medtraf_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='medtraf_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="medtraf_campaign_input_id" name='medtraf_campaign'>
					<input type='hidden' id='medtraf_campaign_input_id-id' name='medtraf_campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='medtraf_cpm_id' style='display:none;'>
				<th>
					<label>Average CPC:</label>
				</th>
				<td>
					<input class="formtxt wide_number" autocomplete='off' type='text' size='5' id="medtraf_cpm_input_id" name='medtraf_cpm'>
					<input type='hidden' id='medtraf_cpm_input_id-id' name='medtraf_cpm_input_id-id' value=''/>
				</td>
			</tr>
			<!-- ENDOF Additional MediaTraffic Fields -->
			
			
			
			<!-- BEGIN Additional LeadImpact Fields -->
			<tr id='leadimpact_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='leadimpact_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="leadimpact_campaign_input_id" name='leadimpact_campaign'>
					<input type='hidden' id='leadimpact_campaign_input_id-id' name='leadimpact_campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='leadimpact_cpm_id' style='display:none;'>
				<th>
					<label>Average CPC:</label>
				</th>
				<td>
					<input class="formtxt wide_number" autocomplete='off' type='text' size='5' id="leadimpact_cpm_input_id" name='leadimpact_cpm'>
					<input type='hidden' id='leadimpact_cpm_input_id-id' name='leadimpact_cpm_input_id-id' value=''/>
				</td>
			</tr>
			<!-- ENDOF Additional LeadImpact Fields -->
			
			
			
			
			
			<!-- BEGIN Additional DirectCPV Fields -->
			<tr id='dircpv_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='dircpv_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="dircpv_campaign_input_id" name='dircpv_campaign'>
					<input type='hidden' id='dircpv_campaign_input_id-id' name='dircpv_campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='dircpv_cpm_id' style='display:none;'>
				<th>
					<label>Average CPC:</label>
				</th>
				<td>
					<input class="formtxt wide_number" autocomplete='off' type='text' size='5' id="dircpv_cpm_input_id" name='dircpv_cpm'>
					<input type='hidden' id='dircpv_cpm_input_id-id' name='dircpv_cpm_input_id-id' value=''/>
				</td>
			</tr>
			<!-- ENDOF Additional MediaTraffic Fields -->
			
			
			
			
			
			<!-- BEGIN Additional Media Buy Fields -->
			<tr id='mediabuy_campaign-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Enter a name for this campaign or select one from the list of existing campaigns.</i>
				</td>
			</tr>
			
			<tr id='mediabuy_campaign_id' style='display:none;'>
				<th>
					<label for="campaign_input_id">Campaign Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="campaign_input_id" name='mediabuy_campaign'>
					<input type='hidden' id='campaign_input_id-id' name='campaign_input_id-id' value=''/>
				</td>
			</tr>
			
			
			<tr id='mediabuy_adgroup-desc_id' style='display:none;'>
				<td colspan='2'>
					<i>Optionally, specify a group and variation name that this ad or traffic source belongs to.</i>
					<br/>
				</td>
			</tr>
			
			<tr id='mediabuy_adgroup_id' style='display:none;'>
				<th>
					<label for="adgroup_input_id">Ad Group Name:</label>
				</th>
				<td>
					<input class="formtxt" autocomplete='off' type='text' size='50' id="adgroup_input_id" name='mediabuy_adgroup'>
					<input type='hidden' id='adgroup_input_id-id' name='adgroup_input_id-id' value=''/>
				</td>
			</tr>
			
			<tr id='mediabuy_advar_id' style='display:none;'>
				<th>
					<label for="advar_input_id">Ad Variation:</label>
				</th>
				<td>
					<input class="formtxt" type='text' size='50' id="advar_input_id" name='mediabuy_advar'>
				</td>
			</tr>
			
			<tr id='mediabuy_cost_id' style='display:none;'>
				<th>
					<label for="mediabuy_cost_id">Cost:</label>
				</th>
				<td>
					<input class="formtxt wide_number" type='text' size='5' id="mediabuy_cost_id" name='mediabuy_cost'>
					&nbsp;&nbsp;
					<input style="display:none;" type='radio' name='mediabuy_cost_type' value='CPM'>
					<input style="display:none;" type='radio' name='mediabuy_cost_type' value='CPC' checked="checked">
					<br/><br/>
				</td>
			</tr>
			<!-- ENDOF Additional Media Buy Fields -->
			<tr><td colspan=2><h3>Landing Page</h3></td></tr>
		
<script type="text/javascript">
	function DirectLinkClick(CheckBox)
	{
//		$(".hideOnDirectLink").toggle(); 

setVisible($(".landingPageItems"), !CheckBox.checked);
//			setVisible($("#landingPageUrlRow"), !CheckBox.checked);

//		if (CheckBox.checked)
//		{
//			setVisible($("#landingPageRotatorRow"), !CheckBox.checked);
//			setVisible($("#landingPageUrlRow"), !CheckBox.checked);
//		} else
//		{
//			setVisible($("#landingPageRotatorRow"), !$("#enableLandingPageRotatorCheckbox").checked);
//			setVisible($("#landingPageUrlRow"), $("#enableLandingPageRotatorCheckbox").checked);
//		}



		if(CheckBox.checked) 
		{
//			$(".hideOnDirectLink").hide();
			setDisabled($("#landing"), true); 
			setDisabled($("#landingPageRotatorSelectionBox"), true); 
		} else 
		{ 
//			$(".hideOnDirectLink").show();
			setDisabled($("#landing"), document.getElementById('enableLandingPageRotatorCheckbox').checked); 
			setDisabled($("#landingPageRotatorSelectionBox"), document.getElementById('enableLandingPageRotatorCheckbox').checked==false);
			

//				
//			if ($("#enableLandingPageRotatorCheckbox").checked)
//			{
//				
//			}
//			$("#enableLandingPageRotatorCheckbox") = !$("#enableLandingPageRotatorCheckbox").checked; 
//			$("#enableLandingPageRotatorCheckbox").click();  
//			setDisabled($("#landing"), $("#enableLandingPageRotatorCheckbox").checked); 
//			setDisabled($("#landingPageRotatorSelectionBox"), !$("#enableLandingPageRotatorCheckbox").checked); 
		}
	}
</script>
		
			<tr>
				<th><label for="directlink">Don't use landing page:</label></th>
				<td>
					<p>
						<input type='checkbox' id='directlink' name='directlink' value='on' onclick='DirectLinkClick(this);'>
						<span class='hideOnDirectLink'>Enable this to generate a direct link to the offer page.</span>
						<span class='hideOnDirectLink' style='display: none'>Disable this to configure a landing page (or "pre-sell page").</span>
					</p>
				</td>
			</tr>
			
	<?php if($this->User->vaultID == 0):?>
	<script language="javascript">
	    $(document).ready(function() {
	        $('#geotargetcheckbox').click(function(){
		        var a = document.createElement('a');
		        a.href = '/BevoMedia/Publisher/VerifySelfHosted.html?ajax=true';
		        a.rel = 'shadowbox;width=640;height=480;player=iframe';
		        Shadowbox.open(a);

	            return false;
    	    });
        });
	</script>
	<?php else:?>
	<script language="javascript">
		$(document).ready(function() {
			$('#geotargetcheckbox').click(function(){
				document.location.href = "/BevoMedia/Geotargeting/Index.html";
			});
		});
	</script>
	<?php endif;?>			
			
			<tr>
				<th><label for="geotargeting">Geotargetting <font color="red">(Verified Only):</font></label></th>
				<td>
					<p id='geotargetcheckbox'>
						<input type='checkbox' value='on'>
						<span>Display different landing pages based on location.</span>
					</p>
				</td>
			</tr>
			

			<tr class="landingPageItems">
				<th><label for="landing">Landing Page Rotator:</label></th>
				<td>
				<input type='checkbox' id='enableLandingPageRotatorCheckbox' onClick='$(".landingPageRotatorStuff").toggle(); setDisabled($("#landing"), this.checked); setDisabled($("#landingPageRotatorSelectionBox"), !this.checked); if (!this.checked) { $("#landingPageRotatorSelectionBox").val(""); if ($("#landing").val().indexOf("ROTATE")==0) $("#landing").val("");  } '>
				<span class="landingPageRotatorStuff">Enable this to rotate multiple landing pages</span>
				<span class="landingPageRotatorStuff" style="display: none">
					Disable this to use a single landing page destination
					
    				<select class="formselect" id='landingPageRotatorSelectionBox' style='width:90%;' onChange="if (this.value!='') { document.getElementById('landing').disabled='disabled'; document.getElementById('landing').value='ROTATE.'+this.value; } else { document.getElementById('landing').disabled=''; } ">
    				<option value='' onClick='' style='font-weight:bold;'>Select a Landing Page Rotation Group below...</option>
    				<?php foreach($this->LandingPageGroups as $LandingPageGroup):?>
    					<option value='<?=$LandingPageGroup->id?>'><?php print $LandingPageGroup->label; ?>
    				<?php endforeach?>
    			
    				</select>
    				<br/>
    					<a rel='shadowbox;width=640;height=480;' href='LandingRotationNewAjax.html'>Click here to add a landing page rotation.</a>
				</span>
				</td>
			</tr>
			
			<tr class='hideOnDirectLink landingPageRotatorStuff'>
				<th class="landingPageItems"><label for="landing">Landing Page URL:</label></th>
				<td class="landingPageItems">
					<input type="text" value="<?php echo $fdb_landingpage?>" name="lp" id="landing" size="50" class="required url formtxt" />
					<div id="errorDesc_landing" style='color:#ff0000; font-size: 10px; font-weight: bold; display:none;'>Please enter a valid URL in the format 'http://myurl.com'.</div>
				</td>
			</tr>

						
			<tr><td colspan=2><h3>Offer</h3></td></tr>
			<tr>
			<tr>
				<th><label for="unique">Unique visitors:</label></th>
				<td>
					<p>
						<input class="formcheck" type='checkbox' id='unique' name='unique' value='on'>
						Each visitor's click will only be counted once.
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="cloak">Cloak Referrers:</label></th>
				<td>
					<p>
						<input class="formcheck" type='checkbox' id='cloak' name='cloak' value='on'>
						Referrers will not pass through to the offer page.
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="offer">Offer Rotator:</label></th>
				<td>
				<input class="formcheck" type='checkbox' id='enableOfferRotatorCheckbox' onClick='$(".offerRotatorStuff").toggle(); setDisabled($("#offer"), this.checked);'>
				<span class="offerRotatorStuff">Enable this to rotate multiple offers</span>
				<span class="offerRotatorStuff" style="display: none">
					Disable this to use a single offer destination
    				<select class="formselect" id='offerRotatorSelectionBox' style='width:90%;' onchange="">
    				<option value='' style='font-weight:bold;'>Select an Offer Rotation Group below...</option>
    			
    				<?php foreach($this->OfferGroups as $OfferGroup):?>
    					<option value="<?php print $OfferGroup->id; ?>" ><?php print $OfferGroup->label; ?>
    				<?php endforeach?>
    			
    				</select>
    				<br/>
    					<a rel='shadowbox;width=640;height=480;' href='OfferRotationNewAjax.html'>Click here to add an offer rotation.</a>
    					<p>Your offer URL will be automatically modified to be the one used from the Bevomedia offer rotator.</p>
				</span>
				</td>
			</tr>
			
			<tr class="offerRotatorStuff">
				<th><label for="offer">Offer URL:</label></th>
				<td>
					<input type="text" name="offurl" id="offer" value="<?php echo $fdb_offerurl?>" size="45" class="required formtxt" /> <span class="suffix">[subid]</span>
					<div id="errorDesc_offer" style='color:#ff0000; font-size: 10px; font-weight: bold; display:none;'>Please enter a valid URL in the format 'http://myurl.com'.</div>
					<div class="example">Example: http://network.com/?&amp;subid=</div>
					<br />
					<div>The &quot;subid&quot; for each visitor will be added to the end of your offer's URL.</div>
					<div>or</div>
					<div>
						Use {bevosubid} to have the subid anywhere in the offer's url.<br />
						Example: http://network.com/offer/{bevosubid}/affid	
					</div>
					
				</td>
			</tr>
			
			
			<tr><td colspan=2><h3>Sales Tracking</h3></td></tr>
			<tr>
				<th><label for="cloak">Automatic Conversion Tracking:</label></th>
				<td>
					<p>
						<input class="formcheck" type='checkbox' id='autotrack' name='autotrack' value='on' checked=checked onclick=' $("#saleAmtSpan").toggle(); $("#autoSpan").toggle();'>
						<span id="autoSpan">Your conversion stats will be automatically retrieved from the network.<br />(Most accurate, you must first setup your affiliate networks on the My Networks page, uncheck for Pixel/Postback)</span>
						<span id="saleAmtSpan" style="display: none">Conversions are worth: $<input type="text" name="saleAmt" size=4 value="3.00" /></span>
					</p>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><input class="formsubmit track_getcodes" type="submit" value="Get codes" /></td>
			</tr>
		</table>
	</form>

	<div id="results" style="display: none;"></div>

</div>
	
	
<script type="text/javascript">
$('#offerRotatorSelectionBox').change(function() {
	if ($(this).val()=='')
	{
		$('#offer').attr('disabled', false);
		return;
	}

	$('#offer').attr('disabled', true);
	$('#offer').val('ROTATE.'+$(this).val());
});
</script>
	
<script language="javascript">
//<![CDATA[
	function setDisabled(el, val) {
		if(val)
			el.attr('disabled', true);
		else
			el.removeAttr('disabled');
	}
	function setVisible(el, val) {
		if(val)
			el.show();
		else
			el.hide();
	}
	this.tooltip = function(){
	yOffset = 20;
	xOffset = 10;
	$("a.tooltip").hover(function(e){
		this.t = this.title;
		this.title = "";
		$("body").append("<p id='tooltip'>"+ this.t +"</p>");
		$("#tooltip")
			.css("top",(e.pageY - xOffset) + "px")
			.css("left",(e.pageX + yOffset) + "px")
			.fadeIn("fast");
	 		},function(){
				this.title = this.t;
				$("#tooltip").remove();
			}
		);
	$("a.tooltip").mousemove(
			function(e){
				$("#tooltip")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
			}
		);
	};
	
	$(document).ready( function() {

		$('textarea.code').live('click', function() {
			$(this).select();
		})
		
		$('#fTs').validate({
			errorPlacement: function(error, element) {
				
			},
			highlight: function(element, errorClass) {
				$('label[for="' + element.id + '"]').addClass('validation-error');
				$('#errorDesc_'+element.id).show();
			},
			unhighlight: function(element, errorClass) {
				$('label[for="' + element.id + '"]').removeClass('validation-error');
				$('#errorDesc_'+element.id).hide();
			},
			submitHandler: function(form) {
				objDivMsg = document.getElementById('results');
				objDivMsg.style.display = 'block';

				form =  document.getElementById('fTs');
				postData = buildPOST(form);

				getContentFromUrl('AjaxTracker.html', objDivMsg , 'POST', postData);

				$('textarea.code').unbind('focus');
				$('textarea.code').bind('focus', function() {
					$(this).select();
				});
			}
		});

	});
	var xmlHttp;
	var oTargetDiv	= '';
	var object		= '';
	var funcName	= '';

	function getContentFromUrl(url, targetDiv, method, postData, fName)
	{
		if ( url == '' )
		{
			alert ("URL for HTTP Request is empty");
			return;
		}
		if ( targetDiv == '' )
		{
			alert ("Target DIV not specified for HTTP Request");
			return;
		}
		if ( method == '' || method == undefined )
			method = 'GET';

		// because variables in js are global, once set they are not reset and call the same function again and again
		funcName = fName;

		object = GetXmlHttpObject();
		if ( object == null )
		{
			alert ("Browser does not support HTTP Request");
			return;
		}

		oTargetDiv					= targetDiv;
		object.onreadystatechange	= stateChanged4Content;

		try
		{
			object.open(method, url, true);
			object.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			object.send(postData==''?null:postData);
		}
		catch (e)
		{
			alert(e.message);
		}
	}

	function stateChanged4Content()
	{
		if ( object.readyState == 4 || object.readyState == "complete" )
		{
			// If Target DIV is null then just use response to eval
			if ( oTargetDiv == null )
			{
				try
				{
					eval(object.responseText);
				}
				catch (e)
				{
					alert(e.message+"\n"+object.responseText);
				}
			}
			// Else Target DIV is assigned response
			else
			{
				oTargetDiv.innerHTML = object.responseText;
				tooltip();
			}

			// Function Name is not empty then that function name is also called
			if ( funcName != '' )
			{
				eval(funcName);
			}
		}
		//	State	Description
		//	0		The request is not initialized
		//	1		The request has been set up
		//	2		The request has been sent
		//	3		The request is in process
		//	4		The request is complete

	}

	function GetXmlHttpObject()
	{
		var xmlHttp = null;
		try
		{
			// Firefox, Opera 8.0+, Safari
			xmlHttp = new XMLHttpRequest();
		}
		catch (e)
		{
			//Internet Explorer
			try
			{
				xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}

		return xmlHttp;
	}
	function validateForm(f)
	{
		for (var i=0; i<f.elements.length; i++)
		{
			if (f.elements[i].alt=="blank" && f.elements[i].value=="")
			{
				alert("Please provide "+f.elements[i].name);
				f.elements[i].focus();
				return false;
			}
			if (f.elements[i].alt=="numeric" && (f.elements[i].value=="" || isNaN(f.elements[i].value)))
			{
				alert("Please provide "+f.elements[i].name);
				f.elements[i].focus();
				return false;
			}
			if (f.elements[i].alt!="" && f.elements[i].alt!=null)
			{
				if (f.elements[i].alt.substr(0, 7)=="equalto")
				{
					if ((f.elements[i].value!=f[f.elements[i].alt.substr(8)].value))
					{
						alert(f.elements[i].name+" should be same as "+f[f.elements[i].alt.substr(8)].name);
						f.elements[i].focus();
						return false;
					}
				}
			}
		}

		return true;
	}

	function buildPOST(f)
	{
		var qs = '';
		for (var i=0; i<f.elements.length; i++)
		{
	        if ( f.elements[i].name != '' )
			{
	            var name = f.elements[i].name;
				
				if ( ( f.elements[i].type == 'checkbox' || f.elements[i].type == 'radio' ) && !f.elements[i].checked )
					continue;

				if ( f.elements[i].type == 'select-multiple' )
				{
					for (var j=0; j<f.elements[i].options.length; j++)
					{
						if ( f.elements[i].options[j].selected )
						{
				            qs += (qs == '') ? '' : '&';
				            qs += name + '=' + escape2(f.elements[i].options[j].value);
						}
					}
		
					continue;
				}

	            qs += (qs == '') ? '' : '&';
	            qs += name + '=' + escape2(f.elements[i].value);
	        }
	    }
	    qs += "\n";

	    return qs;
	}

	function escape2(val)
	{
		val = escape(val);
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		val = val.replace('+', '%2B');
		return val;
	}

	function checkEmail(email, outputIn)
	{
		obj = document.getElementById(outputIn);
		obj.innerHTML = 'Checking availability...';
		getContentFromUrl('ajax_check_email.php?email=' + escape(email), obj, 'GET', '', '');
	}

	function checkPwd(pwd, outputIn)
	{
		obj = document.getElementById(outputIn);
		obj.innerHTML = 'Checking strength...';
		getContentFromUrl('ajax_check_pwd.php?pwd=' + escape(pwd), obj, 'GET', '', '');
	}
//]]>
</script>


<style type='text/css'>
.mediabuy_getcode-choice_table {
	background-color: #ffffff;
	border: solid 1px #000000;
	position: absolute;
	display: block;
	height: 200px;
	width: 400px;
	overflow: auto;
	}
.mediabuy_getcode-choice_table td a:hover {
	background-color:#d0e0f0;
	}
.mediabuy_getcode-choice_table td {
	width: 400px;
	margin: 0;
	padding: 0;
	}
.mediabuy_getcode-choice_table td a {
	display: block;
	width: 100%;
	}
.mediabuy_getcode-choice_table td i {
	display: block;
	width: 100%;
	color: #555555;
	text-align: center;
	}

</style>

<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>/Themes/BevoMedia/mediabuy_getcode.js"></script>
<script language="javascript">
mediabuy_getcode.init('<?php echo isset($_GET['Select'])?$_GET['Select']:''?>');
<?php foreach($this->MediaBuyCampaigns as $Campaign):?>
mediabuy_getcode.addCampaign('<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>', <?php print $Campaign->id; ?>, 'mediabuy');
<?php endforeach?>
<?php foreach($this->MediaBuyAdGroups as $AdGroup):?>
mediabuy_getcode.addAdGroup('<?php print str_replace("'", '', htmlentities($AdGroup->name)); ?>', <?php print $AdGroup->id; ?>, 'mediabuy');
<?php endforeach?>

<?php foreach($this->PPVCampaigns as $Campaign):?>
mediabuy_getcode.addCampaign('<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>', <?php print $Campaign->id; ?>, 'ppv');
<?php endforeach?>

<?php foreach($this->AdOnCampaigns as $Campaign):?>
mediabuy_getcode.addCampaign('<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>', <?php print $Campaign->id; ?>, 'adon');
<?php endforeach?>

<?php foreach($this->MedTrafCampaigns as $Campaign):?>
mediabuy_getcode.addCampaign('<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>', <?php print $Campaign->id; ?>, 'medtraf');
<?php endforeach?>

<?php foreach($this->DirectCPVCampaigns as $Campaign):?>
mediabuy_getcode.addCampaign('<?php print str_replace("'", '', htmlentities($Campaign->name)); ?>', <?php print $Campaign->id; ?>, 'dircpv');
<?php endforeach?>

</script>