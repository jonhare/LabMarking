<?php

if (!(isset($_REQUEST['sid']) && isset($_REQUEST['year']) && isset($_REQUEST['ex']) && isset($_REQUEST['mod']))) {
	http_response_code (400);
} else {
	$sid = $_REQUEST['sid'];
	$year = $_REQUEST['year'];
	$ex = $_REQUEST['ex'];
	$mod = $_REQUEST['mod'];

	// $handin = "data/{$year}/{$mod}/handin-filtered/{$sid}/{$ex}.pdf";
	//find the students
    $students = array();
    $files = scandir("data/$year/$mod/handin-filtered/");
    foreach ($files as $file) {
        $base = basename($file);
        if ($base == '..' || $file[0] == '.')
        continue;
        
        $fullrelpath = "data/$year/$mod/handin-filtered/$file";
        if (is_dir($fullrelpath)) {
        	$expath = "$fullrelpath/$ex.pdf";

        	if (file_exists($expath))
            	array_push($students, $base);
        }
    }

    $idx = array_search($sid, $students);
    $prev = $idx - 1;
    if ($prev < 0) $prev = count($students) - 1;
    $next = $idx + 1;
    if ($next >= count($students)) $next = 0;
	
	echo "{\"prev\":\"{$students[$prev]}\", \"next\":\"{$students[$next]}\"}";
}
?>
