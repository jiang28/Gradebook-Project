<?php if (count($args) == 0) { ?>

<h3>Export by:</h3>

<ul>
  <li><a href="assignment/">Assignment</a></li>
  <li><a href="student/">Student</a></li>
  <li><a href="lab/">Lab</a></li>
  <li><a href="<?php echo $BASE_URL; ?>export.php?type=all">All</a></li>
</li>

<?php } elseif (count($args) == 1 && $args[0] == 'assignment') { ?>

<h3>Choose an assignment to export:</h3>

<ul>
<?php
$assignments = db_query('
SELECT A.Assignment_id AS ID
FROM Assignment AS A');
foreach ($assignments as $assignment) {
  echo '  <li><a href="' . $BASE_URL . 'export.php?type=assignment&which=' . $assignment['ID'] . '">' . $assignment['ID'] . '</a>';
}
?>
</ul>

<?php } elseif (count($args) == 1 && $args[0] == 'student') { ?>

<h3>Choose a student to export:</h3>

<ul class="lablist">
<?php
$students = db_query('
SELECT S.Student_id AS ID, S.Name AS Name, L.Name AS Lab
FROM Student AS S, Lab AS L
WHERE S.Lab_id = L.Lab_id
ORDER BY L.Name ASC, S.Student_id ASC
');

$lab = false;
foreach ($students as $student) {
	if ($student['Lab'] != $lab) {
		if ($lab != false) echo '</ul>';
		$lab = $student['Lab'];
		echo '<li class="lab">' . $lab . '<ul class="studentlist">';
	}
	echo '    <li class="student"><a href="' . $BASE_URL . 'export.php?type=student&which=' . $student['ID'] . '">' . $student['Name'] . ' (' . $student['ID'] . ')</a></li>';
}
if ($lab != false) echo '</ul>';
?>
</ul>

<?php } elseif (count($args) == 1 && $args[0] == 'lab') { ?>

<h3>Choose a lab to export:</h3>

<ul>
<?php
$labs = db_query('
SELECT L.Name AS name, L.Lab_id as id
FROM Lab as L
ORDER BY L.Name ASC
');

foreach ($labs as $lab) {
	echo '<li><a href="' . $BASE_URL . 'export.php?type=lab&which=' . $lab['id'] . '">' . $lab['name'] . ' (' . $lab['id'] . ')</a></li>';
}
?>
</ul>

<?php } elseif (count($args) == 1 && $args[0] == 'all') { ?>

... export all ...


<?php } else { ?>

... export <?php echo $args[1]; ?> from <?php echo $args[0]; ?> ...

<?php } ?>
