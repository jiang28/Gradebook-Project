<?php
$DB_DEBUG_MODE = false;

date_default_timezone_set('America/New_York');
$DB = new PDO('sqlite:db/b561_group9.sqlite');
$DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function db_query($query) {
	$args = func_get_args();
	array_shift($args);

	$DB = $GLOBALS['DB'];

	if ($GLOBALS['DB_DEBUG_MODE']) {
		echo "<pre style=\"border: 1px dashed red;\">\n";
		echo interpolateQuery($query, $args);
		echo "\n</pre>\n";
	}

	$stmt = $DB->prepare($query);
	$stmt->execute($args);
	return $stmt->fetchAll();
}

/**
 * SOURCE: http://stackoverflow.com/questions/210564/pdo-prepared-statements
 *
 * Replaces any parameter placeholders in a query with the value of that
 * parameter. Useful for debugging. Assumes anonymous parameters from 
 * $params are are in the same order as specified in $query
 *
 * @param string $query The sql query with parameter placeholders
 * @param array $params The array of substitution parameters
 * @return string The interpolated query
 */
function interpolateQuery($query, $params) {
    $keys = array();

    # build a regular expression for each parameter
    foreach ($params as $key => $value) {
        if (is_string($key)) {
            $keys[] = '/:'.$key.'/';
        } else {
            $keys[] = '/[?]/';
        }
    }

    $query = preg_replace($keys, $params, $query, 1, $count);

    #trigger_error('replaced '.$count.' keys');

    return $query;
}
?>

