<?php
function onSubmit($page, $args) {
	$USER = $GLOBALS['USER'];
	$result = array();
	$result['success'] = false;
	$result['reason'] = "The gremlins got it.";

	try {

		// enter new grades
		// todo: check permissions
		if ($page == 'grade/enter') {
			db_query('
INSERT INTO Grade_Details (Student_id, Assignment_id, Staff_id, score, when_graded)
VALUES (?, ?, ?, ?, ?)
', $args['student'], $args['assignment'], $USER['username'], $args['grade'], strftime('%Y/%m/%d %H:%M:%S'));
			$result['success'] = true;
		}

		// edit old grades
		// todo: check permissions
		elseif ($page == 'grade/edit') {
			db_query('
INSERT INTO Grade_Details (Student_id, Assignment_id, Staff_id, score, when_graded)
VALUES (?, ?, ?, ?, ?)
', $args['student'], $args['assignment'], $USER['username'], $args['grade'], strftime('%Y/%m/%d %H:%M:%S'));
			$result['success'] = true;
		}
		elseif($page == 'admin/staff')
			{
				
				if($USER['role']!='ADMIN')
					{
						
						$result['success'] = false;
						$result['reason'] = 'You don\'t have permission to do that.';
						
					}
				elseif($args['param']==0)
					{
						//$staff_id=$args['Staff_id'];
						db_query('DELETE FROM Staff where Staff_id = ?',$args['Old_Staff_id']);
						db_query('DELETE FROM GRADE_PERMISSIONS where Staff_id=?',$args['Old_Staff_id']);
						db_query('DELETE FROM GRADE_Details where Staff_id=?',$args['Old_Staff_id']);
						db_query('DELETE FROM Lecture where Staff_id=?',$args['Old_Staff_id']);
						//echo 'result';
						$result['success'] = true;
						
					}
				elseif($args['param']==1)
					{
						db_query('Update Staff set Staff_id=?,Name=?,Role=? WHERE Staff_id=?',$args['Staff_id'],$args['Name'],$args['Role'],$args['Old_Staff_id']);
						db_query('Update Grade_Details set Staff_id=? where Staff_id=?',$args['Staff_id'],$args['Old_Staff_id']);
						db_query('Update Grade_Permissions set Staff_id=? where Staff_id=?',$args['Staff_id'],$args['Old_Staff_id']);
						db_query('Update Lecture set Staff_id=? where Staff_id=?',$args['Staff_id'],$args['Old_Staff_id']);
						db_query('Update Staff_Lab_Participation set Staff_id=? where Staff_id=?',$args['Staff_id'],$args['Old_Staff_id']);
						$result['success'] = true;
					}
			}

		elseif ($page == 'admin/permissions') {
			// check permissions
			if ($USER['role'] != 'ADMIN') {
				$result['success'] = false;
				$result['reason'] = 'You don\'t have permission to do that.';
			} else {
				foreach (explode(';', $args['Permissions']) as $Permission) {
					$parts = explode('=', $Permission);
					
					if ($parts[1] == 'read') {
						$text='r-r';
					}	elseif ($parts[1] == 'write') {
						$text='r-w';
					}
			
					if (count(db_query('SELECT * FROM Grade_Permissions WHERE Staff_id = ? AND Assignment_id = ?', $args['Staff'], $parts[0])) == 0) {
						db_query('
INSERT INTO Grade_Permissions (type, Staff_id, Assignment_id)
VALUES (?, ?, ?)
', $text, $args['Staff'], $parts[0]);
					} else {
						db_query('
UPDATE Grade_Permissions
SET type=?
WHERE
Staff_id= ?
AND  Assignment_id= ?
', $text, $args['Staff'], $parts[0]);
					}
		
				}
				$result['success'] = true;
			}
		}
		
		// entering rollbacks
		elseif ($page == 'admin/rollback') {
			// check permissions
			if ($USER['role'] != 'ADMIN') {
				$result['success'] = false;
				$result['reason'] = 'You don\'t have permission to do that.';
			} else {

				// unback the rollbacks
				$rollbacks = array();
				foreach (explode(';', $args['rollbacks']) as $rollback) {
					$parts = explode(',', $rollback);
					db_query('
DELETE FROM Grade_Details
WHERE
Student_id = ?
AND Assignment_id = ?
AND Staff_id = ?
AND when_graded = ?
', $parts[1], $parts[2], $parts[3], $parts[4]);
				}
				$result['success'] = true;
			}
		}
	} catch (Exception $e) {
		$result['success'] = false;
		$result['reason'] = $e->getMessage();
	}

	if ($result['success'])
		unset($result['reason']);

	echo json_encode($result);
	exit(0);
}
?>
