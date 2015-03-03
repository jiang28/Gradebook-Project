<?php
if (count($args) == 0) {
?>
<h3>Choose an option below:</h3>

<ul>
<li><a href="by-student/">Rollback by student</a></li>
<li><a href="by-lab/">Rollback by lab</a></li>
<li><a href="by-assignment/">Rollback by assignment</a></li>
<!-- <li><a href="by-date/">Rollback by date</a></li> -->
</ul>

<?php
} elseif (count($args) == 1 && $args[0] == 'by-student') {
?>

<h3>Choose a student:</h3>
<p><em>Click a lab name to expand</em></p>

<ul class="lablist">
<?php
$students = db_query('
SELECT S.Student_id AS ID, S.Name AS Name, L.Name AS Lab
FROM Student AS S, Lab as L
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
	echo '    <li class="student"><a href="' . $student['ID'] . '/">' . $student['Name'] . ' (' . $student['ID'] . ')</a></li>';
}
if ($lab != false) echo '</ul>';
?>
</ul>

<?php
} elseif (count($args) == 1 && $args[0] == 'by-lab') {
?>

<h3>Choose a lab:</h3>

<ul>
<?php
$labs = db_query('
SELECT L.Name AS name, L.Lab_id as id
FROM Lab as L
ORDER BY L.Name ASC
');

foreach ($labs as $lab) {
	echo '<li><a href="' . $lab['id'] . '">' . $lab['name'] . ' (' . $lab['id'] . ')</a></li>';
}
?>
</ul>

<?php
} elseif (count($args) == 1 && $args[0] == 'by-assignment') {
?>

<h3>Choose an assignment:</h3>

<ul>
<?php
$assignments = db_query('
SELECT A.Assignment_id AS ID
FROM Assignment AS A');
foreach ($assignments as $assignment) {
  echo '  <li><a href="' . $assignment['ID'] . '/">' . $assignment['ID'] . '</a>';
}
?>
</ul>


<?php
} elseif (count($args) == 1 && $args[0] == 'by-date') {
?>

<h3>Choose a date range:</h3>

<?php
}

if (count($args) == 2) {

	$cond = false;
	if ($args[0] == 'by-student')
		$cond = 'GD.Student_id = ?';
	elseif ($args[0] == 'by-lab')
		$cond = 'S.Lab_id = ?';
	elseif ($args[0] == 'by-assignment')
		$cond = 'GD.Assignment_id = ?';

	$grades = db_query('
SELECT 
  S.Student_id AS student,
  S.Lab_id AS lab,
  GD.Assignment_id AS assignment,
  GD.Staff_id AS graded_by,
  GD.score AS grade,
  A.Max_Score AS out_of,
	GD.when_graded AS date
FROM 
  Grade_Details AS GD,
  Assignment AS A,
  Student AS S
WHERE
  GD.Assignment_id = A.Assignment_id
  AND GD.Student_id = S.Student_id
  AND ' . $cond . '
ORDER BY
  assignment ASC,
  date ASC
', $args[1]);
?>

<p><em>(Click a column header to sort)</em></p>

<table>
<thead>
<tr>
  <td>Rollback?
	<br />
	Select
	<a href="javascript:$(':checkbox').attr('checked', true)">all</a> /
	<a href="javascript:$(':checkbox').attr('checked', false)">none</a>
</td><td>Student</td><td>Lab</td><td>Assignment</td><td>Graded by</td><td>Grade</td><td>Date</td></tr>
</thead>
<tbody>
<?php
foreach ($grades as $grade) {
  echo '  <tr>
  <td><input name="rollback,' . $grade['student'] . ',' . $grade['assignment'] . ',' . $grade['graded_by'] . ',' . $grade['date'] . '" type="checkbox"></td>
  <td>' . $grade['student'] . '</td>
  <td>' . $grade['lab'] . '</td>
  <td>' . $grade['assignment'] . '</td>
  <td>' . $grade['graded_by'] . '</td>
  <td>' . $grade['grade'] . ' / ' . $grade['out_of'] . '</td>
  <td>' . $grade['date'] . '</td>
</tr>
';
}
?>

</tbody>
</table>

<p>
	<a href="javascript:submitRollback()">submit rollback</a>
</p>

<?php
}
?>

