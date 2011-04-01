<?php echo SoapPageMenu('timetargeting','timetargets','existing',true); ?>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper); ?>


<br />

<?php
	foreach ($this->timetargets as $timetarget) {
?>
	<div class="geotarget-list-item">
		<a class="titleUrl" href="/BevoMedia/Timetargeting/EditTimetarget.html?ID=<?=$timetarget->ID?>"><?=$timetarget->Name;?></a>
		<div class="urls" timeId="<?=$timetarget->ID?>">
		<?php 
			foreach ($timetarget->urls as $url) {
		?>
			<span><?=$url->URL?></span><br />
		<?php 	
			}
		?>
		</div>
		<div>
			<a href="#" class="delete" timeId="<?=$timetarget->ID?>">Delete</a>
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
			window.location = '/BevoMedia/Timetargeting/EditTimetarget.html?ID='+$(this).attr('timeId');
		});

		$('.geotarget-list-item .delete').click(function() {
			if (!confirm('Are you sure you want to remove this Timetarget?')) return;

			$.get('/BevoMedia/Timetargeting/RemoveTimetarget.html?ID='+$(this).attr('timeId'));
			
			$(this).parent().parent().remove();		

			return false;	
		});
	});
</script>