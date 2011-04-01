<?php /* ##################################################### OUTPUT ############### */ ?>

	<?php echo SoapPageMenu('kwt','codes','existing');
	echo $this->PageDesc->ShowDesc($this->PageHelper); ?>

<?php 
function shorten($text, $len = 30)
{
	if(strlen($text) > $len)
	{
		return substr($text, 0, $len) . '...';
	}else{
		return $text;
	}
}


?>

<table width="100%">
<tr>
	<th>
		Landing Page
	</th>
	<th class="textAlignCenter">
		Destination URL
	</th>
	<th class='textAlignCenter'>
		&nbsp;
	</th>
</tr>
<?php foreach($this->codeRows as $Key=>$Row):?>
<tr style="<?php if($Key%2==0):?>background-color:#dfdfdf;<?php endif?>">
	<td style="padding-top: 5px; padding-bottom: 5px;">
		<?php print $Row->landingPage; ?>
	</td>
	<td style='padding-top: 5px; padding-bottom: 5px;'>
		<span title="<?php print htmlentities($Row->destinationUrl); ?>">
		<?php print shorten($Row->destinationUrl); ?>
		</span>
	</td>
	<td class="textAlignCenter">
		<a rel="shadowbox;width=640;height=480;player=iframe" href='/BevoMedia/KeywordTracker/CreatedCode.html?ID=<?php print $Row->id; ?>'>View</a>
		 &nbsp; &nbsp; 
		<a href="/BevoMedia/KeywordTracker/Code.html?Load=<?php print $Row->id; ?>">
			Create&nbsp;Similar
		</a>
		<a href="/BevoMedia/KeywordTracker/Code.html?delete=<?php print $Row->id; ?>">
			Delete
		</a>
	</td>
</tr>
<?php endforeach?>
</table>