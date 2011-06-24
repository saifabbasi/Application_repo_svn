

<style type="text/css">
	p { font-family: Arial; font-size: 13px; }
</style>
<h1><img src="/Themes/BevoMedia/img/networklogos/<?php print $this->network->id; ; ?>.png" alt="<?php print htmlentities($this->network->title); ; ?>" /></h1>

<?
	//if ($this->network->id==1038)
	if (1==0)
	{
?>
		Are you trying to install Commission Junction? <br /><br />
		We need a Commission Junction
		account with stats in it to test our system reporting. If you'd be
		interested in helping us out, please email us at <a href="mailto:help@bevomedia.com">help@bevomedia.com</a>.
<?
		return;
	}
?>

<p>Are you signed up with <?php print htmlentities($this->network->title); ; ?>?</p>
<p><a href="NetworkRedirect.html?network=<?php print urlencode($this->network->id); ; ?>" target="_top" style="font-size: 22px; font-weight: bold; color: #00cc00">Click here to apply to <?php print htmlentities($this->network->title); ; ?></a></p>
<p><a href="EditNetwork.html?network=<?php print $this->network->id; ; ?>" style="font-size: 11px;">No thanks, I'm already signed up and approved &raquo;</a></p>

<? if(!empty($_GET['apiKey'])) { ?>
  <br />
  <br />
  <h3>Add this network to my BevoLive account: <?=$this->User->email?></h3>
  You are entering these details on your Bevo Live account.
<? } ?>