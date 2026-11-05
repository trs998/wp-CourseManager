<?php
//split out to avoid massive file

function yvts_coursemanager_admin_courses() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    
    //Add_new_course and newcourse
    $newcoursemessage = "";
    if (isset($_POST["Add_new_course"])) {
        $newcourse = trim($_POST["newcourse"]);
        $newcoursedesc = trim($_POST["newcoursedesc"]);
        $newcourselongdesc = trim($_POST["newcourselongdesc"]);
        if (strlen($newcourse) < 1) {
            $newcoursemessage = "<span style=\"color: red\">New Course Name needs to be entered</span>";
        } else {
            //add new course
            $newcourseresult = yvts_course::createCourse($newcourse,$newcoursedesc,$newcourselongdesc);
            if ($newcourseresult === true) {
                $newcoursemessage = "<span style=\"color: green\">New Course \"$newcourse\" created successfully</span>";
                $newcourse = $newcoursedesc = "";
            } else {
                $newcoursemessage = "<span style=\"color: red\">$newcourseresult</span>";
            }
        }
    }

    $editcoursemessage = "";
    if (isset($_POST["Edit_Course"])) {
        //editing course
        //$_POST["editcourseID"];
        //$_POST["editcourse".$_POST["editcourseID"]];
        $editedcourseID = trim($_POST["editcourseID"]);
        $editedcourse = trim($_POST["editcourse".$_POST["editcourseID"]]);
        $editedcoursedesc = trim($_POST["editcoursedesc".$_POST["editcourseID"]]);
        $editedcourselongdesc = trim($_POST["editcourselongdesc".$_POST["editcourseID"]]);
        if (strlen($editedcourse) < 1) {
            $editcoursemessage = "<span style=\"color: red\">Edited Course Name needs to be entered</span>";
        } else {
            $editcourseresult = yvts_course::updateCourse($editedcourseID, $editedcourse, $editedcoursedesc, $editedcourselongdesc);
            if ($editcourseresult === true) {
                $editcoursemessage = "<span style=\"color: green\">Edited Course \"$editedcourse\" saved successfully</span>";
            } else {
                $editcoursemessage = "<span style=\"color: red\">$editcourseresult</span>";
            }
        }
    }

    //$_POST["newLevelCourseID"]
    //$_POST["newlevel".$_POST["newLevelCourseID"]];
    $newlevelmessage = "";
    if (isset($_POST["newLevelCourseID"])) {
        $newlevelcourseID = $_POST["newLevelCourseID"];
        $newlevel = trim($_POST["newlevel".$_POST["newLevelCourseID"]]);
        $newleveldesc = trim($_POST["newleveldesc".$_POST["newLevelCourseID"]]);
        $newlevelsh = trim($_POST["newlevelsh".$_POST["newLevelCourseID"]]);
        $newlevelprice = trim($_POST["newlevelprice".$_POST["newLevelCourseID"]]);
        $newleveldropdown = trim($_POST["newleveldropdown".$_POST["newLevelCourseID"]]);
        $newleveltechniques = trim($_POST["newleveltechniques".$_POST["editlevelID"]]);
        if (strlen($newlevel) < 1) {
            $newlevelmessage = "<span style=\"color: red\">New Level Name needs to be entered</span>";
        } else {
            //add new level
            $newlevelresult = yvts_level::createLevel($newlevelcourseID,$newlevel,$newleveldesc,$newlevelprice,$newleveldropdown,$newlevelsh,$newleveltechniques);
            if ($newlevelresult === true) {
                $newlevelmessage = "<span style=\"color: green\">New Level \"$newlevel\" created successfully</span>";
            } else {
                $newlevelmessage = "<span style=\"color: red\">$newlevelresult</span>";
            }
        }
    }
    
    $editlevelmessage = "";
    if (isset($_POST["Edit_Level"])) {
        //editing course
        //$_POST["editcourseID"];
        //$_POST["editcourse".$_POST["editcourseID"]];
        $editedlevelID = trim($_POST["editlevelID"]);
        $editedlevel = trim($_POST["editlevel".$_POST["editlevelID"]]);
        $editedleveldesc = trim($_POST["editleveldesc".$_POST["editlevelID"]]);
        $editedlevelsh = trim($_POST["editlevelsh".$_POST["editlevelID"]]);
        $editedlevelprice = trim($_POST["editlevelprice".$_POST["editlevelID"]]);
        $editedleveldropdown = trim($_POST["editleveldropdown".$_POST["editlevelID"]]);
        $editleveltechniques = trim($_POST["editleveltechniques".$_POST["editlevelID"]]);
        if (strlen($editedlevel) < 1) {
            $editlevelmessage = "<span style=\"color: red\">Edited Level Name needs to be entered</span>";
        } else {
            $editlevelresult = yvts_level::updateLevel($editedlevelID, $editedlevel, $editedleveldesc, $editedlevelprice, $editedleveldropdown, $editedlevelsh,$editleveltechniques);
            if ($editlevelresult === true) {
                $editlevelmessage = "<span style=\"color: green\">Edited Level \"$editedlevel\" saved successfully</span>";
            } else {
                $editlevelmessage = "<span style=\"color: red\">$editlevelresult</span>";
            }
        }
    }

    $deletelevelmessage = "";
    if (isset($_POST["deleteLevel"])) {
        $deleteLevelID = $_POST["deleteLevel"];
        $deletelevelresult = yvts_level::deleteLevel($deleteLevelID);
        if ($editlevelresult === true) {
            $deletelevelmessage = "<span style=\"color: green\">Deleted Level successfully</span>";
        } else {
            $deletelevelmessage = "<span style=\"color: red\">$deletelevelresult</span>";
        }
    }

    $newexammessage = "";
    if (isset($_POST["newExamLevelID"])) {
        $newExamLevelID = $_POST["newExamLevelID"];
        $newexam = trim($_POST["newexam".$_POST["newExamLevelID"]]);
        if (strlen($newexam) < 1) {
            $newexammessage = "<span style=\"color: red\">New Exam Name needs to be entered</span>";
        } else {
            //add new level
            $newexamresult = yvts_exam::createExam($newExamLevelID,$newexam);
            if ($newexamresult === true) {
                $newexammessage = "<span style=\"color: green\">New Exam \"$newexam\" created successfully</span>";
            } else {
                $newexammessage = "<span style=\"color: red\">$newexamresult</span>";
            }
        }
    }
    
    $editexammessage = "";
    if (isset($_POST["Edit_Exam"])) {
        $editedexamID = trim($_POST["editexamID"]);
        $editedexam = trim($_POST["editexam".$_POST["editexamID"]]);
        if (strlen($editedexam) < 1) {
            $editexammessage = "<span style=\"color: red\">Edited Exam Name needs to be entered</span>";
        } else {
            $editexamresult = yvts_exam::updateExam($editedexamID, $editedexam);
            if ($editexamresult === true) {
                $editexammessage = "<span style=\"color: green\">Edited Exam \"$editedexam\" saved successfully</span>";
            } else {
                $editexammessage = "<span style=\"color: red\">$editexammessage</span>";
            }
        }
    }
    
    $deleteexammessage = "";
    if (isset($_POST["deleteExam"])) {
        $deleteExamID = $_POST["deleteExam"];
        $deleteexamresult = yvts_exam::deleteExam($deleteExamID);
        if ($deleteexamresult === true) {
            $deleteexammessage = "<span style=\"color: green\">Deleted Exam successfully</span>";
        } else {
            $deleteexammessage = "<span style=\"color: red\">$deleteexamresult</span>";
        }
    }

    echo '<div class="wrap">';
    echo "<h2>List of courses (methods) on system, with levels for each course.</h2>";
    echo "<h3>TODO: Ability to delete a course with a warning it'll remove all the levels and exams inside it. What does that do about scheduled courses? Should prevent unless course can be deleted (user would need to remove levels, first)</h3>";

    $courseList = yvts_course::getCourses();
    echo "<p><strong>Course List: " . count($courseList) . " courses</strong></p>";
    echo "<p>$editcoursemessage $newlevelmessage $editlevelmessage $editexammessage $deleteexammessage</p>";
    foreach ($courseList as $course) {
        echo "<div class=\"yvts_course\"><h2>" . $course->name . "  ";
        if (strlen($course->description) > 0) { echo "<span class=\"yvts_course_description\">" . $course->description . "</span> "; }
        if (($course->longdescription != null) && (strlen($course->longdescription) > 0)) { echo "<span class=\"yvts_course_longdescription\">" . $course->longdescription . "</span> "; }
        echo "<span id=\"yvtsDisplayCourse" . $course->courseid . "\"><a class=\"yvts_course_edit\" onclick=\"document.getElementById('yvtsDisplayCourse" . $course->courseid . "').style.display = 'none'; document.getElementById('yvtsEditCourse" . $course->courseid . "').style.display = 'block';\">(edit)</a></span>";
        echo "</h2>";
        echo " Actions: Delete ";
        echo "<div id=\"yvtsEditCourse" . $course->courseid . "\" style=\"display: none;\">
		<form method=\"post\">
		<table><tr><td>
		<label class=\"yvts_edit_label\" for=\"editcourse" . $course->courseid . "\">Name</label></td><td><input type=\"text\" id=\"editcourse" . $course->courseid . "\" name=\"editcourse" . $course->courseid . "\" value=\"" . $course->name . "\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"editcoursedesc" . $course->courseid . "\">Description:</label></td><td><input id=\"editcoursedesc" . $course->courseid . "\" type=\"text\" name=\"editcoursedesc" . $course->courseid . "\" value=\"" . $course->description . "\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"editcourselongdesc" . $course->courseid . "\">Text Block:</label></td><td><textarea id=\"editcourselongdesc" . $course->courseid . "\" name=\"editcourselongdesc" . $course->courseid . "\">" . $course->longdescription . "</textarea></td></tr>
		<td><td colspan=\"2\"><input type=\"hidden\" name=\"editcourseID\" value=\"" . $course->courseid . "\" />
		<input type=\"submit\" name=\"Edit_Course\" value=\"Save Course\" /></td></tr></table>
		</form></div>";
        echo "<br />Levels within this course: " . count($course->levels) . "";
        foreach($course->levels as $level)
        { 
            echo "<div class=\"yvts_level\">Level: <span id=\"yvtsLevel" . $level->levelid . "\">" . $level->name . " ";
            if (strlen($level->description) > 0) { echo "<i>(" . $level->description . ")</i> "; }
            if ($level->levelprice == 0) { 
                echo "-P.O.A-";
            } else {
                echo "&pound;" . $level->levelprice;
            }
            echo " <a onclick=\"document.getElementById('yvtsLevel" . $level->levelid . "').style.display = 'none'; document.getElementById('yvtsEditLevel" . $level->levelid . "').style.display = 'block';\">(edit)</a>";
            echo " <form method=\"post\" style=\"display: inline\"><input type=\"hidden\" name=\"deleteLevel\" value=\"" . $level->levelid . "\" /><input type=\"submit\" class=\"yvts_delete_button\" name=\"Delete_Level\" value=\"Delete Level\" onclick=\"return confirm('Delete this level and all exams within it?');\" /></form>";
            echo "</span> ";
            echo "<div id=\"yvtsEditLevel" . $level->levelid . "\" style=\"display: none;\" class=\"yvts_level_edit_form\"><form method=\"post\">
				<table>
				<tr><td><label for=\"editlevel" . $level->levelid . "\">Name</label></td><td><input type=\"text\" name=\"editlevel" . $level->levelid . "\" value=\"" . $level->name . "\" /></td></tr>
			<tr><td><label for=\"editleveldesc" . $level->levelid . "\">Description</label></td><td><input type=\"text\" name=\"editleveldesc" . $level->levelid . "\" value=\"" . $level->description . "\" /></td></tr>
			<tr><td><label for=\"editlevelsh" . $level->levelid . "\">Subheading</label></td><td><input type=\"text\" name=\"editlevelsh" . $level->levelid . "\" value=\"" . $level->subheading . "\" /></td></tr>
			<tr><td><label for=\"editlevelprice" . $level->levelid . "\">Price</label></td><td><input type=\"text\" name=\"editlevelprice" . $level->levelid . "\" value=\"" . $level->levelprice . "\" /></td></tr>
			<tr><td><label for=\"editleveldropdown" . $level->levelid . "\">Dropdown</label></td><td><input type=\"text\" name=\"editleveldropdown" . $level->levelid . "\" value=\"" . $level->leveldropdown . "\" /> (Item1|Item2|Item3)</td></tr>
			<tr><td><label for=\"editleveltechniques" . $level->levelid . "\">Techniques</label></td><td><input type=\"text\" name=\"editleveltechniques" . $level->levelid . "\" value=\"" . $level->leveltechniques . "\" /> (Technique1|Technique2|Technique3)</td></tr>
			<tr><td colspan=\"2\"><input type=\"hidden\" name=\"editlevelID\" value=\"" . $level->levelid . "\" /><input type=\"submit\" name=\"Edit_Level\" value=\"Save Level\" /></td></tr>
			</table>
			</form></div>";
            echo "<br />"  . count($level->exams) . " exams offered <a id=\"yvtsShowExams" . $level->levelid . "\" onclick=\"document.getElementById('yvtsShowExams" . $level->levelid . "').style.display = 'none'; document.getElementById('yvtsexamsin" . $level->levelid . "').style.display = 'block';\">(show)</a> <a id=\"yvtsAddExam" . $level->levelid . "\" onclick=\"document.getElementById('yvtsAddExam" . $level->levelid . "').style.display = 'none'; document.getElementById('newexamin" . $level->levelid ."').style.display = 'block';\">(add)</a>";
            echo "<div style=\"display:none;\" id=\"newexamin" . $level->levelid ."\"><form method=\"post\" style=\"display: inline\"><label for=\"newexam" . $level->levelid . "\">Name</label><input type=\"text\" class=\"yvts_edit_exam_textbox\" name=\"newexam" . $level->levelid . "\" value=\"\" /><br /><input type=\"hidden\" name=\"newExamLevelID\" value=\"" . $level->levelid . "\" /><input type=\"submit\" name=\"Add_New_Exam\" value=\"Add New Exam to level " . $level->name . " in course " . $course->name . "\" /></form></div>";
            echo "</div>";
            echo "<div id=\"yvtsexamsin" . $level->levelid . "\" class=\"yvts_exam\" style=\"display:none\">";
            foreach($level->exams as $exam)
            {
                echo "<div class=\"yvts_exam\"><span id=\"yvtsExam" . $exam->examid . "\">" . $exam->name . " <a onclick=\"document.getElementById('yvtsExam" . $exam->examid . "').style.display = 'none'; document.getElementById('yvtsEditExam" . $exam->examid . "').style.display = 'block';\">(edit)</a> ";
                echo " <form method=\"post\" style=\"display: inline\"><input type=\"hidden\" name=\"deleteExam\" value=\"" . $exam->examid . "\" /><input type=\"submit\" class=\"yvts_delete_button\" name=\"Delete_Exam\" value=\"Delete Exam\" onclick=\"return confirm('Delete this exam?');\" /></form>";
                echo " </span><br />";
                echo "<div id=\"yvtsEditExam" . $exam->examid . "\" style=\"display: none;\"><form method=\"post\"><label for=\"editexam" . $exam->examid . "\">Name</label><input class=\"yvts_edit_exam_textbox\" type=\"text\" name=\"editexam" . $exam->examid . "\" value=\"" . $exam->name . "\" /><input type=\"hidden\" name=\"editexamID\" value=\"" . $exam->examid . "\" /><input type=\"submit\" name=\"Edit_Exam\" value=\"Save Exam\" /></form></div>";
                echo "</div>";
            }
            echo "</div>";
        }
        echo "<p><a onclick=\"document.getElementById('newlevelin" . $course->courseid ."').style.display = 'block';\">Add a new level within " . $course->name . "</a> <div style=\"display:none;\" id=\"newlevelin" . $course->courseid ."\"><form method=\"post\" style=\"display: inline\">
		  <table><tr><td><label for=\"newlevel" . $course->courseid . "\">Name</label></td><td><input type=\"text\" name=\"newlevel" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"newleveldesc" . $course->courseid . "\">Description</label></td><td><input type=\"text\" name=\"newleveldesc" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"newlevelsh" . $course->courseid . "\">Subheading</label></td><td><input type=\"text\" name=\"newlevelsh" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"newlevelprice" . $course->courseid . "\">Price</label></td><td><input type=\"text\" name=\"newlevelprice" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"newleveldropdown" . $course->courseid . "\">Drop down</label></td><td><input type=\"text\" name=\"newleveldropdown" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td><label class=\"yvts_edit_label\" for=\"newleveltechniques" . $course->courseid . "\">Techniques</label></td><td><input type=\"text\" name=\"newleveltechniques" . $course->courseid . "\" value=\"\" /></td></tr>
		<tr><td colspan=\"2\"><input type=\"hidden\" name=\"newLevelCourseID\" value=\"" . $course->courseid . "\" /><input type=\"submit\" name=\"Add_New_Level\" value=\"Add New Level to " . $course->name . "\" /></td></tr></table></form></div></p>";
        
        echo "</div>";
    }
    echo "<p>$newcoursemessage<br /><a onclick=\"document.getElementById('newCourse').style.display = 'block';\">Add a new course</a><div style=\"display:none;\" id=\"newCourse\"> <form method=\"post\">
	<label class=\"yvts_edit_label\" for=\"newcourse\">Name</label><input type=\"text\" name=\"newcourse\" value=\"$newcourse\" /><br />
	<label class=\"yvts_edit_label\" for=\"newcoursedesc\">Description</label><input type=\"text\" id=\"newcoursedesc\" name=\"newcoursedesc\" value=\"$newcoursedesc\" /><br />
	<label class=\"yvts_edit_label\" for=\"newcourselongdesc\">Text Block</label><textarea id=\"newcourselongdesc\" name=\"newcourselongdesc\" />$newcourselongdesc</textarea><br />
	<input type=\"submit\" name=\"Add_new_course\" value=\"Add New Course\" />
	</form>
	</div></p>";

    echo "<div class=\"yvts_footer\">Database Version: " . get_option("yvts_coursemanager_db_version") . "</div>";

    echo '</div>';
}

?>