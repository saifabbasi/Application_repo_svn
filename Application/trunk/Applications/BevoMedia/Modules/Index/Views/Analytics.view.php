	<div id="pageinfo" class="analytics"><p>BeVo Media lets publishers integrate their Google analytics account onto the BeVo Platform.  All of the important information webmasters rely on with their analytics account is easily accessible through the BeVo interface.  Quickly check your web site stats as you manage your advertising from the BeVo Interface.</p></div>

	<div class="clear"></div>

	<div id="vidbox" class="analytics">
		<div id="movie">
		
			<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/rotators/js/swfobject.js"></script>
			
			<script type="text/javascript">
				
				// JAVASCRIPT VARS
				// cache buster
				var cacheBuster = "?t=" + Date.parse(new Date());
				// stage dimensions		
				var stageW = "304";//"100%"; 560
				var stageH = "267";//"100%"; 374
				
				
				// ATTRIBUTES
				var attributes = {};
				attributes.id = 'FlabellComponent';
				attributes.name = 'FlabellComponent';

				// PARAMS
				var params = {};
				params.bgcolor = "#ffffff";
				params.menu = "false";
				params.scale = 'noScale';
				params.wmode = "opaque";
				params.allowfullscreen = "true";
				params.allowScriptAccess = "always";			
				
				
				/* FLASH VARS */
				var flashvars = {};
				
				/// if commented / delete these lines, the component will take the stage dimensions defined 
				/// above in "JAVASCRIPT SECTIONS" section or those defined in the settings xml
				flashvars.componentWidth = stageW;
				flashvars.componentHeight = stageH;
				
				/// path to the content folder(where the xml files, images or video are nested)
				/// if you want to use absolute paths(like "http://domain.com/images/....") then leave it empty("")			
				flashvars.pathToFiles = "<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/rotators/5/banner/";
				
				// path to content XML
				flashvars.xmlPath = "<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/rotators/5/banner/xml/banner.xml";
				
				
				/** EMBED THE SWF**/
				swfobject.embedSWF("<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/rotators/preview.swf"+cacheBuster, attributes.id, stageW, stageH, "9.0.124", "<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/rotators/js/expressInstall.swf", flashvars, params, attributes);
				
			</script>
			
	
				
			<!-- this div will be overwritten by SWF object -->		
			<div id="FlabellComponent">
				<p>In order to view this object you need Flash Player 9+ support!</p>
				<a href="http://www.adobe.com/go/getflashplayer">
					<img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player"/>
				</a>
			</div>
		
		</div>
	</div>
