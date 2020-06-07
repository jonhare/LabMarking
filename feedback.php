<?php
if (!(isset($_REQUEST['sid']) && isset($_REQUEST['year']) && isset($_REQUEST['mod']))) {
	http_response_code (400);
} else {
	require_once('lib.php');
	
	$student = $_REQUEST['sid'];
	$year = $_REQUEST['year'];
	$mod = $_REQUEST['mod'];

    header('Content-type: text/plain');
    echo(getFeedback($student, $year, $mod));
}
?>
