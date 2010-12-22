<?php if($this->PostComplete === true):?>
	<?php foreach($this->OfferGroups as $OfferGroup):?>
		Label:
		<?php print $OfferGroup->label; ?>
		<br/>
	<?php endforeach?>
	
	<script language='javascript'>
		Output = "";
		<?php foreach($this->OfferGroups as $OfferGroup):?>
		Output += "<option <?php echo($OfferGroup->id == $this->OfferLinkID)?"selected":''?> onClick='document.getElementById(\"offer\").disabled=\"disabled\"; document.getElementById(\"offer\").value=\"ROTATE.<?php print $OfferGroup->id; ?>\"'><?php print $OfferGroup->label; ?>";
		<?php endforeach?>
		parent.document.getElementById('offerRotatorSelectionBox').innerHTML = Output;
		parent.document.getElementById('offer').value="ROTATE.<?php print $this->OfferLinkID; ?>";
		parent.document.getElementById('offer').disabled = "DISABLED";
		parent.Shadowbox.close();
	</script>
	<?php print_r($OfferGroup)?>
<?php endif?>
<?php print $this->offerLinkID; ?>
<style type='text/css'>
fieldset {padding: 5px;}
legend {font-size: 12px; font-weight: bold; font-family: Tahoma;}
p.descript {float: right; width: 60%;}

.colN {width: 30%; float: left; margin: 0px;}
.colS {width: 15%; float: left; margin: 0px;}

</style>

<h1>Create New Offer Rotation</h1>

<br/>
	
	<div id='offer-link-clone' style='display:none;'>
		<div class='colN'>
			<input type='text' name='link[]' value="http://" />
		</div>
		<div class='colS'>
			<input type='text' size='2' value='1' onkeydown='javascript: updatePcts();' onkeyup='javascript: updatePcts();' name='ratio[]'/>
		</div>
		<div class='colN'>
			<input type='text' size='4' name='pct[]' disabled='disabled' value='100%'/>
		</div>
		<div>
			<button onclick="removeLink(this); return false;" name='delete[]'>Delete</button>
		</div>
	</div>

<form method='post'>

<fieldset>
	<legend>Offer Group</legend>
	
	<p class='descript'>
		Set a label for the group of links you are going to create so that you can easily reference it later on.
	</p>
	<label>Offer Group Label<br/>
	<input name='groupLabel' type='text'>
	</label>

</fieldset>

<br/>

<fieldset>
	<p>Add links to this offer rotator group below. Set a ratio for how much each link should be displayed.</p>
	<br/>
	
	<legend>Offer Links</legend>
	
	
	<div class='colN'>
		<b>Link</b>
	</div>
	<div class='colS'>
		<b>Ratio</b>
	</div>
	<div class='colN'>
		<b>Percentage Shown</b>
	</div>
	<div class='colS'>
		<b>Remove</b>
	</div>
	
	<br class='clearBoth'/>
	<div id='offer-link-container'>
	</div>
	
	<br/><br/>
	<button onclick="addLink(); return false;">Add Link</button>
	
	<br/><br/>
	
	<input type='submit'/>
</fieldset>

</form>

<script language='javascript'>
function removeLink(s)
{
	var r = s.parentNode.parentNode;
	r.parentNode.removeChild(r);
}
function addLink()
{
	var s = document.getElementById('offer-link-clone');
	var c = document.getElementById('offer-link-container');
	var add = document.createElement('div');
	add.innerHTML = s.innerHTML;
	c.appendChild(add);
	updatePcts();
}

function updatePcts()
{
	var s = document.forms[0];
	var total = 0;
	for(var i in s["ratio[]"])
	{
		var itm = s["ratio[]"][i];

		if(itm == undefined)
			continue;
		
		if(itm.name !== "ratio[]")
			continue;

		var val = itm.value;
		if(val == '')
			val = 0;
		
		total += parseInt(val);
	}

	for(var i in s["ratio[]"])
	{
		var itm = s["ratio[]"][i];

		if(itm == undefined)
			continue;
		if(itm.name !== "ratio[]")
			continue;

		s["pct[]"][i].value = Math.round((itm.value / total)*100) + "%";
	}
}

addLink();

</script>