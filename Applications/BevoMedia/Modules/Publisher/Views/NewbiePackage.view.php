<?php 
include_once PATH.'ShowMovie.include.php';

//get chapter
if(!isset($_GET['chapter']) || !is_numeric($_GET['chapter']) || $_GET['chapter'] == '' || $_GET['chapter'] == 0 || $_GET['chapter'] > 18) { //18 chapters
	$chapter = 1;
} else	$chapter = intval($_GET['chapter']);

//pagination
$pagination = '<div class="pagination aligncenter">
		<span>Chapter:</span>';
		
for($i=1; $i<=18; $i++) {
	if($chapter == $i)
		$pagination .= '<span class="active">'.$i.'</span>';	
	else	$pagination .= '<a class="page" href="?chapter='.$i.'">'.$i.'</a>';
}

if($chapter < 18) {
	$nextpage = $chapter+1;
	$nextpage = '<a class="tbtn big aligncenter" href="?chapter='.$nextpage.'">Next Chapter &raquo;</a>';
}

$pagination .= '</div><div class="clear"></div>';

$content = array();

//videos for each chapter: youtube IDs
$vid = array(
	1 => 'BSIIQnj1e_Y',
	2 => 'BSIIQnj1e_Y',
	3 => 'BSIIQnj1e_Y',
	4 => 'BSIIQnj1e_Y',
	5 => 'BSIIQnj1e_Y',
	6 => 'BSIIQnj1e_Y',
	7 => 'BSIIQnj1e_Y',
	8 => 'BSIIQnj1e_Y',
	9 => 'BSIIQnj1e_Y',
	10 => 'BSIIQnj1e_Y',
	
	11 => 'BSIIQnj1e_Y',
	12 => 'BSIIQnj1e_Y',
	13 => 'BSIIQnj1e_Y',
	14 => 'BSIIQnj1e_Y',
	15 => 'BSIIQnj1e_Y',
	16 => 'BSIIQnj1e_Y',
	17 => 'BSIIQnj1e_Y',
	18 => 'BSIIQnj1e_Y'
); //$vid


################# chapter 1
ob_start(); ?>

	<h3>1. The newbie pep talk and starter advice.</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<ul>
		<li>-Dont buy! http://www.joelannesley.com/blogging/the-top-7-internet-marketing-newbie-mistakes<br />
		(podcast and article about how to break in to internet marketing, what to spend money on and  what not to)</li>
		<li>-Ask for help</li>
		<li>-Use resources<br />
		Become a member of wariorforum.com Post any and every question you have along the way and somebody will answer them.</li>
		<li>-Trial and error<br />
		http://blog.bevomedia.com/2010/03/09/the-difference-between-a-super-affiliate-and-a-newbie/ (ryanbuke.com blog post about the difference between super affiliate and newbie)</li>
	</ul>

<?php 
$content[1] = ob_get_contents();
ob_end_clean();

################# chapter 2
ob_start();
?>
	<h3>2. Basics of aff maketing</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	
	<p>
-Affiliate networks vs Traffic Sources
Affiliate network definition-http://en.wikipedia.org/wiki/Affiliate_network
Traffic source definition-Anyway to drive traffic to an offer/website
Paid Traffic Sources-PPC, PPV, Media Buying 
Organic Traffic Sources-SEO
-CPA
     Cost per action-affiliate is paid per ‘action’ which is often free to the ‘lead’ (i.e. zip submit, email submit)
-CPS
     Cost per sale-affiliate is paid if sale of product is made
-CPM
     Cost per mili-affiliate pays per thousand views of ad. Must have certain amount of ‘leads’ convert per thousand views in order to profit
