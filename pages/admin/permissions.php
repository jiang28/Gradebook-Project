<?php if (count($args) == 0) { ?>

<h3>Choose a staff member to assign permissions:</h3>

<ul>
<?php
$staff=db_query("Select Staff_id,Name from Staff where role='AI' or role='Instructor'");
foreach ($staff as $each) {
  echo '  <li><a href="' . $each[0] . '/">' . $each[1] . ' (' . $each[0] . ')</a>';
}
?>
</ul>

<?php } elseif (count($args) == 1) { ?>

<h3>Choose permissions for "<?php echo $args[0]; ?>":</h3>

<?php
$labs = array('Permissions');
$asns = db_query('Select Assignment_id from Assignment');
$permissions=db_query('Select Assignment_id from Grade_Permissions where Staff_id=? and type=\'r-w\'',$args[0]);
?>
<table id="permissionTable">
<thead>
<tr><td>Assignment</td><td>Permission</td></tr>
</thead>
<tbody>
<?php
foreach ($asns as $row) {
  echo '<tr><td>'.$row['Assignment_id'].'</td>';
  foreach ($labs as $lab){
		$text='read';
		foreach($permissions as $p){
			if (strcasecmp($row['Assignment_id'],$p['Assignment_id'])==0){
  					$text='write';
					break;
					}
}
echo '<td><button name="'.$row['Assignment_id'].'">'.$text.'</button></td>';
}
  echo "</tr>";
}
?>
</tbody>
</table>
<button onclick="javascript:savePermissions('<?php echo $args[0]; ?>')">Save</button>
<button "window.location.href='..'">Cancel</button>
<?php } ?>

