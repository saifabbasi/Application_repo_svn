<table>
<tr>
<th>Date</th>
<th>Username</th>
<th>Amount Paid</th>
</tr>
<?php
foreach($this->orders as $o)
{
  ?><tr><td><?=date('Y-m-d', strtotime($o->created))?></td><td><?=$this->db->fetchOne('select email from bevomedia_user where id='.$o->user__id)?></td><td>$<?=number_format($o->paid, 2)?>
<? } ?>