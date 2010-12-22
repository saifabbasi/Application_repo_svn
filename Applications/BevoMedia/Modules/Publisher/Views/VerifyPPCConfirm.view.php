<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	</head>
	<body>

<script language="javascript" src="/Themes/BevoMedia/jquery.js"></script>
<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>

<link href="/Themes/BevoMedia/lightbox_style.css" rel="stylesheet" type="text/css" />
<link href="/Themes/BevoMedia/global.css" rel="stylesheet" type="text/css" />
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:285px;
	margin-left: -330px;
	}
.tooltip {
	color: #ffffff;
	text-decoration: none !important;
	font-weight: bold;
	font-size: 12pt;
	}
.tooltip.defaultLink {
	color: maroon;
	font-size: 12px;
	font-style: normal;
	font-weight: normal;
	font-size: 12px;
	}
.successInstall {
	background-color: #008800;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.failInstall {
	background-color: #880000;
	border: solid 2px #ffffff;
	color: #ffffff;
	}
.wrap {
	background-color: #fff;
	height: 100%;
}
</style>


<?php if(isset($this->message)): ?>
<p class="updated"><?php print htmlentities($this->message); ; ?></p>
<script type="text/javascript">
	window.setTimeout('closeThis()', 1500);

	function closeThis()
	{
		parent.Shadowbox.close();
	}
</script>
<?php endif; ?>

<?php 
	if (isset($_GET['Error']))
	{
?>
	<div style="border: 1px #E85163 solid; background-color: #F5AEB6; width: 560px; height: 45px; line-height: 45px; color: #000; text-align: center; margin-left: 40px;">
		<?php echo $_GET['Error'];?>
	</div>
	<br />
<?php 
	}
?>

