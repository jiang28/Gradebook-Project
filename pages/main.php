<p>You are currently logged in as <?php echo $USER['username']; ?>. Please choose an option below:</p>

<?php
if($USER['role'] == 'STUDENT') {

	$grades = db_query('
SELECT 
  GD.Assignment_id AS id,
  GD.when_graded AS date,
  GD.score AS grade,
  A.Max_Score AS out_of,
  A.Type AS type
FROM
  Grade_Details AS GD,
  Assignment AS A
WHERE
  GD.Assignment_id = A.Assignment_id
  AND GD.Student_id = ?
ORDER BY
  GD.Assignment_ID ASC,
  GD.when_graded DESC
', $USER['username']);

	$weights = array();
	foreach (db_query('
SELECT
  AT.Name AS type,
  AT.Weight AS weight
FROM
  Assignment_Type AS AT
') as $row) {
		$weights[$row['type']] = $row['weight'];

		$agg[$row['type']] = array();
		$agg[$row['type']]['score'] = 0;
		$agg[$row['type']]['out-of'] = 0;
		$agg[$row['type']]['count'] = 0;
	}  
?>

<table>
<thead>
<tr><td>Assignment</td><td>Grade entered</td><td>Grade</td></tr>
</thead>
<tbody>
<?php
	foreach ($grades as $grade) {
		if ($grade['id'] != $prev_id) {
			echo '  <tr><td>' . $grade['id'] . '</td><td>' . $grade['date'] . '</td><td>' . $grade['grade'] . ' / ' . $grade['out_of'] . '</td></tr>';

			$agg[$grade['type']]['score'] += $grade['grade'];
			$agg[$grade['type']]['out-of'] += $grade['out_of'];
			$agg[$grade['type']]['count'] += 1;
		}
		$prev_id = $grade['id'];
	}
?>
</table>

<br /><br /><br />

<table>
<thead>
<tr><td>Type</td><td>Score</td><td>Weight</td><td>Total</td></td>
</thead>
<tbody>
<?php
	$total = 0;
	foreach($agg as $key => $val) {
		if ($val['out-of'] == 0)
			$weighted = $weights[$key];
		else
			$weighted = $weights[$key] * $val['score'] / $val['out-of'];

		$total += $weighted;

		echo '<tr' . ($val['out-of'] == 0 ? ' class="unassigned"' : '') . '>
  <td>' . $key . '</td>
  <td>' . $val['score'] . ' / ' . $val['out-of'] . '</td>
  <td>' . $weights[$key] . '</td>
  <td>' . round($weighted * 100, 2) . '%</td>
</tr>
';

	}
		echo '</tbody>
<tfoot>
<tr>
  <td>Total</td>
  <td></td>
  <td></td>
  <td>' . round($total * 100, 2) . '%</td>
</tr>
</tfoot></table>
';
?>

<?php
}
if($USER['role'] == 'STAFF' || $USER['role'] == 'ADMIN') {  
?>
<h3>Staff tasks</h3>
<ul>
	<li><a href="grade/enter/">Enter grades online</a></li>
	<li><a href="grade/edit/">Edit grades online</a></li>
	<li><a href="grade/import/">Import grades from a file</a></li>
	<li><a href="grade/export/">Export grades to a file</a></li>
	<li><a href="grade/view/">View grades<!-- / generate reports--></a></li>
</ul>
<?php 
}
if ($USER['role'] == 'ADMIN') {
?>
<h3>Administrator tasks</h3>
<ul>
	<li><a href="admin/rollback/">Rollback incorrect grades</a></li>
	<li><a href="admin/assignments/">Modify assignments</a></li>
	<li><a href="admin/staff/">Modify course staff</a></li>
	<li><a href="admin/permissions/">Modify permissions</a></li>
</ul>
<?php
}
?>
<h3>Other tasks</h3>
<ul>
	<li><a href="logout">Logout</a></li>
</ul>
