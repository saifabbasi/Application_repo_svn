<style type="text/css">
<!--
.box.tos { height:120px; padding:8px; margin:8px; border:1px solid #e6e6e6; overflow:auto; }
.box.tos h2 { font-size:1em; color:#808080; }
.box.tos p { font-size:1em; margin:0 0 5px; }
-->
</style>

<div id="pageinfo" class="sub">
	<h2>Register a BeVo Media Account</h2>	
</div>


<div class="clear"></div>

<form method="post" 
	name="registerForm" 
	class="registerForm" 
	onSubmit="javascript:return registerFormValidation.validateForm();">

<div style='
		width: 80px; height: 30px; float: left; color: #0077B3;
		background-image: url(/Themes/BevoMedia/img/Signup_03.gif); 
		position: relative; left: 20px; margin-right: 5px;
		line-height: 30px; text-align: center;'>
	Step 1
</div>

<div style='
		width: 80px; height: 30px;  float: left; color: #FFFFFF;
		background-image: url(/Themes/BevoMedia/img/Signup_05.gif); 
		position: relative; left: 20px;
		line-height: 30px; text-align: center;'>
	Step 2
</div>

<br class='clearBoth'/>
<div
	style="width: 624px; background-color: #F2FCFF; border-left: solid 2px #00B4E1; border-right: solid 2px #00B4E1;">

	<img src='/Themes/BevoMedia/img/Signup_08.gif' style='position: relative; left: -2px; top:-2px;'/>
		
	<label for="firstname_id">
		<span class="label">[required] First Name:</span>
		<input type="text" name="FirstName" value="" id="firstname_id" />
		<span class="validation" id="firstname_validation_id">INVALID</span>
	</label>
	
	<label for="lastname_id">
		<span class="label">[required] Last Name:</span>
		<input type="text" name="LastName" value="" id="lastname_id" />
		<span class="validation" id="lastname_validation_id">INVALID</span>
	</label>
	
	<label for="email_id">
		<span class="label">[required] Email:</span>
		<input type="text" name="Email" value="" id="email_id" />
		<span class="validation" id="email_validation_id">INVALID</span>
	</label>

	<label for="password_id">
		<span class="label">[required] Desired Password:</span>
		<input type="password" name="Password" value="" id="password_id" />
		<span class="validation" id="password_validation_id">INVALID</span>
	</label>

	<label for="re-enter_password_id">
		<span class="label">[required] Re-enter Password:</span>
		<input type="password" name="re-enter_password" value="" id="re-enter_password_id" />
		<span class="validation" id="re-enter_password_validation_id">INVALID</span>
	</label>

	<label for="companyname_id">
		<span class="label">Company Name:</span>
		<input type="text" name="CompanyName" value="" id="companyname_id" />
	</label>

	<label for="address_id">
		<span class="label">Address:</span>
		<input type="text" name="Address" value="" id="address_id" />
	</label>

	<label for="city_id">
		<span class="label">City:</span>
		<input type="text" name="City" value="" id="city_id" />
	</label>

	<label for="state_id">
		<span class="label">State:</span>
		<input type="text" name="State" value="" id="state_id" />
	</label>

	<label for="zip_id">
		<span class="label">Zip:</span>
		<input type="text" name="Zip" value="" id="zip_id" />
	</label>

	<label for="country_id">
		<span class="label">Country:</span>
		<input type="text" name="Country" value="" id="country_id" />
	</label>

	<label for="phone_id">
		<span class="label">Phone Number:</span>
		<input type="text" name="Phone" value="" id="phone_id" />
	</label>

	<label for="website_id">
		<span class="label">Primary Website:</span>
		<input type="text" name="Website" value="" id="website_id" />
	</label>
	
	<label>
		<span class="label">Instant Messenger Service:</span>
		<div class="radioFloat">
			<input type="radio" name="Messenger" value="AIM">AIM<br/>
			<input type="radio" name="Messenger" value="YAHOO_MESSENGER">Yahoo Messenger<br/>
			<input type="radio" name="Messenger" value="MSN_MESSENGER">MSN Messenger<br/>
			<input type="radio" name="Messenger" value="GTALK">Gtalk<br/>		
		</div>
	</label>
	
	<label for="messengerhandle_id">
		<span class="label">Messenger Handle:</span>
		<input type="text" name="MessengerHandle" value="" id="messengerhandle_id" />
	</label>
	
	<label>
		<span class="label">Marketing Method:</span>
		<div class="radioFloat">
			<input type="radio" name="MarketingMethod" value="EMAIL">Email<br/>
			<input type="radio" name="MarketingMethod" value="KEYWORD">Keyword<br/>
			<input type="radio" name="MarketingMethod" value="WEB">Web / Seo<br/>
			<input type="radio" name="MarketingMethod" value="OTHER">Other<br/>		
		</div>
	</label>
	
	<label for="marketingmethodother_id">
		<span class="label">Marketing Method:</span>
		<input type="text" name="MarketingMethodOther" value="" id="marketingmethodother_id" />
	</label>
	
	<label for="username_id">
		<span class="label">[required] Forum Username:</span>
		<input type="text" name="Username" value="" id="username_id" />
		<span class="validation" id="username_validation_id">INVALID</span>
	</label>
	
	<label for="howheard_id">
		<span class="label">[required] How did you hear about us:</span>
		<input type="text" name="HowHeard" value="" id="howheard_id" />
		<span class="validation" id="howheard_validation_id">INVALID</span>
	</label>
	
	<label for="comments_id">
		<span class="label">Additional Comments:</span>
		<textarea name="Comments" id="comments_id"></textarea>
	</label>
	
	<label for="timezone_id">
		<span class="label">Timezone:</span>
		<select name="Timezone" id="timezone_id">
		<?php $tz = new TimezoneHelper()?>
		<?php foreach($tz->getTimezones() as $timezone):?>
			<option value="<?php print $timezone->PHPTimezone; ?>"><?php print $timezone->GMTLabel; ?></option>
		<?php endforeach?>
		</select>
	</label>
	
<script type="text/javascript" src="/JS/charts/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
#tooltip {
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	width:285px;
	z-index: 10000;
	}
</style>
<script language="javascript">
$(document).ready(function(){
	$('#bpc').change(function(){
		if($(this).attr('checked')){
			$('#niche').show();
		}else{
			$('#niche').hide();
		}
	});

	$('#bpc').change();
});
</script>
	
	<br/>
	
<div class="box tos">
	
	<h2>Affiliate Program Operating Agreement Terms of Service</h2>
	
	<p>This Affiliate Program Operating Agreement (the "Agreement") is made and entered into by and between BeVo Media, LLC ("BeVo Media" or "we"), and you, ("you" or "Affiliate") the party submitting an application to become a BeVo Media affiliate. The terms and conditions contained in this Agreement apply to your participation with www.BeVoMedia.com ("Affiliate Program"), and its partnered affiliate networks ("Partners"). Each Affiliate Program offer may be for any offering by BeVo Media or Partners and may link to a specific web site for that particular offer ("Program Web Site"). Furthermore, each Affiliate Program may have additional terms and conditions on pages within the Affiliate Program and are incorporated as part of this Agreement. By submitting an application or participating in an offer, you expressly consent to all the terms and conditions of this Agreement.</p>
	
	<p>WHEREAS, BeVo Media and Affiliate desire to provide for the terms and conditions of this Agreement as more specifically set forth herein; THEREFORE, the Parties agree to be legally bound as follows:</p>

	<p>1. Enrollment in the Affiliate Program</p>
	<p>You must submit an Affiliate Program application from our website. You must accurately complete the application, provide us with updated information and not use any aliases or other means to mask your true identity or contact information in order to become an Affiliate. Acceptance into our Affiliate Program will be communicated upon review of your application. We may accept or reject your application at our sole discretion for any reason.</p>

	<p>2. Affiliate Requirements</p>
	<p>2.1. Affiliate is subject to review and BeVo Media may reject or terminate, for any reason, and at any time.</p>
	<p>2.2. Affiliate must submit valid and correct contact information, including but not limited to name, e-mail address, street address, and telephone number. Affiliate must ensure this information remains up-to-date at all times within BeVo Media. Affiliate must accurately, clearly and completely describe all promotional methods in their descriptions and providing additional information when necessary.</p>
	<p>2.3. Affiliate websites must not be associated with or contain any illegal activity, or pornographic, obscene, racist, or hateful content, or deceptive advertising, piracy, libelous or defamatory statements.</p>
	<p>2.4. Affiliate websites must not contain any mechanisms that could be downloaded on to a User's computer without the User's explicit knowledge and consent.</p>
	<p>2.5. Affiliate websites must be English language websites.</p>
	<p>2.6. In its sole discretion, if at any time BeVo Media deems the Affiliate's website or advertising activities are contrary to the terms set out in the Agreement, the Affiliate shall be terminated from the Network and shall forfeit any and all commissions and earnings.</p>
	
	<p>3. Obligations of the Parties</p>
	<p>(a) Subject to our acceptance of you as an affiliate and your continued compliance with the terms and conditions of this Agreement, BeVo Media agrees as follows:</p>
	
	<p>1. We will make available to you via BeVo's interface graphic and textual links to the Program Web Site and/or other creative materials (collectively, the "Links") which you may display on web sites owned or controlled by you, in emails sent by you and clearly identified as coming from you and in online advertisements (collectively, "Media"). The Links will serve to identify you as a member of our Affiliate Program and will establish a link from your Media to the Program Web Site.</p>
	
	<p>2. Our Partners will pay Affiliate for each Qualified Action (the "Commission"). A "Qualified Action" means an individual person who (i) accesses the Program Web Site via the Link, where the Link is the last link to the Program Web Site, (ii) is not a computer generated user, such as a robot, spider, computer script or other automated, artificial or fraudulent method to appear like an individual, real live person, (iii) is not using pre-populated fields (iv) completes all of the information required for such action within the time period allowed by BeVo Media and (v) is not later determined by BeVo Media to be fraudulent, incomplete, unqualified or a duplicate.</p>

	<p>3. Our Partners will pay you any Commissions earned on a basis in adherence to their payment terms of service. BeVo Media is not responsible for any earnings or payments claimed by the Affiliate earned through its Partners. Any revenue derived from ads promoted for BeVo's Partners by its affiliates is the responsibility of the Partners to compensate.</p>

	<p>4. You hereby release BeVo Media from any claim for commissions earned through Partners. You also agree to release BeVo Media for any claims arising from codes, creatives, or other any other technology distributed though Partners.</p>

	<p>(b) Affiliate also agrees to:</p>
	
	<p>1. Have sole responsibility for the development, operation, and maintenance of, and all content on or linked to, your Media.</p>
	
	<p>2. Ensure that all materials posted on your Media or otherwise used in connection with the Affiliate Program (i) are not illegal, (ii) do not infringe upon the intellectual property or personal rights of any third party and (iii) do not contain or link to any material which is harmful, threatening, defamatory, obscene, sexually explicit, harassing, promotes violence, promotes discrimination (whether based on sex, religion, race, ethnicity, nationality, disability or age), promotes illegal activities (such as gambling), contains profanity or otherwise contains materials that BeVo Media informs you that it considers objectionable (collectively, "Objectionable Content").</p>

	<p>3. Not make any representations, warranties or other statements concerning BeVo Media or any of its products or services, except as expressly authorized herein.</p>

	<p>4. Comply with all (i) obligations, requirements and restrictions under this Agreement and (ii) laws, rules and regulations as they relate to your business, your Media or your use of the Links.</p>

	<p>5. CPM rate payouts as determined by BeVo Media. Revenue share is negotiated between BeVo Media and the respective affiliate network, with BeVo Media taking a commission of the revenue share. Commission is at the discretion of BeVo Media and varies by ad network.</p>

	<p>4. Affiliate Rules</p>

	<p>4.1. Failure to adhere to the following rules is a violation of the Agreement and will result in immediate termination of the Affiliate from BeVo Media with forfeiture of all monies due to Affiliate.</p>

	<p>4.2. Affiliate must not load Advertiser's website within a frameset or iframe unless prior written approval is obtained from BeVo Media, or Partner offering the ad.</p>
	<p>4.3. Affiliate must not modify the Ads supplied by BeVo Media or its Partners in any way unless prior written approval is obtained from BeVo Media or its Partners.</p>
	<p>4.4. Affiliate must not make misleading or disparaging statements, oral or written, about any Ad, Advertiser, Partner, or BeVo Media.</p>
	<p>4.5. Affiliate must agree to receive periodic communications from BeVo Media, Partners and Advertiser. This communication could be in the form of e-mail, postal mail, telephone or fax.</p>
	<p>4.6. Affiliate must not display any Ad in third party newsgroups, message boards, blogs, unsolicited email and other types of spam, link farms, counters, chatrooms, or guestbooks.</p>
	<p>4.7. Affiliate must comply with all Campaign Terms as outlined in Ads.</p>
	<p>4.8. Affiliate must not generate any Actions in bad faith or through fraudulent mechanisms. This includes, but is not limited to, generating own Actions using manual or automated processes, generating Actions using adware or spyware, and encouraging or educating Surfers to cancel any service provided by Advertisers.</p>
	<p>4.9. Affiliate must not display any Ad through any mechanism on MySpace or Facebook accounts. This includes, but is not limited to, bulletins, comments, mail, profiles or any other area of a MySpace or Facebook account.</p>
	<p>4.10. Any Affiliate engaged in the distribution of Ads via email must comply with all of the following rules:</p>
	<p>4.10.1. Affiliate must distribute Ads only to those recipients who have Opted-in to receive such email from the Affiliate. BeVo Media prohibits the use of Spam. Any use of Spam whatsoever by Affiliate will result in the forfeiture of Affiliate's entire commission for all campaigns, and the termination of the Affiliate's account. Affiliate will also be held liable for any and all damages resulting from a violation of this provision including reasonable court costs.</p>
	<p>4.10.2. If requested by BeVo Media, Affiliate must be able within 48 hours of such request, to supply the name, date, time and IP address where the User gave permission to the Affiliate to receive such Ads through e-mail.</p>
	<p>4.10.3. Affiliate must ensure each email recipient is provided with a valid opt-out mechanism within each email delivered in order for the recipients to "opt-out" of future mailings from Affiliate.</p>
	<p>4.10.4. Affiliate must not use the Advertiser, Partner, or BeVo Media name (including any abbreviation thereof) in the originating email address line ("From" line) or subject line of any email transmission, unless specific permission is given otherwise.</p>
	<p>4.10.5. Affiliate must not use falsified sender information or falsified IP Addresses.</p>
	<p>4.10.6. Affiliate must use only pre-approved Advertiser subject lines and from lines as set out in Campaign Terms.</p>
	<p>4.10.7. Affiliate must use only legitimate routing information.</p>
	<p>4.10.8. Affiliate must use their own tracking links that redirect to the tracking links supplied by BeVo Media.</p>
	<p>4.10.9. Affiliate must have a proper privacy policy on their website, and it must be in compliance with all FTC guidelines, rules and regulations in respect to online privacy and shall warrant that email campaigns are conducted in accordance with that privacy policy, and in accordance with any applicable local or international laws.</p>
	<p>4.10.10. Affiliate must ensure each email contains Advertiser's unsubscribe mechanism as set out in Campaign Terms.</p>
	<p>4.10.11. Affiliate must not send email to any email address or domain contained in an Advertiser's suppression list as set out in Campaign Terms.</p>
	<p>4.10.12. Affiliate must ensure each email clearly contains the Affiliate's physical address, which cannot be a PO BOX.</p>
	<p>4.10.13. Affiliate must comply with all campaign instructions from BeVo Media, Partners and Advertiser as set out in Campaign Terms.</p>
	<p>4.11.14. Affiliate must comply with any and all applicable rules, regulations and laws, specified or not within this Agreement, in respect to email distribution and advertising.</p>

	<p>5. Representations and Warranties</p>
	<p>5.1. Each Party represents and warrants they have full corporate right, power, and authority to enter into this Agreement, to grant the rights and licenses granted and to perform the acts required of it.</p>
	<p>5.2. Each Party acknowledges that the other Party makes no representations, warranties, or agreements related to the subject matter hereof that are not expressly provided for in this Agreement.</p>
	<p>6. Non-Circumvention</p>
	<p>6.1. Affiliate shall not solicit or recruit, directly or indirectly, any Advertiser that is known to Affiliate to be an Advertiser of BeVo Media, for purposes of offering products or services that are competitive with BeVo Media, nor contact such Advertisers for any purpose, during the term of Affiliate's membership in the BeVo Media Network and for the twelve (12) month period following termination of Affiliate's membership in the BeVo Media Network.</p>

	<p>7. Limitation of Liability</p>
	<p>7.1. UNDER NO CIRCUMSTANCES SHALL EITHER PARTY BE LIABLE TO THE OTHER FOR INDIRECT, INCIDENTAL, PUNITIVE, CONSEQUENTIAL, SPECIAL OR EXEMPLARY DAMAGES OR COSTS, DIRECT OR INDIRECT, (EVEN IF SUCH PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES), ARISING FROM AFFILIATE PARTICIPATION IN BEVO MEDIA OR ITS PARTNERS. BEVO MEDIA SHALL NOT, IN ANY EVENT, BE LIABLE TO AFFILIATE FOR MORE THAN THE AMOUNT PAID TO AFFILIATE HEREUNDER. NO ACTION, SUIT OR PROCEEDING SHALL BE BROUGHT AGAINST BEVO MEDIA MORE THAN ONE YEAR AFTER THE DATE OF SERVICE.</p>
	<p>7.2. Affiliate agrees to not to hold BeVo Media, Partners or Advertisers liable for any of the consequences of interruption or service.</p>
	<p>7.3 Affiliate agrees to litigate any and all claims in arbitration at the discretion of BeVo Media, in the state of Delaware.</p>
	
	<p>8. Indemnification</p>
	<p>8.1. Affiliate hereto agrees to indemnify and hold harmless BeVo Media, Advertiser, Partners and each if its agents, officers, directors and employees against all liability to third parties resulting from the acts or failure to act of such indemnifying party, or any act of its customers or users. Affiliate is solely responsible for any legal liability arising out of or relating to the Affiliate's website(s), any material to which Users can link through the Affiliate's website(s) and/or any consumer and/or governmental/regulatory complaint arising out of any e-mail campaign or other advertising campaign conducted by Affiliate, including but not limited to any Spam or fraud complaint and/or any complaint relating to failure to have proper permission to conduct such campaign to the consumer.</p>

	<p>9. Confidentiality</p>
	<p>9.1. Affiliate agrees to refrain from disclosing BeVo Media's confidential information, the Partner's confidential information, or the Advertiser's confidential information (including but not limited to commission rates, conversion rates, email addresses, fees, identities of Advertisers) to any third-party without prior written permission from BeVo Media.</p>

	<p>10. Remedies</p>
	<p>10.1. BeVo Media reserves the right to withhold payment and take appropriate legal action to cover its damages against any Affiliate that violates the terms of this Agreement or breaches the representations and warranties set forth in this Agreement, or commits fraudulent activity against BeVo Media. Except as otherwise specified, the rights and remedies granted to a Party under the Agreement are cumulative and in addition to, not in lieu of, any other rights and remedies which the Party may possess at law or in equity.</p>

	<p>11. Entire Agreement</p>
	<p>11.1. This Agreement constitutes the entire and only agreement and supersedes any and all prior agreements, whether written, oral, express, or implied, of the Parties with respect to the transactions set forth herein.</p>
	
	<p>12. Governing Law</p>
	<p>12.1. The rights and obligations of the parties under this Agreement shall be governed by and construed under the laws of the United States of America, without reference to conflict of laws principles.</p>

	<p>13. Termination</p>
	<p>13.1. This Agreement may be terminated by either party. This Agreement may be terminated immediately upon notice for your breach of this Agreement.</p>
	<p>13.2. An Advertiser may terminate Affiliate from the Advertiser's program for any or no reason. </p>
	<p>13.3. Upon termination of this Agreement, any permissions granted under this Agreement will terminate, and Affiliate must immediately remove all Ads and link to Advertiser(s). IN WITNESS WHEREOF, the Parties have caused this Agreement to be duly executed and binding upon Affiliate's submission and BeVo Media's acceptance of Affiliate's properly completed Affiliate network application without need for further action by BeVo Media.</p>

	<p>Refund Policy:</p>
	<p>In the event that you are unsatisfied with our products or services, you may request a refund within thirty (30) days of payment, less all administrative, banking, and merchant fees. A refund may be requested via email at refunds@bevomedia.com or via phone at 1-888-644-BEVO.  No refund will be issued after the 30 day period has passed for any reason. No partial refund will be issued for service interruption or failure. The Bevo Media Hours of operation are Mon-Fri 10am-7pm PST.</p>

	<p>Services provided through the BeVo website are hosted by Amazon.com.  BeVo Media makes no guarantee of server uptime and offers no partial refund for web hosting service disruption.  </p>

	<p>Cardholder Terms:</p>

	<p>By purchasing a premium package or service from Bevo Media, the cardholder agrees to the terms of the service for the given product or service, which may include a monthly charge. Email notification will be sent prior to any charge. In all events, BeVo Media�s total liability shall be limited to, and shall not exceed, the amount actually paid by the cardholder to BeVo Media.</p>
	
	

	<h2>Privacy Policy</h2>

	<p>BevoMedia, LLC ('<b>BevoMedia</b>') is committed to respecting the privacy rights of users of BevoMedia's website (the '<b>Website</b>') and the services available through the website (the '<b>Services</b>'). BevoMedia created this Privacy Policy to give you ('<b>You</b>' or '<b>Your</b>') confidence as You visit and use the Website, and to demonstrate its commitment to fair information practices and to the protection of privacy.</p>

	<p>The Website is linked with the websites of third parties ('<b>Third-Party Websites</b>'). This Privacy Policy is only applicable to the Website and not to any Third-Party Websites, which may have data collection, storage and use practices and policies that differ materially from this Privacy Policy. For additional information, see the section concerning Third-Party Websites, below.</p>

	<p>BY USING THE WEBSITE AND SERVICES, YOU REPRESENT AND WARRANT THAT YOU HAVE READ AND UNDERSTOOD, AND AGREE TO THE TERMS OF, THIS PRIVACY POLICY. IF YOU DO NOT UNDERSTAND OR DO NOT AGREE TO BE BOUND BY THIS PRIVACY POLICY, YOU MUST IMMEDIATELY LEAVE THE WEBSITE.</p>
		
	<p>Each time You use the Website, the then-current version of this Privacy Policy will apply. Accordingly, each time You use the Website You should check the date of this Privacy Policy (which appears at the end) and review any changes since the last time You used the Website. For additional information, see the section concerning Updates and Changes to Privacy Policy, below.</p>

	<h2>INFORMATION COLLECTION PRACTICES</h2>
				
	<p>Traffic Data.</p>
	<p>Like most website operators, BevoMedia automatically gathers information of the sort that browsers automatically make available, including: (i) IP addresses; (ii) domain servers; (iii) types of computers accessing the Website; and (iv) types of Web browsers accessing the Website (collectively '<b>Traffic Data</b>'). Traffic Data is anonymous information that does not personally identify You.</p>
	
	<p>Cookies.</p>
	<p>A '<b>Cookie</b>' is a string of information that a website stores on a user's computer, and that the user's browser provides to the website each time the user submits a query to the website.  The purpose of a Cookie is to identify the user as a unique user of the Website.  BevoMedia uses Cookies to customize Your experience on the Website to Your interests, to ensure that You do not see the same advertisements or informational messages repeatedly, and to store Your password so You do not have to re-enter it each time You visit the Website.  For additional information on BevoMedia' uses of Cookies, see the section concerning Information Use and Disclosure Practices/Traffic Data and Information Gathered Using Cookies, below.</p>

	<p>IF YOU DO NOT WISH TO HAVE COOKIES PLACED ON YOUR COMPUTER, YOU SHOULD SET YOUR BROWSERS TO REFUSE COOKIES BEFORE ACCESSING THE WEBSITE, WITH THE UNDERSTANDING THAT CERTAIN OF THE SERVICES AND CERTAIN FEATURES OF THE WEBSITE MAY NOT FUNCTION PROPERLY WITHOUT THE AID OF COOKIES.  IF YOU REFUSE COOKIES, YOU ASSUME ALL RESPONSIBILITY FOR ANY RESULTING LOSS OF FUNCTIONALITY. </p>
				
	<p>Personal Information.</p>
	<p>In order for You to use certain of the Services, You may be asked to provide BevoMedia certain information that personally identifies You ('<b>Personal Information</b>'). Personal Information includes, without limitation: (1) '<b>Contact Data</b>' (such as Your name, company name, email address, mailing address, phone number and instant messenger handle); (2) '<b>Login Information</b>' (such as Your user name and password of Your affiliate/marketing accounts that you wish to view using the Website and Services);  and (3) '<b>Demographic Data</b>' (such as Your instant messenger service, primary website, marketing method, zip code and country). In each case, You will be asked to provide Personal Information; the Website will not gather it surreptitiously. BevoMedia may supplement the Personal Information You provide with additional Personal Information gathered from public sources or from third-parties (e.g., consumer reporting agencies) who may lawfully provide such information to BevoMedia.</p>
	
	<p>You are under no obligation to provide Personal Information, with the caveat that Your refusal to do so may prevent You from using certain of the Services. </p>
				 <p>INFORMATION USE AND DISCLOSURE PRACTICES</p>
				
	<p>Traffic Data and Information Gathered Using Cookies </p>
	
	<p>BevoMedia analyzes Traffic Data and information gathered using Cookies to help BevoMedia better understand who is using the Website and how they are using it. By identifying patterns and trends in usage, BevoMedia is able to better design the Website to improve Your experience, and to serve You more relevant and interesting content and advertisements. From time to time, BevoMedia may release Traffic Data and information gathered using Cookies in the aggregate, such as by publishing a report on trends in the usage of the Website. BevoMedia does not attempt to link information gathered using Cookies to Personal Information.</p>
	
	<p>Personal Information</p>
	
	<p>Generally.</p>
	<p>BevoMedia uses Your Contact Data to send You information about BevoMedia and BevoMedia' products and services and to contact You when necessary in connection with the Services. BevoMedia uses Your Login Information to provide the Services. BevoMedia uses Your Demographic Data to customize and tailor Your experience on the Website. As with Traffic Data and information gathered using Cookies, from time to time BevoMedia may release Demographic Data in the aggregate, such as by publishing a report on trends in the usage of the Website.</p>

	<p>Disclosure Practices</p>
	<p>Except under the following circumstances, BevoMedia will keep Your Personal Information private, and will not share it with third parties.</p>

	<p>Disclosure in Connection with Services.</p>
	<p>BevoMedia discloses Personal Information to those who help it provide Services, including those who perform technical, administrative and data processing tasks such as hosting, data storage and security.</p>

	<p>By Law or to Protect Rights.</p>
	<p>BevoMedia discloses Personal Information when required to do so by law, or in response to a subpoena or court order, or when BevoMedia believes in its sole discretion that disclosure is reasonably necessary to protect the property or rights of BevoMedia, third parties or the public at large.</p>

	<p>Business Transfers; Bankruptcy.</p>
	<p>BevoMedia reserves the right to transfer all Personal Information in its possession to a successor organization in the event of a merger, acquisition, or bankruptcy or other sale of all or a portion of BevoMedia' assets. Other than to the extent ordered by a bankruptcy or other court, the use and disclosure of all transferred Personal Information will be subject to this Privacy Policy, or to a new privacy policy if You are given notice of that new privacy policy and an opportunity to affirmatively opt-out of it.  Personal Information submitted or collected after a transfer, however, may be subject to a new privacy policy adopted by BevoMedia successor organization.</p>

	<p>SECURITY OF PERSONAL INFORMATION</p>

	<p>BevoMedia has implemented and maintains reasonable security procedures and practices to protect against the unauthorized access, use, modification, destruction or disclosure of Your Personal Information. </p>
	
	<p>USER ABILITY TO ACCESS, UPDATE, AND CORRECT PERSONAL   INFORMATION</p>

	<p>You must promptly notify us if Your user name or password is lost, stolen or used without permission.  In such and event, we will remove that user name or password from Your account and updated our records accordingly.</p>
	
	<p>LOST OR STOLEN INFORMATION</p>
	
	<p><b>BevoMedia wants Your Personal Information to be complete and accurate. You represent and warrant that all information You provide on any registration form or otherwise in connection with Your use of the Website and Services will be complete and accurate, and that You will update that information as necessary to maintain its completeness and accuracy. To confirm the completeness and accuracy of, or make changes to, Your Personal Information, visit Your personal profile. Through Your personal profile You may review and update You Personal Information that we have already collected. </b></p>
	<p>THIRD-PARTY WEBSITES</p>

	<p>BevoMedia neither owns nor controls Third-Party Websites. Accordingly, Third-Party Websites are under no obligation to comply with this Privacy Policy except with respect to Personal Information provided directly to them by BevoMedia. Before visiting or providing Personal Information to a Third-Party Website, You should inform Yourself of the privacy policies and practices (if any) of that Third-Party Website, and should take those steps necessary to, in Your discretion, protect Your privacy.</p>
	
	<p>SURVEYS</p>
	
	<p>From time to time, BevoMedia may also ask You to participate in surveys designed to help BevoMedia improve the Websites or Services.  Any Personal Information provided to BevoMedia in connection with any survey will be used only in relation to that survey, and will be disclosed to third parties not bound by this Privacy Policy only in aggregated form.</p>
	
	<p>UPDATES AND CHANGES TO PRIVACY POLICY</p>
	<p>Although most changes are likely to be minor, BevoMedia reserves the right, at any time and without notice, to add to, update, change or modify this Privacy Policy, simply by posting such update, change or modification on this page. Any such addition, update, change or modification will be effective immediately upon posting on the Website.  Each time You use the Website, the then-current version of this Privacy Policy will apply.  Accordingly, each time You use the Website You should check the date of this Privacy Policy (which appears at the end) and review any changes since the last time You used the Website.  Unless BevoMedia obtains Your express consent, any revised Privacy Policy will apply only to information collected after the effective date of such revised Privacy Policy, and not to information collected under any earlier Privacy Policy.</p>

	<p>Effective Date: [January 1st, 2010]</p>

	
	</div><!--close box.tos-->
	
	<label for="X">
	<a class="tooltip" title="The Bevo Performance Connector is a free service connecting networks with publishers who want the best opportunity of promoting an offer. Bevo Media has close relationships with all of its partnered networks. If you want to be a part of the Bevo Performance Connector program, you will simply tell Bevo Media what niches you are interested in, and a Bevo Representative will get you in touch with one of their networks that will do whatever it takes to get you on their network, make sure you are satisfied, including Top payouts and guaranteed EPC's. Bevo Performance Connector ensures that publishers are getting the best performance from their offers and from the networks they run with, on a personal level. You will receive periodic intro emails from a Bevo representative connecting you with high potential opportunities.">
		<br/>
		<input type="checkbox" name="bevoPerformanceConnector" id ="bpc" /> &nbsp; I would like to be a part of the Bevo Performance Connector
		<img width="12" height="12" src="/Themes/BevoMedia/img/questionMarkIcon.png">
	</a>
	</label>
	
	<div style="display:none;" id="niche" >
	<label>
	Hold down Ctrl to select multiple niches.<br/>
	<select name="niche[]" size="10" class="required formselect" rel="Niche" multiple="multiple">
		<?php 
			$selected = '';
			foreach ($this->Niches as $Niche) {
		?>
				<option value="<?php echo $Niche->ID?>" <?php echo $selected;?>><?php echo $Niche->Name?></option>
		<?php 
			}
		?>
	</select>
	</label>
	<br/>
	<label for="Y">
	Promotion Method (check all that apply): <br/>
	<?php foreach($this->PromoMethods as $PromoMethod):?>
	<input type="checkbox" name="promomethod[]" value="<?php echo $PromoMethod->id;?>" <?php echo $selected;?>/><?php echo $PromoMethod->promomethod;?>
	<?php endforeach;?>
	</label>
	
	<label for="Z">
	Experience: <br/>
	<?php foreach($this->ExpLevels as $ExpLevel):?>
	<input type="radio" name="explevel[]" value="<?php echo $ExpLevel->id;?>" <?php echo $selected?>/><?php echo $ExpLevel->explevel;?>&nbsp;
	<?php endforeach;?>
	</label>
	
	
	</div>
	<br/>
	
	<label><input type="checkbox" id="EULAAccepted" name="EULAAccepted" value="1" /> &nbsp; I accept the User Agreement, Privacy Policy, and Terms of Service</label>
	
	<br/>
	
	
	<input type='submit' name='registerFormSubmit' class='formRegister' onclick="if ($$('EULAAccepted').checked==false) { alert('You must accept the END USER SOFTWARE AGREEMENT in order to continue.'); return false;} ">
	<br/>
	
	<img src='/Themes/BevoMedia/img/Signup_18.gif' style='position: relative; left: -2px;'/>
</div>
<br/>

</form>