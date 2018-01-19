<?php
//split out to avoid massive file

function yvts_coursemanager_admin_schedules() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	$displayYear = $_GET["year"];
	if ((! is_numeric($displayYear)) || (($displayYear < 1980) || ($displayYear > 2050))) {
		$displayYear = date("Y");
	}
	
	//handle course_add action via POST
	if (isset($_POST["course_add"])) {
		//yvts_level as level to book against
		//yvts_scheduled as "Yes" or "No"
		//yvts_starttime as possible dates
		//yvts_endtime as possible dates
		$addError = "";
		$newlevel = $_POST["yvts_course_new_level"];
		$newscheduled = $_POST["yvts_scheduled"];
		$newnote = $_POST["yvts_new_note"];

		if ($newscheduled == "Yes") {
			//add course with dates in current year
			$newstarttime = $_POST["yvts_course_new_starttime"];
			$newendtime = $_POST["yvts_course_new_endtime"];
			$newstarttimeU = strtotime($newstarttime);
			$newendtimeU = strtotime($newendtime);
			if ($newstarttimeU === false) {
				$addError = $addError . "Start date " . $newstarttime . " not valid.<br />";
			}
			if ($newendtimeU === false) {
				$addError = $addError . "End date " . $newendtime . " not valid.<br />";
			}
			if ($newendtimeU < $newstarttimeU) {
				$addError = $addError . "End date must be later than start date!<br />";
			}
			/*if ($addError == "") {
				echo "<p>New Course<br />Level: $newlevel<br />Scheduled: $newscheduled<br />Start: $newstarttime<br />End: $newendtime<br />Note: $newnote</p>";
			}*/
		} else {
			//add course without dates in correct year
			
			$newstarttimeU = strtotime($newstarttime . "-01-01");
			$newendtimeU = null;
			/*
			echo "<p>New Course<br />Level: $newlevel<br />Scheduled: $newscheduled<br />Start: $newstarttime<br />Note: $newnote</p>";
			*/
		}
		if ($addError == "") {
			$addResult = yvts_courseRunning::addCourse($newlevel, $newstarttimeU, $newendtimeU, $newnote);
		}
	}

	if (isset($_POST["deleteCourseRunning"])) {
		$deleteCourseID = $_POST["deleteCourseRunning"];
		$deleteresult = yvts_courseRunning::deleteCourseRunning($_POST["deleteCourseRunning"]);
		if ($deleteresult === true) {
			echo "<span style=\"color: green\">Deleted Course Booking successfully</span>";
		} else {
			echo "<span style=\"color: red\">$deleteresult</span>";
		}
	}

	echo '<div class="wrap">';
	echo "<h2>List of courses scheduled.</h2>";
	$years = yvts_courseRunning::getYears();
	$doneNow = false;
	$myURL = menu_page_url("yvts_coursemanager_admin_schedules",false);
	for ($i = 0; $i < count($years); $i++) {
		if ($years[$i]->year == date("Y")) { $doneNow = true; }
		else if (($doneNow == false) && ($years[$i]->year < date("Y"))) { //if we passed the current year without displaying it, because nothing is booked in it yet.
			echo "<h1 class=\"yvts_course_year "; 
			if ($displayYear == date("Y")) { echo "yvts_thisone"; }
			echo "\"><a href=\"" . add_query_arg(array("year" => date("Y")),$myURL) . "\">" . date("Y") . "</a></h1>";
			$doneNow = true;
		}
		echo "<h1 class=\"yvts_course_year "; 
		if ($displayYear == $years[$i]->year) { echo "yvts_thisone"; }
		echo "\"><a href=\"" . add_query_arg(array("year" => $years[$i]->year),$myURL) . "\">" . $years[$i]->year . "</a></h1>";
	}
	if ($doneNow == false) { //in the event no entries currently in the database
		echo "<h1 class=\"yvts_course_year "; 
		if ($displayYear == date("Y")) { echo "yvts_thisone"; }
		echo "\"><a href=\"" . add_query_arg(array("year" => date("Y")),$myURL) . "\">" . date("Y") . "</a></h1>";
	}

	$courses = yvts_courseRunning::getCoursesRunning($displayYear);
	echo '<p>TODO: Edit displayed courses scheduled</p>';
	/*	
	courseRunning_ID
	edittime
	starttime
	starttimeU
	endtime
	endtimeU
	note
	levelname
	coursename
	*/

	echo "<h1>Courses Running this year</h1>";
	$currentCourse = "---------";
	$currentLevel = "---------";
	for($i = 0; $i < count($courses); $i++) {
		echo "<p><form method=\"post\">";
		echo "<input type=\"hidden\" name=\"courseEdit\" value=\"" . $courses[$i]->courseRunning_ID . "\" />";
		echo "<div class=\"courseEntry\">";
		if ($courses[$i]->coursename != $currentCourse) {
			$currentCourse = $courses[$i]->coursename;
			echo "<div class=\"yvts_course\"><h2>" . $courses[$i]->coursename . " <span class=\"yvts_course_description\">" . $courses[$i]->coursedesc . "</span> </h2></div>";
		}
		if ($courses[$i]->levelname != $currentLevel) {
			$currentLevel = $courses[$i]->levelname;
			echo "<div class=\"yvts_level\">Level: " . $courses[$i]->levelname . "</div>";
		}
		if ($courses[$i]->endtimeU != 0) {
			echo "" . date("d-m-Y \(l",$courses[$i]->starttimeU) . " of week " . date("W",$courses[$i]->starttimeU) . ") to "  . date("d-m-Y \(l",$courses[$i]->endtimeU) . " of week " . date("W",$courses[$i]->endtimeU) . ") running for " . round($courses[$i]->days) . " days";
		} else {
			echo " No Specific schedule, in " . date("Y",$courses[$i]->starttimeU);
		}
		echo " (edit) </form>";
		echo " <form method=\"post\" style=\"display: inline\"><input type=\"hidden\" name=\"deleteCourseRunning\" value=\"" . $courses[$i]->courseRunning_ID . "\" /><input type=\"submit\" class=\"yvts_delete_button\" name=\"Delete_Level\" value=\"Delete Booked Course\" onclick=\"return confirm('Delete this course booking?');\" /></form>";
		if ($courses[$i]->note != null) { echo "<br /><span class=\"yvts_coursenote\">" . $courses[$i]->note . "</span>"; }
		echo "</div>";
		echo "</p>";
	}

	//schedule a new course
	//input:
	// course
	// level
	// start time (tick for no times)
	// end time (tick for no times)
	
	$courses_levels = yvts_level::getCoursesAndLevels();

	echo "
	<div class=\"yvts_course_new\">
	";
	if ($addError != "") {
		echo "<div style=\"color: red\">$addError</div>";
	}
	echo "
	<form method=\"post\">
	<label for=\"yvts_level\">Course and level: </label>
	<select id=\"yvts_level\" name=\"yvts_course_new_level\">
	";
	for ($i = 0; $i < count($courses_levels); $i++) {
		echo "<option value=\"" . $courses_levels[$i]->levelid . "\"";
		if (($addError != "") && ($courses_levels[$i]->levelid == $newlevel)) { echo " selected=\"selected\""; }
		echo ">" . $courses_levels[$i]->coursename . " - " . $courses_levels[$i]->levelname . "</option>";
	}
	echo " 
	</select><br />
	<label for=\"yvts_scheduled\">Scheduled Time?</label>
	<input type=\"radio\" id=\"yvts_scheduled\" name=\"yvts_scheduled\" value=\"Yes\" onclick=\"document.getElementById('yvts_newcourse_dates').style.display = 'inline';\" checked />Yes
	<input type=\"radio\" id=\"yvts_scheduled2\" name=\"yvts_scheduled\" value=\"No\" onclick=\"document.getElementById('yvts_newcourse_dates').style.display = 'none';\" />No (Booked on demand)<br />
	<span id=\"yvts_newcourse_dates\">
	<label for=\"yvts_starttime\">Start Time: </label><input id=\"yvts_starttime\" type=\"date\" name=\"yvts_course_new_starttime\" value=\"";
	if ($addError != "") { echo $newstarttime; }
	echo "\" /><br />
	<label for=\"yvts_endtime\">End Time: </label><input id=\"yvts_endtime\" type=\"date\" name=\"yvts_course_new_endtime\" value=\"";
	if ($addError != "") { echo $newendtime; }
	echo "\" /><br />
	</span>
	<label for=\"yvts_new_note\">Note:</label><input id=\"yvts_new_note\" name=\"yvts_new_note\" value=\"";
	if ($addError != "") { echo $newnote; }
	echo "\" /><br />
	<input type=\"submit\" name=\"course_add\" value=\"Add Scheduled Course\" />
	</form>
	</div>
	";

	echo '</div>';
}

/*
Todo:
 edit course booked
 set scheduled/unscheduled status when failed to submit new course
*/

?>