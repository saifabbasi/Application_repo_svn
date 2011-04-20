<div id="odial">
	<?
		function isUserRegisteredOnNetwork($networkID)
		{
			$sql = "SELECT 
						id
					FROM 
						bevomedia_user_aff_network 
					WHERE 
						(bevomedia_user_aff_network.network__id = {$networkID}) AND
						(bevomedia_user_aff_network.user__id = {$_SESSION['User']['ID']})
					";
			$isMemberOfNetwork = mysql_query($sql);
			return (mysql_num_rows($isMemberOfNetwork)==1);
		}
	
		$sql = "SELECT
					*
				FROM
					bevomedia_aff_network
				";
		$networksData = mysql_query($sql);
		
		$networks = array();
		while ($network = mysql_fetch_object($networksData))
		{
			$networks[] = $network;
		}
	?>
	
	<?php /*

	THIS IS THE ENHANCED ODIAL, including country selection and media type options.
	It's already fully CSS'd, but not yet JS'd.
	For now, we are using the SIMPLE .odial .top below this one. It has separate CSS rules.
	Once we implement countries and media type, we can bring this one back and remove the simple one.
	
	################ START enhanced top
	
	<div class="top">
		<div class="label_searchfor"></div>
		
		<div class="main">
			<div class="obox upper">
				<label for="osearch">any offer name or vertical...</label>
				<input type="text" class="formtxt" id="osearch" name="osearch" value="any offer name or vertical.." />
				<a class="btn ocheck ocheck_lead active" href="#" data-key="type" data-value="lead">Lead</a>
				<a class="btn ocheck ocheck_sale" href="#" data-key="type" data-value="sale">Sale</a>
				<div class="clear"></div>
			</div><!--close obox upper-->
			
			<div class="obox mediatype">
				<a class="btn ocheck ocheck_display active" href="#" data-key="media" data-value="display">Display</a>
				<a class="btn ocheck ocheck_search active" href="#" data-key="media" data-value="search">Search</a>
				<a class="btn ocheck ocheck_email" href="#" data-key="media" data-value="email">Email</a>
				<a class="btn ocheck ocheck_other" href="#" data-key="media" data-value="other">Other</a>
				<div class="clear"></div>
			</div><!--close obox mediatype-->
			
			<div class="obox countries">
				<div class="number j_showolay" id="number_countries" data-olay="olay_countries">1</div>
				<div class="preview">
					<ul>
						<li>USA</li>
					</ul>
				</div>
				<div class="drop"></div>
			</div><!--close obox countries-->
			
			<div class="obox include">
				<a class="btn ocheck ocheck_mysaved active" href="#" data-value="mysaved">My Saved Offers</a>
				<div class="number j_showolay" id="number_networks" data-olay="olay_networks">0</div>
			</div><!--close obox include-->
			<div class="clear"></div>
		</div><!--close main-->
		
		<a class="btn odial_go" href="#">Go</a>
		
	</div><!--close top-->
	
	############## END enhanced top
	
	*/ ?>
	
	<div class="top simple">
		<div class="label_searchfor"></div>
		
		<form id="osearchform" method="post" action="">
			<div class="main">
				<div class="obox upper">
					<label for="osearch">any offer name or vertical...</label>
					<input type="text" class="formtxt" id="osearch" name="osearch" value="any offer name or vertical..." />
					
					<input type="hidden" id="osearch_type" name="type" value="lead" />
					<input type="hidden" id="osearch_include_mysaved" name="include_mysaved" value="1" />
					<input type="hidden" id="osearch_include_networks" name="include_networks" value="<?php /* 1000,1002,1004,1006,1008,1010*/?>" />
					
					<div class="clear"></div>
				</div><!--close obox upper-->
				
				<div class="obox convtype">
					<a class="btn ocheck ocheck_lead active" href="#" data-hiddenfield="type" data-value="lead">Lead</a>
					<a class="btn ocheck ocheck_sale" href="#" data-hiddenfield="type" data-value="sale">Sale</a>
					<div class="clear"></div>
				</div><!--close obox mediatype-->
				
				<div class="obox include">
					<a class="btn ocheck ocheck_mysaved active" href="#" data-hiddenfield="include_mysaved" data-value="0">My Saved Offers</a>
					<div class="clear"></div>
				</div><!--close obox countries-->
				
				<div class="obox networks">
					<div class="number j_showolay" id="number_networks" data-olay="olay_networks">0</div>
					<div class="clear"></div>
				</div><!--close obox include-->
				<div class="clear"></div>
			</div><!--close main-->
		
			<!--<a class="btn odial_go" href="#">Go</a>-->
			<input type="submit" class="btn formsubmit odial_go" value="Go" />
		</form>
		
	</div><!--close top.simple-->
	<div class="butt">
		<div class="rows">
			<div class="selebtn" href="#">200<span class="down"></span></div>
			<div class="selebtn hide">
				<a href="#" data-value="25">25</a>
				<a href="#" data-value="50">50</a>
				<a href="#" data-value="100">100</a>
				<a class="active" href="#" data-value="200">200</a>
			</div>
		</div>
		<div class="save">
			<div class="selebtn" href="#">My Offers (2 Feb 2011)<span class="down"></span></div>
			<a class="btn ovault_saveallpage" href="#">Save All Of This Page</a>
		</div>
	</div><!--close butt-->
	
	<!-- olay_networks -->
	<div id="olay_networks" class="ovault_olay simple"><?php /*ODIAL TOP SIMPLE: when we switch to the normal/enhanced dial, also remove the class "simple" from this!*/ ?>
		<div class="olaytop"></div>
		
		<div class="olaycont">
			<a class="btn ovault_olay_close" href="#">Close</a>
			<p>Select Networks you want to include in the search:</p>
			
			<div class="olaybox col2">
				<div class="olayboxtitle allnetworks"></div>
				<div class="olayboxstripe">
					<a class="btn ovault_smallyell_all j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_allnetworkslist" data-action="addall" data-number="number_networks">All</a>
					<a class="btn ovault_smallyell_none j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_allnetworkslist" data-action="removeall" data-number="number_networks">None</a>
				</div>
				<ul class="olay_selelist j_olay_allnetworkslist">
					<?php	//list all networks
					$fakenetworks = array('CPA Staxx','Clickbooth','Convert2Media','Wolf Storm Media','W4','Diablo Media');
					
					for($i=1; $i<=3; $i++) {
						for($j=0; $j<=count($fakenetworks)-1; $j++) {
							echo '<li><a href="#" data-hiddenfield="include_networks" data-value="1000" data-number="number_networks">'.$fakenetworks[$j].'</a></li>';
						}
					} ?>
				</ul>
			</div>
			<div class="olaybox col1 nomargin">
				<div class="olayboxtitle mynetworks"></div>
				<div class="olayboxstripe">
					<a class="btn ovault_smallyell_all j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_mynetworklist" data-action="addall" data-number="number_networks">All</a>
					<a class="btn ovault_smallyell_none j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_mynetworklist" data-action="removeall" data-number="number_networks">None</a>
				</div>
				<ul class="olay_selelist j_olay_mynetworklist">
					<?php for($j=0; $j<=count($fakenetworks)-1; $j++) {
						echo '<li><a class="active" href="#" data-hiddenfield="include_networks" data-value="1000" data-number="number_networks">'.$fakenetworks[$j].'</a></li>';
					} ?>
				</ul>
			</div>
			<div class="clear"></div>
		</div><!--close olaycont-->
	
		<div class="olaytopflag"></div>
	</div><!--close #olay_networks-->
</div><!--close #odial-->
