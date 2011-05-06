<?	global $ovaultSavelist;

	if(!isset($hideOdialExtras))
		$hideOdialExtras = false;
		
	//networks
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
		WHERE	model = 'CPA'
			AND isValid = 'Y'
		ORDER BY title
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
		
	//current savelist
	if($ovaultSavelist['current'] == 'new') {
		$ovaultSavelist['currentname'] = 'New List';
	
	} else {
		$ovaultSavelist['currentname'] = $this->PageHelper->TruncTxt($ovaultSavelist['lists'][$ovaultSavelist['current']]->name);
	}
	
	//build savelist tables
	$ovaultSavelist['dialtable'] = '';
	$ovaultSavelist['selecttable'] = '';	
	
	if(isset($ovaultSavelist['lists']) && is_array($ovaultSavelist['lists']) && !empty($ovaultSavelist['lists'])) {
		$listcount = 0;
		$offerz = 0;
		
		foreach($ovaultSavelist['lists'] as $list) {
			
			$listcount++;
			
			//format date
			if(date('Y') == date('Y', strtotime($list->created)))
				$listdate = 'M j';
			else	$listdate = 'M j, Y';
			
			//re-used values
			$truncname = $this->PageHelper->TruncTxt($list->name,27);
			$nicedate = date($listdate, strtotime($list->created));
			
			//build output
			$ovaultSavelist['dialtable'] .= '<tr class="j_list-'.$list->id;
			$ovaultSavelist['dialtable'] .= $listcount % 2 ? '' : ' alt';
			$ovaultSavelist['dialtable'] .= '" data-listid="'.$list->id.'" data-listname="'.$list->name.'">
				<td class="no">'.$listcount.'.</td>
				<td class="name">'.$truncname.'<span>Created: '.$nicedate; 
			//$ovaultSavelist['dialtable'] .= strtotime($list->updated) ? ' - Last edit: '.date($listdate, strtotime($list->created)) : ''; //scratch for now
			$ovaultSavelist['dialtable'] .= '</span></td>
				<td class="use" title="Make this list the default"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
				<td class="view" title="View offers saved in this list" data-listid="'.$list->id.'" data-listname="'.$list->name.'">
					<a class="btn icon_ovault_savelist_view" href="#" data-listid="'.$list->id.'" data-listname="'.$list->name.'">View</a>
				</td>'; 
				//<td class="download"><a class="btn icon_ovault_savelist_csv" href="#">CSV</a></td> //too big of an overhead to enable csv download on this page. scratched
			$ovaultSavelist['dialtable'] .= '<td class="delete" data-listid="'.$list->id.'" data-listname="'.$list->name.'" title="Delete this list">
					<a class="btn icon_ovault_savelist_delete" href="#" data-listid="'.$list->id.'" data-listname="'.$list->name.'">Delete</a>
				</td>
			</tr>';
			
			//build select olay output: simpler list (list gets updated by js on list create/delete)
			$ovaultSavelist['selecttable'] .= '<tr title="Make this list the default" class="j_list-'.$list->id;
			$ovaultSavelist['selecttable'] .= $listcount % 2 ? '' : ' alt';
			$ovaultSavelist['selecttable'] .= '" data-listid="'.$list->id.'" data-listname="'.$list->name.'">
				<td class="no">'.$listcount.'.</td>
				<td class="name">'.$truncname;
			//$ovaultSavelist['selecttable'] .= '<span>Created: '.$nicedate.'</span>'; //scrap this - keep list simple
			$ovaultSavelist['selecttable'] .= '</td>
				<td class="use"><a class="btn icon_ovault_savelist_use" href="#">Use</a></td>
			</tr>';
			
			//count offers in all lists
			$offerz = $list->num_offers ? $offerz + $list->num_offers : $offerz;
			
		} //endforeach lists
		
		$ovaultSavelist['stats'] = array('lists'=>$listcount, 'offers'=>$offerz);
			
	}//endif isset lists
	
?>

