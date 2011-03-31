<?php echo SoapPageMenu('geotargeting','geotargets','existing',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>



<script type="text/javascript">
	var countries = <?php echo json_encode($this->Countries); ?>
</script>

<br />


<form method='post' class="geotargeting">
	
	<label>
		<span  style="width: 50px; display: inline-block;">Name: </span>
		<input class="formtxt" name='name' type='text' value='<?php echo $this->Name;?>'>			
	</label>
	
	<br /><br />
	
	<a id="AddLandingPage" href="#">Add Landing Page</a>
	
	<br /><br />
	
	<div id="LandingPages">
		
	</div>
	
	<input type="submit" class="edit" id="Submit" name="Submit" value="" />
	
</form>

<br /><br />

<div style="font-weight: bold; font-size: 14px;">Geotarget Code</div>
<div>Please place this coding on a blank php file. Viewers will then be redirected accordingly when they access the page.</div>
<textarea class="code" style="width: 100%; margin: 0px; line-height: 20px; padding: 0px; padding-top: 20px;" wrap="off"><?php 
		$Code = "<script type=\"text/javascript\" src=\"http://track.bevomedia.com/geo/{$_GET['ID']}/{$this->User->id}/".'<?=$_SERVER["REMOTE_ADDR"]?>/<?=urlencode(urlencode(base64_encode(gzcompress(serialize($_GET),9))))?>'."\"></script>";
		echo htmlentities($Code);
	?></textarea>

<br />

	