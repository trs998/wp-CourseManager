<?php

function yvts_coursemanager_render($attributes) {
    //$attributes
    $targetYear = date("Y");
    
    if ((isset($attributes["year"])) && ($attributes["year"] > 1970) && ($attributes["year"] < 2050)) {
        $targetYear = $attributes["year"];
    }
    
    $courses = yvts_courseRunning::getCoursesRunning($targetYear);
    
   // ob_start();

  // var_dump($courses);

    $currentCourse = "-";
	$currentLevel = "-";
	for($i = 0; $i < count($courses); $i++) {
		//if ($courses[$i]->coursename != $currentCourse) {
		//	$currentCourse = $courses[$i]->coursename;
		//	echo "<div class=\"yvts_course\"><h2>" . $courses[$i]->coursename . " <span class=\"yvts_course_description\">" . $courses[$i]->coursedesc . "</span> //</h2></div>";
      //  }
        //  if ($courses[$i]->levelname != $currentLevel) {
        if ($courses[$i]->coursename != $currentCourse) {
            if ($currentLevel != "-") { echo "</table>"; }
            $currentLevel = $courses[$i]->levelname;
			$currentCourse = $courses[$i]->coursename;
            echo "<table class=\"yvts_courseRunning\">";
            echo "<tr><th>" . $courses[$i]->coursename . " " . $courses[$i]->levelname . " <span class=\"yvts_course_description\"> " . $courses[$i]->coursedesc . "</span></th><th>Price</th></tr>";
			//echo "<div class=\"yvts_level\">Level: " . $courses[$i]->levelname . "</div>";
		}
		if ($courses[$i]->endtimeU != 0) {
            echo "<tr><td>";
            echo $courses[$i]->note;
            $applicationpage = get_option("yvts_coursemanager_application_page");
            if (($applicationpage != false) && ($courses[$i]->starttimeU > date("U"))) {
                echo " <a href=\"" . add_query_arg("yvtscourse",$courses[$i]->courseRunning_ID,$applicationpage) . "\">Apply for this course.</a>";
            };
            echo "</td><td>";
            echo date("d M",$courses[$i]->starttimeU) . " to " . date("d M",$courses[$i]->endtimeU);
            echo "</td></tr>";
            // date("d-m-Y \(l",$courses[$i]->starttimeU) . " of week " . date("W",$courses[$i]->starttimeU) . ") to "  . date("d-m-Y \(l",$courses[$i]->endtimeU) . " of week " . date("W",$courses[$i]->endtimeU) . ") running for " . round($courses[$i]->days) . " days";
		} else {
			echo "<tr><td>Level " . $courses[$i]->levelname . " are scheduled on demand</td></tr>";
        }
    }
        if (count($courses) > 0) { echo "</table>"; }
    
	//return ob_get_clean();

}

?>