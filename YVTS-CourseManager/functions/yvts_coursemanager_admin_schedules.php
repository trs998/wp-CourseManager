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
	
	$courses_levels = yvts_level::getCoursesAndLevels();

	//handle course_edit action via POST
	if (isset($_POST["course_edit"])) {
		//yvts_level as level to book against
		//yvts_scheduled as "Yes" or "No"
		//yvts_starttime as possible dates
		//yvts_endtime as possible dates
		$editError = "";
		$editingSchedule = $_POST["yvts_course_edit_level"];
		$editlevel = $_POST["yvts_course_edit_level_$editingSchedule"];
		$editscheduled = $_POST["yvts_scheduled_edit_$editingSchedule"];
		$editnote = $_POST["yvts_edit_note_edit_$editingSchedule"];

		if ($editscheduled == "Yes") {
			//add course with dates in current year
			$editstarttime = $_POST["yvts_course_edit_starttime_edit_$editingSchedule"];
			$editendtime = $_POST["yvts_course_edit_endtime_edit_$editingSchedule"];
			$editstarttimeU = strtotime($editstarttime);
			$editendtimeU = strtotime($editendtime);
			if ($editstarttimeU === false) {
				$editError = $editError . "Start date " . $editstarttime . " not valid.<br />";
			}
			if ($editendtimeU === false) {
				$editError = $editError . "End date " . $editendtime . " not valid.<br />";
			}
			if ($editendtimeU < $editstarttimeU) {
				$editError = $editError . "End date must be later than start date!<br />";
			}
			/*if ($addError == "") {
				echo "<p>New Course<br />Level: $newlevel<br />Scheduled: $newscheduled<br />Start: $newstarttime<br />End: $newendtime<br />Note: $newnote</p>";
			}*/
		} else {
			//add course without dates in correct year
			if (isset($_GET["year"])) { $editstarttime = $displayYear; }
			$editstarttimeU = strtotime($editstarttime . "-01-01");
			$editendtimeU = null;
			/*
			echo "<p>New Course<br />Level: $newlevel<br />Scheduled: $newscheduled<br />Start: $newstarttime<br />Note: $newnote</p>";
			*/
		}
		if ($editError == "") {
			$editResult = yvts_courseRunning::editCourse($editingSchedule,$editlevel, $editstarttimeU, $editendtimeU, $editnote);
		}
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
			if (isset($_GET["year"])) { $newstarttime = $displayYear; }
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

	if (isset($editResult)) {
		echo "<div  style=\"color: green\">";
		if ($editResult == 1) {
			echo "Saved changed to $editResult course.";
		} else {
			echo "Saved changed: $editResult.";
		}
		echo "</div>";
	}
	if ($editError != "") {
		echo "<div style=\"color: red\">$editError</div>";
	}
	$currentCourse = "---------";
	$currentLevel = "---------";
	for($i = 0; $i < count($courses); $i++) {
		echo "<p>";
		echo "<div>";
		// <form method=\"post\" id=\"displayForm$i\">
		echo "<div id=\"displayForm$i\">";
		if ($courses[$i]->coursename != $currentCourse) {
			$currentCourse = $courses[$i]->coursename;
			echo "<div class=\"yvts_course\"><h2>" . $courses[$i]->coursename . " <span class=\"yvts_course_description\">" . $courses[$i]->coursedesc . "</span> </h2></div>\n";
		}
		if ($courses[$i]->levelname != $currentLevel) {
			$currentLevel = $courses[$i]->levelname;
			echo "<div class=\"yvts_level\">Level: " . $courses[$i]->levelname . "</div>\n";
		}
		if ($courses[$i]->endtimeU > 1000000) {
			echo "" . date("d-m-Y \(l",$courses[$i]->starttimeU) . " of week " . date("W",$courses[$i]->starttimeU) . ") to "  . date("d-m-Y \(l",$courses[$i]->endtimeU) . " of week " . date("W",$courses[$i]->endtimeU) . ") running for " . round($courses[$i]->days) . " days";
		} else {
			echo " No Specific schedule, in " . date("Y",$courses[$i]->starttimeU);
		}
		

		echo "\n <form method=\"post\" style=\"display: inline\"><input type=\"hidden\" name=\"deleteCourseRunning\" value=\"" . $courses[$i]->courseRunning_ID . "\" /><input type=\"submit\" class=\"yvts_delete_button\" name=\"Delete_Level\" value=\"Delete Booked Course\" onclick=\"return confirm('Delete this course booking?');\" /></form>\n";

		if ($courses[$i]->note != null) { echo "<br /><span class=\"yvts_coursenote\">" . $courses[$i]->note . "</span>\n"; }

		echo "\n <a href=\"#\" onclick=\"document.getElementById('displayForm$i').style.display='none';document.getElementById('editForm$i').style.display='block';return false;\">(edit)</a>\n";
		//echo "</form>";
		echo "</div>";
		
		echo "
		<div id=\"editForm$i\" style=\"display:none;\">
		<form method=\"post\">
		<input type=\"hidden\" name=\"yvts_course_edit_level\" value=\"" . $courses[$i]->courseRunning_ID . "\" />
		<label for=\"yvts_level_edit$i\">Course and level: </label>
		<select id=\"yvts_level_edit$i\" name=\"yvts_course_edit_level_" . $courses[$i]->courseRunning_ID . "\">
		";
		for ($j = 0; $j < count($courses_levels); $j++) {
			echo "<option value=\"" . $courses_levels[$j]->levelid . "\"";
			if ($courses_levels[$j]->levelid == $courses[$i]->levelid) { echo " selected=\"selected\""; }
			echo ">" . $courses_levels[$j]->coursename . " - " . $courses_levels[$j]->levelname . "</option>";
		}
		echo " 
		</select><br />
		<label for=\"yvts_scheduled_edit$i\">Scheduled Time?</label>";

		if ($courses[$i]->endtimeU > 1000000) {
			//display date selector version
			echo "<input type=\"radio\" id=\"yvts_scheduled_edit$i\" name=\"yvts_scheduled_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"Yes\" onclick=\"document.getElementById('yvts_edittcourse_dates$i').style.display = 'inline';\" checked=\"checked\" />Yes
			<input type=\"radio\" id=\"yvts_scheduled2_edit$i\" name=\"yvts_scheduled_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"No\" onclick=\"document.getElementById('yvts_edittcourse_dates$i').style.display = 'none';\" />No (Booked on demand)<br />
			<span id=\"yvts_edittcourse_dates$i\">
			<label for=\"yvts_starttime_edit$i\">Start Time: </label><input id=\"yvts_starttime_edit$i\" type=\"date\" name=\"yvts_course_edit_starttime_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"";
			echo date("Y-m-d",$courses[$i]->starttimeU);
			echo "\" /><br />
			<label for=\"yvts_endtime_edit$i\">End Time: </label><input id=\"yvts_endtime_edit$i\" type=\"date\" name=\"yvts_course_edit_endtime_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"";
			echo date("Y-m-d",$courses[$i]->endtimeU);
			echo "\" /><br />
			</span>";
		} else {
				//display year-only version
				echo "<input type=\"radio\" id=\"yvts_scheduled_edit$i\" name=\"yvts_scheduled_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"Yes\" onclick=\"document.getElementById('yvts_edittcourse_dates$i').style.display = 'inline';\" />Yes
			<input type=\"radio\" id=\"yvts_scheduled2_edit$i\" name=\"yvts_scheduled_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"No\" onclick=\"document.getElementById('yvts_edittcourse_dates$i').style.display = 'none';\" checked=\"checked\" />No (Booked on demand)<br />
			<span id=\"yvts_edittcourse_dates$i\" style=\"display:none\" >
			<label for=\"yvts_starttime_edit$i\">Start Time: </label><input id=\"yvts_starttime_edit$i\" type=\"date\" name=\"yvts_course_edit_starttime_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"";
			echo date("Y-m-d",$courses[$i]->starttimeU);
			echo "\" /><br />
			<label for=\"yvts_endtime_edit$i\">End Time: </label><input id=\"yvts_endtime_edit$i\" type=\"date\" name=\"yvts_course_edit_endtime_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"";
			echo date("Y-m-d",$courses[$i]->endtimeU);
			echo "\" /><br />
			</span>";
		}
		echo "<label for=\"yvts_new_note_edit$i\">Note:</label><input id=\"yvts_new_note_edit$i\" name=\"yvts_edit_note_edit_" . $courses[$i]->courseRunning_ID . "\" value=\"";
		echo $courses[$i]->note;
		echo "\" /><br />
		<input type=\"submit\" name=\"course_edit\" value=\"Save Edited Course\" />
		</form></div>";
		//echo "</div>";
		echo "</p>";
	}

	//schedule a new course
	//input:
	// course
	// level
	// start time (tick for no times)
	// end time (tick for no times)
	

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