<style type="text/css">
	table, tr, td {
		border: 1px #ababab solid;
	}
	
	td {
		padding: 5px;
	}
</style>

<?php 
include_once PATH.'ShowMovie.include.php';

//get chapter
if(!isset($_GET['chapter']) || !is_numeric($_GET['chapter']) || $_GET['chapter'] == '' || $_GET['chapter'] == 0 || $_GET['chapter'] > 18) { //18 chapters
	$chapter = 1;
} else	$chapter = intval($_GET['chapter']);

//pagination
$pagination = '<div class="pagination aligncenter">
		<span>Chapter:</span>';
		
for($i=1; $i<=14; $i++) {
	if($chapter == $i)
		$pagination .= '<span class="active">'.$i.'</span>';	
	else	$pagination .= '<a class="page" href="?chapter='.$i.'">'.$i.'</a>';
}

if($chapter < 14) {
	$nextpage = $chapter+1;
	$nextpage = '<a class="tbtn big aligncenter" href="?chapter='.$nextpage.'">Next Chapter &raquo;</a>';
}

$pagination .= '</div><div class="clear"></div>';

$content = array();

//videos for each chapter: youtube IDs
$vid = array(
	1 => 'HnDb3OjjeDA',
	2 => 'NPYyx41i_60',
	3 => 'NDoQjDLcd5o',
	4 => 'omMJ5BlmxBo',
	5 => 'X27-TES7y7k',
	6 => 'jt2srH_2SEg',
	7 => 'XZVvcLxrz8w',
	8 => 'XZVvcLxrz8w',
	
	9 => '0X-6GxOV5Ws',
	10 => 'DQ1Lx61JLc8',
	11 => 'CU8PBW8H_rg',
	12 => 'wyrXl86jkuI',
	13 => 'tWF8364Gh54',
	14 => '',
	15 => 'BSIIQnj1e_Y',
	16 => 'BSIIQnj1e_Y'
); //$vid


################# chapter 1
ob_start(); ?>


	<h3>The newbie pep talk and starter advice.</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>A small pep talk of all aspiring internet marketers need to know before they get started.</td>
		</tr>
		<tr>
			<td>
				<ul>
					<li>-Use resources<br />
						Become a member of popular forums (ex. Warrior Forum, Wicked Fire, Digital Point etc.) Post
						any and every question you have along the way and somebody will answer them.
					</li>
					<li>- Trial and error<br />
					<a target="_blank" href="http://blog.bevomedia.com/2010/03/09/the-difference-between-a-super-affiliate-and-a-newbie/">http://blog.bevomedia.com/2010/03/09/the-difference-between-a-super-affiliate-and-a-newbie/</a> 
					<br />
					(ryanbuke.com blog post about the difference between super affiliate and newbie)
					</li>
				</ul>
			</td>
		</tr>
	</table>
	
	
	

<?php 
$content[1] = ob_get_contents();
ob_end_clean();

################# chapter 2
ob_start();
?>
	<h3>Basics of affiliate marketing</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>The basic terms and concepts of affiliate marketing:</td>
		</tr>
		<tr>
			<td>
				<p>
					-Affiliate networks vs Traffic Sources<br />
					Title: Affiliate Network<br /><br />
					
					http://en.wikipedia.org/wiki/Affiliate_network
				</p>
				
				<p>
					<b>Traffic source definition-Anyway to drive traffic to an offer/website</b>
				</p>
				
				<p>
					<b>Paid Traffic Sources-PPC, PPV, Media Buying</b>
				</p>
				
				<p>
					<b>Organic Traffic Sources-SEO</b>
				</p>
				
				<ul>
					<li>
						-CPA<br />
						Cost per action-affiliate is paid per `action` which is often free to the `lead` (i.e. zip submit, email submit)
					</li>
					<li>
						-CPS<br />
						Cost per sale-affiliate is paid if sale of product is made
					</li>
					<li>
						-CPM<br />
						Cost per mili-affiliate pays per thousand views of ad. Must have certain amount of `leads` convert per thousand views in order to profit
					</li>
					<li>
						-CPC<br />
						Cost per click-affiliate pays per click. Must have certain amount of conversions to gain revenue that will be more than the expense of the click
					</li>
					<li>
						-EPC<br />
						Earnings per click		
					</li>
				</ul>
				
				<p>
					Title: Importance of EPC<br />
					<a target="_blank" href="http://econsultancy.com/uk/blog/3836-definition-of-epc">http://econsultancy.com/uk/blog/3836-definition-of-epc</a> 
				</p>
				
				<p>
					Title: Paid Traffic<br />
					<a target="_blank" href="http://affbuzz.com/x/am1adbf9">http://affbuzz.com/x/am1adbf9</a>
				</p>
				
			</td>
		</tr>
	</table>
	<div></div>
	<br /><br />
	
	

