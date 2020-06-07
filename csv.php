<?php

// grid and links -- add ability to filter to 1 assessment
$year = $_REQUEST['year'];
$mod = $_REQUEST['mod'];

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

//find the students
$students = array();
$files = scandir("data/$year/$mod/handin-filtered/");
foreach ($files as $file) {
    $base = basename($file);
    if ($base == '..' || $file[0] == '.')
    continue;
    
    $fullrelpath = "data/$year/$mod/handin-filtered/$file";
    if (is_dir($fullrelpath)) {
        array_push($students, $base);
    }
}

//find the grades {student->assessment->grade}
$hasungraded = false;
$student_grades = array();
foreach ($students as $student) {
    $student_grades[$student] = array();
    foreach ($assessment_configs as $assessment => $cfg) {
        $gpath = "data/$year/$mod/grades/$student/$assessment.json";
        $spath = "data/$year/$mod/handin-filtered/$student/$assessment.pdf";
        if (file_exists($gpath) && file_exists($spath)) {
            $grade = json_decode(file_get_contents($gpath));
            $score = 0;
            foreach ($grade as $g)
                if ($g)
                    $score = $score + 1;
            $score = "$score";
            $student_grades[$student][$assessment] = $score;
        } else if (file_exists($spath)) {
            //ungraded so far
            $student_grades[$student][$assessment] = '-';
            $hasungraded = true;
        } else {
            //no submission
            $student_grades[$student][$assessment] = ' ';
        }
    }
}

header('Content-type: text/plain');
if ($hasungraded) {
	echo("### Warning: ungraded attempts present ###\n\n");
}

echo("id");
foreach ($assessment_configs as $assessment => $cfg)
    echo(",{$cfg->title}");
echo(",total\n");

foreach ($students as $student) {
    echo("$student");
    $total = 0;
    foreach ($assessment_configs as $assessment => $cfg) {
        $score = $student_grades[$student][$assessment];
        if ($score != '-' && $score != ' ')
        	$total += $score;
        echo(",$score");
    }
    echo(",$total\n");
}

?>