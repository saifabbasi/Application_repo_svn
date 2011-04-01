<div style="z-index:10000; position: fixed; left: 0px; top: 0px; padding-top: 48px; width: 100%; height: 100%; text-align: center;">
	<div style="background: url(/Themes/BevoMedia/img/LightboxTemplate_Final<?php echo(isset($this->Presets[$this->PresetItem]['DONE']))?'_Done':''?>.png); width: 640px; height: 480px; display: block; margin: auto;">
		<div style="text-align: center; width: 505px; margin-left: 70px; padding-top: 80px; height: 270px;">
		<h3 style="margin-top: 5px; color: #273b98; font-size: 22px; font-family: Arial;">
			<?php print $this->Presets[$this->PresetItem]['TITLE']; ?>
		</h3>
		
		<br/>
		
		<span style="line-height: 130%; font-size: 15px; font-family: Tahoma;">
			<?php print $this->Presets[$this->PresetItem]['CONTENT']; ?>
		</span>
		
		</div>

		<a <?php echo(isset($this->Presets[$this->PresetItem]['DONE']))?'onClick="Close();"':''?> href='<?php print $this->Presets[$this->PresetItem]['LINK']; ?>' style="width: 185px; height: 80px; margin-left: 390px; margin-top:5px; display: block;"></a>
	</div>
	<a href='#' onClick='javascript:firstlogin.close();'>Cancel Tutorial</a>
</div>

<script type="text/javascript">

	function Close() {
		
		firstlogin.close();

		Shadowbox.open({
	        content:    '/BevoMedia/Publisher/VerifyTutorial.html?ajax=true',
	        player:     "iframe",
	        title:      "Verify",
	        height:     480,
	        width:      640
	    });

	}

</script>
