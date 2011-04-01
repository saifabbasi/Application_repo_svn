<?php
$job = $this->db->fetchRow('select * from bevomedia_marketplace where id='.intval($_GET['id']));
				echo 'You have accepted this job, and agreed to the quoted price of <b>$'.$job->quotedPrice.'</b>';
				echo '<br />';
				echo '<a href="#" onclick="parent.window.location.reload();">Return to the marketplace to make a payment</a>';