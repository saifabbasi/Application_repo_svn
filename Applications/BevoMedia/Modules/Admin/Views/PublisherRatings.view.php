
<div align="right">
	<a href="/BevoMedia/Admin/PublisherRatings.html?ViewAll=1">View All</a>
</div>

<table class='adminPublisherTable' cellpadding=0 cellspacing=0>
<tr>
	<th>
		User
	</th>
	<th>
		Network
	</th>
	<th>
		Rating
	</th>
	<th>
		Comment
	</th>
	<th>
		Approved
	</th>
</tr>

<?php foreach($this->Ratings as $Key=>$Rating):?>
<tr class='<?php echo($Key%2)?'lightBlueRow':'darkBlueRow'?>'>
	<td>
		<a href="/BevoMedia/Admin/ViewPublisher.html?id=<?=$Rating->user__id?>"><?php print $Rating->firstName.' '.$Rating->lastName; ?></a>
	</td>
	<td class='nameCell'>
		<?php print $Rating->networkTitle; ?>
	</td>
	<td>
		<?
			for ($i=1; $i<=$Rating->rating; $i++)
			{
		?>
			<img src="/Themes/BevoMedia/img/star-on.gif" />
		<?
			}

			for ($i=$Rating->rating+1; $i<=5; $i++)
			{
		?>
			<img src="/Themes/BevoMedia/img/star-off.gif" />
		<?
			}
		?>
		
	</td>
	<td>
		<?php print $Rating->userComment; ?>
	</td>
	<td>
		<?
			if ($Rating->approved==0)
			{
		?>
		<a href="/BevoMedia/Admin/PublisherRatings.html?Approve=<?=$Rating->id?><?=isset($_GET['ViewAll'])?'&ViewAll=1':''?>">Approve</a>
		<?
			} else
			{
		?>
		<a href="/BevoMedia/Admin/PublisherRatings.html?Disapprove=<?=$Rating->id?><?=isset($_GET['ViewAll'])?'&ViewAll=1':''?>">Disapprove</a>
		<?
			}
		?>
	</td>
</tr>
<?php endforeach?>

<?php if(!sizeOf($this->Ratings)):?>
	<tr>
		<td class="textAlignCenter" colspan="10">
			<i>No Results</i>
		</td>
	</tr>
<?php endif?>
</table>
