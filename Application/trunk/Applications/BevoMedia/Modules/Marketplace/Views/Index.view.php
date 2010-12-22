<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/facebox.css" rel="stylesheet" type="text/css" />
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/facebox.js" type="text/javascript"></script>
<?php /*<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/bob.style.css" rel="stylesheet" type="text/css" /> */ ?>
<?php

require(PATH."classes/clsMarketProjects.php");
require(PATH."classes/clsMarketProviders.php");
require(PATH."classes/clsMarketServices.php");

global $intServiceID, $intFreelanceID, $userId;

$userId = $this->User->id;

if(isset($_GET['ServiceID']))
	$intServiceID = $_GET['ServiceID'];
	
if(isset($_GET['FreelanceID']))
	$intFreelanceID = $_GET['FreelanceID'];

if (!is_numeric($intServiceID)) {
	$intServiceID = 3;
}


function ListProviders($intInServiceID) {
	$objProviders = new MarketProviders();
	$objProviders->GetListByServiceID($intInServiceID);
	
	if ($objProviders->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objProviders->GetRow()) {
		if (empty($arrThisRow['ThumbImage'])) {
			$arrThisRow['ThumbImage'] = 'default-40x40.gif';
		}
?>
  <li><a href="#mp-wrapper" onclick="PickProvider('<?php echo $intInServiceID . '-' . $arrThisRow['id']; ?>');">
	<img src="<?php echo '/freelancers/images/' . $arrThisRow['thumbImage']; ?>" alt="<?php echo $arrThisRow['name']; ?>" />
	<div class="mp-list-name"><?php echo $arrThisRow['name']; ?></div>
	<p><?php echo substr($arrThisRow['description'], 0, 40); ?>...</p>
	</a></li>
<?php
	}
}

function ListProviders2($intInServiceID) {
	$objProviders = new MarketProviders();
	$objProviders->GetListByServiceID($intInServiceID);
	
	if ($objProviders->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objProviders->GetRow()) {
		if (empty($arrThisRow['thumbImage'])) {
			$arrThisRow['thumbImage'] = 'default-40x40.gif';
		}
		
		if (empty($arrThisRow['image'])) {
			$arrThisRow['image'] = 'default-130x130.gif';
		}

?>

  <div id="profile<?php echo $intInServiceID . '-' . $arrThisRow['id']; ?>" class="mp-profile">

			<div class="mp-profile-right">
			    <img class="mp-pic-130" src="/freelancers/images/<?php echo $arrThisRow['image']; ?>" alt="<?php echo $arrThisRow['name']; ?>" width="130" height="130"/>
				<div class="clear"></div>
				<a class="button sendmessage" href="#message" onclick="currProviderID = <?php echo $arrThisRow['id']; ?>;" rel="facebox">Send Message</a>
				<a class="button makepayment" href="#deposit" onclick="$('input#ProviderID').val(<?php echo $arrThisRow['id']; ?>);" rel="facebox">Initiate Payment</a>
			</div>
			<div class="mp-profile-left">
				<div class="mp-profile-title">Name</div>
				<div class="mp-profile-txt bigbold"><?php echo $arrThisRow['name']; ?></div>
				<div class="clear"></div>
				
				<div class="mp-profile-title">Price Range</div>
				<div class="mp-profile-txt bigbold"><?php echo $arrThisRow['priceRange']; ?></div>
				<div class="clear"></div>
				
				<div class="mp-profile-title">More Info</div>
				<div class="mp-profile-txt">
					<p><?php echo $arrThisRow['description']; ?></p>
				</div>
				<div class="clear nomargin"></div>
			</div>
			<div class="clear"></div>
		</div>
<?php
	}
}

function LoadProvider() {
	global $intFreelanceID, $strFreelanceName, $strFreelanceImg, $strFreelanceDesc;
	
	$objProvider = new MarketProviders();
	$objProvider->ID = $intFreelanceID;
	$objProvider->GetDetails();
	
	$strFreelanceName = $objProvider->name;
	$strFreelanceImg = $objProvider->image;
	$strFreelanceDesc = $objProvider->description;
}

function GetServiceDescription($intInServiceID) {
	$objService = new MarketServices();
	$objService->ID = $intInServiceID;
	$objService->GetDetails();
	
	$strServiceDesc = $objService->description;
	
	return $strServiceDesc;
}

