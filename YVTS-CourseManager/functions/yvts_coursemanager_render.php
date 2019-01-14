<?php

function yvts_coursemanager_render($attributes) {
    //$attributes
    $targetYear = date("Y");
    
    if ((isset($attributes["year"])) && ($attributes["year"] > 1970) && ($attributes["year"] < 2050)) {
        $targetYear = $attributes["year"];
    }
	 
	$text_default ="Apply for this course";
	 $yvts_text_scheduled=get_option("yvts_text_scheduled");
    if ($yvts_text_scheduled == false) { $yvts_text_scheduled = $text_default; };
	 $yvts_text_unscheduled=get_option("yvts_text_unscheduled");
    if ($yvts_text_unscheduled == false) { $yvts_text_unscheduled = $text_default; };
	 
    $applicationpage = get_option("yvts_coursemanager_application_page");
    
    $courses = yvts_courseRunning::getCoursesRunning($targetYear);
    
    $schedule = "";

  // var_dump($courses);

    $currentCourse = "-";
	$currentLevel = "-";
	
     if (count($courses) > 0) {  $schedule = $schedule . "<table class=\"yvts_courseRunning\">"; }
	for($i = 0; $i < count($courses); $i++) {
		//if ($courses[$i]->coursename != $currentCourse) {
		//	$currentCourse = $courses[$i]->coursename;
		//	$schedule = $schedule .  "<div class=\"yvts_course\"><h2>" . $courses[$i]->coursename . " <span class=\"yvts_course_description\">" . $courses[$i]->coursedesc . "</span> //</h2></div>";
      //  }
        //  if ($courses[$i]->levelname != $currentLevel) {
        if ($courses[$i]->coursename != $currentCourse) {
           // if ($currentLevel != "-") { $schedule = $schedule .  "</table>"; }
            $currentLevel = $courses[$i]->levelname;
			$currentCourse = $courses[$i]->coursename;
            $schedule = $schedule .  "<tr><th>" . $courses[$i]->coursename . " " . $courses[$i]->levelname . " <span class=\"yvts_course_description\"> " . $courses[$i]->coursedesc . "</span></th><th>";
            if ($courses[$i]->levelprice != 0) {
                $schedule = $schedule .  "&pound;" . number_format($courses[$i]->levelprice,0,".",",");
            } /* elseif ((isset($courses[$i+1])) && ($courses[$i+1]->coursename == $courses[$i]->coursename) && ($courses[$i+1]->levelprice != 0)) {
                $schedule = $schedule .  "&pound;" . $courses[$i+1]->levelprice;
            } */ else {
                $schedule = $schedule .  "P.O.A";
            }
            $schedule = $schedule .  "</th></tr>";
			//$schedule = $schedule .  "<div class=\"yvts_level\">Level: " . $courses[$i]->levelname . "</div>";
		}
		if ($courses[$i]->endtimeU > 1000000) {
            $schedule = $schedule .  "<tr><td>";
            $schedule = $schedule .  $courses[$i]->note;
            if (($applicationpage != false) && ($courses[$i]->starttimeU > date("U"))) {
                $schedule = $schedule .  " <a href=\"" . add_query_arg("yvtscourse",$courses[$i]->courseRunning_ID,$applicationpage) . "\">" . $yvts_text_scheduled . ".</a>";
            };
            $schedule = $schedule .  "</td><td>";
            $schedule = $schedule .  date("d M",$courses[$i]->starttimeU) . " to " . date("d M",$courses[$i]->endtimeU);
            $schedule = $schedule .  "</td></tr>";
            // date("d-m-Y \(l",$courses[$i]->starttimeU) . " of week " . date("W",$courses[$i]->starttimeU) . ") to "  . date("d-m-Y \(l",$courses[$i]->endtimeU) . " of week " . date("W",$courses[$i]->endtimeU) . ") running for " . round($courses[$i]->days) . " days";
		} else {
            $schedule = $schedule .  "<tr><td colspan=\"2\">Level " . $courses[$i]->levelname . " are scheduled on demand ";
            if (($applicationpage != false) && (date("Y",$courses[$i]->starttimeU) >= date("Y"))) {
                $schedule = $schedule .  " <a href=\"" . add_query_arg("yvtscourse",$courses[$i]->courseRunning_ID,$applicationpage) . "\">" . $yvts_text_unscheduled . ".</a>";
            }/* else {
                $schedule = $schedule .  "Year of course: " . date("Y",$courses[$i]->starttimeU) . " -  Year of now: " . date("Y") . " and  application page is " . $applicationpage;
            }*/
            $schedule = $schedule .  "</td></tr>";
        }
    }
        if (count($courses) > 0) { $schedule = $schedule .  "</table>"; }
    
	return $schedule;

}

?>