-CPC
     Cost per click-affiliate pays per click. Must have certain amount of conversions to gain revenue that will be more than the expense of the click (learn about importance of EPC http://econsultancy.com/uk/blog/3836-definition-of-epc )

Good article on paid traffic- http://affbuzz.com/x/am1adbf9
</p>

<?php
$content[2] = ob_get_contents();
ob_end_clean();

################# chapter 3
ob_start();
?>
	<h3>Finding A Niche</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Aarayofsites.com-article on finding a niche http://www.arrayofsites.com/blog/choosing-the-right-affiliate-marketing-niche.htm
-Finding a unique avenue
	Unique Niches http://affbuzz.com/x/st7c96e3
-Going with whats hot (recommended)
	Ask affiliate manager
You tube vid on hot niches http://www.youtube.com/watch?v=NDoQjDLcd5o
	http://www.nichetrends.net/

	</p>
	
<?php
$content[3] = ob_get_contents();
ob_end_clean();

################# chapter 4
ob_start();
?>
	<h3>Finding an offer</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Once you find niche:
*Decide what kind offer you want to run based on niche. Browse offers in the ‘bevo offer hub’ 
Subscribe to ryanbuke.com for ‘Top Offers’ reports throughout the indsutry
-Find from competitors
-Ask affiliate manager
	**topical offers based on season, holiday etc. (eg fathers day offers http://affbuzz.com/x/af2526f3)

	</p>
<?php
$content[4] = ob_get_contents();
ob_end_clean();

################# chapter 5
ob_start();
?>
	<h3>What to look for in an offer</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Payout
-Conversion %
	Advice on Conversion rate Jonathan Volk http://www.jonathanvolk.com/how-to-make-money-online/potentially-double-or-even-triple-your-conversion-rates.html
-EPC
	Jonathan Volk on the importance of EPC http://www.jonathanvolk.com/affiliate-marketing/higher-payout-is-not-always-the-better-offer.html
-How the page looks
Importance of landing pages at seodiva.net
http://www.seodiva.net/2011/02/the-importance-of-landing-pages/

Importance of landing pages by James borzilleri
http://feedfront.com/archives/article003240
	
Advice on how to create good landing pages at searchenginewatch.com http://forums.searchenginewatch.com/showthread.php?threadid=3327

	</p>
<?php
$content[5] = ob_get_contents();
ob_end_clean();

################# chapter 6
ob_start();
?>
	<h3>6. Who to model after</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Find a competitor aka, a spark.
	How to find competitors in SEO by Search Engine Journal
http://www.searchenginejournal.com/how-to-find-seo-competitor-keywords-social-media-backlinks/7827/

	Find competitors keywords in PPC by PPC Hero
http://www.ppchero.com/find-competitors-top-performing-keywords-and-use-them-to-your-advantage/

-Alexa
	*always check competitors Alexa rank
Article on why Aleza Rank is important by TechNascent.com
http://technascent.com/reasons-why-alexa-rank-is-important/
Video on Alexa Rank Toolbar on youtube.com
http://www.youtube.com/watch?v=o6QFGkEaqcE

-Quantcast

-Compete
*Model after competition because they are already successful. If they have a high Alexa rank then they are getting a lot of traffic, etc.
-3rd party tools.
	Research by smartmoneymarketing.com
http://smartmoneymarketing.com/2007/11/29/keyword-research-how-to-find-related-terms-easily/
 
Research tools: Bevo Media PPV Spy-only research tool for PPV
		Bevo Media FREE Keyword List Builder

	</p>
<?php
$content[6] = ob_get_contents();
ob_end_clean();

################# chapter 7
ob_start();
?>
	<h3>Dissecting your models/Notepad</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Record the information you get on a notepad
*Save all information gathered during research

	</p>
<?php
$content[7] = ob_get_contents();
ob_end_clean();

################# chapter 8
ob_start();
?>
	<h3>Choosing your KWs - Generating KW List</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Use notes to generate ideas
	Adwords help on building keyword List
	http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=16928
-Google keyword tool
	https://adwords.google.com/o/Targeting/Explorer?__u=1000000000&__c=1000000000&ideaRequestType=KEYWORD_IDEAS#search.none

-No more than 3 words in a keyword
-No more than 150 keywords

	</p>
<?php
$content[8] = ob_get_contents();
ob_end_clean();

################# chapter 9
ob_start();
?>
	<h3>Organizing KWs</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Tight adgroups!
How to organize keywords by certifiedknowledge.org
	http://certifiedknowledge.org/blog/how-to-organize-keywords-into-ad-groups/
How to organize campaigns by Pay Per Click journal
	http://payperclickjournal.com/organize-ppc-campaigns/04/27/2009/
Things to consider when organizing keywords by searchengineland.com
	http://searchengineland.com/things-to-consider-when-organizing-your-ad-groups-campaigns-42245
-Seperate by core keywords
-No more than 25 in an adgroup
-No repeats

	</p>
<?php
$content[9] = ob_get_contents();
ob_end_clean();

################# chapter 10
ob_start();
?>
	<h3>10. Keyword Match types (some traffic sources only)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Learn the difference between broad, phrase and exact at earnersblogs.com
http://www.earnersblog.com/difference-between-broad-phrase-exact-matching/
Understanding keyword match types by searchengineland.com
http://searchengineland.com/understanding-keyword-match-types-42789
-Broad
-Phrase
-Exact


	</p>
<?php
$content[10] = ob_get_contents();
ob_end_clean();

################# chapter 11
ob_start();
?>
	<h3>Quality Score Explanation (some traffic sources only)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Search
Quality Score defined by Google
http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=10215
Video by Google about quality score on youtube:
http://www.youtube.com/watch?v=qwuUe5kq_O8
10 ways to increase quality score by redflymarketing.com:
http://www.redflymarketing.com/blog/10-ways-to-increase-your-adwords-quality-score-a-mini-case-study/

-Keyword Density
	Definition of keyword density
	http://www.mikes-marketing-tools.com/marketing-tips/keyword-densities.html
-"Full" website, not just a lander
	
-CTR
Video/article about why CTR is important and how to increase it at redflymarketing.com
http://www.redflymarketing.com/adwords-tutorials/adwords-advanced-techniques/how-to-improve-your-click-through-rate-ctr/

-Ad Variation Density
 Ad Variation Defined by Google
http://adwords.google.com/support/aw/bin/answer.py?hl=en&answer=43273

Facebook
How to start you facebook ad campaign by Jonathan Volk
http://www.jonathanvolk.com/facebook-ads/how-to-start-your-facebook-advertising-campaign.html
Facebook Ads 101 Subliminal Pixels
http://subliminalpixels.com/facebook/how-to-set-up-and-track-facebook-ads/
video on facebook advertising:
http://www.youtube.com/watch?v=MDPPoRJlmSU
paidworkhome.com Facebook Advertising:
http://paidworkhome.com/affiliate-marketing-on-facebook.htm
-Demographics
-CTR
-Bid Price

	</p>
<?php
$content[11] = ob_get_contents();
ob_end_clean();

################# chapter 12
ob_start();
?>
	<h3>Chapter 12 MISSING IN DOC</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	No content.
	</p>
<?php
$content[12] = ob_get_contents();
ob_end_clean();

################# chapter 13
ob_start();
?>
	<h3>Page Design</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	Importance of Ad Placement and Landing Page Design by MapleNorth.com
http://www.maplenorth.com/2011/06/06/the-importance-of-good-ad-placement-and-converting-landing-pages/
Basic Web Design Importance 
-Direct Linking vs Landing Page by Click Consultant
http://www.clickconsultants.com/direct-linking-vs-landing-pages-vs-landing-sites
Direct linking vs Landing pages moneymakingdiscussion.com
http://www.moneymakerdiscussion.com/forum/pay-per-view-ppv/12634-direct-linking-vs-landing-page.html
Direct linking vs. landing page whoisandrewwee.com
http://www.whoisandrewwee.com/affiliate-marketing/affiliate-marketing-direct-linking-vs-landing-pages/
-Above the Fold
-Clear Call to Action
Importance of Call to Action from community.micrsoftadvertising.com
http://community.microsoftadvertising.com/blogs/advertiser/archive/2008/05/20/constructing-an-effective-call-to-action-for-your-ad-copy.aspx
25 examples of good call to action buttons by designshack
http://designshack.co.uk/articles/inspiration/25-examples-of-convincing-call-to-action-buttons

	</p>
<?php
$content[13] = ob_get_contents();
ob_end_clean();

################# chapter 14
ob_start();
?>
	<h3>. Ad variations</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	-Text Ads
	Text-based ads, although common in email, have been dominated on the Web by their graphical-based counterparts.
Affiliate marketing is one area where text ads have flourished. However, many mainstream advertisers are only beginning to discover the power of text. Google has caused a buzz with its text advertising options, generating a self-proclaimed "click-through rate 4-5 times higher than industry standard for banner ads."
While lacking some of the advantages of graphical ads, text-based ads have some powerful advantages of their own. They download almost instantly and are not affected by ad blocking software.

-Banners
	Love them or hate them, banner ads are one of the dominant forms of advertising online. Due to the widespread acceptance of the standard 468x60 banner ad size, buyers can easily secure placements at most sites, and publishers can accept ads from most advertisers.
Banner ads were initially judged primarily on the basis of click-through rate (CTR). In the early days of the Web, click-through rates were generally much higher than they are now, perhaps due to the novelty factor. Other causes for the decline in CTR may include technical limitations, the awkward horizontal shape, poor banner design, an excessive percentage of run-of-network buys, and accumulated bad experiences of Web surfers.
"Banners never work" is a common refrain from the anti-banner crowd. Although click-through rates have gone consistently downward, the same can be said of banner ad prices. It is still possible to achieve a click-through rate many times the industry average by combining good placement and design. Combining below-average ad rates and above-average response rates can lead to an acceptable return on investment, just as in any other advertising medium.
	</p>
<?php
$content[14] = ob_get_contents();
ob_end_clean();

################# chapter 15
ob_start();
?>
	<h3>Posting your campaign (PPV,Adwords, Facebook, traditional media buys)</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	No content in doc.
	</p>
<?php
$content[15] = ob_get_contents();
ob_end_clean();

################# chapter 16
ob_start();
?>
	<h3>Optimizing</h3>	
	<center><?php echo ShowMovie($vid[$chapter], 480, 360); ?></center><br />
	<p>
	How Shoemoney optimizes his campaigns
http://www.shoemoney.com/2008/08/31/how-i-optimize-my-ppc-campaigns/
Jonathan Volk on ‘Trimming Fat’
http://www.jonathanvolk.com/internet-marketing/cut-the-fat-some-quick-tips-to-instantly-gain-profit.html
-Finding leads
-Acquiring data
-3x spend
-Even is good

	</p>
<?php
$content[16] = ob_get_contents();
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
			<li><a class="active" href="/BevoMedia/Publisher/NewbiePackage.html">Newbie Package<span></span></a></li>
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