<?php
$content[2] = ob_get_contents();
ob_end_clean();

################# chapter 3
ob_start();
?>
	<h3>Finding A Niche</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>How to find a Niche to work with</td>
		</tr>
		<tr>
			<td>
				<p>
					<b>Supporting Links:</b><br />
					<a target="_blank" href="http://www.arrayofsites.com/blog/choosing-the-right-affiliate-marketing-niche.htm">Choosing the Right Niche</a>
				</p>
				
				<p>
					<a target="_blank" href="http://affbuzz.com/x/st7c96e3">Unique Niches</a>
				</p>
				
				<p>
					<a target="_blank" href="http://www.nichetrends.net/">Hot Niches</a>
					<a target="_blank" href="http://www.youtube.com/watch?v=NDoQjDLcd5o">http://www.youtube.com/watch?v=NDoQjDLcd5o</a>
				</p>
			</td>
		</tr>
	</table>
		
	
<?php
$content[3] = ob_get_contents();
ob_end_clean();

################# chapter 4
ob_start();
?>
	<h3>What to look for in an offer</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>
				<ul>
					<p>
						Once you find niche:<br />
						*Decide what kind offer you want to run based on niche. Browse offers in the `bevo offer hub`
						Subscribe to ryanbuke.com for `Top Offers` reports throughout the industry
					</p>
					
					<p>
						Supporting Links:<br />
						-Find from competitors
					</p>
					
					<p>
						Topical Offers<br />
						**topical offers based on season, holiday etc. (eg fathers day offers <a target="_blank" href="http://affbuzz.com/x/af2526f3">http://affbuzz.com/x/af2526f3</a>)
					</p>
					
					<li>-Payout</li>
					<li>-Conversion %</li>
					<li>-EPC</li>
					<li>-How the page looks</li>
				</ul>			
			</td>
		</tr>
		<tr>
			<td>
				<p>		
					<a target="_blank" href="http://www.jonathanvolk.com/how-to-make-money-online/potentially-double-or-even-triple-your-
			conversion-rates.html">Advice on Conversion Rate</a>
					<br />
					
					<a target="_blank" href="http://www.jonathanvolk.com/affiliate-marketing/higher-payout-is-not-always-the-better-offer.html">Importance of EPC</a>
					<br />
					
					<a target="_blank" href="http://www.seodiva.net/2011/02/the-importance-of-landing-pages/">Importance of landing pages</a>
					<br />
					
					<a target="_blank" href="http://feedfront.com/archives/article003240">Importance of landing pages in Online Marketing</a>
					<br />
					
					<a target="_blank" href="http://forums.searchenginewatch.com/showthread.php?threadid=3327">How to Create Good Landing Pages</a>
					<br />
				</p>
			</td>
		</tr>
	</table>

	
<?php
$content[4] = ob_get_contents();
ob_end_clean();

################# chapter 5
ob_start();
?>
	<h3>Choosing your Keywords - Generating KW List</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>
				Use your knowledge and resources to build a keyword list.
			</td>
		</tr>	
		
		<tr>
			<td>
				<b>Supporting Links:</b>
					<br />
					
					<a target="_blank" href="http://www.youtube.com/watch?v=J2CxvbLZpmY">Generating Keyword List for SEO</a>
					<br />
					
					<a target="_blank" href="http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=16928">Adwords on building keyword List</a>
					<br />
					
					<a target="_blank" href="https://adwords.google.com/o/Targeting/Explorer?__u=1000000000&__c=1000000000&ideaRequestType=KEYWORD_IDEAS#search.none">Google keyword tool</a>
					<br />
					
			</td>
		</tr>	
	</table>
	
	
	
	
<?php
$content[5] = ob_get_contents();
ob_end_clean();

################# chapter 6
ob_start();
?>
	<h3>Organizing KWs</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>
				<a target="_blank" href="http://certifiedknowledge.org/blog/how-to-organize-keywords-into-ad-groups/">How to organize Keywords:</a>
				<br />
				
				<a target="_blank" href="http://payperclickjournal.com/organize-ppc-campaigns/04/27/2009/">How to organize Campaigns</a>
				<br />
				
				<a target="_blank" href="http://searchengineland.com/things-to-consider-when-organizing-your-ad-groups-campaigns-42245">Things to consider when organizing Keywords</a>
				<br />
			</td>
		</tr>
	</table>
	
	
	
<?php
$content[6] = ob_get_contents();
ob_end_clean();

################# chapter 7
ob_start();
?>
	<h3>Keyword Match types (some traffic sources only)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>
				Find out the difference between the keyword match types and how it can affect your campaigns.
			</td>
		</tr>
		<tr>
			<td>
				<p>
					Supporting Links:
				</p>
				
				<a target="_blank" href="http://www.earnersblog.com/difference-between-broad-phrase-exact-matching/">Difference Between Broad, Phrase and Exact</a>
				<br />
				
				<a target="_blank" href="http://searchengineland.com/understanding-keyword-match-types-42789">Understanding keyword match types</a>
				<br />
				
			</td>
		</tr>
	</table>
	
	
<?php
$content[7] = ob_get_contents();
ob_end_clean();

################# chapter 8
ob_start();
?>
	<h3>Keyword Match types (some traffic sources only)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>
				Learn the difference between broad, phrase and exact at earnersblogs.com
			</td>
		</tr>
		<tr>
			<td>				
				<a target="_blank" href="http://www.earnersblog.com/difference-between-broad-phrase-exact-matching/">Learn the difference between broad, phrase and exact</a>
				<br />
				
				<a target="_blank" href="http://searchengineland.com/understanding-keyword-match-types-42789">Understanding keyword match types</a>	
				<br />
				<br />
				
				<p>
					-Broad<br />
					-Phrase<br />
					-Exact<br />
				</p>
			</td>
		</tr>
	</table>
	
		
<?php
$content[8] = ob_get_contents();
ob_end_clean();

################# chapter 9
ob_start();
?>
	<h3>Quality Score Explanation (some traffic sources only)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>Learn about the quality score and how to use it to your advantage.</td>
		</tr>
		<tr>
			<td>
				<p>
					Supporting links:
				</p>
				
				<a target="_blank" href="http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=10215">Quality Score defined by Google</a>
				<br />
				
				<a target="_blank" href="http://www.youtube.com/watch?v=qwuUe5kq_O8">Video by Google about quality score on youtube</a>
				<br />
				
				<a target="_blank" href="http://www.redflymarketing.com/blog/10-ways-to-increase-your-adwords-quality-score-a-mini-case-study/">10 ways to increase quality score by redflymarketing.com</a>
				<br />
				
				<a target="_blank" href="http://www.mikes-marketing-tools.com/marketing-tips/keyword-densities.html">Definition of keyword density</a>
				<br />
				
				<a target="_blank" href="http://www.redflymarketing.com/adwords-tutorials/adwords-advanced-techniques/how-to-improve-your-click-through-rate-ctr/">Why CTR is important</a>
				<br />
				
				<a target="_blank" href="http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=43273">Ad Variation Defined by Google</a>
				<br />
				
			</td>
		</tr>
	</table>
	
	
	
	
<?php
$content[9] = ob_get_contents();
ob_end_clean();

################# chapter 10
ob_start();
?>
	<h3>Facebook</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table border="1" width="100%">
		<tr>
			<td>
				Learn how to get your campaigns live on Facebook.
			</td>
		</tr>
		<tr>
			<td>
				<a target="_blank" href="http://www.youtube.com/watch?v=4A227Cr_Iwc&feature=related">Facebook Ads by Facebook</a>
				<br />
				
				<a target="_blank" href="http://www.jonathanvolk.com/facebook-ads/how-to-start-your-facebook-advertising-campaign.html">How to start you facebook ad campaign</a>
				<br />
				
				<a target="_blank" href="http://subliminalpixels.com/facebook/how-to-set-up-and-track-facebook-ads/">Facebook Ads 101</a>
				<br />
				
				<a target="_blank" href="http://www.youtube.com/watch?v=MDPPoRJlmSU">Facebook Affiliate Marketing</a>
				<br />
				
				<a target="_blank" href="http://paidworkhome.com/affiliate-marketing-on-facebook.htm">Facebook Advertising</a>
				<br />
			</td>
		</tr>
	</table>
	
	
	
	
	
<?php
$content[10] = ob_get_contents();
ob_end_clean();

################# chapter 11
ob_start();
?>
	<h3>Page Design</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>Learn the basics of a quality page design.</td>
		</tr>
		<tr>
			<td>
				<p>
					Supporting Links:
				</p>
				
				<a target="_blank" href="http://www.maplenorth.com/2011/06/06/the-importance-of-good-ad-placement-and-converting-landing-pages/">Importance of Ad Placement and Landing Page Design</a>
				<br />
				
				<a target="_blank" href="http://www.clickconsultants.com/direct-linking-vs-landing-pages-vs-landing-sites">Direct Linking vs Landing Page</a>
				<br />
				
				<a target="_blank" href="http://www.moneymakerdiscussion.com/forum/pay-per-view-ppv/12634-direct-linking-vs-landing-page.html">Is it Better to Direct Link or Use a Landing Page?</a>
				<br />
				
				<a target="_blank" href="http://www.whoisandrewwee.com/affiliate-marketing/affiliate-marketing-direct-linking-vs-landing-pages/">Direct linking vs. landing page</a>
				<br />
				
				<a target="_blank" href="http://community.microsoftadvertising.com/blogs/advertiser/archive/2008/05/20/constructing-an-effective-call-to-action-for-your-ad-copy.aspx">Importance of Call to Action</a>
				<br />
				
				<a target="_blank" href="http://designshack.co.uk/articles/inspiration/25-examples-of-convincing-call-to-action-buttons">25 examples of good call to action buttons by designshack</a>
				<br />
			</td>
		</tr>
	</table>
	
	
	
	
<?php
$content[11] = ob_get_contents();
ob_end_clean();

################# chapter 12
ob_start();
?>
	<h3>14. Ad variations</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>Learn to write quality ad variations that get conversions.</td>
		</tr>
	</table>
		
<?php
$content[12] = ob_get_contents();
ob_end_clean();

################# chapter 13
ob_start();
?>
	<h3>Managing/Scaling</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>Learn how to manage your campaigns and boost your profts.</td>
		</tr>
		<tr>
			<td>
				<p>
					Supporting Links
				</p>
				
				<a target="_blank" href="http://uberaffiliate.com/affiliate-tips/how-to-scale-a-campaign/">Scaling a campaign</a>
				<br />
				
				<a target="_blank" href="http://www.warriorforum.com/ad-networks-cpa-cpm-cpl-millionaire-makers/191946-how-properly-scale-up-your-profitable-campaign.html">How to properly scale a profitable campaign</a>
				<br />
				
				<a target="_blank" href="http://www.paulymath.com/2008/12/03/the-art-of-scaling-a-campaign/">The art of scaling a campaign, paulmath.com</a>
				<br />
			</td>
		</tr>
	</table>
	
	
	
<?php
$content[13] = ob_get_contents();
ob_end_clean();

################# chapter 14
ob_start();
?>
	<h3>Your First hit</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<table width="100%" border="1">
		<tr>
			<td>Once you have your campaign profitable, learn to be smart to keep the profits coming.</td>
		</tr>
		<tr>
			<td>
				<p>
					Supporting Links:
				</p>
				
				<p>
					<b>Additional Resources:</b>
				</p>
				
				<a target="_blank" href="http://htmldog.com/guides/htmlbeginner/">HTML for beginners</a>
				<br />
				
				<a target="_blank" href="http://www.youtube.com/watch?v=GwQMnpUsj8I">HTML and CSS for beginners/how to make your own website</a>
				<br />
				
				<a target="_blank" href="http://www.youtube.com/watch?v=Q6rRLw5ggOg">Dreamweaver tutorial</a>
				<br />
			</td>
		</tr>
	</table>
	
	
	
	
	
	
<?php
$content[14] = ob_get_contents();
ob_end_clean();

################# chapter 17
ob_start();
?>
	<h3>Managing/Scaling</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Scaling a campaign-uberaffiliate.com
http://uberaffiliate.com/affiliate-tips/how-to-scale-a-campaign/
Warrior Forum Post, how to properly scale a profitable campaign
http://www.warriorforum.com/ad-networks-cpa-cpm-cpl-millionaire-makers/191946-how-properly-scale-up-your-profitable-campaign.html
The art of scaling a campaign, paulmath.com
http://www.paulymath.com/2008/12/03/the-art-of-scaling-a-campaign/

-Expand the traffic source
-Other traffic sources
-Different Countries
-Different Langages

	</p>
<?php
$content[17] = ob_get_contents();
ob_end_clean();

################# chapter 18
ob_start();
?>
	<h3>. Your First hit</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Finding funds
-KEEP SWINGING!
-Dont take it for granted
-Conserve future $

Additional Resources:
HTML for beginners
http://htmldog.com/guides/htmlbeginner/

HTML and CSS for beginners/how to make your own website
http://www.youtube.com/watch?v=GwQMnpUsj8I

Dreamweaver tutorial:
http://www.youtube.com/watch?v=Q6rRLw5ggOg

	</p>
<?php
$content[18] = ob_get_contents();
ob_end_clean();

?>

<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a href="/BevoMedia/Publisher/Classroom.html">Classroom<span></span></a></li>
			<li><a class="active" href="/BevoMedia/Publisher/OvernightAffiliate.html">Overnight Affiliate<span></span></a></li>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent pub_newbpack">
	<?php echo $pagination; ?>
	
	<h2>Chapter <?php echo $chapter; ?></h2>
	
	<?php	echo $content[$chapter];
		echo isset($nextpage) ? $nextpage : '';
		//echo $pagination;
	?>
	
	<a href="<?=$this->{'System/BaseURL'};?><?=$this->{'Application/Theme'};?>/Marketplace/MentorshipProgram.html">
		<img style="display:block; margin:30px auto 0;" src="/Themes/BevoMedia/img/mentorshipprogram_banner.jpg" />
	</a>
</div>