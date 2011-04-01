<?php

require('include.php');
require(PATH.'classes/clsClassroomSections.php');
require(PATH.'classes/clsClassroomChapters.php');
require('auth.php');

function ListSections() {
	$objSections = new ClassroomSections();
	$objSections->GetList();
	$blnAltRow = false;
	
	if ($objSections->RowCount == 0) {
		return false;
	}
	
	while ($arrThisRow = $objSections->GetRow()) {
?>
  <tr <?php if ($blnAltRow) { echo 'class="AltRow"'; } ?>>
    <td><a href="section.php?ID=<?php echo $arrThisRow['id']; ?>"><?php echo $arrThisRow['title']; ?></a></td>
  </tr>
<?php
		$blnAltRow = !$blnAltRow;
	}
}

$strPageTitle = 'Classroom';
include('templates/classroom.php');

?>
