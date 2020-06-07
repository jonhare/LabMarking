<?php

require_once('zip.lib.php');
require_once('lib.php');

$year = $_REQUEST['year'];
$mod = $_REQUEST['mod'];

$zip = new zipfile();

//find the students
$files = scandir("data/$year/$mod/handin-filtered/");
foreach ($files as $file) {
    $base = basename($file);
    if ($base == '..' || $file[0] == '.')
    continue;
    
    $fullrelpath = "data/$year/$mod/handin-filtered/$file";
    if (is_dir($fullrelpath)) {
    	$file_content = getFeedback($base, $year, $mod);
        $zip->addFile($file_content, "$base.txt");
    }
}

//prepare the proper content type
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=my_archive.zip");
header("Content-Description: Feedback");

//get the zip content and send it back to the browser
echo $zip->file();

?>