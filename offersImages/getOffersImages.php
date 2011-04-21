<?
	$config = parse_ini_file('../config.ini', true);
	$databaseInfo = $config['Database/'.$config['Application']['Mode']];
	
	
	mysql_connect($databaseInfo['Host'], $databaseInfo['User'], $databaseInfo['Pass']);
	mysql_select_db($databaseInfo['Name']);
?>
<html>

<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
</head>

<body>

<?php

	$sql = "SELECT id, network__id, imageUrl, previewUrl FROM bevomedia_offers WHERE (imageUrl = '') AND (previewUrl <> '') LIMIT 100";
	$offers = mysql_query($sql);
	
	$websitesArray = array();
	while ($offer = mysql_fetch_assoc($offers))
	{
		$websitesArray[$offer['id']] = array('previewUrl' => $offer['previewUrl'], 'networkId' => $offer['network__id']);
	}
	
	foreach ($websitesArray as $key => $url)
	{
?>
		<iframe id="frame_<?=$key?>" offerId="<?=$key?>" offerPreviewUrl="<?=$url['previewUrl']?>" networkId="<?=$url['networkId']?>" src="/offersImages/getOffersInclude.php/?url=<?=urlencode($url['previewUrl'])?>"></iframe>
<?	
	}
?>

		<script type="text/javascript">
		
			$(window).bind('load', function()
			{

				$('iframe').each(function(index) {
					var imageUrl = ($(this).contents().find("img").attr('src'));
					
					$.post("/offersImages/getOffersUrlImagePost.php", { 'offerId': $(this).attr('offerId'), 'networkId': $(this).attr('networkId'), 'imageUrl': imageUrl });
				});

			});
		
		</script>

</body>
</html>
