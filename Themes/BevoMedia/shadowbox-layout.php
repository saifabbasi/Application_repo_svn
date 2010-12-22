<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title><?=$this->{'Instance/PageTitle'};?></title>
<meta name="keywords" content="<?=$this->{'Instance/PageKeywords'};?>" />
<meta name="description" content="<?=$this->{'Instance/PageDescription'};?>" />
<script language='javascript'>
var modToday = 'today';
var modYesterday = 'yesterday';
</script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Functions.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Ajax.js" type="text/javascript"></script>
<script src="<?=$this->{'System/BaseURL'};?>JS/Lock.js" type="text/javascript"></script>
<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>JS/charts/jquery-1.4.2.min.js"></script>
<link href="<?=$this->{'System/BaseURL'};?>CSS/Application.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/main.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/style.css" rel="stylesheet" type="text/css" />
<link href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/default.css" rel="stylesheet" type="text/css" />
<script src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/LayoutAssist.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.css">
<script type="text/javascript" src="<?=$this->{'System/BaseURL'};?>Themes/<?=$this->{'Application/Theme'};?>/shadowbox-source-3.0b/shadowbox.js"></script>
<script type="text/javascript">
	Shadowbox.init({
	    language: 'en',
	    players:  ['html', 'iframe']
	}); 

function ParentToSelfhost(link) {
  p = parent.window.location;
  l = p.protocol + "//" + p.hostname + link.href;
  alert(l);
  parent.window.location  = l;
  return false;
}
function ParentToBevoLive(link) {
	objURL = new Object();
	apiKey = false;
	link.href.replace(new RegExp( "([^?=&]+)(=([^&]*))?", "g" ), function(all,key,eq,val) { if(key=='apiKey') apiKey=val; objURL[key] = val;});
	// If they don't have an API key, don't show the Bevo redirect notice
	if(apiKey == false)
	  return true;
	$('#shadowbox-body').fadeOut(1500, function() {
		msg = $('<h2>').html("You are being redirected to BevoMedia.com");
		center = $('<center>').html(msg);
		$('#shadowbox-body').empty().html(center);
		$('#shadowbox-body').fadeIn(500, function() { 
			setTimeout(function() {
			parent.window.location = link.href;
			}, 5000);
		});
	});
	return false;
}
</script>
</head>

<body id='shadowbox-body'>

 	<?=$this->{'Instance/ViewContent'};?>

</body>

</html>