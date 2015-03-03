<?php 
if (isset($_FILES['csvfile'])) {
	// get the import type
	if (count($args) >= 1 && $args[0] == 'assignment')
		$type = 'assignment';
	else
		$type = 'multiple';

	$file = fopen($_FILES['csvfile']['tmp_name'], 'r');
	if (!$file) {
		print_r($_FILES);
		echo '<p>Unable to upload file. Please try again later.</p>';
		exit(0);
	}

	$skipped = array();
	while (!feof($file)) {
		$list = fgetcsv($file);

		// skip comments and invalid lines
		if ($list[0][0] == '#'
				|| $list[0] == 'student'
				|| ($type == 'assignment' && count($list) != 2)
				|| ($type == 'multiple' && count($list) != 3)) {

			$skipped[] = implode(',', $list);
			continue;

		}

		// assign the rest
		if ($type == 'assignment') {
			$student = $list[0];
			$assignment = $args[1];
			$staff = $USER['username'];
			$score = $list[1];
			$when = strftime('%Y/%m/%d %H:%M:%S');
		} else {
			$student = $list[0];
			$assignment = $list[1];
			$staff = $USER['username'];
			$score = $list[2];
			$when = strftime('%Y/%m/%d %H:%M:%S');
		}

		// import the rest
		db_query('
INSERT INTO Grade_Details (Student_id, Assignment_id, Staff_id, score, when_graded)
VALUES (?, ?, ?, ?, ?)', $student, $assignment, $staff, $score, $when);
	}
?>

<p>File successfully imported.</p>

<?php if (count($skipped) > 0) { ?>
<p>The following lines were skipped:
<pre style="border: 1px solid black; padding: 1em;">
<?php foreach ($skipped as $line) echo "$line\n"; ?>
</pre>
<?php } ?>

<?php
} elseif (count($args) == 0) { 
?>

<p>Choose an option:</p>

<ul>
  <li><a href="assignment/">Enter grades for a specific assignment</a></li>
  <li><a href="multiple/">Enter grades for multiple assignments</a></li>
</ul>

<?php } elseif (count($args) == 1 && $args[0] == 'assignment') { ?>

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

<?php } elseif (count($args) == 2 && $args[0] == 'assignment') { ?>

<h3>Upload a CSV containing grades for '<?php echo $args[1]; ?>':</h3>

<p>The expected format is 'username,grade'. Empty lines and lines starting with # will be ignored.</p>

<form enctype="multipart/form-data" action="#" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<input name="csvfile" type="file" /><br />
<input type="submit" value="Upload File" />
<button onclick="window.location.href='..'">Cancel</button>
</form>

<?php } elseif (count($args) == 1 && $args[0] == 'multiple') { ?>

<h3>Upload a CSV containing grades:</h3>

<p>The expected format is 'username,assignment,grade'. Empty lines and lines starting with # will be ignored.</p>

<form enctype="multipart/form-data" action="#" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
<input name="csvfile" type="file" /><br />
<input type="submit" value="Upload File" />
<button onclick="window.location.href='..'">Cancel</button>
</form>

<?php } ?>