<div id="odial">	
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
					<input type="hidden" id="osearch_numresults" name="numresults" value="100" />
					<input type="hidden" id="osearch_page" name="page" value="1" />
					
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
					<div class="number j_expand" id="number_networks" data-target="olay_networks" data-closeclass="ovault_olay"><?php echo count($mynetworks); ?></div>
					<div class="clear"></div>
				</div><!--close obox include-->
				<div class="clear"></div>
			</div><!--close main-->
		
			<!--<a class="btn odial_go" href="#">Go</a>-->
			<input type="submit" class="btn formsubmit odial_go" value="Go" />
		</form>
		
	</div><!--close top.simple-->
	
	<?php if(!$hideOdialExtras) : ?>
	
		<div class="butt">
			<div class="rows" id="numresults_sele">
				<div class="selebtn showolay_simplenext" href="#">100<span class="down"></span></div>
				<div class="selebtn hide olaysimplenext">
					<a class="numresults-25" href="#" data-value="25" data-hiddenfield="numresults">25</a>
					<a class="numresults-50" href="#" data-value="50" data-hiddenfield="numresults">50</a>
					<a class="numresults-100 active" href="#" data-value="100" data-hiddenfield="numresults">100</a>
					<a class="numresults-200" href="#" data-value="200" data-hiddenfield="numresults">200</a>
				</div>
			</div>
			<div class="save">
				<div class="selebtn j_expand" data-target="olay_savedlists" data-closeclass="ovault_olay"><?php echo $ovaultSavelist['currentname']; ?><span class="down"></span></div>
				<?php /*
				
				LATERRRRRRRRRRRRRR
				
				<a class="btn ovault_saveallpage" href="#">Save All Of This Page</a>
				
				*/ ?>
			</div>
		</div><!--close butt-->
	
	<?php endif; //hideodialextras
	?>		
	
	<?php  /*
		*
		*
		* OLAYS
		*
		*
		*/ ?>
	
	<!-- olay_networks -->
	<div id="olay_networks" class="ovault_olay simple"><?php /*ODIAL TOP SIMPLE: when we switch to the normal/enhanced dial, also remove the class "simple" from this!*/ ?>
		<div class="olaytop"></div>
		
		<div class="olaycont">
			<a class="btn ovault_olay_close j_close" href="#" data-target="olay_networks">Close</a>
			<p>Select Networks you want to include in the search:</p>
			
			<div class="olaybox col3">
				<div class="olayboxtitle allnetworks"></div>
				<div class="olayboxstripe">
					<a class="btn ovault_smallyell_all j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_allnetworkslist" data-action="addall" data-number="number_networks">All</a>
					<a class="btn ovault_smallyell_none j_olay_selelist" href="#" data-hiddenfield="include_networks" data-ul="j_olay_allnetworkslist" data-action="removeall" data-number="number_networks">None</a>
				</div>
				<ul class="olay_selelist j_olay_allnetworkslist">
					<?php	foreach($allnetworks as $network) {
							echo '<li><a class="j_nwid-'.$network->id.'" href="#" data-hiddenfield="include_networks" data-value="'.$network->id.'" data-number="number_networks">'.$network->title.'</a></li>';
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
							echo '<li><a class="j_nwid-'.$network->id.' active" href="#" data-hiddenfield="include_networks" data-value="'.$network->id.'" data-number="number_networks">'.$network->title.'</a></li>';
					}
					?>
				</ul>
			</div>
			<div class="clear"></div>
		</div><!--close olaycont-->
	
		<div class="olaytopflag j_close" data-target="olay_networks"></div>
	</div><!--close #olay_networks-->
	
	<?php if(!$hideOdialExtras) : ?>
	
	<!-- olay_savedlists -->
	<div id="olay_savedlists" class="ovault_olay">
		<div class="olaytop"></div>
		
		<div class="olaycont">
			<a class="btn ovault_olay_close j_close" data-target="olay_savedlists" href="#">Close</a>
			<p>Manage your Offer Lists here.</p>
			
			<div class="j_olay_savedlists_havelists<?php if($ovaultSavelist['dialtable'] == '') echo ' hide'; ?>">
				<p class="small"><span class="icon icon_ovault_savelist_use"></span>Click on an existing list to make it the default when you click <em>Save</em> on any offer</p>
				<p class="small"><span class="icon icon_ovault_savelist_view"></span>View, review, and manage offers that you've added to a list</p>
				<p class="small"><span class="icon icon_ovault_savelist_csv"></span>Quickly download a list with all offers in it as a CSV</p>
				<p class="small"><span class="icon icon_ovault_savelist_delete"></span>Delete a list</p>
			</div>
			<div class="j_olay_savedlists_nolists<?php if($ovaultSavelist['dialtable'] != '') echo ' hide'; ?>">				
				<p class="small">Welcome to the Bevo Offer Lists!</p>												
				<p class="small"><strong>Offer Lists</strong> make it easy for you to save offers you are considering for a campaign. Use the yellow button to the left of any offer to add that offer to your lists. You can add as many offers as you like to any of your lists.</p>				
				<p class="small"><strong>Your Default List</strong> is the list you want to quicksave an offer to. This way, you don't have to select a list first, but you just click the button once and it's saved. Once you have created one or more lists, you will be able to set your Default List in this menu by clicking the "Use" button.</p>				
				<p class="small">Lists you create are always private. No one will ever be able to see how many lists you have or which offers you have saved.</p>				
				<p class="small"><a href="#" class="j_expand" data-target="ovault_createnewlistform">Create your first Offer List now</a></p>
			</div>	
			
			<div class="floatleft">
				<div class="olaybox">
					<a class="btn ovault_yell_createnewlist j_expand" href="#" data-target="ovault_createnewlistform">Create New List</a>
					<a class="btn ovault_yell_gotolistpage" href="/BevoMedia/Offers/MySavedLists.html">Go to the List Management Page</a>
					
					<form method="post" action="" id="ovault_createnewlistform" class="hide">
						<div class="row">
							<label class="hide">Enter a name for your new list...</label>
							<input type="text" class="formtxt ovault_newlistname" id="ovault_newlistname" name="newlistname" value="Enter a name for your new list..." />
							<input type="submit" class="btn formsubmit ovault_savenewlist" value="Save" />
						</div>
						<a class="btn ovault_olay_close_gray j_close" href="#" data-target="ovault_createnewlistform">Close</a>
					</form>
				</div><!--close olaybox-->
				
				<?php if($ovaultSavelist['dialtable'] != '') : ?>
					<div class="olaybox nomarginbutt j_olisttable">
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
									<?php /*<td class="download">Download</td> //scratched */ ?>
									<td class="delete">Delete</td>
								</tr>
							</thead>
							<tbody><?php echo $ovaultSavelist['dialtable']; ?></tbody>
						</table>				
					</div><!--close olaybox-->
				<?php endif; ?>
				
			</div><!--close floatleft-->
			<div class="floatright">
				<?php if($ovaultSavelist['dialtable'] != '' 
					&& is_array($ovaultSavelist['stats']) 
					&& !empty($ovaultSavelist['stats'])) { ?>
						
						<div class="olayfeat floatright j_oliststats">
							<p>Overall, you have</p>
							<div class="hilite">
								<h3 class="j_savelists_listnum"><?php echo $ovaultSavelist['stats']['lists'] ?></h3>
								<p>List<?php if($ovaultSavelist['stats']['lists'] != 1) echo 's'; ?></p>
							</div>
							<div class="j_updateOnListDelete">
								<p>and a total of</p>
								<div class="hilite">
									<h3><?php echo $ovaultSavelist['stats']['offers']; ?></h3>
									<p>Saved Offer<?php if($ovaultSavelist['stats']['offers'] != 1) echo 's'; ?></p>
								</div>
							</div>
						</div><!--close olayfeat-->
					
					<?php } //endif stats
					?>
			</div><!--close floatright-->			
			<div class="clear"></div>
		</div><!--close olaycont-->
	
		<div class="olaytopflag_big j_close" data-target="olay_savedlists"><?php echo $ovaultSavelist['currentname']; ?></div>
	</div><!--close #olay_savedlists-->
	
	<!-- #opagi -->
	<div id="opagi">
		<div class="numbers"></div>		
		<div class="totalresults hide"></div>
	</div><!--close #opagi-->
	
	<?php endif; //hideodialextras
	?>
	
