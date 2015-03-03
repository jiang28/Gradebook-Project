<p>You are current logged in as <?php echo $USER['username']; ?>.</p>

<?php
if (count($args) == 0) {
?>
<h3>Choose an option below:</h3>

<ul>
<li><a href="view-all/"/>View all grades online</a></li>
<li><a href="view-assignment/">View Assignment</a></li>
<li><a href="View-Roster/">View Roster</a></li>
<!-- <li><a href="View-report/">Generate Report</a></li> -->
</ul>


<?php
    }if (count($args) == 1 && $args[0] == 'view-all') {
      $assignments = db_query('SELECT * FROM Grade_Details ORDER BY Assignment_id');
    // TODO: deal with permissions
    // TODO: remove labs that have no one to enter
    echo '<table>';
   echo '<thead><tr><td>Student id</td><td>Assignment id</td><td>Score</td></tr></thead><tbody>';    
   foreach ($assignments as $assignment) {
     echo '<tr><td>' . $assignment['Student_id']. '</td><td>' . $assignment['Assignment_id'] .'</td><td>'. $assignment['score'] . '</td></tr>';
   }
   echo '</tbody></table>';
 }
?>

<?php if(count($args)==1 && $args[0]=='view-assignment') { 
	$assignments = db_query('SELECT Distinct Assignment_id FROM Assignment');
   // TODO: deal with permissions                                             
   // TODO: remove labs that have no one to enter                             
   foreach ($assignments as $assignment) {
     echo '<li><a href="' . $assignment['Assignment_id']. '/">'.'<span>'.$assignment['Assignment_id'].'</span> </a></li>';
   }}
?>


<?php if(count($args)==1 && $args[0]=='View-Roster') {
   $assignments = db_query('SELECT Distinct Student_id FROM Student GROUP BY Student_id');
   // TODO: deal with permissions                                                                                                      
   // TODO: remove labs that have no one to enter                                                                                      
   foreach ($assignments as $assignment) {
     echo '<li><a href="' . $assignment['Student_id']. '/">'.'<span>'.$assignment['Student_id'].'</\
span> </a></li>';
   }}
?>     

<?php
if (count($args) == 2) {
  if($args[0]=='view-assignment'){
    $assignments = db_query('SELECT u.Student_id, u.score,u.Assignment_id FROM Grade_Details as u join(SELECT Student_id,max(when_graded) as timestamp,Assignment_id FROM Grade_Details group by Student_id,Assignment_id) as q where u.Student_id=q.Student_id and u.when_graded=q.timestamp and u.Assignment_id=q.Assignment_id and u.Assignment_id=?',$args[1]); 
    echo '<table>';
   echo '<thead><tr><td>Student id</td><td>Assignment id</td><td>Score</td></tr></thead><tbody>';
   foreach ($assignments as $assignment) {
     echo '<tr><td>' . $assignment['Student_id']. '</td><td>' . $assignment['Assignment_id'] .'</td><td>'. $assignment['score'] . '</t\
d></tr>';
   }
   echo '</tbody></table>';
  }
?>
<?php   
if($args[0]=='View-Roster'){
  echo $args[1];
  $assignments = db_query('SELECT u.Student_id, u.score,u.Assignment_id FROM Grade_Details as u join(SELECT Student_id,max(when_graded) as timestamp,Assignment_id FROM Grade_Details group by Student_id,Assignment_id) as q where u.Student_id=q.Student_id and u.when_graded=q.timestamp and u.Assignment_id=q.Assignment_id and u.Student_id=?',$args[1]);
  echo '<table>';
   echo '<thead><tr><td>Student id</td><td>Assignment_id</td><td>Score</td></tr></thead><tbody>';
   foreach ($assignments as $assignment) {
     echo '<tr><td>' . $assignment['Student_id']. '</td><td>' . $assignment['Assignment_id'] .'</td><td>'. $assignment['score'] . '</td></tr>';
   }
   echo '</tbody></table>';
}}
?>
