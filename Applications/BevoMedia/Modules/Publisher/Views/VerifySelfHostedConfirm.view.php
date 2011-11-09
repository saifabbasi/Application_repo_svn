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
		<h2>Bevo Self-Hosted</h2>
		<p>The Bevo Self-Hosted version is a premium feature that has a nominal one-time licensing fee.</p>
	</div>
	
	<div class="lpop_content">
		<h2>Your Cart</h2>
		
		<div class="lpop_cart">
			<div class="lpop_cartbox">
				<h3 class="floatright">$600.00</h3>
				
				<h3>Bevo Self-Hosted</h3>
				<p>License for unlimited self-hosted installs for personal use</p>
				
				<div class="clear"></div>
			</div>
			<div class="lpop_cartbox lpop_securicons">
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PaySelfHostedYearly.html">Pay Now</a><div class="clear"></div>
			</div>
			<div class="lpop_cartbox">
				<ul>
					<li>Your card on file will be billed for the above noted amount. You will receive immediate access to the feature.</li>
					<li>We will only bill your card once for the above mentioned license. Your card on file will be billed. To change this card, <a id="VerifyLink" href="#">click here</a>.</li>
				</ul>
				
				<div class="lpop_tos">
				
					<h2>END USER SOFTWARE AGREEMENT</h2>
					
					<p>1. AGREEMENT</p>
					
					<p>This End User Software Agreement (the "Agreement") is entered into as of the date last written below (the "Effective Date") by and between BeVo Media, LLC, a Delaware limited liability company with its principle place of business at 11622 El Camino Real, Suite 100, San Diego, CA 92130, (hereinafter, "BeVo Media") and you or your company (hereinafter, "Client") and governs your access and use of the BeVo Media software (the "Software"). CLIENT REPRESENTS AND WARRANTS THAT THE PERSON SIGNING THIS AGREEMENT ON ITS BEHALF HAS BEEN PROPERLY AUTHORIZED AND EMPOWERED TO DO SO.</p>
					
					<p>As used in this Agreement, "Software" shall further refer to and consist of the following: (i) the BeVo Media owned hosted software for managing and tracking affiliate marketing, including, without limitation, any software code, scripts, interfaces, graphics, displays, text, documentation and other components; and (ii) any updates, modifications or enhancements to the items listed in subsection (i). </p>
					
					<p>2. LICENSE AND RESTRICTIONS ON USE</p>
					
					<p>(a) Grant of Rights. BeVo Media grants Client a revocable, non-exclusive, non-transferable, non-assignable, non-sublicensable, limited right to access and use the Software strictly in accordance with the terms and conditions of this Agreement.</p>
					
					<p>(b) Restrictions on Use. Client shall use the Software strictly in accordance with the terms of this Agreement and shall not: (a) decompile, reverse engineer, disassemble, attempt to derive the source code of, or decrypt the Software; (b) make any modification, adaptation, improvement, enhancement, translation or derivative work from the Software; (c) violate any applicable laws, rules or regulations in connection with Client's access or use of the Software; (d) remove, alter or obscure any proprietary notice (including any notice of copyright or trademark) of BeVo Media or its affiliates, partners, suppliers or the licensors of the Software; (e) use the Software for any purpose for which it is not designed or intended; (f) use a single account for multiple business entities; (g) provide third-parties with access to Client's account; (h) use the Software for creating a product, service or software that is, directly or indirectly, competitive with or in any way a substitute for any services, product or software offered by BeVo Media; (i) use the Software to send automated queries to any website or to send any unsolicited commercial e-mail; or (j) use any proprietary information or interfaces of BeVo Media or other intellectual property of BeVo Media in the design, development, manufacture, licensing or distribution of any applications, accessories or devices for use with the Software. </p>
					
					<p>3. FEES AND PAYMENT </p>
					
					<p>(a) Service Fees. In consideration of the rights granted hereunder, Client shall pay BeVo Media the service fees (the "Fees"), as set forth in the Services Exhibit, which is attached hereto and incorporated herein by reference.  In addition to Fees, You must be a verified member of BeVo Media to use all premium sections of the website, including the Services described in this Agreement. Verified Membership requires a monthly fee as described on www.bevomedia.com.</p>
					
					<p>(b) Accounting; Payment. On the first day of each calendar month, BeVo Media shall render to Client a detailed accounting statement setting forth all Fees payable hereunder for the preceding calendar month. Unless otherwise specified in the Services Exhibit, the Fees shall be paid monthly in arrears within fifteen (15) days after the end of each calendar month. </p>
					
					<p>(c) Late Payments. Interest shall accrue on any amount due and payable hereunder and remaining unpaid for more than thirty (30) days (the "Principal Amount") at a rate per annum which shall from day to day be equal to the lesser of (i) one and a half percent (1.5%) per year, computed on the basis of a year of 360 days for the actual number of days elapsed (including the first day but excluding the last day) until payment of the Principal Amount; or (ii) the maximum rate of interest permitted from day to day under applicable law. </p>
					
					<p>(d) Taxes. All taxes and charges of any kind imposed by any national, federal, state or local government with respect to the products, services, or other items covered by this Agreement, shall be paid by Client (exclusive of taxes based on BeVo Media's income, which shall be paid by BeVo Media). </p>
					
					<p>(e) Refund. In the event that you are unsatisfied with our products or services, you may request a refund within thirty (30) days of payment, less all administrative, banking, and merchant fees.. No refund will be issued after the 30 day period has passed for any reason.  No partial refund will be issued for service interruption or failure.  A refund may be requested via email at refunds@bevomedia.com or via phone at 1-888-644-BEVO.    </p>
					
					<p>Cardholder Terms:</p>
					
					<p>By purchasing a premium package or service from Bevo Media, the cardholder agrees to the terms of the service or product which may include a monthly or annual charge. In all events, BeVo Media's total liability shall be limited to, and shall not exceed, the amount actually paid by the cardholder to BeVo Media. </p>
					
					<p>4. SOFTWARE &amp; CLIENT DATA STORAGE</p>
					
					<p>As used in this Agreement, "Client Data" means, collectively, Client's keywords, bid prices, clicks, conversions,
					impressions, costs per click, click through ratio, average position, unique visits, page views, page visits, bounce rate, average time on site, sub-IDs, landing page URLs, and referring URLs. The Software and Client Data will be hosted on BeVo Media's servers or on the servers of BeVo Media's third-party hosting providers. Client hereby authorizes and consents to the collection and storage by BeVo Media and its third-party hosting providers of Client Data. Client Data will be treated as Confidential Information (as defined in Section 10, below). CLIENT HEREBY ACKNOWLEDGES THAT A CATASTROPHIC SERVER FAILURE OR OTHER EVENT COULD RESULT IN THE LOSS OF ALL OF CLIENT DATA. CLIENT AGREES AND UNDERSTANDS THAT IT IS CLIENT'S RESPONSIBILITY TO BACKUP CLIENT DATA TO CLIENT'S COMPUTER OR EXTERNAL STORAGE DEVICE AND TO ENSURE SUCH BACKUPS ARE SECURE. </p>
					
					<p>5. TECHNICAL SUPPORT &amp; MAINTENANCE </p>
					
					<p>BeVo Media shall provide Client with incident-based technical support in connection with Client's use of the Software. In addition, BeVo Media shall, upon payment by Client of any charges, fees or costs (if any), provide Client with all modifications, bug fixes, and updates for the Software; however, BeVo Media has no obligation to provide Client with subsequent product releases. </p>
					
					<p>6. TERM AND TERMINATION; SURVIVAL</p>
					
					<p>(a) Term. Unless otherwise specified in the Services Exhibit, the initial term of this Agreement shall be one (1) year from the Effective Date, and shall automatically renew for successive one-year renewal terms (at the renewal rates applicable at the start of each renewal term), unless sooner terminated in accordance with this Section 7. </p>
					
					<p>(b) Termination for Convenience. Either party may terminate this Agreement at any time, for any reason or no reason, by giving the other party thirty (30) days written notice of termination.</p>
					
					<p>(c) Termination for Cause. Either party, as applicable, shall have the right, in addition, and without prejudice to any other rights or remedies, to terminate this Agreement as follows: (i) by BeVo Media, upon fifteen (15) days written notice, if Client fails to pay the amounts due to BeVo Media pursuant to this Agreement; (ii) by BeVo Media, upon fifteen (15) days written notice, if there is a change in control of Client, whether by sale of assets, stock, or otherwise; (iii) by either party for any material breach of this Agreement, other than failure to make payments under Section 3, that is not cured within ten (10) days of receipt of notice of breach from the other party specifying the breach and requiring its cure; or (iv) by either party, immediately upon written notice, if: (a) all or a substantial portion of the assets of the other party are transferred to an assignee for the benefit of creditors, to a receiver, or to a trustee in bankruptcy; (b) a proceeding is commenced by or against the other party for relief under bankruptcy or similar laws and such proceeding is not dismissed within sixty (60) days; or (c) the other party is adjudged bankrupt.</p>
					
					<p>(e) Effect of Termination. Upon termination this Agreement, all outstanding obligations relating to payments will survive. Without limiting the foregoing, any termination of this Agreement will automatically terminate all rights, licenses and obligations granted by or created hereunder, except that the following sections of this Agreement and any other provisions of this Agreement which by their express language or by their context are intended to survive the termination of this Agreement shall survive such termination: 2 (b), 3, 6(e), 7, 8, 9, 10, 11, 12, 13, 15, 16 and 17. Upon the termination of this Agreement, Client shall immediately cease all access and use of the Software. </p>
					
					<p>7. INTELLECTUAL PROPERTY RIGHTS</p>
					
					<p>(a) Rights to Software. Client acknowledges and agrees that the Software and all copyrights, patents, trademarks, trade secrets and other intellectual property rights associated therewith are, and shall remain, the property of BeVo Media. Furthermore, Client acknowledges and agrees that the source and object code of the Software and the format, directories, queries, algorithms, structure and organization of the Software are the intellectual property and proprietary and confidential information of BeVo Media and its affiliates, licensors and suppliers. Except as expressly stated in this Agreement, Client is not granted any intellectual property rights in or to the Software by implication, estoppel or other legal theory, and all rights in and to the Software not expressly granted in this Agreement are hereby reserved and retained by BeVo Media. </p>
					
					<p>(b) Third Party Software. The Software may utilize or include third party software that is subject to open source and third party license terms ("Third Party Software"). Client acknowledges and agrees that Client's right to use such Third Party Software as part of the Software is subject to and governed by the terms and conditions of the open source or third party license applicable to such Third Party Software, including, without limitation, any applicable acknowledgements, license terms and disclaimers contained therein. In the event of a conflict between the terms of this Agreement and the terms of such open source or third party licenses, the terms of the open source or third party licenses shall control with regard Client's use of the relevant Third Party Software. In no event, shall the Software or components thereof be deemed to be "open source" or "publically available" software. </p>
					
					<p>(c) BeVo Media Marks. BEVO MEDIA, BEVOMEDIA.COM, CLIENT'S INTERNET MARKETING HOMEBASE and the "BeVo Media Logo" (collectively, the "BeVo Media Marks") are trademarks or registered trademarks of BeVo Media. Other trademarks, service marks, graphics, logos and domain names appearing on the Software may be the trademarks of third-parties. Neither Client's use of the Software nor this Agreement grant Client any right, title or interest in or to, or any license to reproduce or otherwise use, the BeVo Media Marks or any third-party trademarks, service marks, graphics, logos or domain names. Client agrees that any goodwill in the BeVo Media Marks generated as a result of Client's use of the Software will inure to the benefit of BeVo Media, and Client agrees to assign, and hereby does assign, all such goodwill to BeVo Media. Client shall not at any time, nor shall Client assist others to, challenge BeVo Media, Inc's right, title, or interest in or to, or the validity of, the BeVo Media Marks. </p>
					
					<p>(d) BeVo Media Copyrights; Copyright Notice. All content and other materials available through the Software , including without limitation any software code, scripts, interfaces, graphics, displays, text, documentation and other components, and the selection, arrangement and organization thereof, are either owned by BeVo Media, LLC or are the property of BeVo Media, LLC's licensors and suppliers. Except as explicitly provided, neither Client's use of the Software or this Agreement grant Client any right, title or interest in or to any such materials. Copyright &copy; 2009 to the present, BeVo Media, LLC. ALL RIGHTS RESERVED. </p>
					
					<p>8. RESTRICTION ON TRANSFER</p>
					
					<p>CLIENT MAY NOT RENT, LEASE, LEND, SUBLICENSE OR TRANSFER THE SOFTWARE, THIS AGREEMENT OR ANY OF THE RIGHTS GRANTED HEREUNDER. ANY ATTEMPTED TRANSFER IN CONTRAVENTION OF THIS PROVISION SHALL BE NULL AND VOID AND OF NO FORCE OR EFFECT. </p>
					
					<p>9. THIRD PARTY CONTENT AND SERVICES. </p>
					
					<p>(a) General. Client acknowledges that the Software may permit access to products, services, websites, advertisements, promotions, recommendations, advice, information, and materials created and provided by networks, advertisers, publishers, content partners, marketing agents, vendors and other third parties ("Third Party Content and Services"). </p>
					
					<p>(b) Disclaimer. Client acknowledges that BeVo Media does not investigate, monitor, represent or endorse the Third Party Content and Services (including any third party websites available through the Software). Furthermore, Client's access to and use of the Third Party Content and Services is at Client's sole discretion and risk, and BeVo Media and its affiliates, partners, suppliers and licensors shall have no liability to Client arising out of or in connection with Client's access to and use of the Third Party Content and Services. BeVo Media hereby disclaims any representation, warranty or guaranty regarding the Third Party Content and Services, whether express, implied or statutory, including, without limitation, the implied warranties of merchantability or fitness for a particular purpose, and any representation, warranty or guaranty regarding the availability, quality, reliability, features, appropriates, accuracy, completeness, or legality of the Third Party Content and Services. </p>
					
					<p>(c) Third Party Terms of Service. Client acknowledges and agrees that Client's access to and use of the Third Party Content and Services and any correspondence or business dealings between Client and any third party located using the Software are governed by and require Client's acceptance of the terms of service of such third party, including, without limitation, any terms, privacy policies, conditions, representations, warranties or disclaimers contained therein. Furthermore, Client acknowledges and agrees that the Third Party Content and Services and any related third party terms of service are subject to change by the applicable third party at its sole discretion and without any notice. Client assumes all risks arising out of or resulting from Client's transaction of business over the Internet and with any third party, and Client agrees that BeVo Media and its affiliates, partners, suppliers and licensors are not responsible or liable for any loss or result of the presence of information about or links to such advertisers or service providers. Furthermore, Client acknowledges and agrees that Client is not being granted a license to: (i) the Third Party Content and Services; (ii) any products, services, processes or technology described in or offered by the Third Party Content and Services; or (iii) any copyright, trademark, patent or other intellectual property right in the Third Party Content or Services or any products, services, processes or technology described or offered therein. </p>
					
					<p>(d) Endorsements. Client acknowledges and agrees that the provision of access to any Third Party Content and Service shall not constitute or imply any endorsement by BeVo Media or its affiliates of such Third Party Content and Services. BeVo Media reserves the right to restrict or deny access to any Third Party Content and Services otherwise accessible through the Software, although BeVo Media has no obligation to restrict or deny access even if requested by Client. </p>
					
					<p>(e) Inappropriate Materials. Client understand that by accessing and using the Third Party Content and Services, Client may encounter information, materials and subject matter: (i) that Client or others may deem offensive, indecent, or objectionable; (ii) which may or may not be identified as having explicit language: and (iii) that automatically and unintentionally appears in search results, as a link or reference to objectionable material. Notwithstanding the foregoing, Client agrees to use the Third Party Content and Services at Client's sole risk and that BeVo Media and its affiliates, partners, suppliers and licensors shall have no liability to Client for information, material or subject matter that is found to be offensive, indecent, or objectionable.</p>
					
					
					<p>10. CONFIDENTIALITY </p>
					<p>(a) Confidential Information. Without limiting any other provision in this Agreement, "Confidential Information" shall mean any proprietary information, technical data, trade secrets or know-how (which shall include, without limitation, the Software, Client Data, research, product plans, products, services, customer lists and customers, markets, software, developments, inventions, processes, formulas, technology, designs, drawings, engineering, hardware configuration information, marketing, finances or other business information) whether disclosed orally or in writing through any media, whether or not designated as confidential, that is known or should reasonably be known by the receiving party to be treated as confidential. Confidential Information does not include information, technical data or know-how which: (i) is known to the receiving party at the time of disclosure to the receiving party by the disclosing party as evidenced by written records of the receiving party; (ii) has become publicly known and made generally available through no wrongful act of the receiving party; or (iii) has been rightfully received by the receiving party from a third party who is authorized to make such disclosure.</p>
					
					<p>(b) Nondisclosure of Confidential Information. The parties hereby agree to maintain the secrecy of the other party's Confidential Information, and to safeguard the other party's Confidential Information with the same degree of care as is exercised in connection with its own proprietary and confidential materials. Each party shall not disclose, use, modify, copy, reproduce or otherwise divulge any Confidential Information of the other party other than as necessary to fulfill the receiving party's obligations under this Agreement. The parties acknowledge that unauthorized disclosure or use of Confidential Information may cause irreparable harm to the disclosing party for which recovery of money damages would be inadequate, and that disclosing party shall therefore be entitled to seek timely injunctive relief to protect its rights under this Agreement, in addition to any and all remedies available at law.</p>
					
					<p>11. NON-COMPETE</p>
					
					<p>During the term of this Agreement, and for two (2) years thereafter, Client shall not, directly or indirectly: (i) create a product, service or software that is, directly or indirectly, competitive with or in any way a substitute for any services, product or software offered by BeVo Media; (ii) plan, organize, or render services for, whether as an employee, consultant, contractor or otherwise, invest in or become a shareholder, member, partner, creditor, officer, director, or owner in any business that BeVo Media reasonably believes competes, directly or indirectly, with any business, activity, product, software or service of BeVo Media. </p>
					
					<p>12. DISCLAIMER OF WARRANTIES</p>
					
					<p>CLIENT ACKNOWLEDGES AND AGREES THAT THE SOFTWARE IS PROVIDED ON AN "AS IS" AND "AS AVAILABLE" BASIS, AND THAT CLIENT'S USE OF OR RELIANCE UPON THE SOFTWARE IS AT CLIENT'S SOLE RISK AND DISCRETION. BEVO MEDIA AND ITS AFFILIATES, PARTNERS, SUPPLIERS AND LICENSORS HEREBY DISCLAIM ANY AND ALL REPRESENTATIONS, WARRANTIES AND GUARANTIES REGARDING THE SOFTWARE, WHETHER EXPRESS, IMPLIED OR STATUTORY, AND INCLUDING, WITHOUT LIMITATION, THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. FURTHERMORE, BEVO MEDIA AND ITS AFFILIATES, PARTNERS, SUPPLIERS AND LICENSORS MAKE NO WARRANTY THAT: (I) THE SOFTWARE WILL MEET CLIENT'S REQUIREMENTS; (II) THE SOFTWARE WILL BE UNINTERRUPTED, ACCURATE, RELIABLE, TIMELY, SECURE OR ERROR-FREE; (III) THE QUALITY OF ANY PRODUCTS, SERVICES, INFORMATION OR OTHER MATERIAL ACCESSED OR OBTAINED BY CLIENT THROUGH THE APPLICATION WILL BE AS REPRESENTED OR MEET CLIENT'S EXPECTATIONS; OR (IV) ANY ERRORS IN THE SOFTWARE WILL BE CORRECTED. WHILE BEVO MEDIA SHALL MAKE COMMERCIALLY REASONABLE EFFORTS TO PROTECT CLIENT DATA, BEVO MEDIA IS NOT RESPONSIBLE FOR ANY LOSS OR DAMAGE TO CLIENT DATA THAT RESULTS FROM CLIENT'S USE OF THE SOFTWARE. NO ADVICE OR INFORMATION, WHETHER ORAL OR WRITTEN, OBTAINED BY CLIENT FROM BEVO MEDIA OR FROM THE SOFTWARE SHALL CREATE ANY REPRESENTATION, WARRANTY OR GUARANTY. FURTHERMORE, CLIENT ACKNOWLEDGES THAT BEVO MEDIA HAS NO OBLIGATION TO CORRECT ANY ERRORS OR OTHERWISE SUPPORT OR MAINTAIN THE APPLICATION. </p>
					
					<p>13. LIMITATION OF LIABILITY</p>
					
					<p>UNDER NO CIRCUMSTANCES SHALL BEVO MEDIA OR ITS AFFILIATES, PARTNERS, SUPPLIERS OR LICENSORS BE LIABLE FOR ANY INDIRECT, INCIDENTAL, CONSEQUENTIAL, SPECIAL OR EXEMPLARY DAMAGES, OR DAMAGES FOR LOST PROFITS OR LOSS OF REVENUE, ARISING OUT OF OR IN CONNECTION WITH CLIENT'S ACCESS OR USE OF OR INABILITY TO ACCESS OR USE THE SOFTWARE, WHETHER OR NOT THE DAMAGES WERE FORESEEABLE AND WHETHER OR NOT BEVO MEDIA WAS ADVISED OF THE POSSIBILITY OF SUCH DAMAGES. WITHOUT LIMITING THE GENERALITY OF THE FOREGOING, BEVO MEDIA'S AGGREGATE LIABILITY TO CLIENT (WHETHER UNDER CONTRACT, TORT, STATUTE OR OTHERWISE) SHALL NOT EXCEED THE AGGREGATE PRICE CLIENT PAID TO BEVO MEDIA DURING THE SIX MONTHS PRECEDING THE INCIDENT OR INCIDENTS GIVING RISE TO SUCH LIABILITY. CLIENT AGREES THAT THIS LIMITATION OF LIABILITY REPRESENTS A REASONABLE ALLOCATION OF RISK AND IS A FUNDAMENTAL ELEMENT OF THE BASIS OF THE BARGAIN BETWEEN BEVO MEDIA AND CLIENT. THE SOFTWARE WOULD NOT BE PROVIDED WITHOUT SUCH LIMITATIONS. THE FOREGOING LIMITATIONS WILL APPLY EVEN IF THE ABOVE STATED REMEDY FAILS OF ITS ESSENTIAL PURPOSE. </p>
					
					<p>14. INDEMNIFICATION</p>
					
					<p>(a) Intellectual Property Indemnity. BeVo Media shall indemnify, defend and hold harmless Client from and against any claims, actions, or demands alleging that the Software infringes any patent, copyright, or other intellectual property right of a third party. If use of the Software is permanently enjoined for any reason, BeVo Media, at BeVo Media's option, and in its sole discretion, may: (i) modify the Software so as to avoid infringement; (ii) procure the right for Client to continue to access and use the Software; or (iii) terminate this Agreement and refund to Client all Fees paid. BeVo Media shall have no obligation under this Section 15(a) for or with respect to claims, actions, or demands alleging infringement that arise as a result of: (a) the combination of non-infringing items supplied by BeVo Media with any items not supplied by BeVo Media; (b) modification of the Software by Client; (c) the direct or contributory infringement of any process patent by Client through the use of the Software; and (d) continued allegedly infringing activity by Client after Client has been notified of the possible infringement.</p>
					
					<p>(b) Client Indemnification. Client shall indemnify, defend and hold harmless BeVo Media and its affiliates, partners, suppliers and licensors, and each of their respective officers, directors, agents and employees from and against any claim, proceeding, loss, damage, fine, penalty, interest and expense (including, without limitation, fees for attorneys and other professional advisors) arising out of or in connection with the following: (i) Client's and/or Client's customer's access to or use of the Software; (ii) Client's breach of this Agreement; (iii) Client's and/or client's customer's violation of law, including without limitation laws designed to regulate unsolicited email or other electronic advertising; (iv) Client's negligence or willful misconduct; or (v) Client's and/or client's customer's violation of the rights of a third party, including the infringement by Client of any intellectual property or misappropriation of any proprietary right or trade secret of any person or entity. These obligations will survive any termination of the Agreement. </p>
					
					<p>(c) Condition to Indemnification. Should any claim subject to indemnity be made against BeVo Media or Client, the party against whom the claim is made agrees to provide the other party with prompt written notice of the claim. BeVo Media will control the defense and settlement of any claim under Section 15(a) and Client will control the defense and settlement of any claim under Section 15(b). The indemnified party agrees to cooperate with the indemnifying party and provide reasonable assistance in the defense and settlement of such claim. The indemnifying party is not responsible for any costs incurred or compromise made by the indemnified party unless the indemnifying party has given prior written consent to the cost or compromise.</p>
					
					<p>15. COMPATIBILITY</p>
					
					<p>BeVo Media does not warrant that the Software will be compatible or interoperable with Client's computer or any other piece of hardware, software, equipment or device installed on or used in connection with Client's computer. Furthermore, Client acknowledges that compatibility and interoperability problems can cause the performance of Client's computer to diminish or fail completely, and may result in permanent damage to Client's computer, loss of the data located on Client's computer, and corruption of the software and files located on Client's computer. CLIENT ACKNOWLEDGES AND AGREES THAT BEVO MEDIA AND ITS AFFILIATES, PARTNERS, SUPPLIERS AND LICENSORS SHALL HAVE NO LIABILITY TO CLIENT FOR ANY LOSSES SUFFERED RESULTING FROM OR ARISING IN CONNECTION WITH COMPATIBILITY OR INTEROPERABILITY PROBLEMS. </p>
					
					<p>16. GOVERNING LAW; JURISDICTION AND VENUE</p>
					
					<p>The Software and this Agreement, including without limitation this Agreement's interpretation, shall be treated as though this Agreement were executed and performed in San Diego, CA and shall be governed by and construed in accordance with the laws of the State of California without regard to its conflict of law principles. ANY CAUSE OF ACTION BY CLIENT ARISING OUT OF OR RELATING TO THE APPLICATION OR THIS AGREEMENT MUST BE INSTITUTED WITHIN ONE (1) YEAR AFTER THE CAUSE OF ACTION AROSE OR BE FOREVER WAIVED AND BARRED. ALL ACTIONS SHALL BE SUBJECT TO THE LIMITATIONS SET FORTH IN ABOVE. </p>
					
					<p>(a) Requirement of Arbitration. Client agrees that any dispute, of any nature whatsoever, between Client and BeVo Media arising out of or relating to the Software or this Agreement, shall be decided by neutral, binding arbitration before a representative of JAMS in San Diego, CA unless Client and BeVo Media mutually agree to a different arbitrator, who shall render an award in accordance with the substantive laws of California and JAMS' Streamlined Arbitration Rules &amp; Procedures. A final judgment or award by the arbitrator may then be duly entered and recorded by the prevailing party in the appropriate court as final judgment. The arbitrator shall award costs (including, without limitation, the JAMS fee and reasonable attorney's fees) to the prevailing party. </p>
					
					<p>(b) Remedies in Aid of Arbitration; Equitable Relief. This agreement to arbitrate will not preclude Client or BeVo Media from seeking provisional remedies in aid of arbitration, including without limitation orders to stay a court action, compel arbitration or confirm an arbitral award, from a court of competent jurisdiction. Furthermore, this agreement to arbitrate will not preclude Client or BeVo Media from applying to a court of competent jurisdiction for a temporary restraining order, preliminary injunction, or other interim or conservatory relief, as necessary. THE PROPER VENUE FOR ANY ACTION PERMITTED UNDER THIS SUBSECTION REGARDING "EQUITABLE RELIEF" WILL BE THE FEDERAL AND STATE COURTS LOCATED IN SAN DIEGO, CALIFORNIA; THE PARTIES HEREBY WAIVE ANY OBJECTION TO THE VENUE AND PERSONAL JURISDICTION OF SUCH COURTS.</p>
					
					<p>17. MISCELLANEOUS</p>
					
					<p>(a) Severability. If any provision of this Agreement is held to be invalid or unenforceable with respect to a party, the remainder of this Agreement, or the application of such provision to persons other than those to whom it is held invalid or unenforceable, shall not be affected, and each remaining provision of this Agreement shall be valid and enforceable to the fullest extent permitted by law. </p>
					
					<p>(b) Waiver. Except as provided herein, the failure to exercise a right or require performance of an obligation under this Agreement shall not affect a party's ability to exercise such right or require such performance at any time thereafter nor shall the waiver of a breach constitute waiver of any subsequent breach. </p>
					
					<p>(c) Export Control. Client may not use or otherwise export or re-export the Software except as authorized by United States law and the laws of the jurisdiction(s) in which the Software was obtained. Client represents and warrants that Client is not (i) located in any country that is subject to a U.S. Government embargo, or that has been designated by the U.S. Government as a "terrorist supporting" country; or (ii) listed on any U.S. U.S. Government list of prohibited or restricted parties including the Treasury Department's list of Specially Designated Nationals or the U.S. Department of Commerce Denied Person's List or Entity List. Client also agrees that Client will not use the Software for any purposes prohibited by United States law.</p> 
					
					<p>(d) Third Party Beneficiaries. Except as provided in Section 12, nothing contained in this Agreement is intended or shall be construed to confer upon any person (other than the parties hereto) any rights, benefits or remedies of any kind or character, or to create any obligations or liabilities of a party to any such person. </p>
					
					<p>(e) Assignment. Client shall not assign this Agreement or any rights or obligations herein without the prior written consent of BeVo Media, and any attempted assignment in contravention of this provision shall be null and void and of no force or effect. BeVo Media may assign this Agreement in connection with a merger, acquisition, or a sale of all or substantially all of its assets related to this Agreement.</p>
					
					<p>(f) Entire Agreement; Amendment. This Agreement, including the documents incorporated herein by reference, constitutes the entire agreement with respect to the use of the Software licensed hereunder and supersedes all prior or contemporaneous understandings regarding such subject matter. Except as specifically provided for herein, this Agreement may not be altered, amended, or modified except by instrument in writing signed by a duly authorized representative of each party. </p>
					
					<p>(g) Headings. The headings contained in this Agreement are inserted as a matter of convenience and for ease of reference only and shall be disregarded for all other purposes, including the construction or enforcement of this Agreement or any of its provisions.</p>
					
					<p>(h) Relationship. The relationship of the parties under this Agreement is one of independent contractors and nothing herein should be construed to constitute the parties as partners, joint ventures, agent and principal or employer and employee. Nothing herein will give either party any right or authority to bind the other, and neither party will bind the other to any obligation to any third party.</p>
					
					<p>(i) Notices. All notices under this Agreement will be in writing and will be delivered by personal service, confirmed fax, confirmed e-mail, express courier, or certified mail, return receipt requested, to BeVo Media at 11622 El Camino Real, Suite 100, San Diego, CA 92130, and to Client at the fax, e-mail or mailing address designated by Client during registration with BeVo Media, or at such different address as may be designated by such party by written notice to the other party from time to time. Notice will be effective upon receipt. </p>
					
					<p>(j) Construction. Each party acknowledges and agrees that it has had the opportunity to seek the advice of independent legal counsel and has read and understood all of the terms and conditions of this Agreement. This Agreement shall not be construed against either Party by reason of its drafting.</p>
					
					<p>(k) Force Majeure. Neither party shall be deemed in default of this Agreement to the extent that performance of its obligations or attempts to cure any breach are delayed or prevented by reason of any act of God, fire, natural disaster, accident, riots, acts of government, shortage of materials or supplies, or any other cause beyond the reasonable control of such party. </p>
					
					<p>(l) Successors. This Agreement will be binding upon and will inure to the benefit of the parties, their successors and permitted assigns. </p>
					
					<p>The Bevo Media Hours of operation are Mon-Fri 10am-7pm PST.</p>
					
				</div><!--close lpop_tos-->
			</div><!--close lpop_cartbox-->
			<div class="lpop_cartbox last">
				<p>By clicking the Pay Now button, you agree to the terms of service.</p>
				<a class="btn btn_lpop_paynow" href="/BevoMedia/User/PaySelfHostedYearly.html">Pay Now</a>
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
