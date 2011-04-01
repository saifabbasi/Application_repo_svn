<div id="pagemenu">
	<ul>
		<li><a class="active" href="/BevoMedia/Marketplace/">Marketplace<span></span></a></li>
		<li><a href="/BevoMedia/Marketplace/MentorshipProgram.html">Mentorship Program<span></span></a></li>
		<?php if($this->User->membershipType != 'premium')
			echo '<li><a href="/BevoMedia/Marketplace/Premium.html">Get Bevo Premium<span></span></a></li>';?>
	</ul>
</div>
<?php echo $this->PageDesc->ShowDesc($this->PageHelper,false); ?>