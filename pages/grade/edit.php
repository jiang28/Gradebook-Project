<?php if (count($args) == 0) { ?>

<h3>Choose an assignment to modify:</h3>

<ul>
<?php
if ($USER['role'] == 'ADMIN') {
	$assignments = db_query('SELECT Assignment_id AS ID FROM Assignment');
} else {
	$assignments = db_query('
SELECT 
  Assignment_id AS ID
FROM
  Grade_Permissions as GP
WHERE
  GP.Staff_id = ?
  AND GP.type = "r-w"
ORDER BY 
  ID ASC
', $USER['username']);
}
foreach ($assignments as $assignment) {
  echo '  <li><a href="' . $assignment['ID'] . '/">' . $assignment['ID'] . '</a>';
}
?>
</ul>

<?php } elseif (count($args) == 1) { ?>

<h3>Choose a user to modify for assignment '<?php echo $args[0]; ?>':</h3>

<ul class="lablist">
<?php
$students = db_query('
SELECT S.Student_id AS ID, S.Name AS Name, L.Name AS Lab
FROM Student AS S, Lab as L
WHERE S.Lab_id = L.Lab_id
  AND EXISTS (
    SELECT * 
    FROM Grade_Details AS GD
    WHERE S.Student_id = GD.Student_id
      AND GD.Assignment_id = ?)
ORDER BY L.Name ASC, S.Student_id ASC
', $args[0]);

if (count($students) == 0) {
  echo '  <li>There are no students with grades for ' . $args[0] . '.</li>';
} else {
  $lab = false;
	foreach ($students as $student) {
    if ($student['Lab'] != $lab) {
			if ($lab != false) echo '</ul>';
			$lab = $student['Lab'];
      echo '<li class="lab">' . $lab . '<ul class="studentlist">';
		}
		echo '    <li class="student"><a href="' . $student['ID'] . '/">' . $student['Name'] . ' (' . $student['ID'] . ')</a></li>';
	}
	if ($lab != false) echo '</ul>';
}
?>
</ul>

<?php } else { ?>

<h3>Enter a new grade for '<?php echo $args[1]; ?>' on '<?php echo $args[0]; ?>':</h3>

<?php
$grade = db_query('SELECT score FROM Grade_Details WHERE Student_id = ? AND Assignment_id = ? ORDER BY when_graded DESC LIMIT 1', $args[1], $args[0]);
$out_of = db_query('SELECT Max_Score FROM Assignment WHERE Assignment_id = ?', $args[0]);
?>
Grade (out of <?php echo $out_of[0]['Max_Score']; ?>): <input id="grade" type="text" value="<?php echo $grade[0]['score']; ?>" />

<button onclick="submitUpdate({student: '<?php echo $args[1]; ?>', assignment: '<?php echo $args[0]; ?>', grade: $('#grade' ).val()})">Save</button>
<button onclick="window.location.href='..'">Cancel</button>

<?php } ?>
