<?php /* ##################################################### OUTPUT ############### */ ?>
	<?php echo SoapPageMenu('tut','ppc');
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<div class="pagecontent">
	<h3 id="AddingAffNetworks">Adding your Affiliate Networks</h3>
	<?php echo ShowMovie('1kbq1rqgO1I'); ?>	
	<br /><br /><br /><br />
	
	<h3 id="AddingPPCNetworks">Adding your PPC Networks</h3>
	<?php echo ShowMovie('3xUdJvXMMqY'); ?>
	<br /><br /><br /><br />
	
	<h3 id="CampaignEditorTutorial">Campaign Editor Tutorial</h3>
	<?php echo ShowMovie('gCfilEG9h6c'); ?>
	<br /><br /><br /><br />
	
	<h3 id="BevoEditorHowLong">I have uploaded a new campaign from the Bevo Editor. How long does it take for my campaign to appear in my actual PPC Account?</h3>
	<p>Campaigns may take up to 15-20 minutes to appear in your PPC Account. You may view your progress of a recently upload campaign on the campaign queue status page. Please note that you may navigate as you wish throughout the site, and even close your browser during this upload time.</p>
	
	<h3 id="APIFeesExplained">API Fees Explained</h3>
	<p>Each user is assigned a unique Bevo API token to sync their account statistics. API calls are used for all networks, analytics and PPC accounts. In some cases, Bevo is charged by providers for API Calls within their system. Because of this, we have put a cap for users who are on the non-premium version. However, the premium version of Bevo has unlimited API calls as access to the premium features come with a user fee.</p>
	
	<p>When a non-premium user runs out of API calls, their network accounts, PPC accounts and analytics accounts will stop syncing with the Bevo interface. Each month, Standard Bevo users are assigned 10,000 Bevo API calls. Premium Bevo users  have unlimited API calls assigned to their Bevo API key.</p>
	
	<h3>The API Calls  breakdown is as follows:</h3>
	<ul>
		<li><strong>Affiliate Networks:</strong> 2 Calls per network, per hourly update.</li>
		<li><strong>Pay Per Click Accounts:</strong> Varies based on and campaign size. Calls used for writing campaigns as well using the Bevo Editor.</li>
		<li><strong>Analytic Accounts:</strong> 500 Calls per account, per day.</li>
	</ul>
</div><!--close pagecontent-->
