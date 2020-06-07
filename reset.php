<?php

if (!(isset($_REQUEST['sid']) && isset($_REQUEST['year']) && isset($_REQUEST['ex']) && isset($_REQUEST['mod']))) {
	http_response_code (400);
} else {
	$sid = $_REQUEST['sid'];
	$year = $_REQUEST['year'];
	$ex = $_REQUEST['ex'];
	$mod = $_REQUEST['mod'];

	$handin = "data/{$year}/{$mod}/handin-filtered/{$sid}/{$ex}.pdf";
	if (file_exists($handin)) {
		$dirname = "data/{$year}/{$mod}/grades/{$sid}/";

		$filename = "{$dirname}/{$ex}.json";
		if (file_exists($filename))
			unlink($filename);
		print("{\"reset\":true}");
	} else {
		http_response_code (400);
	}
}
?>
