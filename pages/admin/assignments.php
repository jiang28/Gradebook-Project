<?php 

if (count($args) == 0) { ?>

<h3>Choose an assignment to modify:</h3>

<table>
<thead>
<tr><td>Assignment</td><td>Type</td><td>Out Of</td><td>Due Date</td><td></td></tr>
</thead>
<tbody>
<?php $data = db_query('Select * from Assignment');
foreach ($data as $row) {
  echo '<tr><td>'.$row['Assignment_id'].'</td>';
  echo '<td>'.$row['Type'].'</td>';
  echo '<td>'.$row['Max_Score'].'</td>';
  echo '<td>'.$row['Due_date'].'</td>';

 echo '<td> <button onclick="window.location.href += \'' . $row['Assignment_id'] . '/\'" class="small edit"><span>Edit</span></button> ';
  echo '<button onclick=" submitUpdate({type:\'delete\', assignment_id: \'' . $row['Assignment_id'] . '\'})" class="small delete"><span>Delete</span></button></td></tr>';
  echo "\n";
}
?>

</tbody>
</table>

<?php } else {
$arg1 = $args[0];
?>

<?php
$out_of = db_query('SELECT Max_Score,Due_date,Type FROM Assignment WHERE Assignment_id = ?', $arg1);
?>

<h3>Enter the new details for <?php echo $out_of[0]['Type']; ?> : '<?php echo $arg1; ?>' </h3>

Max Score<input id="Max_Score" type="text" value="<?php echo $out_of[0]['Max_Score']; ?>" />
Due date<input id="Due_date" type="text" value="<?php echo $out_of[0]['Due_date']; ?>" /><br>

<button onclick="submitUpdate({type: 'edit', assignment_id: '<?php echo $arg1; ?>', Max_Score: $('#Max_Score').val(), Due_date: $('#Due_date').val()})">Save</button>
<button onclick="window.location.href='..'">Cancel</button>

<?php } ?>