function ListProjects() {
	global $userId;
	
	$objProjects = new MarketProjects();
	$objProjects->GetListByUserID($userId);
	
	if ($objProjects->RowCount == 0) {
		return false;
	}
	
	$blnAltRow = false;
	$out = array();
	while ($arrThisRow = $objProjects->GetRow()) {
		$strStatus = '';
		if ($arrThisRow['acceptedId'] != 0) {
			$strStatus = 'active';
		}
		if ($arrThisRow['userComplete'] != 0 && $arrThisRow['providerComplete'] != 0) {
			$strStatus = 'complete';
		}
		$out[] = array(
						'title' => $arrThisRow['name'],
						'date_start' => $arrThisRow['date'], //assuming you are storing the date as an integer
						'provider_username' => $arrThisRow['Providername'],
						'price' => $arrThisRow['Deposit'],
		                'description' => $arrThisRow['Terms'],
						'status' => $strStatus
						);
	}
	return $out;
}

function GetProjectCount() {
	global $userId;
	
	$objProjects = new MarketProjects();
	$objProjects->GetListByUserID($userId);
	
	if ($objProjects->RowCount == 0) {
		return 0;
	}
	
	$arrProjects = $objProjects->GetRows();
	return count($arrProjects);
}
?>

<?php
$strPageHead = '<script language="Javascript" src="js/jquery-1.3.1.min.js"></script>
					<link href="/facebox/facebox.css" media="screen" rel="stylesheet" type="text/css"/>
					<script src="facebox/facebox.js" type="text/javascript"></script>';
?>

<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu">
		<ul>
			<li><a class="active" href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
			<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
			<?php if($this->User->membershipType != 'premium')
				echo '<li><a href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>';?>
		</ul>
	</div>
	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>
	

<div id="MyProjects" style="display: none;">
<h2 style="color: #29ABE2; margin:10px 0 10px 15px;">My Projects</h2>
	
	<table class="mtable" cellspacing="0">
	  <tr class="table_header">
	    <td class="hhl">&nbsp;</td>
		<td>Project</td>
		<td>Freelancer</td>
		<td align="center">Status</td>
		<td align="center">Last Update</td>
		<td class="hhr">&nbsp;</td>
	  </tr>
	<?php ListProjects(); ?>
	  <tr class="table_footer">
	    <td class="hhl">&nbsp;</td>
		<td colspan="4">&nbsp;</td>
		<td class="hhr">&nbsp;</td>
	  </tr>
	</table>
</div>
	
<script>
<?php


// Default Tab
switch((int) $intServiceID) {
	case 3:
		$strMarketTab = 'mp-consulting';
		break;
	case 2:
		$strMarketTab = 'mp-lp';
		break;
	case 1:
		$strMarketTab = 'mp-writing';
		break;
	case 4:
		$strMarketTab = 'mp-programming';
		break;
	case 5:
		$strMarketTab = 'mp-seo';
		break;
	default:
		$strMarketTab = 'mp-consulting';
		break;
}
?>

$(document).ready(function(){
  PickTab('<?php echo $strMarketTab; ?>');
  $('a[rel*=facebox]').facebox();
});
	
function PickTab(strInTab) {
	var $ = jQuery;
	$('#mp-catmenu li a').removeClass('active'); // Remove active class from links
	$('#mp-catmenu li a.' + strInTab).addClass('active'); // Set the class for active state
	$('#mp-tabs .tab').hide(); // Hide all tabs
	$('.mp-profile').hide();
	$('#mp-providers ul').hide(); // Hide all providers
	$('#mp-tabs #' + strInTab + 'Left').show();
	$('#mp-providers #' + strInTab + 'Right').show();
}

