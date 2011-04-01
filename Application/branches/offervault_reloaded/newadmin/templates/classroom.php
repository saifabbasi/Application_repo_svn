<?php include('templates/header.php'); ?>

<?php include('templates/classroomheader.php'); ?>

<h1>Classroom</h1>

<h2>Chapter Sections</h2>

<table class="ListingTable">
  <tr class="HeaderRow">
    <td>Title</td>
  </tr>
<?php ListSections(); ?>
</table>

<?php include('templates/footer.php'); ?>