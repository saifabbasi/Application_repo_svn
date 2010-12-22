
<div style="float: left; margin: 5px;">
  <span style="margin: 10px; font-size:14pt; font-weight: bold">SubID: <?=$this->click->subId?></span>
  <span style="margin: 10px;"><b>IP Address: </b><?=$this->click->ipAddress ?></span>
</div>
<div style="clear: both"></div>
<div style="float: left; margin: 5px;">
  <span style="margin: 10px;"><b>Conversions: </b><?=$this->click->conv ?></span>
  <span style="margin: 10px;"><b>Creative: </b><?=$this->click->creativeTitle?></span>
  <span style="margin: 10px;"><b>Ad Group: </b><?=$this->click->adgroupName ?></span>
  <span style="margin: 10px;"><b>Campaign: </b><?=$this->click->campaignName ?></span>
</div>
<div style="clear: both"></div>
<div style="float: left; margin: 5px;">
  <span style="margin: 10px;"><b>At: </b><?= $this->click->at ?></span>
  <span style="margin: 10px;"><b>Search Term: </b><?= $this->click->rawKeyword ?></span>
  <span style="margin: 10px;"><b>Optional Data: </b><?= $this->click->optional ?></span>
</div>
<div style="clear: both"></div>
<div style="float: left; margin: 5px;">
  <span style="margin: 10px;"><b>Referrer: </b><?= $this->click->referrerUrl ?></span>
  <span style="margin: 10px;"><b>Landing page: </b><?= $this->click->lp ?></span>
  
  
</div>
