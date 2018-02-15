<?php

function yvts_coursemanager_application($attributes) {
    
    //yvtscourse
    //yvtsexam
    //yvts_company

    $course = false;
    if ((isset($_GET["yvtscourse"])) && (is_numeric($_GET["yvtscourse"]))) {
        $course = yvts_courseRunning::getCourseRunningDetails($_GET["yvtscourse"]);
    }
    if ($course == false) { echo "(submitted level " . $_GET["yvtscourse"] . ")"; }
    if ($course !== false) {
		
		
			$fields = yvts_application::getApplicationFields();
			
			
        echo "Application form for " . $course->coursename . " " . $course->levelname . " <span class=\"yvts_course_description\"> " . $course->coursedesc . "</span>";
        if ($course->endtimeU > 0) {
            echo "<p>Course running from " . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . "</p>";
        } else {
            echo "<p>Course in " . date("Y",$course->starttimeU) . " scheduled on demand.</p>";
        }

        echo "
        <script type=\"text/javascript\">
        function yvts_validate_application() {
            var error = \"\";

			";
			
			for ($i = 0; $i < count($fields); $i++) {
				//foreach field
				//$fields[$i]->name;
				//$fields[$i]->type;
				//$fields[$i]->position;
				//$fields[$i]->minlength;
				//$fields[$i]->hint;
                //$fields[$i]->applicationid;
                
                if (($fields[$i]->type == "checkbox") && ($fields[$i]->minlength != 0)) {
                    echo "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").checked != true) {
                        error = error + \"" . $fields[$i]->name . ": " . $fields[$i]->hint . "\\n\\n\";
                    }\n";
                } else if (($fields[$i]->type == "textbox") || ($fields[$i]->type == "textarea")) {
                    echo "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").value.length < " . $fields[$i]->minlength . ") {
					    error = error + \"" . $fields[$i]->name . ": " . $fields[$i]->hint . "\\n\\n\";
                    }\n";
                }
			}
			
			echo "

            if (error != \"\") {
                alert(\"Problem with application form:\\n\" + error);
                return false;
            }
            return true;
        }

        function yvts_validate_box(boxname, minlength) {
            var elem = document.getElementById(boxname);
            if (elem != null) {
                if ((elem.type == \"checkbox\") && (minlength != 0)) {
                    var elemcheckname = boxname+\"_checkbox\";
                    var elemcheck = document.getElementById(elemcheckname);
                    if (elemcheck != null) {
                        if (elem.checked) {
                            elemcheck.style.borderColor = \"#d1ffd2\";
                        } else {
                            elemcheck.style.borderColor = \"pink\";
                        }
                    }
                } else {   
                    if (elem.value.length > minlength) {
                        elem.style.backgroundColor = \"#d1ffd2\";
                    } else {
                        elem.style.backgroundColor = \"pink\";
                    }
                }
            }
        }

        </script>
        ";

        if (isset($_POST["yvts_student"])) {
            echo "<div style=\"color: green\">TEST: form data submitted at " . date("r") . "</div>";
        }

        echo "
        <form method=\"post\" action=\"" . add_query_arg(null,null) . "\" onsubmit=\"return yvts_validate_application();\" >
            <input type=\"hidden\" name=\"yvtscourse\" value=\"" . $_GET["yvtscourse"] . "\" />\n
            <label for=\"yvtsexam\">Exam Specification:</label> <select id=\"yvtsexam\" name=\"yvtsexam\">\n";
            $exams = yvts_exam::getExams($course->levelid);
            if (count($exams) == 0) {
                echo "<option selected=\"selected\">Contact ISA for your specification</option>\n";
            } else {
                for ($i = 0; $i < count($exams); $i++) {
                    echo "<option value=\"" . $exams[$i]->examid . "\"";
                    if ($_POST["yvtsexam"] == $exams[$i]->examid) { echo " selected=\"selected\" "; }
                    echo ">" . $exams[$i]->name . "</option>\n";
                }
                echo "<option value=\"-1\"";
                if ($_POST["yvtsexam"] == -1) { echo " selected=\"selected\" "; }
                echo ">Contact ISA for your specification if it's not in this list.</option>\n";
            }
            echo "
            </select>\n";
			
			
			for ($i = 0; $i < count($fields); $i++) {
				//foreach field
				//$fields[$i]->name;
				//$fields[$i]->type;
				//$fields[$i]->position;
				//$fields[$i]->minlength;
				//$fields[$i]->hint;
				//$fields[$i]->applicationid;
				echo "<div class=\"yvts_input_text";
				if ($fields[$i]->minlength > 0) { echo " yvts_mandatory"; }
				echo "\">";
                echo "<label for=\"yvts_field_" . $fields[$i]->applicationid . "\">" . $fields[$i]->name . ":</label>";
                if ($fields[$i]->type == "textbox") {
				echo "<input type=\"text\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"" . $_POST["yvts_field_" . $fields[$i]->applicationid . ""] . "\"";
				if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) > $fields[$i]->minlength) { echo " style=\"background-color: #d1ffd2\" "; } else { echo " style=\"background-color: pink\" "; }
                echo " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" />";
                } elseif ($fields[$i]->type == "textarea") {
                    echo "<textarea id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                    if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) > $fields[$i]->minlength) { echo " style=\"background-color: #d1ffd2\" "; } else { echo " style=\"background-color: pink\" "; }
                    echo " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\">\n" . $_POST["yvts_field_" . $fields[$i]->applicationid . ""] . "</textarea>\n";

                } elseif ($fields[$i]->type == "checkbox") {
                    echo "<input type=\"checkbox\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                    echo " onchange=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" ";
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { echo " checked=\"checked\""; }
                    echo "/>\n";
                    echo "<div id=\"yvts_field_" . $fields[$i]->applicationid . "_checkbox\" class=\"yvts_field_checkbox\" ";
                     if ($fields[$i]->minlength != 0) {
                        echo " style=\"border-left: 10px solid;";
                        if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { echo " border-color: #d1ffd2;\" "; } else { echo " border-color: pink;\""; }
                    }
                    echo ">"  . $fields[$i]->hint . "</div>\n";
                } else {
                    echo "<p>Can't handle type: " . $fields[$i]->type . "</p>\n";
                }
				echo "</div>";
			}
			
			echo "
            <div class=\"yvts_input_text\">
            <input type=\"submit\" id=\"submit_button\" name=\"submit_button\" value=\"Send Application Form\" />
            </div>
        </form>
        ";
        
    } else {
        echo " show application form for all levels";
    }
}

?>