function PickProvider(strInTab) {
	var $ = jQuery;
	$('.mp-profile').hide(); // Hide all profiles
	$('#profile' + strInTab).show();
}
function SendMessage(objForm) {
	strFrom = objForm.elements['From'].value;
	strSubject = objForm.elements['Subject'].value;
	strMessage = objForm.elements['Message'].value;
	
	$.ajax({
		type: "POST",
		url: "/Themes/BevoMedia/ajax/ajax_sendmessage.php",
		data: "UserID=<?php echo $userId; ?>&From=" + strFrom + "&ProviderID=" + currProviderID + "&Subject=" + strSubject + "&Message=" + strMessage,
		complete: function(msg){ alert('Your message has been sent'); $.facebox.close();  },
		success: function(msg){   }
	});
	
	//$.facebox.close();
}
</script>
<div class="mpmentor-wrapper"><!-- mentorship program dropdown  -->
	<a id="mpmentor_button" class="button isclosed" href="#" title="Click to view">Mentorship Program</a>
	
	<div id="mpmentor">
		<div class="mpmentor-box mpmentor-phone">
			<p>Unlimited access via AIM, E-mail and Phone to your Mentor</p><!-- space is limited: fixed height for all 4 elements! -->
		</div>
		<div class="mpmentor-box mpmentor-connections">
			<p>Take advantage of Bevo Connections to get top placements, get into networks, highest payouts...</p>
		</div>
		<div class="mpmentor-box mpmentor-consultants">
			<p>Access to our Designers, Article Writers, Programmers, Legal and Accounting Teams</p>
		</div>
		<div class="mpmentor-box mpmentor-resources">
			<p>Campaign funding and partnership opportunities available</p>
		</div>
		<a class="button mpmentor-moreinfo" href="/BevoMedia/Marketplace/MentorshipProgram.html">More Info</a>
	</div>
</div><!-- end marketplace dropdown -->

<!-- Javascript for the dropdown has been moved to the bottom -->



