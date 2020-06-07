<?php
function getFeedback($student, $year, $mod) {
	$str = "";
	//find the configured assessments
    $assessment_configs = array();
    $files = scandir("data/$year/$mod/configs/");
    foreach ($files as $file) {
        $file_parts = pathinfo($file);
        $base = $file_parts['filename'];
        if ($file_parts['extension'] == 'json') {
            $fullrelpath = "data/$year/$mod/configs/$file";
            $cfg = json_decode(file_get_contents($fullrelpath));
            $assessment_configs[$base] = $cfg;
        }
    }

    $str.="Feedback for {$student}\n\n";
    
    //find the grades {student->assessment->grade}
    foreach ($assessment_configs as $assessment => $cfg) {
        $gpath = "data/$year/$mod/grades/$student/$assessment.json";
        $spath = "data/$year/$mod/handin-filtered/$student/$assessment.pdf";
        if (file_exists($gpath) && file_exists($spath)) {
            $grade = json_decode(file_get_contents($gpath));
        } else {
            $grade =array_fill(0,count($cfg->scheme), false);
        }

        $str.="{$cfg->title}\n";
        foreach ($grade as $i => $g) {
        	$gg = ' ';
        	if ($g) {
        		$gg = 'x';
        	}
        	$str.="[$gg] {$cfg->scheme[$i]}\n";
        }
        $str.="\n";
    }
    return $str;
}
?>