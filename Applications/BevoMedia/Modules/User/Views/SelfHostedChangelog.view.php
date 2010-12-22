<h1>Selfhost Changelog</h1>
<table>
  <tr>
	<th>Release date</th>
	<th>Version</th>
	<th>Changes</th>
  </tr>
<?php foreach($this->hist as $h)
{
?>
<tr>
  <td><?=date('Y-m-d', strtotime($h->release_date))?></td>
  <td><?=$h->id?></td>
  <td><pre><?=$h->changelog?></pre></td>
</tr>
<?php } ?></table>