<div id="mp-wrapper"><!-- marketplace content wrapper start -->
	<div id="mp-tabs">
		<!-- 	add / remove / move tabs anytime
			use the tab link "href" attribute to connect the tab to an mp-box ID
		-->
		<ul>
			<li><a href="#mpbox-consulting">Consulting</a></li>
			<li><a href="#mpbox-design">Design</a></li>
			<li><a href="#mpbox-programming">Programming</a></li>
			<li><a href="#mpbox-content">Content</a></li>
			<li><a href="#mpbox-seo">SEO</a></li>
			<li class="mp-tablite"><a class="active" href="#mpbox-myprojects">My Projects</a></li>
			<li class="mp-tablast"></li>
		</ul>
		
		<div id="mp-tabstop"></div>
	</div><!--close tabs-->
	<div id="mp-content">
		<div id="mp-contenttop"></div>
		
		<div class="mp-box" id="mpbox-consulting"><!-- box consulting -->
			<h2 class="boxtitle">consulting</h2>
			
			<p>The Bevo Mentorship Program is an all inclusive, all access program designed to guide those who are new to the industry into becoming successful internet marketers. The Bevo consultants are well-seasoned affiliate marketers who have experienced great success in the industry and enjoy helping others learn the ropes. The mentorship program is operated by BevoSearch, a subsidiary consulting firm of BevoMedia.</p>
			
			<div class="mp-sectiontitle moreinformation">
				<small>* All payments must be paid prior to job.</small>
			</div>
			
			<p>Price Per Call: Contact Us<br />
			Price Per Month: Contact Us<br />
			Consulting can be done either by one time phone call or unlimited month access. For more information about the unlimited month long access <a href="/BevoMedia/Marketplace/MentorshipProgram.html">click here</a>.</p>
			
			<a class="button contact" rel="shadowbox;height=400;width=480;" href="JobRequest.html?type=consulting">Contact</a>
		</div>
		
		<div class="mp-box" id="mpbox-design"><!-- box design -->
			<h2 class="boxtitle">design</h2>
			
			<p>Get a landing page, header or logo professionally designed by some of the best designers in the industry. All of the Bevo designers have worked with affiliate marketers and understand the internet marketing industry. Landing pages, headers and logo’s are some of the most important aspects of any online marketing campaign, so it is important to have one designed by somebody who understands the space. The Bevo designers are not just limited to affiliate marketing projects either, contact us for more details about web design.</p>
			
			<div class="mp-sectiontitle moreinformation">
				<small>* All payments must be paid prior to job.</small>
			</div>
			
			<p>More information:<br />
			Contact us for details and pricing</p>
			
			<a class="button contact" rel="shadowbox;height=400;width=480;" href="JobRequest.html?type=design">Contact</a>
		</div>
		
		<div class="mp-box" id="mpbox-programming"><!-- box programming -->
			<h2 class="boxtitle">programming</h2>
			
			<p>Internet marketers always need programming work done. Our programmers have worked with affiliate marketers and understand what they are looking for. So, if you need a project done or just a quick fix, our programmers can get it done. Contact us to find out about any sized project.</p>
			
			<div class="mp-sectiontitle moreinformation">
				<small>* All payments must be paid prior to job.</small>
			</div>
			
			<p>More information:<br />
			Contact us for project quote.</p>
			
			<a class="button contact" rel="shadowbox;height=400;width=480;" href="JobRequest.html?type=programming">Contact</a>
		</div>
		
		<div class="mp-box" id="mpbox-content"><!-- box content -->
			<h2 class="boxtitle">content</h2>
			
			<p>Our content writers provide quality, affordability and a very quick turnaround time. We offer both high quality content writers who specialize in landing page content, and also offer bulk content writers for publishers who need a high volume of articles. Whether you have a big project, or just a few articles, our content writers can get it done efficiently.</p>
			
			<div class="mp-sectiontitle moreinformation">
				<small>* All payments must be paid prior to job.</small>
			</div>
			
			<p>More information:<br />
			Contact us for pricing</p>
			
			<a class="button contact" rel="shadowbox;height=400;width=480;" href="JobRequest.html?type=content">Contact</a>
		</div>
		
		<div class="mp-box" id="mpbox-seo"><!-- box seo -->
			<h2 class="boxtitle">seo</h2>
			
			<p>The Bevo Search Engine Optimization team has several years of website optimization experience. Our team can SEO affiliate marketing landing pages, websites, blogs or anything you need. Each project differs in price and time frame, but our team works fast and provides quality results.</p>
			
			<div class="mp-sectiontitle moreinformation">
				<small>* All payments must be paid prior to job.</small>
			</div>
			
			<p>More information:<br />
			Contact us for project quote</p>
			
			<a class="button contact" rel="shadowbox;height=400;width=480;" href="JobRequest.html?type=seo">Contact</a>
		</div>
		
		<div class="mp-box active" id="mpbox-myprojects"><!-- box myprojects -->
			<h2 class="boxtitle">my projects</h2>
			
			<?php
				$myprojects = $this->db->fetchAssoc('select * from bevomedia_marketplace where status != "pendingApproval" and user__id='.$this->User->id);
				$count = $this->db->fetchOne('select count(*) from bevomedia_marketplace where status != "pendingApproval" and user__id='.$this->User->id);
				//BUILD OUTPUT. I hope you can reuse this without many changes (that's the idea)
				if($count) {
					unset($out); //just to make sure
					$out = array('active' => '', 'complete' => '');
					foreach($myprojects as $k => $p) {
						if($p['status'] == 'complete') //show completed projects below active ones
							$o = 'complete';
						else	$o = 'active';
						
						$out[$o] .= '<li';
						$out[$o] .= $p['status'] == 'complete' ? ' class="complete"' : ''; //if complete, add a class to the li
						$out[$o] .= '>
								<h3 id="mp-myprojectlist-'.$k.'"';
							$out[$o] .= $p['description'] ? ' class="hasdesc" title="Click to toggle Project Description">' : '>';
							$out[$o] .= '<span>'.$p['projectName'].'</span>';
							$out[$o] .= $p['description'] ? ' <span class="mp-expand"></span>' : '';
							$out[$o] .= '</h3>
								<div class="mpsub">
									<span class="mpsub-date">'.date('F j, Y', strtotime($p['created'])).'</span>
									<span class="mpsub-provider">'.$p['contactEmail'].'</span>
									<span class="mpsub-price">$'.round($p['quotedPrice'], 2).'</span>
								</div>';
								
						switch($p['status']) {
								
							case 'pendingAccept':
								$out[$o] .= '<a class="button mp-initiate" href="AcceptQuote.html?id='.$p['id'].'" rel="shadowbox;height=320;width=480;">Initiate</a>'; //link
								break;
								
							case 'accepted':
								$out[$o] .= '<a class="button mp-makepayment" href="MarketplacePayment.html?id='.$p['id'].'">Make Payment</a>'; //link
								break;

							case 'paid':
								$out[$o] .= '<div class="mp-icon mp-inprogress">In Progress</div>';
								break;
							
							default: //status complete
								$out[$o] .= '<div class="mp-icon mp-completed">Project Completed</div>';
						}
						$out[$o] .= $p['description'] ? '<div class="mp-myprojectlist-description" id="mp-myprojectlist-'.$k.'-description">
											<a class="button mp-close">Close</a>'.$p['description'].'</div>' : '';
						$out[$o] .= '</li>';
					}//enforeach myprojects
				}//endif myprojects > 0
				
				//OUTPUT
				if(!empty($out)) {
					$out = $out['active'].$out['complete'];
					echo '<a class="button mp-sectiontitle projectstatus" title="Toggle all Project Descriptions" href="#">Toggle all Project Descriptions</a><ul class="mp-myprojectlist">'.$out.'</ul>';
					unset($out); //clear again
					
				} else	echo '<p>You don\'t have any projects yet. Once you have one or more projects, you will be able to manage them here.</p>';
				?>
		</div><!--close my projects box-->
		
	<div id="mp-contentbutt"></div>
	</div><!--close mp-content-->
	<div class="clear"></div>

