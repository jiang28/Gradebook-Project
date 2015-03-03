<?php
$USER['username'] = explode('@', $_SERVER['REMOTE_USER']);
$USER['username'] = $USER['username'][0];

$students = array_map('array_shift', db_query('SELECT Student_id FROM Student'));
$staff = array_map('array_shift', db_query('SELECT Staff_id FROM Staff WHERE role = "AI" OR role = "UI"'));
$admin = array_map('array_shift', db_query('SELECT Staff_id FROM Staff WHERE role = "Instructor"'));
$debug = array('jiang28', 'maygupta', 'sajkhand', 'sjeganat', 'syerneni', 'verkampj', 'yuqwu', 'panyif', 'yichfeng', 'dkmuchla', 'kangzhao');

$USER['role'] = 'NONE';
if (in_array($USER['username'], $students)) $USER['role'] = 'STUDENT';
elseif (in_array($USER['username'], $staff)) $USER['role'] = 'STAFF';
elseif (in_array($USER['username'], $admin)) $USER['role'] = 'ADMIN';

// this will override existing in the database
if (in_array($USER['username'], $debug)) $USER['role'] = 'DEBUG';

session_start();
if ($USER['role'] == 'DEBUG' && isset($_GET['SET_DEBUG_ROLE'])) 
	$_SESSION['DEBUG_ROLE'] = $_GET['SET_DEBUG_ROLE'];

if (isset($_SESSION['DEBUG_ROLE'])) {
	$USER['role'] = $_SESSION['DEBUG_ROLE'];
	if ($USER['role'] == 'STUDENT')
		$USER['username'] = $students[0];
	elseif ($USER['role'] == 'STAFF')
		$USER['username'] = $staff[0];
	elseif ($USER['role'] == 'ADMIN')
		$USER['username'] = $admin[0];
}
?>