</div><!--close #odial-->

<?php if(!$hideOdialExtras) : ?>

<div id="opagi_bg">
	<div class="numbers"></div>
	<div class="totalresults"></div>
</div>

<?php endif; //hideodialextras
?>

<div id="j_olay_savedlists_select_wrap" class="hide">
	<div id="olay_savedlists_select" class="ovault_olay hide">
		<div class="olaytop"></div>
		<div class="olaycont">
			<a class="btn ovault_olay_close j_close j_orowSelect" href="#" data-target="olay_savedlists_select" title="Cancel"></a>
			<p class="small" title=" ">Select an existing list<br />
			to add this offer to:</p>
			
			<?php /* LATERRRRRRRRRRRRR allow to create new lists from this place as well!
			<div class="olaybox">
				<a class="btn ovault_yell_createnewlist j_expand" href="#" data-target="ovault_createnewlistform">Create New List</a>
			</div>
			*/?>
			<div class="olaybox nomarginbutt j_olisttable">
				<div class="olayboxtitle myofferlists" title=""></div>
				<table cellspacing="0" cellpadding="0" id="ovault_olay_savelists_select" class="odarktable">
					<thead>
						<tr>
							<td class="no">&nbsp;</td>
							<td class="name">Name</td>
							<td class="use">Use</td>
						</tr>
					</thead>
					<tbody><?php if($ovaultSavelist['selecttable'] != '') echo $ovaultSavelist['selecttable']; ?></tbody>
				</table>
			</div>
			<div class="clear"></div>
		</div>
		<div class="olaytopflag j_close j_orowSelect" data-target="olay_savedlists_select" title="Cancel"></div>
	</div><!--close olay_savedlists_select-->
</div><!--close #j_olay_savedlists_select_wrap-->
