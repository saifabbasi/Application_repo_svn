<?
	$SelfHosted = false;
	if ( $this->{'Application/Mode'} == 'SelfHosted' )
	{
		$SelfHosted = true;
	}
?>


<div class="pagecontent" id="nwpage">

<script language="javascript" src="/Themes/BevoMedia/jquery_tooltip.js"></script>
<style type="text/css">
#tooltip{
	line-height: 1.231; font-family: Arial; font-size: 13px;
	position:absolute;
	border:1px solid #333;
	background:#f7f5d1;
	padding:2px 5px;
	display:none;
	width:320px;
	text-align:center;
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
</style>
<script language="javascript">
function ratingTill(id, amnt, state)
{
	for(var i=1; i<6; i++)
	{
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-off.gif';
	}
	for(var i=1; i<amnt+1; i++)
	{
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-on.gif';
	}
}

function ratingRst(id, amnt)
{
	for(var i=1; i<6; i++)
	{
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-on.gif';
	}
	for(var i=amnt+1; i<6; i++)
	{
		document.getElementById(id+"_"+i).src = '/Themes/BevoMedia/img/star-off.gif';
	}
}
</script>
<?php /* ##################################################### OUTPUT ############### */ ?>
	<div id="pagemenu"></div>

	<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>





<div class="nwotm_outer"><!-- Network of the Month -->
		<div class="nwotm_inner">
			<div class="top"></div>
		
			<div class="left">
				<p>Each month around the end of the month, we here at Bevo sit down and evaluate every single network featured here. We compare stats, payouts, and a whole lot more. And every month, there's that one network that outperformed all others.</p>
				
				<p class="txtdred"><strong>That one network gets crowned as Bevo's<br />
				network of the month.</strong></p>
			</div>
			<div class="middle">
				<img class="nwpic" src="/Themes/BevoMedia/img/networklogos/uni/<?=$this->Network1->id?>.png" alt="" />
			</div>
			<div class="right">
				<p>
					Publisher's Rating:
					<?php $this->Network1->rating = 5;?>
					<?php for($i=1; $i<6; $i++):?>
					<?php if($this->Network1->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<?
							if (!$SelfHosted)
							{
						?>
							<a href='/BevoMedia/Publisher/NetworkRating.html?Rating=<?php print $i?>&ID=<?php print $this->Network1->id; ; ?>' rel='shadowbox;width=400;height=400;options={animate:true,showOverlay:true};player=iframe;'>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_month_<?php print $this->Network1->id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_top_month_<?php print $this->Network1->id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_top_month_<?php print $this->Network1->id; ; ?>', <?php print $this->Network1->rating; ?>)" style="" align="absbottom" border="0" />
							</a>
						<?
							} else {
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_month_<?php print $this->Network1->id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
					<?php endfor?>
					
					<a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$this->Network1->id?>">Network Reviews</a>
				</p>
				<div class="clear"></div>
				
				<p class="txtdred"><strong>Don't have an account with <?=$this->Network1->title?> yet?<br />
				Sign up now:</strong></p>
				
				
				
				<?php if(empty($this->Network1->status) || $this->Network1->status != 3): ?>
						<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network1->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $this->Network1->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network1->title)?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						
						?>
						<a class="btn nw_applyadd_bigred" href="ApplyAdd.html?network=<?php print $this->Network1->id?>" title="<?php print htmlentities($this->Network1->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply / Add this network</a>
					<?php else: ?>
					<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network1->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/EditNetwork.html?network=<?php print $this->Network1->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network1->title); ; ?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						?>
						<a class="btn nw_edit_bigred" href="EditNetwork.html?network=<?php print $this->Network1->id; ; ?>" title="Edit account details for <?php print htmlentities($this->Network1->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Edit</a>
					<?php endif; ?>
				
				
				
				
				
				
				
				<p>You'll thank us later.</p>
			</div>
			<div class="clear"></div>
		
		</div><!--close nwotm_inner-->
	</div><!--close nwotm_outer-->
	
	<div class="nwalso"><!-- the #2 and #3 recommended networks -->

		<table cellspacing="0" cellpadding="0" border="0" class="cleantable widehalf"><!-- recommended nw #2 -->
			<tr class="top">
				<td class="topleft">&nbsp;</td>
				<td class="top borderright">&nbsp;</td>
				<td class="top">&nbsp;</td>
				<td class="top borderleft">&nbsp;</td>
				<td class="topright">&nbsp;</td>
			</tr>
			<tr>
				<td class="left" rowspan="3">&nbsp;</td>
				<td class="borderright" rowspan="3">
					<img class="nwpic medium" src="/Themes/BevoMedia/img/networklogos/uni/<?=$this->Network2->id?>.png" alt="" /><!-- universal network logo, with class "medium" to show smaller -->
				</td>
				<td class="borderbutt flushtop">
					<img class="icon_cleantable_why" src="/Themes/BevoMedia/img/icon_cleantable_why.gif" alt="Why?" />
					<p><?php echo $this->Network2->title?> is an emerging network dedicated to personally helping each publisher meet their campaign goals.</p>
				</td>
				<td class="borderleft" rowspan="3">
					
					<?php if(empty($this->Network2->status) || $this->Network2->status != 3): ?>
						<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network2->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $this->Network2->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network2->title)?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						
						?>
						<a class="btn nw_applyadd" href="ApplyAdd.html?network=<?php print $this->Network2->id?>" title="<?php print htmlentities($this->Network2->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply / Add this network</a>
					<?php else: ?>
					<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network2->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/EditNetwork.html?network=<?php print $this->Network2->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network2->title); ; ?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						?>
						<a class="btn nw_edit" href="EditNetwork.html?network=<?php print $this->Network2->id; ; ?>" title="Edit account details for <?php print htmlentities($this->Network2->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Edit</a>
					<?php endif; ?>
				</td>
				<td class="right" rowspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="borderbutt aligncenter">
					<p>Publisher's Rating:</p>
					<?php $this->Network2->rating = 4; ?>
					<?php for($i=1; $i<6; $i++):?>
					<?php if($this->Network2->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<?
							if (!$SelfHosted)
							{
						?>
							<a href='/BevoMedia/Publisher/NetworkRating.html?Rating=<?php print $i?>&ID=<?php print $this->Network2->id; ; ?>' rel='shadowbox;width=400;height=400;options={animate:true,showOverlay:true};player=iframe;'>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_<?php print $this->Network2->id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_top_<?php print $this->Network2->id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_top_<?php print $this->Network2->id; ; ?>', <?php print $this->Network2->rating; ?>)" style="" align="absbottom" border="0" />
							</a>
						<?
							} else {
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_<?php print $this->Network2->id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
					<?php endfor?>
					
				</td>
			</tr>
			<tr>
				<td class="flushbutt aligncenter">
					<a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$this->Network2->id?>">Network Reviews</a>
				</td>
			</tr>
			<tr class="butt">
				<td class="buttleft">&nbsp;</td>
				<td class="butt borderright">&nbsp;</td>
				<td class="butt">&nbsp;</td>
				<td class="butt borderleft">&nbsp;</td>
				<td class="buttright">&nbsp;</td>
			</tr>
		</table><!-- end recommended #2 -->
		
		<table cellspacing="0" cellpadding="0" border="0" class="cleantable widehalf"><!-- recommended nw #3. Same markup as #2! -->
			<tr class="top">
				<td class="topleft">&nbsp;</td>
				<td class="top borderright">&nbsp;</td>
				<td class="top">&nbsp;</td>
				<td class="top borderleft">&nbsp;</td>
				<td class="topright">&nbsp;</td>
			</tr>
			<tr>
				<td class="left" rowspan="3">&nbsp;</td>
				<td class="borderright" rowspan="3">
					<img class="nwpic medium" src="/Themes/BevoMedia/img/networklogos/uni/<?=$this->Network3->id?>.png" alt="" />
				</td>
				<td class="borderbutt flushtop">
					<img class="icon_cleantable_why" src="/Themes/BevoMedia/img/icon_cleantable_why.gif" alt="Why?" />
					<p><?php echo $this->Network3->title?> is one of the most established and well respected networks in the industry.</p>
					<br />
				</td>
				<td class="borderleft" rowspan="3">
					
					<?php if(empty($this->Network3->status) || $this->Network3->status != 3): ?>
						<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network3->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $this->Network3->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network3->title)?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						
						?>
						<a class="btn nw_applyadd" href="ApplyAdd.html?network=<?php print $this->Network3->id?>" title="<?php print htmlentities($this->Network3->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply / Add this network</a>
					<?php else: ?>
					<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $this->Network3->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/EditNetwork.html?network=<?php print $this->Network3->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($this->Network3->title); ; ?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						?>
						<a class="btn nw_edit" href="EditNetwork.html?network=<?php print $this->Network3->id; ; ?>" title="Edit account details for <?php print htmlentities($this->Network3->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Edit</a>
					<?php endif; ?>
					
				</td>
				<td class="right" rowspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td class="borderbutt aligncenter">
					<p>Publisher's Rating:</p>
					<?php $this->Network3->rating = 4; ?>
					<?php for($i=1; $i<6; $i++):?>
					<?php if($this->Network3->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<?
							if (!$SelfHosted)
							{
						?>
							<a href='/BevoMedia/Publisher/NetworkRating.html?Rating=<?php print $i?>&ID=<?php print $this->Network3->id; ; ?>' rel='shadowbox;width=400;height=400;options={animate:true,showOverlay:true};player=iframe;'>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_<?php print $this->Network3->id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_top_<?php print $this->Network3->id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_top_<?php print $this->Network3->id; ; ?>', <?php print $this->Network3->rating; ?>)" style="" align="absbottom" border="0" />
							</a>
						<?
							} else {
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_top_<?php print $this->Network3->id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
					<?php endfor?>
				</td>
			</tr>
			<tr>
				<td class="flushbutt aligncenter">
					<a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$this->Network3->id?>">Network Reviews</a>
				</td>
			</tr>
			<tr class="butt">
				<td class="buttleft">&nbsp;</td>
				<td class="butt borderright">&nbsp;</td>
				<td class="butt">&nbsp;</td>
				<td class="butt borderleft">&nbsp;</td>
				<td class="buttright">&nbsp;</td>
			</tr>
		</table><!-- end recommended #3 -->		
		
		<div class="clear"></div>
	</div><!--close nwalso-->
	
	



















<table class="btable widehalf" border="0" cellpadding="3" cellspacing="0">
	<tbody>
		<tr class="table_header">
            <td class="hhl">&nbsp;</td>
            <td>Other Networks</td>
            <td style="width: 90px;" class="STYLE2">
            	<a title="Vote on how much you like a network by clicking on the stars below.<br/>Every publisher gets one vote.<br/>The number of stars lit up represents the average number of stars BeVo publishers have given to a specific network." class="tooltip">
            		Publisher's Rating
					<img height="12" width="12" src="/Themes/BevoMedia/img/questionMarkIcon.png"/>
				</a>
            </td>
            <td class="STYLE2" style="width: 130px; text-align: center;">Reviews</td>
            <!--<td style="width: 100px; text-align: center;" class="STYLE2">Payout Options</td>-->
            <td style="width: 60px;">&nbsp;</td>
            <td class="hhr">&nbsp;</td>
        </tr>
        
        
        <?php
		foreach($this->CpaNetworks as $CpaNetwork):
		?>
		<tr>
			<td>&nbsp;</td>
			<td class="GridRowHead" style="border-left: medium none; text-align: center;">
				<img class="nwpic small" src="/Themes/BevoMedia/img/networklogos/uni/<?php print $CpaNetwork->id; ; ?>.png" alt="<?php print htmlentities($CpaNetwork->title); ; ?>" />
			</td>
			<td class="rating">
				<div id="div_<?php print $CpaNetwork->id; ; ?>" style="white-space: nowrap;">
				<?
					$CpaNetwork->rating = $this->PageHelper->FixNetworkRating($CpaNetwork->title, $CpaNetwork->rating);
				?>
				<?php for($i=1; $i<6; $i++):?>
					<?php if($CpaNetwork->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<?
							if (!$SelfHosted)
							{
						?>
							<a href='/BevoMedia/Publisher/NetworkRating.html?Rating=<?php print $i?>&ID=<?php print $CpaNetwork->id; ; ?>' rel='shadowbox;width=400;height=400;options={animate:true,showOverlay:true};player=iframe;'>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->id; ; ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $CpaNetwork->id; ; ?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $CpaNetwork->id; ; ?>', <?php print $CpaNetwork->rating; ?>)" style="" align="absbottom" border="0" />
							</a>
						<?
							} else {
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->id; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
					<?php endfor?>
				</div>
				
			</td>
			<td class="GridRowCol" style="text-align: center;"><a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$CpaNetwork->id?>">Reviews</a></td>
			<td class="GridRowCol" style="white-space: nowrap;" width="94">
				<span>
			
					<?php if(empty($CpaNetwork->status) || $CpaNetwork->status != 3): ?>
						<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $CpaNetwork->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/ApplyAdd.html?network=<?php print $CpaNetwork->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($CpaNetwork->title)?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						
						?>
						<a class="btn nw_applyadd" href="ApplyAdd.html?network=<?php print $CpaNetwork->id?>" title="<?php print htmlentities($CpaNetwork->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Apply / Add this network</a>
					<?php else: ?>
					<?php
							if(isset($_GET['ID']))
							{
								if($_GET['ID'] == $CpaNetwork->id)
								{
								?>
								
								<script language='javascript'>
									window.onload = function(){
										// open a welcome message as soon as the window loads
										Shadowbox.open({
											content:    '/BevoMedia/Publisher/EditNetwork.html?network=<?php print $CpaNetwork->id; ?>',
											player:     "iframe",
											title:      "<?php print htmlentities($CpaNetwork->title); ; ?>",
											height:     480,
											width:      640
										});
									};
								</script>
								
								<?php
								}
							}
						?>
						<a class="btn nw_edit" href="EditNetwork.html?network=<?php print $CpaNetwork->id; ; ?>" title="Edit account details for <?php print htmlentities($CpaNetwork->title); ; ?>" rel="shadowbox;width=640;height=480;player=iframe">Edit</a>
					<?php endif; ?>
				</span>
			</td>
			<td class="tail">&nbsp;</td>
		</tr>
		<?php endforeach; ?>
		<tr class="table_footer">
			<td class="hhl"></td>
			<td colspan="4"></td>
			<td class="hhr"></td>
		</tr>
	</tbody>
</table>

<!-- <a class="tbtn floatleft" href="/BevoMedia/Publisher/PPCTutorials.html#NoNetwork">How to use Bevo without installing any networks</a> -->


<table class="btable widehalf" border="0" cellpadding="3" cellspacing="0">
	<tbody><tr class="table_header">
            <td class="hhl"></td>
            <td>PPC Platforms<a name="PPC" /></td>
            <td style="width: 90px;" class="STYLE2">Publisher's Rating</td>
            <td width="94"></td>
            <td class="hhr"></td>
        </tr>

		<tr style="">
			<td class="border">&nbsp;</td>
			<td class="GridRowHead" style="text-align: center;">
				<img class="nwpic small" src="/Themes/BevoMedia/img/networklogos/uni/1032.png">
			</td>
			
			<?php $CpaNetwork = $this->AdwordsRating?>
			<td class="rating">
			<div id="div_<?php print $CpaNetwork->ID ?>" style="white-space: nowrap;">
				<?php for($i=1; $i<6; $i++):?>
					<?php if($CpaNetwork->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<!-- ifdef __SelfHosted__ -->
						<?
							if (!$SelfHosted)
							{
						?>
							<a href='/BevoMedia/Publisher/_RateNetwork.html?Rating=<?php print $i?>&ID=<?php print $CpaNetwork->ID?>' rel='shadowbox;width=0;height=0;options={animate:false,showOverlay:false,overlayOpacity:0};player=iframe;'>
								<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $CpaNetwork->ID?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $CpaNetwork->ID ?>', <?php print $CpaNetwork->rating; ?>)" style="" align="absbottom" border="0" />
							</a>
						<?
							}
						?>
						<!-- endif __SelfHosted__ -->
							
						<?
							if ($SelfHosted)
							{
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
							
				<?php endfor?>
			</div>
			</td>
			<td class="GridRowCol" style="white-space: nowrap;" width="120">
				<span>
					<a class="btn nw_create" href='https://www.google.com/accounts/ServiceLogin?service=adwords&cd=null&hl=en-US&ltmpl=signup&passive=false&ifr=false&alwf=true&continue=https%3A%2F%2Fadwords.google.com%2Fum%2FSignupToken' target='_blank'>
						Apply for Google AdWords
					</a>
					<a class="btn nw_addedit" title='Google Adwords' href='/BevoMedia/Publisher/GoogleAdwordsAPI.html' rel='shadowbox;width=640;height=480;player=iframe'>
						Edit Google AdWords
					</a>

				</span>
			</td>
			<td class="tail">&nbsp;</td>

		</tr>

		<tr style="">
			<td class="border">&nbsp;</td>
			
			<td class="GridRowHead" style="text-align: center;">
				<img class="nwpic small" src="/Themes/BevoMedia/img/networklogos/uni/1043.png" >
			</td>

			<?php $CpaNetwork = $this->YahooRating?>
			<td class="rating">
			<div id="div_<?php print $CpaNetwork->ID ?>" style="white-space: nowrap;">
				<?php for($i=1; $i<6; $i++):?>
					<?php if($CpaNetwork->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<!-- ifdef __SelfHosted__ -->
						<?
							if (!$SelfHosted)
							{
						?>
						<a href='/BevoMedia/Publisher/_RateNetwork.html?Rating=<?php print $i?>&ID=<?php print $CpaNetwork->ID?>' rel='shadowbox;width=0;height=0;options={animate:false,showOverlay:false,overlayOpacity:0};player=iframe;'>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID ?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $CpaNetwork->ID?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $CpaNetwork->ID?>', <?php print $CpaNetwork->rating; ?>)" style="" align="absbottom" border="0" />
						</a>
						<?
							}
						?>
						<!-- endif __SelfHosted__ -->
						
						
						<?
							if ($SelfHosted)
							{
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
				<?php endfor?>
			</div>
			</td>
						<td class="GridRowCol" style="white-space: nowrap;" width="120"><span>

					<a class="btn nw_create" href='https://adcenter.microsoft.com/customer/SignupPreview.aspx' target='_blank'>
						Apply for MSN Search Marketing
					</a>
					<a class="btn nw_addedit" title='MSN AdCenter' href='/BevoMedia/Publisher/MSNAdCenterAPI.html' rel='shadowbox;width=640;height=480;player=iframe'>
						Edit MSN Search Marketing
					</a>
			</td>
			<td class="tail">&nbsp;</td>
		</tr>
<?php
					if(isset($_GET['OpenPPC']) && in_array($_GET['OpenPPC'], array('GoogleAdwords','MSNAdCenter','Yahoo')))
					{
						$labels = array('GoogleAdwords'=>'Google Adwords', 'MSNAdCenter'=>'MSN AdCenter', 'Yahoo'=>'Yahoo Search Marketing');
						?>
					<script language='javascript'>
						window.onload = function(){
							// open a welcome message as soon as the window loads
							Shadowbox.open({
								content:    '/BevoMedia/Publisher/<?php echo $_GET['OpenPPC']?>API.html',
								player:     "iframe",
								title:      "<?php echo $labels[$_GET['OpenPPC']]?>",
								height:     480,
								width:      640
							});
						};
					</script>
					<?php
					}

					?>
		<tr style="">
			<td class="border">&nbsp;</td>
			<td class="GridRowHead" style="text-align: center;">
				<img class="nwpic medium" src="/Themes/BevoMedia/img/networklogos/uni/1008.png">
			</td>

			<?php $CpaNetwork = $this->MSNRating?>
			<td class="rating">
			<div id="div_<?php print $CpaNetwork->ID; ; ?>" style="white-space: nowrap;">
				<?php for($i=1; $i<6; $i++):?>
					<?php if($CpaNetwork->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
						<!-- ifdef __SelfHosted__ -->
						<?
							if (!$SelfHosted)
							{
						?>
						<a href='/BevoMedia/Publisher/_RateNetwork.html?Rating=<?php print $i?>&ID=<?php print $CpaNetwork->ID?>' rel='shadowbox;width=0;height=0;options={animate:false,showOverlay:false,overlayOpacity:0};player=iframe;'>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $CpaNetwork->ID?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $CpaNetwork->ID?>', <?php print $CpaNetwork->rating; ?>)" style="" align="absbottom" border="0" />
						</a>
						<?
							}
						?>
						<!-- endif __SelfHosted__ -->
						
						
						<?
							if ($SelfHosted)
							{
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
						
				<?php endfor?>
			</div>
			</td>			<td class="GridRowCol" style="white-space: nowrap;" width="120"><span>
					<a class="btn nw_create" href='http://www.dpbolvw.net/click-3266927-10457500?sid=bevomain' target='_blank'>
						Apply for Yahoo Search Marketing
					</a>
					<a class="btn nw_addedit" title='Yahoo Search Marketing' href='/BevoMedia/Publisher/YahooAPI.html' rel='shadowbox;width=640;height=480;player=iframe'>
						Edit Yahoo Search Marketing
					</a>
            </td>
			<td class="tail">&nbsp;</td>

		</tr>
		
		<tr class="table_footer">
			<td class="hhl"></td>
			<td colspan="3"></td>
			<td class="hhr"></td>
		</tr>

	</tbody></table>
	
	<br />
	
	<table class="btable widehalf" border="0" cellpadding="3" cellspacing="0">
	<tbody><tr class="table_header">
            <td class="hhl"></td>
            <td class="STYLE2">Web Analytics</td>
            
            <td style="width: 90px;" class="STYLE2">Publisher's Rating</td>
            <td style="width: 100px;" class="STYLE2" text-align:="" center;=""></td>

            <td class="hhr"></td>
        </tr>


		<tr style="">
			<td class="border">&nbsp;</td>
			</td>
			<td class="GridRowHead" style="text-align: center;">
				<img class="nwpic medium" src="/Themes/BevoMedia/img/networklogos/uni/1017.png" >
			</td>

			<?php $CpaNetwork = $this->AnalyticsRating?>
			<td class="rating">
			<div id="div_<?php print $CpaNetwork->ID?>" style="white-space: nowrap;">
				<?php for($i=1; $i<6; $i++):?>
					<?php if($CpaNetwork->rating >= $i){ $state = 'on'; }else{ $state = 'off'; }?>
					
						<!-- ifdef __SelfHosted__ -->
						<?
							if (!$SelfHosted)
							{
						?>
						<a href='/BevoMedia/Publisher/_RateNetwork.html?Rating=<?php print $i?>&ID=<?php print $CpaNetwork->ID?>' rel='shadowbox;width=0;height=0;options={animate:false,showOverlay:false,overlayOpacity:0};player=iframe;'>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID?>_<?php print $i?>" onmouseover="ratingTill('img_rating_<?php print $CpaNetwork->ID?>', <?php print $i?>)" onmouseout="ratingRst('img_rating_<?php print $CpaNetwork->ID?>', <?php print $CpaNetwork->rating; ?>)" style="" align="absbottom" border="0" />
						</a>
						<?
							}
						?>
						<!-- endif __SelfHosted__ -->
						
						
						<?
							if ($SelfHosted)
							{
						?>
							<img src="/Themes/BevoMedia/img/star-<?php echo $state?>.gif" id="img_rating_<?php print $CpaNetwork->ID; ; ?>_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?
							}
						?>
				<?php endfor?>
			</div>
			</td>
			<td class="GridRowCol" style="white-space: nowrap;" width="120"><span>

					<?php
					if(isset($_GET['Open']) && $_GET['Open'] == 'Analytics')
					{
						?>
					<script language='javascript'>
						window.onload = function(){
							// open a welcome message as soon as the window loads
							Shadowbox.open({
								content:    '/BevoMedia/Publisher/GoogleAnalyticsAPI.html',
								player:     "iframe",
								title:      "Google Analytics",
								height:     480,
								width:      640
							});
						};
					</script>
					<?php
					}

					?>
					<a class="btn nw_create" href='http://www.google.com/analytics/sign_up.html' target='_blank'>
						Apply for Google Analytics
					</a>
					<a class="btn nw_addedit" title='Google Analytics' href='/BevoMedia/Publisher/GoogleAnalyticsAPI.html' rel='shadowbox;width=640;height=480;player=iframe'>
						Edit Google Analytics Accounts
					</a>
			</td>
			<td class="tail">&nbsp;</td>
		</tr>
		
		<tr class="table_footer">
			<td class="hhl"></td>
			<td colspan="3"></td>
			<td class="hhr"></td>
		</tr>
	</tbody></table>
<div class="clear"></div>

</div>
