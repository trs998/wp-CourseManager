<?php

function yvts_coursemanager_application($attributes) {
    
    $output = "";
    //yvtscourse
    //yvtsexam
    //yvts_company

    $course = false;
    if ((isset($_GET["yvtscourse"])) && (is_numeric($_GET["yvtscourse"]))) {
        $course = yvts_courseRunning::getCourseRunningDetails($_GET["yvtscourse"]);
    }
    if ($course == false) { $output = $output . "(submitted level " . $_GET["yvtscourse"] . ")"; }
    if ($course !== false) {
		
		
			$fields = yvts_application::getApplicationFields();
			
			
        $output = $output . "Application form for " . $course->coursename . " " . $course->levelname . " <span class=\"yvts_course_description\"> " . $course->coursedesc . "</span>";
        if ($course->endtimeU > 0) {
            $output = $output .   "<p>Course running from " . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . "</p>";
        } else {
            $output = $output . "<p>Course in " . date("Y",$course->starttimeU) . " scheduled on demand.</p>";
        }

        $output = $output . "
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
                    $output = $output . "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").checked != true) {
                        error = error + \"" . $fields[$i]->name . ": \";
                        ";
                        if (strlen($fields[$i]->hint) > 1) {
                        $output = $output . "error = error + \"" . stripcslashes($fields[$i]->hint) . "\";";
                        } else {
                        $output = $output . "error = error + \"Must be at least " . $fields[$i]->minlength . " letters.\";";
                        }
                        $output = $output . "
                        error = error + \"\\n\\n\";
                    }\n";
                } else if (($fields[$i]->minlength > 0) && (($fields[$i]->type == "textbox") || ($fields[$i]->type == "textarea"))) {
                    $output = $output . "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").value.length < " . $fields[$i]->minlength . ") {
					    error = error + \"" . $fields[$i]->name . ": \";
                        ";
                        if (strlen($fields[$i]->hint) > 1) {
                            $output = $output . "error = error + \"" . stripcslashes($fields[$i]->hint) . "\";";
                        } else {
                            $output = $output . "error = error + \"Must be at least " . $fields[$i]->minlength . " letters.\";";
                        }
                        $output = $output . "
                        error = error + \"\\n\\n\";
                    }\n";
                }
			}
			
			$output = $output . "

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
                    if (elem.value.length >= minlength) {
                        elem.style.backgroundColor = \"#d1ffd2\";
                    } else {
                        elem.style.backgroundColor = \"pink\";
                    }
                }
            }
        }
        

        </script>
        ";

        if (isset($_POST["submit_button"])) {
            
            $csvoutputtop = "";
            $csvoutputdata = "";
            $text = "";
            $errors = "";

			for ($i = 0; $i < count($fields); $i++) {
				//foreach field
				//$fields[$i]->name;
				//$fields[$i]->type;
				//$fields[$i]->position;
				//$fields[$i]->minlength;
				//$fields[$i]->hint;
                //$fields[$i]->applicationid;
                
                if (($fields[$i]->type == "checkbox") && ($fields[$i]->minlength != 0)) {
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid])) {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . "checked\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"true\",";
                    } else {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . "NOT checked\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"false\",";
                        $errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
                    }
                } else if ($fields[$i]->type == "checkbox") {
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid])) {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . "checked\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"true\",";
                    } else {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . "NOT checked\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"false\",";
                    }
                
                } else if ($fields[$i]->type == "captcha") {
                    $captchapublic = get_option("yvts_coursemanager_captcha_public");
                    $captchaprivate = get_option("yvts_coursemanager_captcha_private");
                    if (($captchaprivate != "") && ($captchaprivate != $captchapublic) && ($captchapublic != "")) {
                        // captcha was sent user-side - see if the result is valid.
                        $post_data = "secret=$captchaprivate&response=".
                        $_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR'] ;

                        $ch = curl_init();  
                        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, 
                        array('Content-Type: application/x-www-form-urlencoded; charset=utf-8', 
                        'Content-Length: ' . strlen($post_data)));
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); 
                        $googresp = curl_exec($ch);       
                        $decgoogresp = json_decode($googresp);
                        curl_close($ch);

                        if ($decgoogresp->success != true) {
                            $errors = $errors . stripcslashes($fields[$i]->name) . ": " . $fields[$i]->hint . "\n";
                        }
                    }
                
                } else if (($fields[$i]->minlength > 0) && (($fields[$i]->type == "textbox") || ($fields[$i]->type == "textarea"))) {
                    
                    if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid])) && (strlen($_POST["yvts_field_" . $fields[$i]->applicationid]) >= $fields[$i]->minlength)) {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
                    } else {
                        $text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
                        $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                        $csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
                        $errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
                    }
                } else if (($fields[$i]->type == "textbox") || ($fields[$i]->type == "textarea")) {
                    $text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
                    $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                    $csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
                }
            }
            
            if ($errors == "") {
                $output = $output . "<div style=\"background-color: light green\"><p>TEST: form data submitted at " . date("r") . "</p><p>" . $text . "</p></div>";
            } else {
                $output = $output . "<div style=\"background-color: pink\"><p>Errors: $errors</p><p>form data submitted at " . date("r") . "</p><p>" . $text . "</p></div>";
            }
        }

        $output = $output . "
        <form method=\"post\" action=\"" . add_query_arg(null,null) . "\" onsubmit=\"return yvts_validate_application();\" >
            <input type=\"hidden\" name=\"yvtscourse\" value=\"" . $_GET["yvtscourse"] . "\" />\n
            <label for=\"yvtsexam\">Exam Specification:</label> <select id=\"yvtsexam\" name=\"yvtsexam\">\n";
            $exams = yvts_exam::getExams($course->levelid);
            if (count($exams) == 0) {
                $output = $output . "<option selected=\"selected\">Contact ISA for your specification</option>\n";
            } else {
                for ($i = 0; $i < count($exams); $i++) {
                    $output = $output . "<option value=\"" . $exams[$i]->examid . "\"";
                    if ($_POST["yvtsexam"] == $exams[$i]->examid) { $output = $output . " selected=\"selected\" "; }
                    $output = $output . ">" . $exams[$i]->name . "</option>\n";
                }
                $output = $output . "<option value=\"-1\"";
                if ($_POST["yvtsexam"] == -1) { $output = $output . " selected=\"selected\" "; }
                $output = $output . ">Contact ISA for your specification if it's not in this list.</option>\n";
            }
            $output = $output . "
            </select>\n";
			
			
			for ($i = 0; $i < count($fields); $i++) {
				//foreach field
				//$fields[$i]->name;
				//$fields[$i]->type;
				//$fields[$i]->position;
				//$fields[$i]->minlength;
				//$fields[$i]->hint;
				//$fields[$i]->applicationid;
				$output = $output . "<div class=\"yvts_input_text";
				if ($fields[$i]->minlength > 0) { $output = $output . " yvts_mandatory"; }
                $output = $output . "\">";
                if ($fields[$i]->type != "heading") {
                    $output = $output . "<label for=\"yvts_field_" . $fields[$i]->applicationid . "\">" . stripcslashes($fields[$i]->name) . ":</label>";
                }
                if ($fields[$i]->type == "textbox") {
                    $output = $output . "<input type=\"text\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"" . $_POST["yvts_field_" . $fields[$i]->applicationid . ""] . "\"";
                if ($fields[$i]->minlength > 0) { //set hint colours
                if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
                }
                $output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" />";
                } elseif ($fields[$i]->type == "textarea") {
                    $output = $output . "<textarea id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                    if ($fields[$i]->minlength > 0) { //set hint colours
                        if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
                    }
                    $output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\">\n" . $_POST["yvts_field_" . $fields[$i]->applicationid . ""] . "</textarea>\n";

                } elseif ($fields[$i]->type == "checkbox") {
                    $output = $output . "<input type=\"checkbox\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                    $output = $output . " onchange=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" ";
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { $output = $output . " checked=\"checked\""; }
                    $output = $output . "/>\n";
                    $output = $output . "<div id=\"yvts_field_" . $fields[$i]->applicationid . "_checkbox\" class=\"yvts_field_checkbox\" ";
                     if ($fields[$i]->minlength != 0) {
                        $output = $output . " style=\"border-left: 10px solid;";
                        if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { $output = $output . " border-color: #d1ffd2;\" "; } else { $output = $output . " border-color: pink;\""; }
                    }
                    $output = $output . ">"  . stripcslashes($fields[$i]->hint) . "</div>\n";
                } elseif ($fields[$i]->type == "captcha") {
                    $captchapublic = get_option("yvts_coursemanager_captcha_public");
                    $captchaprivate = get_option("yvts_coursemanager_captcha_private");
                    if (($captchaprivate != "") && ($captchaprivate != $captchapublic) && ($captchapublic != "")) {
                        $output = $output . "<div class=\"g-recaptcha\" data-sitekey=\"" . $captchapublic . "\"></div>";
                    } else {
                        $output = $output . "Captcha settings not complete.";
                    }
                } elseif ($fields[$i]->type == "heading") {
                    $output = $output . "<div class=\"yvts_heading\">" . stripcslashes($fields[$i]->name) . "</div>";
                    $output = $output . "<div class=\"yvts_textblock\">" . stripcslashes($fields[$i]->hint) . "</div>";
                } else {
                    $output = $output . "<p>Can't handle type: " . $fields[$i]->type . "</p>\n";
                }
				$output = $output . "</div>";
			}
			
			$output = $output . "
            <div class=\"yvts_input_text\">
            <input type=\"submit\" id=\"submit_button\" name=\"submit_button\" value=\"Send Application Form\" />
            </div>
        </form>
        ";
        
    } else {
        $output = $output . " show application form for all levels";
    }
    return $output;
}

?>