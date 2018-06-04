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
    if ($course == false) {
        $schedulepage = get_option("yvts_coursemanager_schedule_page");
        if ($schedulepage == false) { $schedulepage = ""; }
        $output = $output . "This application form cannot be accessed directly - <a href=\"" . $schedulepage . "\">please select your course from the schedule first.</a>"; }
    elseif ($course !== false) {
       
        /*
        $nextCourseID = yvts_courseRunning::getNextCourse($_GET["yvtscourse"]);
        if ($nextCourseID != false) {
            $nextCourse = yvts_courseRunning::getCourseRunningDetails($nextCourseID);
        }
        $previousCourseID = yvts_courseRunning::getPreviousCourse($_GET["yvtscourse"]);
        if ($previousCourseID != false) {
            $previousCourse = yvts_courseRunning::getCourseRunningDetails($previousCourseID);
        }
        */
    
        $fields = yvts_application::getApplicationFields();
			
        $output = $output . "<div class=\"yvts_course_application_header\">Application form for " . $course->coursename . " " . $course->levelname . " <span class=\"yvts_course_description\"> " . $course->coursedesc . "</span>";
        if ($course->endtimeU > 0) {
            $output = $output .   "<p>Course running from " . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . "</p>";
        } else {
            $output = $output . "<p>Course in " . date("Y",$course->starttimeU) . " scheduled on demand.</p>";
        }
        $output = $output . "</div>";

        /*
        if ((isset($nextCourse)) && ($nextCourse != false)) {
            $output = $output . "The next course running is " . $nextCourse->coursename . " " . $nextCourse->levelname . " <span class=\"yvts_course_description\"> " . $nextCourse->coursedesc . "</span>";
            if ($nextCourse->endtimeU > 0) {
                $output = $output .   "<p>This next course is running from " . date("d M Y",$nextCourse->starttimeU) . " to " . date("d M Y",$nextCourse->endtimeU) . "";
            } else {
                $output = $output . "<p>Course in " . date("Y",$nextCourse->starttimeU) . " scheduled on demand.";
            }
            $output = $output . " <a href=\"" . add_query_arg("yvtscourse",$nextCourse->courseRunning_ID,$applicationpage) . "\" target=\"_blank\">Also apply for this " . $nextCourse->coursename . " course.</a>";
            $output = $output . "</p>";
        }

        if ((isset($previousCourse)) && ($previousCourse != false)) {
            $output = $output . "The preceding course running is " . $previousCourse->coursename . " " . $previousCourse->levelname . " <span class=\"yvts_course_description\"> " . $previousCourse->coursedesc . "</span>";
            if ($previousCourse->endtimeU > 0) {
                $output = $output .   "<p>This next course is running from " . date("d M Y",$previousCourse->starttimeU) . " to " . date("d M Y",$previousCourse->endtimeU) . "";
            }
            $output = $output . " <a href=\"" . add_query_arg("yvtscourse",$previousCourse->courseRunning_ID,$applicationpage) . "\" target=\"_blank\">Also apply for this " . $previousCourse->coursename . " course.</a>";
            $output = $output . "</p>";
        }
        */

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
            $errors = "";

            $text = "Course Name: " . $course->coursename . " (" . $course->coursedesc . ")\r\n<br />";
            $csvoutputtop = "\"Course\",\"Course_Start\",\"Course_End\",";
            $csvoutputdata = "\"" . $course->coursename . " " . $course->levelname . "\",";
            if ($course->endtimeU > 0) {
                $csvoutputdata = $csvoutputdata . "\"" . date("d M Y",$course->starttimeU) . "\",\"" . date("d M Y",$course->endtimeU) . "\",";
                $text = $text . "Course Start: " . date("d M Y",$course->starttimeU) . "\r\n<br />";
                $text = $text . "Course End: " . date("d M Y",$course->endtimeU) . "\r\n<br />";
            } else {
                $csvoutputdata = $csvoutputdata . "\"" . date("Y",$course->starttimeU) . "\",\"N/A\",";
                $text = $text . "Course Start: " . date("Y",$course->starttimeU) . "\r\n<br />";
            }

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
                } else if ($fields[$i]->type == "examchoice") {
                    $exams = yvts_exam::getExams($course->levelid);
                    $examsticked = "";
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . "_default"])) {
                        $examsticked = $examsticked . $_POST["yvts_field_" . $fields[$i]->applicationid . "_default"];
                    }
                    for ($j = 0; $j < count($exams); $j++) {
                        if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid])) {
                            $examsticked = $examsticked . " - " . $_POST["yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid];
                        }
                    }
                    $text = $text . stripcslashes($fields[$i]->name) . " : " . $examsticked . "\r\n<br />";
                    $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                    $csvoutputdata = $csvoutputdata . "\"" . $examsticked . "\",";
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
                            $errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
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
            $emailtarget = get_option("yvts_coursemanager_email");
            $emailsubject = get_option("yvts_coursemanager_email_subject");
            if ($emailsubject == false) { $emailsubject = "Course data submitted"; }
            $emailfrom = get_option("yvts_coursemanager_email_from");
            if ($emailfrom == false) { $emailfrom = $emailtarget; }
            $sendoutput = "";
            if ($emailtarget != false) {
                $headers = "Content-Type: text/html; charset=UTF-8";
                //\r\nFrom: YVTS Course Manager &lt;" . $emailfrom . "&gt;";
                $email_status = wp_mail($emailtarget,$emailsubject,$text,$headers);
                if ($email_status) {
                    $sendoutput = "Your application was sent to our team, thank you";
                } else {
                    $errors = "yes";
                    $sendoutput = "Your application could not be sent to our team, sorry";
                    $sendoutput = $soundoutput . "<br />wp_mail($emailtarget,$emailsubject,$text,$headers);";
                }
            } else {
                $errors = "yes";
                $sendoutput = "Email cannot be sent - please add an email target in the settings.";
            }
                $output = $output . "<div style=\"background-color: ";
                if ($errors == "" ) { $output = $output . "light green"; } else { $output = $output . "pink"; }
                $output = $output . "\"><p>$sendoutput</p>
                " . $text . "
                </div>";
            } else {
                $output = $output . "<div style=\"background-color: pink\"><p>Errors: $errors</p><p>form data submitted at " . date("r") . "</p><p>" . $text . "</p></div>";
            }
        }

        $output = $output . "
        <form method=\"post\" action=\"" . add_query_arg(null,null) . "\" onsubmit=\"return yvts_validate_application();\" >
            <input type=\"hidden\" name=\"yvtscourse\" value=\"" . $_GET["yvtscourse"] . "\" />\n";
			
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
                } elseif ($fields[$i]->type == "examchoice") {
                    $exams = yvts_exam::getExams($course->levelid);
                    $output = $output . "<div class=\"yvts_exam_box\">";

                    for ($j = 0; $j < count($exams); $j++) {
                        $output = $output . "<div class=\"yvts_exam_line\">";
                        $output = $output . "<input type=\"checkbox\" id=\"yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid . "\" ";
                        $output = $output . " onchange=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid . "'," . $fields[$i]->minlength . ");\"  value=\"" . $exams[$j]->name . "\" ";
                        if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid . ""])) { $output = $output . " checked=\"checked\""; }
                        $output = $output . "/>\n";
                        
                        $output = $output . "<label class=\"yvts_examlabel\" for=\"yvts_field_" . $fields[$i]->applicationid . "_" . $exams[$j]->examid . "\">" . stripcslashes($exams[$j]->name) . "</label>";
                        
                        $output = $output . "</div>";
                    }

                    $output = $output . "<div class=\"yvts_exam_line\">";
                    $output = $output . "<input type=\"checkbox\" id=\"yvts_field_" . $fields[$i]->applicationid . "_default\" name=\"yvts_field_" . $fields[$i]->applicationid . "_default\" ";
                    $output = $output . " onchange=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "_default'," . $fields[$i]->minlength . ");\" value=\"Contact ISA if your specifications are not listed\"" ;
                    if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . "_default"])) { $output = $output . " checked=\"checked\""; }
                    $output = $output . "/>\n";
                    $output = $output . "<label class=\"yvts_examlabel\" for=\"yvts_field_" . $fields[$i]->applicationid . "_default\">Contact ISA if your specifications are not listed</label>";
                    
                    $output = $output . "</div>";

                    $output = $output . "</div>";
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
        
    }
    return $output;
}

?>