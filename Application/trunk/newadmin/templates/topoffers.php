<?php include('templates/header.php'); ?>

<h1>Network of Month</h1>

<form method="post" action="topoffers.php?Action=Update">
<table class="ListingTable">
  <tr class="HeaderRow">
    <td colspan="2">Offer Code</td>
  </tr>
<?php ListOffers(); ?>
  <tr id="NewOffer" style="display: none;">
    <td><textarea name="Offer[]" cols="60" rows="3"></textarea></td>
	<td><a href="#NewOffer" onclick="this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);" /><img src="../images/delete.png" border="0"/></a></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="button" onClick="moreOffers();" value="New Offer"/>
  </tr>
</table>

<p align="center">
<input type="submit" value="Update"/>
</p>

</form>

<script>
function moreOffers() {
	var newFields = document.getElementById('NewOffer').cloneNode(true);
	newFields.id = '';
	newFields.style.display = 'inline';
	
	var insertHere = document.getElementById('NewOffer');
	insertHere.parentNode.insertBefore(newFields,insertHere);
}
</script>

<?php include('templates/footer.php'); ?>