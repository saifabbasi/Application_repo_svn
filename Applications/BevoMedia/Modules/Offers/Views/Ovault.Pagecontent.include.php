<table width="100%" cellspacing="0" cellpadding="3" border="0" class="btable" id="j_otable">
	<tr class="table_header">
		<td class="hhl">&nbsp;</td>
		<td class="td_saved2list" style="width:15px;">&nbsp;</td>
		<td class="td_savelist" style="width:40px;">&nbsp;</td>
		<td class="td_offername" style="width:465px;"><a class="tcol tcol_sortby asc" href="#" data-value="offername">Offer Name <span class="nobold">(Date Added)</span></a></td>
		<td class="td_payout" style="width:54px;"><a class="tcol tcol_sortby" href="#" data-value="payout">Payout</a></td>
		<td class="td_type" style="width:41px;"><a class="tcol tcol_sortby" href="#" data-value="type">Type</a></td>
		<td class="td_vertical" style="width:123px;"><a class="tcol tcol_sortby" href="#" data-value="vertical">Vertical</a></td>
		<td class="td_network" style="width:120px;"><a class="tcol tcol_sortby" href="#" data-value="network">Network</a></td>
		<td class="hhr">&nbsp;</td>
	</tr>
	
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="btable" id="j_otablecont">
	
		<?php $orow = <<<ROW
		<tr class="orow j_oid-1000" data-oid="1000" title="Click to expand or collapse this offer">
			<td class="border">&nbsp;</td>
			<td class="td_saved2list" style="width:15px;">
				<div class="icon icon_ovault_added2list" title="You have already saved this offer"></div>
			</td>
			<td class="td_savelist" style="width:40px;">
				<a class="btn ovault_add2list" href="#" data-offerid="1000" title="Add this offer to the active list">Add</a>
				<a class="btn ovault_add2list_select" href="#" data-offerid="1000" title="Select a list to add this offer to...">Select</a>
			</td>
			<td class="td_offername" style="width:465px;">
				<p>Shield Deluxe<span>12/12/2011</span></p>
			</td>
			<td class="td_payout" style="width:54px;">
				<p>$12.50</p>
			</td>
			<td class="td_type" style="width:41px;">
				<p>Lead</p>
			</td>
			<td class="td_vertical" style="width:123px;">
				<p>Shields &amp; Daggers</p>
			</td>
			<td class="td_network" colspan="2" style="120px">
				<p class="icon icon_nwmember">CPA Empire</p>
			</td>
			<!--<td class="tail">&nbsp;</td>-->
		</tr>
ROW;
		
		for($i=1; $i<=5; $i++)
			echo $orow; ?>
		<?php /*
		<tr class="orow expanded" title="Click to expand/collapse this offer">
			<td class="border">&nbsp;</td>
			<td class="td_saved2list">
				<div class="icon icon_ovault_added2list" title="You have already saved this offer"></div>
			</td>
			<td class="td_savelist">
				<a class="btn ovault_add2list" href="#" data-offerid="1000" title="Add this offer to the active list">Add</a>
				<a class="btn ovault_add2list_select" href="#" data-offerid="1000" title="Select a list to add this offer to...">Select</a>
			</td>
			<td class="td_offername">
				<p>Shield Deluxe<span>12/12/2011</span></p>
			</td>
			<td class="td_payout">
				<p>$12.50</p>
			</td>
			<td class="td_type">
				<p>Lead</p>
			</td>
			<td class="td_vertical">
				<p>Shields &amp; Daggers</p>
			</td>
			<td class="td_network" colspan="2">
				<p class="icon icon_nwmember">CPA Empire</p>
			</td>
			<!--<td class="tail">&nbsp;</td>-->
		</tr>
		
		<tr class="orowbig">
			<td class="border">&nbsp;</td>
			<td class="td_info" colspan="3">
				<div class="td_inner">
					<div class="floatleft">
						<a class="ovault_othumb" href="#" title="Click to view large">
							<img src="/Themes/BevoMedia/img_new/othumb_default.gif" alt="" /><!-- 245x125px -->
							<span></span>
						</a>
						<a class="btn ovault_importoffer" href="#">Import this offer into my network</a>
						<div class="clear"></div>
					</div>
					<div class="floatright">
						<h3>Shield Deluxe</h3>
						<small>Added 12/12/2011</small>
						
						<div class="otitle otitle_offerdesc"></div>
						<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
						<p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
						
						<div class="olink">
							<input type="text" class="formtxt" readonly value="http://google.com/" />
							<a class="btn ovault_visiticon" href="http://google.com/" title="Open link in a new tab" target="_blank">Visit</a>
						</div>
					</div>
					<div class="clear"></div>
				</div><!--close td_inner-->
			</td>
			<td class="td_nw" colspan="2">
				<div class="td_inner">
				
				<div class="otitle otitle_network noborder"></div>
				<div class="onwpic">
					<img class="nwpic w120" src="/Themes/BevoMedia/img/networklogos/uni/1068.png" alt="" title="Dadingo" />
					
					<p class="bordertop aligncenter">Publisher's Rating:<br />
						<?php 	for($i=1; $i<=5; $i++) { ?>
							<img src="/Themes/BevoMedia/img/star-on.gif" id="img_rating_top_month_1068_<?php echo $i?>" onmouseover="" onmouseout="" style="" align="absbottom" border="0" />
						<?php } ?> 
						
						<?php /* UNCOMMENT ONCE IMPLEMENTED, AND DELETE THE ABOVE!
							$this->Network1->rating = 5;
							for($i=1; $i<6; $i++) :
							if($this->Network1->rating >= $i){ $state = 'on'; }else{ $state = 'off'; } ?>
							
								
								
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
							
						<?php endfor 
						?>
						
						<a href="/BevoMedia/Publisher/Reviews.html?NetworkID=<?=$this->Network1->id?>">Network Reviews</a>
						* /?>
					</p><!--close publisher's rating-->
				</div><!--close div.onwpic-->
				
				<p>You're already a member of this network!</p>
				<div class="icon icon_ovault_nwmember_bigwhite"></div>
				<a class="btn ovault_gotomystats_trans" href="/BevoMedia/Offers/MyStats.html">Go to my stats</a>
				
				</div><!--close td_inner-->
			</td>
			<td class="td_nwdesc" colspan="3">
				<div class="td_inner">
	
				<div class="otitle otitle_networkdesc"></div>
				<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum.</p>
				<p>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>
				
				<div class="otitle otitle_latestnwreviews noborder"></div>
				<ul class="ovault_boxlist hastitle">
					<li>not a fan</li>
					<li>famous network, tons of offers</li>
					<li>The amount of help and solid advice they give out is awesome</li>
				</ul>
				</div><!--close td_inner-->
			</td>
			<!--<td class="tail">&nbsp;</td>-->
		</tr><!--close .orowbig-->
		*/ ?>
		
		<?php for($i=1; $i<=15; $i++)
			echo $orow; ?>
		
		<?php /*<tr>
			<td class="border">&nbsp;</td>
			<td class="td_savelist"></td>
			<td class="td_offername"></td>
			<td class="td_payout"></td>
			<td class="td_type"></td>
			<td class="td_vertical"></td>
			<td class="td_network"></td>
			<td class="tail">&nbsp;</td>
		</tr> */ ?>
		
	</table><!--close #j_otablecont-->
	
	<tr class="table_footer">
		<td class="hhl"></td>
		<td style="border-left: none;" colspan="7"></td>
		<td class="hhr"></td>
	</tr>
</table><!--close outer .btable-->
