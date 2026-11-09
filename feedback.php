<?php
if (!(isset($_REQUEST['sid']) && isset($_REQUEST['year']) && isset($_REQUEST['mod']))) {
	http_response_code (400);
} else {
	require_once('lib.php');
	
	$student = $_REQUEST['sid'];
	$year = $_REQUEST['year'];
	$mod = $_REQUEST['mod'];

    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-type: text/plain');
    echo(getFeedback($student, $year, $mod));
}
?>
