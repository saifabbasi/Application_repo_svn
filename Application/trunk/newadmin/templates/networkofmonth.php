<?php include('templates/header.php'); ?>

<h1>Network of Month</h1>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Name</td>
	<td width="15%" align="center">Website</td>
	<td width="15%" align="center">Date</td>
  </tr>
<?php ListNetworkOfMonths(); ?>
</table>

<h2>New Network of Month</h2>

<form method="post" action="networkofmonth.php?Action=Create">
<table>
  <tr>
    <td><label for="Network">Network:</label></td>
	<td><select name="Network" id="Network" onchange=" $('#networkimg').show(); $('#networkimg').attr('src', 
'../Themes/BevoMedia/img/networkoffers/'+this.value+'.png');"><option value="0"></option><?php ListNetworkSelect(); ?></select></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
	<td><img id="networkimg" style="display: none;"></td>
  </tr>
  <tr>
    <td><label for="Content">Details:</label></td>
	<td><textarea name="Content" id="Content" cols="40" rows="5"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" align="center"><input type="submit" value="Create"/></td>
  </tr>
</table>
</form>

<?php include('templates/footer.php'); ?>
