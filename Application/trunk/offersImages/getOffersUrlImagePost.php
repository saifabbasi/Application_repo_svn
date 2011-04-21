<?
	require_once ('S3.php');
	
	$config = parse_ini_file('../config.ini', true);
	$databaseInfo = $config['Database/'.$config['Application']['Mode']];
	
	
	mysql_connect($databaseInfo['Host'], $databaseInfo['User'], $databaseInfo['Pass']);
	mysql_select_db($databaseInfo['Name']);

	
	$offerId = intval($_POST['offerId']);
	$networkId = intval($_POST['networkId']);
	$imageUrl = $_POST['imageUrl'];
	
	
	$imageSavePath = sys_get_temp_dir().'/networks/';
	@mkdir($imageSavePath, 0777, true);
	$imageSavePath .= $offerId.'.jpg';
	
	$data = file_get_contents($imageUrl);
	file_put_contents($imageSavePath, $data);
	
	$imageUrl = 'https://bevomedia-media.s3.amazonaws.com/offers/networks/'.$networkId.'/'.$offerId.'.jpg';	
	$sql = "UPDATE bevomedia_offers SET imageUrl = '{$imageUrl}' WHERE id = {$offerId}";
	mysql_query($sql);
	
	
	$s3 = new S3('AKIAJT2QDN6UELJQEQIQ', 'ZYDvHWw1Y+d/5NeShcWnrza7CwcIZ/cz/031Gz3T');
	$s3->putObjectFile($imageSavePath, 'bevomedia-media', 'offers/networks/'.$networkId.'/'.$offerId.'.jpg', S3::ACL_PUBLIC_READ);
	

	
?>

