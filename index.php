<html>
	<head>
		<title>Jon's E-Marking System</title>
	</head>
	<body>
<?php

if (!isset($_REQUEST['year'])) {
    //Year list view
    echo("<h1 class='year'>Available assesment years</h1>");
    
    $files = scandir("data/");
    foreach ($files as $file) {
        $base = basename($file);
        
        if ($base == '..' || $file[0] == '.')
        continue;
        
        $fullrelpath = "data/$file";
        if (is_dir($fullrelpath)) {
            echo("<div class='dir'><a href='index.php?year=$base'>$base</a></div>");
        }
    }
} else if (!isset($_REQUEST['mod'])) {
    //Module per year list view
    $year = $_REQUEST['year'];
    echo("<h1 class='year'>$year Academic Year: Available modules</h1>");
    echo("<div class='dir'><a href='index.php'>..</a></div>");
    $files = scandir("data/$year/");
    foreach ($files as $file) {
        $base = basename($file);
        
        if ($base == '..' || $file[0] == '.')
        continue;
        
        $fullrelpath = "data/$year/$file";
        if (is_dir($fullrelpath)) {
            echo("<div class='dir'><a href='index.php?year=$year&mod=$base'>$base</a></div>");
        }
    }
} else {
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
            } else {
                //no submission
                $student_grades[$student][$assessment] = 'x';
            }
        }
    }
    
    if (!isset($_REQUEST['ex'])) {
        echo("<h1>$mod $year Summary</h1>");
        // all assessments view
        echo("<div class='grades'>");
        echo("<table>");
        echo("<tr>");
        echo("<td>Student</td>");
        foreach ($assessment_configs as $assessment => $cfg)
            echo("<td><a href='index.php?year=$year&mod=$mod&ex=$assessment'>{$cfg->title}</a></td>");
        echo("</tr>");
        
        foreach ($students as $student) {
            echo("<td>$student</td>");
            foreach ($assessment_configs as $assessment => $cfg) {
                $score = $student_grades[$student][$assessment];
                if ($score == 'x') {
                    echo("<td>x</td>");
                } else {
                    echo("<td><a href='report.html?sid=$student&year=$year&mod=$mod&ex=$assessment'>{$score}</a></td>");
                }
            }
            echo("</tr>");
        }
        echo("</table>");
        echo("</div>");
        
    } else {
        //single assessment view
        $assessment = $_REQUEST['ex'];
        
        echo("<div class='grades'>");
        echo("<table>");
        echo("<tr>");
        echo("<td>Student</td>");
        echo("<td>{$assessment_configs[$assessment]->title}</td>");
        echo("</tr>");
        
        foreach ($students as $student) {
            $score = $student_grades[$student][$assessment];
            if ($score != 'x') {
                echo("<td>$student</td>");
                echo("<td><a href='report.html?sid=$student&year=$year&mod=$mod&ex=$assessment&flt'>{$score}</a></td>");
                echo("</tr>");
            }
        }

        echo("</table>");
        echo("</div>");
    }
}

?>
	</body>
</html>
