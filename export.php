<?php
// database
require('php/db.php');

// user authentication
require('php/user.php');

// any sorts of submissions
require('php/submit.php');

// headers to write a csv file
header('Content-Description: File Transfer');
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="export.csv"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

// choose the query that we want
$cond = '?';
if (!isset($_REQUEST['type']))
	$_REQUEST['type'] = 'all';
if ($_REQUEST['type'] == 'assignment')
	$cond = 'GD.Assignment_id = ?';
elseif ($_REQUEST['type'] == 'student')
	$cond = 'GD.Student_id = ?';
elseif ($_REQUEST['type'] == 'lab')
	$cond = 'S.Lab_id = ?';
else {
	$cond = '?';
	$_REQUEST['which'] = '1 = 1';
}

// perform the query
$grades = db_query('
SELECT
  S.Lab_id AS lab,
  GD.Student_id AS student,
  GD.Assignment_id AS assignment,
  GD.score AS score
FROM
  Grade_Details AS GD,
  Student AS S
WHERE
  GD.Student_id = S.Student_id
  AND ' . $cond . '
ORDER BY
  lab ASC,
  student ASC,
  assignment ASC,
  GD.when_graded DESC
', $_REQUEST['which']);

echo "student,assignment,grade\n";
$last = false;
foreach ($grades as $grade) {
	$key = $grade['student'] . ',' . $grade['assignment'];
	if ($key != $last)  {
		echo $grade['student'] . ',' . $grade['assignment'] . ',' .  $grade['score'] . "\n";
	}
	$last = $key;
}
?>