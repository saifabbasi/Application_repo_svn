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
		
		$allnetworks = array();
		$mynetworks = array();
		while ($network = mysql_fetch_object($networksData))
		{
			if(isUserRegisteredOnNetwork($network->id) == 1)
				$mynetworks[] = $network;
			else	$allnetworks[] = $network;
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
					<input type="hidden" id="osearch_include_networks" name="include_networks" value="<?php foreach($mynetworks as $network) { echo $network->id.',';} ?>" />
					
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
					<div class="number j_showolay" id="number_networks" data-olay="olay_networks"><?php echo count($mynetworks); ?></div>
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
			<div class="selebtn j_showolay" href="#" data-olay="olay_savedlists">My Offers (2 Feb 2011)<span class="down"></span></div>
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
					<?php	foreach($allnetworks as $network) {
							echo '<li><a class="j_nwid-'.$network->id.'" href="#" data-hiddenfield="include_networks" data-value="'.$network->id.'" data-number="number_networks">'.$network->title.' ('.$network->id.')</a></li>';
					}
					?>
				</ul>
			</div>
			<div class="olaybox col1 nomargin">
				<div class="olayboxtitle mynetworks"></div>
				<div class="olayboxstripe">
					<a class="btn ovault_smallyell_all j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_mynetworklist" data-action="addall" data-number="number_networks">All</a>
					<a class="btn ovault_smallyell_none j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_mynetworklist" data-action="removeall" data-number="number_networks">None</a>
				</div>
				<ul class="olay_selelist j_olay_mynetworklist">
					<?php	foreach($mynetworks as $network) {
							echo '<li><a class="j_nwid-'.$network->id.' active" href="#" data-hiddenfield="include_networks" data-value="'.$network->id.'" data-number="number_networks">'.$network->title.' ('.$network->id.')</a></li>';
					}
					?>
				</ul>
			</div>
			<div class="clear"></div>
		</div><!--close olaycont-->
	
		<div class="olaytopflag ovault_olay_close"></div>
	</div><!--close #olay_networks-->
	
	<!-- olay_savedlists -->
	<div id="olay_savedlists" class="ovault_olay">
		<div class="olaytop"></div>
		
		<div class="olaycont">
			<a class="btn ovault_olay_close" href="#">Close</a>
			<p>Manage your Offer Lists here.</p>
			
			<p class="small"><span class="icon icon_ovault_savelist_use"></span>Click on an existing list to make it the default when you click <em>Save</em> on any offer</p>
			<p class="small"><span class="icon icon_ovault_savelist_view"></span>View, review, and manage offers that you've added to a list</p>
			<p class="small"><span class="icon icon_ovault_savelist_csv"></span>Quickly download a list with all offers in it as a CSV</p>
			<p class="small"><span class="icon icon_ovault_savelist_delete"></span>Delete a list</p>
			
			<div class="olaybox">
				<a class="btn ovault_yell_createnewlist" href="#">Create New List</a>
				<a class="btn ovault_yell_gotolistpage" href="/BevoMedia/Offers/MySavedLists.html">Go to the List Management Page</a>
			</div><!--close olaybox-->
			<div class="clear"></div>
			
			<div class="olaybox nomarginbutt">
				<div class="olayboxtitle myofferlists">
					<a class="btn ovault_smallyell_deleteall" href="#">Delete All Lists</a>
				</div>
				
				<table cellspacing="0" cellpadding="0" id="ovault_olay_savelists" class="odarktable">
					<thead>
						<tr>
							<td class="no">&nbsp;</td>
							<td class="name">Name</td>
							<td class="use">Use</td>
							<td class="view">View</td>
							<td class="download">Download</td>
							<td class="delete">Delete</td>
						</tr>
					</thead>
					<tbody>
						<tr class="j_list-1000">
							<td class="no">1.</td>
							<td class="name">My Offers<span>Created: Jan 12, 2011 - Last edit: Feb 1, 2011</span></td>
							<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
							<td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>
							<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td>
							<td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td>
						</tr>
						<tr class="j_list-1001 alt">
							<td class="no">2.</td>
							<td class="name">My Offers<span>Created: Jan 12, 2011 - Last edit: Feb 1, 2011</span></td>
							<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
							<td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>
							<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td>
							<td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td>
						</tr>
						<tr class="j_list-1002">
							<td class="no">3.</td>
							<td class="name">My Offers<span>Created: Jan 12, 2011 - Last edit: Feb 1, 2011</span></td>
							<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
							<td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>
							<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td>
							<td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td>
						</tr>
						<tr class="j_list-1003 alt">
							<td class="no">4.</td>
							<td class="name">My Offers<span>Created: Jan 12, 2011 - Last edit: Feb 1, 2011</span></td>
							<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
							<td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>
							<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td>
							<td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td>
						</tr>
						<tr class="j_list-1004">
							<td class="no">5.</td>
							<td class="name">My Offers<span>Created: Jan 12, 2011 - Last edit: Feb 1, 2011</span></td>
							<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
							<td class="view"><a class="btn icon_ovault_savelist_view" href="#">View</a></td>
							<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td>
							<td class="delete"><a class="btn icon_ovault_savelist_delete" href="#">Delete</a></td>
						</tr>
					</tbody>
				</table>				
			</div><!--close olaybox-->
			
			<div class="olayfeat floatright">
				<p>Overall, you have</p>
				<div class="hilite">
					<h3>12</h3>
					<p>Lists</p>
				</div>
				<p>and a total of</p>
				<div class="hilite">
					<h3>8341</h3>
					<p>Saved Offers</p>
				</div>
			</div><!--close olayfeat-->
			<div class="clear"></div>
		</div><!--close olaycont-->
	
		<div class="olaytopflag_big ovault_olay_close">My Offers (2 Feb 2011)</div>
	</div><!--close #olay_savedlists-->
</div><!--close #odial-->
