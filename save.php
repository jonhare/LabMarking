<?php

if (!(isset($_REQUEST['sid']) && isset($_REQUEST['year']) && isset($_REQUEST['ex']) && isset($_REQUEST['data']) && isset($_REQUEST['mod']))) {
	http_response_code (400);
} else {
	$sid = $_REQUEST['sid'];
	$year = $_REQUEST['year'];
	$ex = $_REQUEST['ex'];
	$mod = $_REQUEST['mod'];
	$data = $_REQUEST['data'];

	$handin = "data/{$year}/{$mod}/handin-filtered/{$sid}/{$ex}.pdf";
	if (file_exists($handin)) {
		$dirname = "data/{$year}/{$mod}/grades/{$sid}/";
		
		if (!file_exists($dirname))
			mkdir($dirname, 0777, true);

		$filename = "{$dirname}/{$ex}.json";
		file_put_contents($filename, $data, LOCK_EX);
		print("{\"saved\":true}");
	} else {
		http_response_code (400);
	}
}
?>