</div><!-- end marketplace wrapper -->

<script type="text/javascript">
$(document).ready(function() {
/*marketplace mentorship dropdown*/
	$('a#mpmentor_button').click(function(){
		if($(this).hasClass('isclosed')) {$('div#mpmentor').show(function(){
			$('a#mpmentor_button').removeClass('isclosed').addClass('isopen');$(this).slideDown(400);
		});
		} else {
			$('div#mpmentor').hide(function(){$('a#mpmentor_button').removeClass('isopen').addClass('isclosed');$(this).slideUp(100);});
		}
		return false;
	});
	
/*marketplace main tabbed content*/
	//set the container min-height depending on how many tabs there are
	var 	mpMinHeight = $('#mp-tabs').outerHeight(false);
	$('#mp-content').css({'min-height': mpMinHeight});
	
	//switch between tabs
	$('#mp-tabs ul li a').live('click', function(e) {
		e.preventDefault();
		
		$(this).parents('ul').find('li > a.active').removeClass('active');
		
		$('#mp-content > .mp-box.active').slideUp(200).removeClass('active');
		$('#mp-content > .mp-box#'+$(this).attr('href')).slideDown(200).addClass('active');
		$(this).addClass('active');
	});
	
	//indicate expand description
	$('ul.mp-myprojectlist li h3.hasdesc').hover(
		function() {
			$(this).css({'cursor':'pointer'});
			$(this).children('.mp-expand').show();
		}, function() {
			if($('#'+$(this).attr('id')+'-description').hasClass('isopen')) {
			} else {
				$(this).children('.mp-expand').fadeOut(400);
			}
		}
	
	//expand description
	).click(function() {
		var targetDesc = $(this).attr('id');
		if($('#'+targetDesc+'-description').hasClass('isopen')) { //close
			$('#'+targetDesc+'-description').slideUp(200).removeClass('isopen');
			$('#'+targetDesc).children('.mp-expand').fadeOut(400);
		} else { //open
			$('#'+targetDesc+'-description').slideDown(200).addClass('isopen');
		}
		
	});
	
	//close description
	$('.mp-myprojectlist-description a.button.mp-close').click(function() {
		$(this).parent().slideUp(200).removeClass('isopen');
		$(this).parents('li').find('h3').children('.mp-expand').fadeOut(200);
	});
	
	//toggle all descriptions
	$('a.mp-sectiontitle.projectstatus').toggle(
		function() { //open
			$('.mp-myprojectlist-description').each(function() {
				if($(this).hasClass('isopen')) {
				} else {
					$(this).slideDown(200).addClass('isopen');
					$(this).parent().find('h3.hasdesc .mp-expand').fadeIn(200);
				}
			});
		}, function() { //close
			$('.mp-myprojectlist-description').each(function() {
				if($(this).hasClass('isopen')) {
					$(this).slideUp(200).removeClass('isopen');
					$(this).parent().find('h3.hasdesc .mp-expand').fadeOut(200);
				}
			});
		}
	);
});
</script>