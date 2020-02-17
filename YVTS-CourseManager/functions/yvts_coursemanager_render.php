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
	 $yvts_text_fullybooked=get_option("yvts_text_fullybooked");
    if ($yvts_text_fullybooked == false) { $yvts_text_fullybooked = $text_default; };

    $applicationpage = get_option("yvts_coursemanager_application_page");

    $courses = yvts_courseRunning::getCoursesRunning($targetYear);

    $schedule = "";

  // var_dump($courses);

    $currentCourse = "-";
	$currentLevel = "-";

     if (count($courses) > 0) {  }
	for($i = 0; $i < count($courses); $i++) {
		//if ($courses[$i]->coursename != $currentCourse) {
		//	$currentCourse = $courses[$i]->coursename;
		//	$schedule = $schedule .  "<div class=\"yvts_course\"><h2>" . $courses[$i]->coursename . " <span class=\"yvts_course_description\">" . $courses[$i]->coursedesc . "</span> //</h2></div>";
      //  }
        //  if ($courses[$i]->levelname != $currentLevel) {
        if ($courses[$i]->coursename != $currentCourse) {

            if ($i > 0) { $schedule = $schedule . "</table>"; }
            //start table
            $schedule = $schedule . "<table class=\"yvts_courseRunning\" id=\"yvts_courseRunning-$i-short\" style=\"display:block\">";

            $tableheading = "";
           // if ($currentLevel != "-") { $schedule = $schedule .  "</table>"; }
            $currentLevel = $courses[$i]->levelname;
			$currentCourse = $courses[$i]->coursename;
            $tableheading = $tableheading .  "<tr><th><a onclick=\"document.getElementById('yvts_courseRunning-$i').style.display='block';document.getElementById('yvts_courseRunning-$i-short').style.display='none'; return false; \" href=\"#\"> " . $courses[$i]->coursename . "  <span class=\"yvts_course_description\"> " . $courses[$i]->coursedesc . "</span></a></th><th>";
           /* if ($courses[$i]->levelprice != 0) {
                $tableheading = $tableheading .  "&pound;" . number_format($courses[$i]->levelprice,0,".",",");
            } else {
                $tableheading = $tableheading .  "P.O.A";
            } */
            $tableheading = $tableheading .  "</th></tr>";

            $schedule = $schedule . $tableheading;
            $schedule = $schedule . "</table>";

            $schedule = $schedule . "<table class=\"yvts_courseRunning\" id=\"yvts_courseRunning-$i\" style=\"display:none\">";

            $tableheading = "";
           // if ($currentLevel != "-") { $schedule = $schedule .  "</table>"; }
            $currentLevel = $courses[$i]->levelname;
			$currentCourse = $courses[$i]->coursename;
            $tableheading = $tableheading .  "<tr><th>" . $courses[$i]->coursename . "  <span class=\"yvts_course_description\"> " . $courses[$i]->coursedesc . "</span></th><th>"; // used to include " . $courses[$i]->levelname . "


			$displayPrice = -1;
			$totalPriceCourses = 0;
			$totalPricedUpCourses = 0;

			//FIXME this doesn't work for "some" individual prices
			for ($pricechecki = $i; (($courses[$pricechecki]->coursename == $currentCourse) && ($pricechecki < count($courses))); $pricechecki++) {
				$totalPriceCourses++;
				if ($courses[$pricechecki]->price > 0) {
				$totalPricedUpCourses++;
				}
			}
			if ($totalPricedUpCourses == 0) { $displayPrice = $courses[$i]->levelprice; }
            if ($displayPrice > 0) {
                $tableheading = $tableheading .  "&pound;" . number_format($displayPrice,0,".",",");
            } else {
				$tableheading = $tableheading .  "as priced";
            }
            $tableheading = $tableheading .  "</th></tr>";

            $schedule = $schedule . $tableheading;
			//$schedule = $schedule .  "<div class=\"yvts_level\">Level: " . $courses[$i]->levelname . "</div>";
        }
      //  $schedule = $schedule . "<tr><td colspan=\"2\">" . print_r($courses[$i],true) . "</td></tr>";
		if ($courses[$i]->endtimeU > 1000000) {
            $schedule = $schedule .  "<tr><td>";
            $schedule = $schedule .  $courses[$i]->note;
            if (strlen($courses[$i]->leveldesc) > 0) { $schedule = $schedule . " <span class=\"yvts_level_description\">" . $courses[$i]->leveldesc . "</span>"; }
        			if ($totalPricedUpCourses > 0) {
					if ($courses[$i]->price > 0) {
						$schedule = $schedule . " <span class=\"yvts_course_price\">&pound;" . number_format($courses[$i]->price,0,".",",") . "</span>";
					} else {
						$schedule = $schedule . " <span class=\"yvts_course_price\">&pound;" . number_format($courses[$i]->levelprice,0,".",",") . "</span>";
					}
			}
            if (($applicationpage != false) && ($courses[$i]->starttimeU > date("U"))) {
					if ($courses[$i]->fullybooked) {
						 $schedule = $schedule .  " " . $yvts_text_fullybooked . " ";
					} else {
						$schedule = $schedule .  " <a href=\"" . add_query_arg("yvtscourse",$courses[$i]->courseRunning_ID,$applicationpage) . "\">" . $yvts_text_scheduled . ".</a>";
					}
            };
            $schedule = $schedule .  "</td><td>";
            $schedule = $schedule .  date("d M",$courses[$i]->starttimeU) . " to " . date("d M",$courses[$i]->endtimeU);
            $schedule = $schedule .  "</td></tr>";
            // date("d-m-Y \(l",$courses[$i]->starttimeU) . " of week " . date("W",$courses[$i]->starttimeU) . ") to "  . date("d-m-Y \(l",$courses[$i]->endtimeU) . " of week " . date("W",$courses[$i]->endtimeU) . ") running for " . round($courses[$i]->days) . " days";
		} else {
            $schedule = $schedule .  "<tr><td colspan=\"2\">" . $courses[$i]->levelname;
            if (strlen($courses[$i]->leveldesc) > 0) { $schedule = $schedule . " <span class=\"yvts_level_description\">" . $courses[$i]->leveldesc . "</span>"; }
			if ($totalPricedUpCourses > 0) {
					if ($courses[$i]->price > 0) {
						$schedule = $schedule . " <span class=\"yvts_course_price\">&pound;" . number_format($courses[$i]->price,0,".",",") . "</span>";
					} else {
						$schedule = $schedule . " <span class=\"yvts_course_price\">&pound;" . number_format($courses[$i]->levelprice,0,".",",") . "</span>";
					}
			}
            $schedule = $schedule . " are scheduled on demand ";
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