<div class="lpop lpop_pay">
	<div class="lpop_title">
		<h2>Bevo PPC Campaign Editor</h2>
		<p>The Bevo PPC Campaign Editor is a premium feature that has a nominal one-time licensing fee.</p>
	</div>
	
	<div class="lpop_content">
		<h2>Your Cart</h2>
		
		<div class="lpop_cart">
			<div class="lpop_cartbox">
				<h3 class="floatright">$160.00</h3>
				
				<h3>Bevo PPC Campaign Editor</h3>
				<p>One time license fee</p>
				<div class="clear"></div>				
			</div>
			<div class="lpop_cartbox lpop_securicons">
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PayPPCYearly.html">Pay Now</a>
				<div class="clear"></div>
			</div>
			<div class="lpop_cartbox">
				<ul>
					<li>Your card on file will be billed for the above noted amount. You will receive immediate access to the feature.</li>
					<li>We will only bill your card once for the above mentioned license. Your card on file will be billed. To change this card, <a id="VerifyLink" href="#">click here</a>.</li>
				</ul>
				
				<div class="lpop_tos">
				
					<h2>WEBSITE TERMS OF SERVICE</h2>
					
					<p>1. AGREEMENT</p>
					<p>These Website Terms of Service (the "Agreement") constitute a legally binding agreement by and between BeVo Media (hereinafter, "BeVo Media") and you or your company (in either case, "You" or "Your") concerning Your use of BeVo Media's website located at www.bevomedia.com(the "Website") and the services available through the Website (the "Services").  By using the Website and Services, You represent and warrant that You have read and understood, and agree to be bound by, this Agreement and BeVo Media's Privacy Policy (the "Privacy Policy"), which is incorporated herein by reference and made part of this Agreement.  IF YOU DO NOT UNDERSTAND THIS AGREEMENT, OR DO NOT AGREE TO BE BOUND BY IT OR THE PRIVACY POLICY, YOU MUST IMMEDIATELY LEAVE THE WEBSITE AND CEASE USING THE SERVICES.</p>
					
					<p>2. PRIVACY POLICY</p>
					<p>By using the Website, You consent to the collection and use of certain information about You, as specified in the Privacy Policy. BeVo Media encourages users of the Website to frequently check BeVo Media's Privacy Policy for changes.</p>
					
					<p>3. CHANGES TO AGREEMENT AND PRIVACY POLICY</p>
					<p>BEVO MEDIA RESERVES THE RIGHT TO CHANGE THIS AGREEMENT AND THE PRIVACY POLICY AT ANY TIME UPON NOTICE TO YOU, TO BE GIVEN BY THE POSTING OF A NEW VERSION OR A CHANGE NOTICE ON THE WEBSITE. IT IS YOUR RESPONSIBILITY TO REVIEW THIS AGREEMENT AND THE PRIVACY POLICY PERIODICALLY. IF AT ANY TIME YOU FIND EITHER UNACCEPTABLE, YOU MUST IMMEDIATELY LEAVE THE WEBSITE AND CEASE USING THE SERVICES. Unless BeVo Media obtains Your express consent, any revised Privacy Policy will apply only to information collected by BeVo Media after such time as the revised Privacy Policy takes effect, and not to information collected under any earlier Privacy Policies.  </p>
					
					<p>4. ELIGIBILITY</p>
					<p>BY USING THE WEBSITE OR SERVICES, YOU REPRESENT AND WARRANT THAT YOU ARE AT LEAST 18 YEARS OLD AND ARE OTHERWISE LEGALLY QUALIFIED TO ENTER INTO AND FORM CONTRACTS UNDER APPLICABLE LAW. Any individual using the Website or Services on behalf of a company further represents and warrants that they are authorized to act and enter into contracts on behalf of that company. This Agreement is void where prohibited.  </p>
					
					<p>5. LICENSE</p>
					<p>Subject to Your compliance with the terms and conditions of this Agreement, BeVo Media grants You a non-exclusive, non-sublicensable, revocable, non-transferable license to use the Website and Services. The Website, or any portion of the Website, may not be reproduced, duplicated, copied, modified, sold, resold, distributed, visited, or otherwise exploited for any commercial purpose without the express written consent of BeVo Media. Except as expressly set forth herein, this Agreement grants You no rights in or to the intellectual property of BeVo Media or any other party. The license granted in this section is conditioned on Your compliance with the terms and conditions of this Agreement.  In the event that You breach any provision of this Agreement, Your rights under this section will immediately terminate.</p>
					
					<p>Services provided through the BeVo website are hosted by Amazon.com.  BeVo Media makes no guarantee of server uptime and offers no partial refund for web hosting service disruption. </p> 
					
					
					<p>6. WEBSITE MEMBERSHIP; FEES</p>
					<p>By registering with the Website, You expressly authorize BeVo Media to charge You the monthly API fees accumulated through the use of Your account (the "Membership Fee"). API fees will vary with account usage and will be billed at the rate of $0.35 per 1,000 API calls. You are responsible for all Google API fees associated with the use of Services. In addition to Membership Fee, You must be a verified member of BeVo Media to use all premium sections of the website, including the Services described in this Agreement. Verified Membership requires a monthly fee as described on www.bevomedia.com. API fees are NON-REFUNDABLE. BEVO MEDIA RESERVES THE RIGHT, IN BEVO MEDIA'S SOLE DISCRETION, TO CHANGE THE MEMBERSHIP FEES IN EFFECT, OR TO ADD NEW FEES AND/OR CHARGES, BY POSTING SUCH CHANGES ON THE WEBSITE OR OTHERWISE PROVIDING NOTICE TO YOU AS PROVIDED HEREIN.</p>
					
					<p>7. USER INFORMATION; PASSWORDS</p>
					<p>You represent and warrant that all information You provide in connection with Your use of the Website and Services will be complete and accurate. You are entirely responsible for maintaining the confidentiality of Your password. You may not use the account, user name, or password of any other member at any time. You agree to notify BEVO MEDIA immediately of any unauthorized use of Your account, user name, or password.</p>
					
					<p>8. PROHIBITED USES</p>
					<p>BeVo Media imposes certain restrictions on Your use of the Website and the Services. You represent and warrant that you will not: (a) violate or attempt to violate any security features of the Website or Services; (b) copy or otherwise duplicate, directly or indirectly, any portion of the Website, including without limitation, all designs, information, photographs, images, drawings, videos, music, text, typefaces, graphics, products, code, and other files, and the selection, arrangement and organization thereof (collectively, "BeVo Media Content"); (c) use any software that enables copying or duplication of BeVo Media Content for later off-line viewing; (d) distribute, display, modify, transmit, resell, reuse, or repost BeVo Media Content in any electronic form, including any online service, the Internet or any other telecommunications medium which now exists or shall exist in the future, for any purpose, without the prior written permission of BeVo Media; (e) access content or data not intended for You, or log onto a server or account that You are not authorized to access; (f) attempt to probe, scan, or test the vulnerability of the Services, the Website, or any associated system or network, or breach security or authentication measures without proper authorization; (g) interfere or attempt to interfere with the use of the Website or Services by any other user, host or network, including, without limitation by means of submitting a virus, overloading, "flooding," "spamming," "mail bombing," or "crashing"; (h) use the Website or Services to send unsolicited e-mail, including without limitation promotions or advertisements for products or services; (i) forge any TCP/IP packet header or any part of the header information in any e-mail or in any uploading or posting to, or transmission, display, performance or distribution by means of, the Website or Services; or (j) attempt to modify, reverse-engineer, decompile, disassemble or otherwise reduce or attempt to reduce to a human-perceivable form any of the source code used by BeVo Media in providing the Website or Services. Any violation of this section may subject You to civil and/or criminal liability.</p>
					
					<p>9. INTELLECTUAL PROPERTY</p>
					<p>The Website and all proprietary content and materials located on the Website, including without limitation any logos, designs, text, graphics and other files, and the selection, arrangement and organization thereof, are the intellectual property of BeVo Media. Except as explicitly provided, neither Your use of the Website and Services, nor your entry into this Agreement, grant You any right, title or interest in or to any such content or materials. BEVO MEDIA (the "BeVo Media Marks") are trademarks or registered trademarks of BeVo Media The Website is Copyright &copy; 2010 to the present, BeVo Media, LLC. ALL RIGHTS ARE RESERVED. </p>
					
					<p>10. DISCLAIMERS; LIMITATION OF LIABILITY</p>
					<p>(a) NO WARRANTIES  </p>
					<p>BEVO MEDIA HEREBY DISCLAIMS ALL WARRANTIES. THE WEBSITE AND SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE." TO THE MAXIMUM EXTENT PERMITTED BY LAW, BEVO MEDIA EXPRESSLY DISCLAIMS ANY AND ALL WARRANTIES, EXPRESS OR IMPLIED, REGARDING THE WEBSITE, INCLUDING, BUT NOT LIMITED TO, ANY IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NONINFRINGEMENT.  BEVO MEDIA DOES NOT WARRANT THAT THE WEBSITE OR THE SERVICES WILL MEET YOUR REQUIREMENTS, THAT THE OPERATION OF THE WEBSITE OR THE SERVICES WILL BE UNINTERRUPTED OR ERROR-FREE. </p>
					
					<p>(b) YOUR RESPONSIBILITY FOR LOSS OR DAMAGE</p>
					<p>YOU AGREE THAT YOUR USE OF THE WEBSITE AND SERVICES IS AT YOUR SOLE RISK. YOU WILL NOT HOLD BEVO MEDIA RESPONSIBLE FOR ANY LOSS OR DAMAGE THAT RESULTS FROM YOUR ACCESS TO OR USE OF THE WEBSITE, INCLUDING WITHOUT LIMITATION ANY LOSS OR DAMAGE TO ANY OF YOUR COMPUTERS OR DATA. THE INFORMATION AND SERVICES MAY CONTAIN BUGS, ERRORS, PROBLEMS OR OTHER LIMITATIONS.  </p>
					
					<p>(c) LIMITATION OF LIABILITY</p>
					<p>THE LIABILITY OF BEVO MEDIA IS LIMITED. TO THE MAXIMUM EXTENT PERMITTED BY LAW, IN NO EVENT SHALL BEVO MEDIA BE LIABLE FOR SPECIAL, INCIDENTAL, OR CONSEQUENTIAL DAMAGES, LOST PROFITS, LOST DATA OR CONFIDENTIAL OR OTHER INFORMATION, LOSS OF PRIVACY, COSTS OF PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES, FAILURE TO MEET ANY DUTY INCLUDING WITHOUT LIMITATION OF GOOD FAITH OR OF REASONABLE CARE, NEGLIGENCE, OR OTHERWISE, REGARDLESS OF THE FORESEEABILITY OF THOSE DAMAGES OR OF ANY ADVICE OR NOTICE GIVEN TO BEVO MEDIA ARISING OUT OF OR IN CONNECTION WITH YOUR USE OF THE WEBSITE OR SERVICES. THIS LIMITATION SHALL APPLY REGARDLESS OF WHETHER THE DAMAGES ARISE OUT OF BREACH OF CONTRACT, TORT, OR ANY OTHER LEGAL THEORY OR FORM OF ACTION. ADDITIONALLY, THE MAXIMUM LIABILITY OF BEVO MEDIA TO YOU UNDER ALL CIRCUMSTANCES WILL BE EQUAL TO THE AGGREGATE PRICE YOU PAID TO BEVO MEDIA DURING THE TWELVE MONTHS PRECEDING THE INCIDENT OR INCIDENTS GIVING RISE TO SUCH LIABILITY. YOU AGREE THAT THIS LIMITATION OF LIABILITY REPRESENTS A REASONABLE ALLOCATION OF RISK AND IS A FUNDAMENTAL ELEMENT OF THE BASIS OF THE BARGAIN BETWEEN BEVO MEDIA AND YOU. THE WEBSITE AND SERVICES WOULD NOT BE PROVIDED WITHOUT SUCH LIMITATIONS. </p>
					
					<p>(d) APPLICATION</p>
					<p>THE ABOVE DISCLAIMERS, WAIVERS AND LIMITATIONS DO NOT IN ANY WAY LIMIT ANY OTHER DISCLAIMER OF WARRANTIES OR ANY OTHER LIMITATION OF LIABILITY IN ANY OTHER AGREEMENT BETWEEN YOU AND BEVO MEDIA. SOME JURISDICTIONS MAY NOT ALLOW THE EXCLUSION OF CERTAIN IMPLIED WARRANTIES OR THE LIMITATION OF CERTAIN DAMAGES, SO SOME OF THE ABOVE DISCLAIMERS, WAIVERS AND LIMITATIONS OF LIABILITY MAY NOT APPLY TO YOU. UNLESS LIMITED OR MODIFIED BY APPLICABLE LAW, THE FOREGOING DISCLAIMERS, WAIVERS AND LIMITATIONS SHALL APPLY TO THE MAXIMUM EXTENT PERMITTED, EVEN IF ANY REMEDY FAILS ITS ESSENTIAL PURPOSE. NO ADVICE OR INFORMATION, WHETHER ORAL OR WRITTEN, OBTAINED BY YOU THROUGH THE WEBSITE OR OTHERWISE SHALL ALTER ANY OF THE DISCLAIMERS OR LIMITATIONS STATED IN THIS SECTION.</p>
					
					<p>11. YOUR REPRESENTATIONS AND WARRANTIES</p>
					<p>You represent and warrant that Your use of the Website and Services will be in accordance with this Agreement and any other BeVo Media policies, and with any applicable laws or regulations.</p>
					
					<p>12. INDEMNITY BY YOU</p>
					<p>Without limiting any indemnification provision of this Agreement, You agree to defend, indemnify and hold harmless BeVo Media and its officers, directors, employees, agents, affiliates, representatives, sublicensees, successors, assigns, and Industry Professionals (collectively, the "Indemnified Parties") from and against any and all claims, actions, demands, causes of action and other proceedings (collectively, "Claims"), including but not limited to legal costs and fees, arising out of or relating to: (i) Your breach of this Agreement, including without limitation any representation or warranty contained in this Agreement; (ii) Your access to or use of the Website or Services; (iii) Your provision to BeVo Media or any of the Indemnified Parties of information or other data; or (iv) Your violation or alleged violation of any foreign or domestic, federal, state or local law or regulation.</p>
					
					<p>The Indemnified Parties will have the right, but not the obligation, to participate through counsel of their choice in any defense by You of any Claim as to which You are required to defend, indemnify or hold harmless the Indemnified Parties. You may not settle any Claim without the prior written consent of the concerned Indemnified Parties.</p>
					
					<p>13. GOVERNING LAW; JURISDICTION AND VENUE</p>
					<p>This Agreement, including without limitation its construction and enforcement, shall be treated as though it were executed and performed in San Diego, California, and shall be governed by and construed in accordance with the laws of the State of California without regard to its conflict of law principles. ANY CAUSE OF ACTION BY YOU ARISING OUT OF OR RELATING TO THIS AGREEMENT OR THE WEBSITE MUST BE INSTITUTED WITHIN ONE (1) YEAR AFTER IT AROSE OR BE FOREVER WAIVED AND BARRED. THE PROPER VENUE FOR ANY JUDICIAL ACTION ARISING OUT OF OR RELATING TO THIS AGREEMENT OR THE WEBSITE WILL BE THE STATE AND FEDERAL COURTS IN OR NEAR SAN DIEGO COUNTY, CALIFORNIA. THE PARTIES HEREBY STIPULATE TO, AND AGREE TO WAIVE ANY OBJECTION TO, THE PERSONAL JURISDICTION AND VENUE OF SUCH COURTS, AND FURTHER EXPRESSLY SUBMIT TO EXTRATERRITORIAL SERVICE OF PROCESS. </p>
					
					<p>14. TERMINATION</p>
					<p>Either party may terminate this Agreement and its rights hereunder at any time, for any or no reason at all, by providing to the other party notice of its intention to do so. This Agreement shall automatically terminate in the event that You breach any of this Agreement's representations, warranties or covenants. Such termination shall be automatic, and shall not require any action by BeVo Media. Upon termination, all rights and obligations created by this Agreement will terminate, except that Sections 1, 4, 5, 7, 8, 9, 10, 11, 12, 13, 14, 15 and 16 will survive any termination of this Agreement. For clarification, termination of this Agreement will not relieve You of Your obligation to pay any fees owed BeVo Media. If BeVo Media, in BeVo Media's discretion, takes legal action against You in connection with any actual or suspected breach of this Agreement, BeVo Media will be entitled to recover from You as part of such legal action, and You agree to pay, BeVo Media's reasonable costs and attorneys' fees incurred as a result of such legal action. The BeVo Media Parties will have no legal obligation or other liability to You or to any third party arising out of or relating to any termination of this Agreement.</p>
					
					<p>15. NOTICES</p>
					<p>All notices required or permitted to be given under this Agreement must be in writing. BeVo Media shall give any notice by email sent to the most recent email address, if any, provided by You to BeVo Media. You agree that any notice received from BeVo Media electronically satisfies any legal requirement that such notice be in writing. YOU BEAR THE SOLE RESPONSIBILITY OF ENSURING THAT YOUR EMAIL ADDRESS ON FILE WITH BEVO MEDIA IS ACCURATE AND CURRENT, AND NOTICE TO YOU SHALL BE DEEMED EFFECTIVE UPON THE SENDING BY BEVO MEDIA OF AN EMAIL TO THAT ADDRESS. You shall give any notice to BeVo Media by means of:  (1) mail, postage prepaid, to 11622 El Camino Real Suite 100 San Diego, CA 92130; or (2) email to Ryan@bevomedia.com. Notice to BeVo Media will be deemed effective upon receipt by BeVo Media thereof.</p>
					
					<p>16. GENERAL</p>
					<p>This Agreement constitutes the entire agreement between BeVo Media and You concerning Your use of the Website and Services. This Agreement may only be modified by a written amendment signed by an authorized executive of BeVo Media or by the unilateral amendment of this Agreement by BeVo Media and by the posting by BeVo Media of such amended version. If any part of this Agreement is held invalid or unenforceable, that part will be construed to reflect the parties' original intent, and the remaining portions will remain in full force and effect. A waiver by either party of any term or condition of this Agreement or any breach thereof, in any one instance, will not waive such term or condition or any subsequent breach thereof. This Agreement and all of Your rights and obligations hereunder will not be assignable or transferable by You without the prior written consent of BeVo Media. BeVo Media may, in its sole discretion, assign or otherwise transfer all or any part of its rights and obligations under this Agreement without Your consent or notice to You. This Agreement will be binding upon and will inure to the benefit of the parties, their successors and permitted assigns. You and BeVo Media are independent contractors, and no agency, partnership, joint venture or employee-employer relationship is intended or created by this Agreement. You acknowledge and agree that any actual or threatened breach of this Agreement or infringement of proprietary or other third party rights by You would cause irreparable injury to BeVo Media and BeVo Media's licensors and suppliers, and would therefore entitle BeVo Media or BeVo Media's licensors or suppliers, as the case may be, to injunctive relief. The headings in this Agreement are for the purpose of convenience only and shall not limit, enlarge, or affect any of the covenants, terms, conditions or provisions of this Agreement.</p>
					
					<p>The Bevo Media Hours of operation are Mon-Fri 10am-7pm PST.</p>

				
				</div><!--close lpop_tos-->
			</div><!--close lpop_cartbox-->
			<div class="lpop_cartbox last">
				<p>By clicking the Pay Now button, you agree to the terms of service.</p>
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PayPPCYearly.html">Pay Now</a>
				<div class="clear"></div>
			</div>
		</div><!--close lpop_cart-->
	</div><!--close lpop_content-->
</div><!--close lpop_license-->

<script type="text/javascript">
	$(document).ready(function() {
		$('#VerifyLink').click(function() {
			parent.window.location = '/BevoMedia/User/CreditCard.html';
			return false;
		});
	});
</script>

</body>
</html>
