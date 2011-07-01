<?php $s = empty($_SERVER['HTTPS']) ? '' : 's'; ?>
<div id="pageinfo" class="home"><p>Bevo Media is a state of the art internet advertising consolidation platform which consolidates all of your affiliate marketing efforts.  The BeVo Media Platform effectively reduces the hardest and most time consuming aspects of affiliate marketing.  BeVo Media literally lets publishers handle any and all of their needs from one central interface! </p></div>

<div class="clear"></div>

<div id="vidbox">
	<div id="movie">
		<object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="305" height="268">
		<param name="movie" value="http<?=$s?>://bevomedia-media.s3.amazonaws.com/mediaplayer-viral/player-viral.swf" />
		<param name="allowfullscreen" value="true" />
		<param name="allowscriptaccess" value="always" />
		<param name="flashvars" value="file=http<?=$s?>://bevomedia-media.s3.amazonaws.com/BevoVid_320x240.flv&image=http<?=$s?>://bevomedia-media.s3.amazonaws.com/mediaplayer-viral/bevo-background-v1.1.jpg" />
		<embed
			type="application/x-shockwave-flash"
			id="player2"
			name="player2"
			src="http<?=$s?>://bevomedia-media.s3.amazonaws.com/mediaplayer-viral/player-viral.swf" 
			width="305" 
			height="268"
			allowscriptaccess="always" 
			allowfullscreen="true"
			flashvars="file=http<?=$s?>://bevomedia-media.s3.amazonaws.com/BevoVid_320x240.flv&image=http://bevomedia-media.s3.amazonaws.com/mediaplayer-viral/bevo-background-v1.1.jpg" 
		/>
	</object>
	</div>
</div>

<?php 
	if (isset($_GET['OfferLogin']))
	{
?>

<script type="text/javascript">

	$(document).ready(function() {
		Shadowbox.open({
	        content:    '/BevoMedia/Offers/OfferLogin.html'+window.location.hash,
	        player:     "iframe",
	        title:      "OfferHub",
	        height:     480,
	        width:      640
	    });
	});
		
</script>

<?php 
	}
?>
