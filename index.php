<?php
// database
require('php/db.php');

// user authentication
require('php/user.php');

// any sorts of submissions
require('php/submit.php');

// get the url root
$BASE_URL = $_SERVER['REQUEST_URI'];
$BASE_URL = substr($BASE_URL, 0, strpos($BASE_URL, 'gradebook') + 10);

// get the page to load
$page = explode('/', trim(substr($_SERVER['REQUEST_URI'], strlen($BASE_URL)), '/'));

// if we have a logout request, do that
if ($page[0] == 'logout') {
	$redirect = 'https://logout@' . $_SERVER['HTTP_HOST'] . $BASE_URL;
	header('Location: ' . $redirect);
	die();
}

if ($page[count($page) - 1][0] == '?')
	unset($page[count($page) - 1]);

if (count($page) == 0 || (count($page) == 1 && $page[0] == ''))
	$page = array('main');

// make sure that people don't view pages they aren't supposed to
if (in_array('grade', $page) && $USER['role'] == 'STUDENT')
  header('Location: ' . $BASE_URL);
elseif (in_array('admin', $page) && ($USER['role'] == 'STUDENT' || $USER['role'] == 'STAFF'))
  header('Location: ' . $BASE_URL);

// generate the breadcrumb links
$breadcrumbs = ucwords($page[count($page) - 1]);
$depth = '..';
$sofar = 0;
for ($i = count($page) - 2; $i >= 0; $i--) {
	if (in_array($page[$i], array('grade', 'admin'))) {
		$breadcrumbs = ucwords($page[$i]) . ' &rarr; ' . $breadcrumbs;
		$depth = '../' . $depth;
	} elseif ($page[$i] == 'main') {
		continue;
	} else {
		$breadcrumbs = '<a href="' . $depth . '">' . ucwords($page[$i]) . '</a> &rarr; ' . $breadcrumbs;
		$depth = '../' . $depth;
	}
}

if ($page[0] == 'main') {
	$breadcrumbs = '';
} else {
	$breadcrumbs = '<a href="' . $depth . '">Main</a> &rarr; ' . $breadcrumbs;
}

// get the page filename
$args = array();
while (count($page) > 0) {
  $filename = 'pages/' . implode('/', $page) . '.php';
  if (file_exists($filename)) {
		$title = ucwords(implode(' -- ', $page));

		break;
	}
	array_unshift($args, array_pop($page));
}

// if we didn't get a page correctly, redirect to the root
if (!isset($title)) {
  header('Location: ' . $BASE_URL);
}

// submit user data
if (isset($_REQUEST['submit']) && $_REQUEST['submit']) {
	onSubmit(implode('/', $page), $_REQUEST);
}
?>
<html>
<head>
<title>Gradebook - <?php echo $title; ?></title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script src="https://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
<script src="<?php echo $BASE_URL; ?>js/gradebook.js"></script>

<link rel="stylesheet" type="text/css" href="https://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $BASE_URL; ?>css/gradebook.css" />

</head>

<body>

<div id="main">

<?php
if ($USER['role'] == 'DEBUG' || isset($_SESSION['DEBUG_ROLE'])) {
?>
<div id="debug">
Debug options: 
[ <a href="?SET_DEBUG_ROLE=STUDENT">student</a> ]
[ <a href="?SET_DEBUG_ROLE=STAFF">staff</a> ]
[ <a href="?SET_DEBUG_ROLE=ADMIN">admin</a> ]
[ <a href="javascript:$('#debug').hide();">hide</a> ]
</div>
<?php
}
?>

<h1><?php echo $title; ?></h1>

<img class="loading" src="<?php echo $BASE_URL; ?>img/loading.gif" />
<div id="feedback"></div>

<p><?php echo $breadcrumbs; ?></p>

<?php
include($filename);
?>

</div>

</body>

</html>
