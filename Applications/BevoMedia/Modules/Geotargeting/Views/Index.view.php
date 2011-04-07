<?php echo SoapPageMenu('geotargeting','geotargets','existing',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>


<br />

<?php
	foreach ($this->geotargets as $geotarget) {
?>
	<div class="geotarget-list-item">
		<a class="titleUrl" href="/BevoMedia/Geotargeting/EditGeotarget.html?ID=<?=$geotarget->ID?>"><?=$geotarget->Name;?></a>
		<div class="urls" geoId="<?=$geotarget->ID?>">
		<?php 
			foreach ($geotarget->urls as $url) {
				
				if ($url->DaytargetID==0) {
		?>
			<span><?=$url->URL?></span><br />
		<?php 	
				} else {
		?>
			<span>Daytarget: <?=$url->Name?></span><br />
		<?php 	
				}
			}
		?>
		</div>
		<div>
			<a href="#" class="delete" geoId="<?=$geotarget->ID?>">Delete</a>
		</div>
		<br clear="all" />
	</div>
<?php 
	}
?>

<script type="text/javascript">
	$(document).ready(function() {

		$('.geotarget-list-item').hover(
			function(){ $(this).css('background-color','#efefef') }, 
		    function(){ $(this).css('background-color','') }
		);

		$('.geotarget-list-item .urls').click(function() {
			window.location = '/BevoMedia/Geotargeting/EditGeotarget.html?ID='+$(this).attr('geoId');
		});

		$('.geotarget-list-item .delete').click(function() {
			if (!confirm('Are you sure you want to remove this Geotarget?')) return;

			$.get('/BevoMedia/Geotargeting/RemoveGeotarget.html?ID='+$(this).attr('geoId'));
			
			$(this).parent().parent().remove();		

			return false;	
		});
	});
</script>