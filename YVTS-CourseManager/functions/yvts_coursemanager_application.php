<?php

function yvts_coursemanager_application($attributes) {
    $output = "";
    //yvtscourse
    //yvtsexam
    //yvts_company
	$MAX_FILESIZE = 15 * 1024 * 1024; //15mb;
	$ALLOWED_MIME_TYPES = ['image/png', 'image/jpeg', 'application/pdf'];

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
    
        $fields = yvts_application::getApplicationFields($course->applicationformid);
			
        $output = $output . "<div class=\"yvts_course_application_header\">Application form for " . $course->coursename . " " . $course->levelname;
		
        if (((get_option("yvts_coursemanager_suppress_description") == true) && ($course->endtimeU > 0)) || (get_option("yvts_coursemanager_suppress_description") == false)) {
            $output = $output . "<span class=\"yvts_course_description\"> " . $course->coursedesc;
			if (strlen($course->note) > 0) { $output = $output . " - " . $course->note; }
			$output = $output . "</span>";
        }

        if ($course->endtimeU > 0) {
			if ($course->choosedaywithin > 0) {
				$output = $output .   "<p>" . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . " - please select which day(s) you need within this range.</p>";
			} else {
				$output = $output .   "<p>" . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . "</p>";
			}
        } else {
            $output = $output . "<p>Course in " . date("Y",$course->starttimeU) . " scheduled on demand.</p>";
        }
        $output = $output . "</div>";

        $output = $output . "
        <script type=\"text/javascript\">
        function yvts_validate_application() {
            var error = \"\";

			";
			
			if ($course->choosedaywithin > 0) {
				//add validator for date checkboxes
				$output .= "
				var items = document.querySelectorAll(\".yvts_date_chooser > input\");
				if ((items != null) && (items.length > 0)) {
					selected = 0;
					for (itemi = 0; itemi < items.length; itemi++) {
						if (items[itemi].checked == true) { selected++; }
					}
					if (selected == 0) {
						error = error + \"Please select at least one day\\n\\n\";
					}
				}
				";
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
                } else if (($fields[$i]->minlength > 0) && (($fields[$i]->type == "dropdown") || ($fields[$i]->type == "leveldropdown"))) {
                    $output = $output . "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").value.length < " . $fields[$i]->minlength . ") {
					    error = error + \"" . $fields[$i]->name . ": \";
                        ";
                        $output = $output . "error = error + \"Must have an option selected\";";
                        $output = $output . "
                        error = error + \"\\n\\n\";
                    }\n";
                } /*else if (($fields[$i]->minlength > 0) && ($fields[$i]->type == "techniquescheckbox")) {
                    $output = $output . "if (document.getElementsByName(\"yvts_field_" . $fields[$i]->applicationid . "\").value.length < " . $fields[$i]->minlength . ") {
					    error = error + \"" . $fields[$i]->name . ": \";
                        ";
                        $output = $output . "error = error + \"Must have an option selected\";";
                        $output = $output . "
                        error = error + \"\\n\\n\";
                    }\n";
                }*/
					 /* else if (($fields[$i]->minlength > 0) && (($fields[$i]->type == "techniques") || ($fields[$i]->type == "leveltechnique"))) {
                    $output = $output . "if (document.getElementById(\"yvts_field_" . $fields[$i]->applicationid . "\").value.length < " . $fields[$i]->minlength . ") {
					    error = error + \"" . $fields[$i]->name . ": \";
                        ";
                        $output = $output . "error = error + \"Must have an option selected\";";
                        $output = $output . "
                        error = error + \"\\n\\n\";
                    }\n";
                }*/
			}
			
			$output = $output . "

            if (error != \"\") {
                alert(\"Problem with application form:\\n\" + error);
                return false;
            }
            return true;
        }
		
		function yvts_validate_datechooser() {
			var items = document.querySelectorAll(\".yvts_date_chooser > input\");
			if ((items != null) && (items.length > 0)) {
				selected = 0;
				for (itemi = 0; itemi < items.length; itemi++) {
					if (items[itemi].checked == true) { selected++; }
				}
				if (selected == 0) {
					for (itemi = 0; itemi < items.length; itemi++) {
						var tage = document.getElementById(items[itemi].id+\"-tag\");
						if (tage != null) {	tage.style.backgroundColor = \"pink\"; }
					}
				} else {
					for (itemi = 0; itemi < items.length; itemi++) {
						var tage = document.getElementById(items[itemi].id+\"-tag\");
						if (tage != null) {	tage.style.backgroundColor = \"#d1ffd2\"; }
					}
				}
			}
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

        function yvts_validate_multiselect(boxname, minlength) {
            var elem = document.getElementById(boxname);
            if (elem != null) {
							var count = 0;
							for (var i=0; i < elem.options.length; i++) {
								if (elem.options[i].selected) count++; 
							}
                    if (count >= minlength) {
                        elem.style.backgroundColor = \"#d1ffd2\";
                    } else {
                        elem.style.backgroundColor = \"pink\";
                    }
                }
        }

        function yvts_validate_multicheckbox(boxname, minlength) {
            var elem = document.getElementsByName(boxname);
            if (elem != null) {
							var count = 0;
							for (var i=0; i < elem.length; i++) {
								if (elem[i].checked) count++; 
							}
							for (var i=0; i < elem.length; i++) {
								var elemlabel = document.getElementById(elem[i].id+'-label');
								if (elemlabel != null) {
								  if (count >= minlength) {
										elemlabel.style.borderColor = \"#d1ffd2\";
								  } else {
										elemlabel.style.borderColor = \"pink\";
								  }
								}
							}
                }
        }
        

        </script>
        ";

$submittedform = false;

        if (isset($_POST["submit_button"])) {
            $errors = "";
			$attachments = array();

            $text = "Method: " . $course->coursename . " (" . $course->coursedesc . ")\r\n<br />";
            $text = $text . "Level: " . $course->levelname . " (" . $course->leveldesc . ")\r\n<br />";
            $csvoutputtop = "\"Method\",\"Level\",\"Course Description\",\"Level Description\",\"Course_Start\",\"Course_End\",";
            $csvoutputdata = "\"" . $course->coursename . "\",\"" . $course->levelname . "\",\"" . $course->coursedesc . "\",\"" . $course->leveldesc . "\",";
            if ($course->endtimeU > 0) {
                $csvoutputdata = $csvoutputdata . "\"" . date("d M Y",$course->starttimeU) . "\",\"" . date("d M Y",$course->endtimeU) . "\",";
                $text = $text . "Course Start: " . date("d M Y",$course->starttimeU) . "\r\n<br />";
                $text = $text . "Course End: " . date("d M Y",$course->endtimeU) . "\r\n<br />";
				
				if ($course->choosedaywithin > 0) {
					//user should have submitted at least one date
					$userDates = $_POST["yvts_dates"];
					$userDatesString = "";
					foreach($userDates as $value){
					  if ($userDatesString != "") { $userDatesString .= ", "; }
					  $userDatesString .= $value;
					}
					$text .= "User Requested Dates: " . $userDatesString . "\r\n<br />";
					$csvoutputtop .= ",\"User Requested Dates\"";
					$csvoutputdata .= ",\"" . $csvoutputdata . "\"";
					//FIXME
				}
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
							 } else if (($fields[$i]->type == "dropdown") || ($fields[$i]->type == "leveldropdown")) {
								if ((strlen($_POST["yvts_field_" . $fields[$i]->applicationid]) < 1) && ($fields[$i]->minlength >= 1)) { //if dropdown but not selected
									$text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
									$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
									$csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
											$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
								} else { //if dropdown but not required
								//not required, add whatever has arrived
									$text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
									$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
									$csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
								}
							} else if (($fields[$i]->type == "techniques") || ($fields[$i]->type == "leveltechniques")) {
							/*	$text = $text . "Field $i is techniques - ";
								$text = $text . var_dump($_POST["yvts_field_" . $fields[$i]->applicationid]);*/
							$valueArray = yvts_level::getTechniques($course->levelid); ////FIXME explode("|",$fields[$i]->hint);
							if ((count($valueArray) > 0) && ($valueArray[0]!= "")) {
								$minlength = $fields[$i]->minlength;
								if ($minlength > count($valueArray)) {
									$minlength = count($valueArray);
								}
								if (isset($_POST["yvts_field_" . $fields[$i]->applicationid])) {
									if ((is_array($_POST["yvts_field_" . $fields[$i]->applicationid])) && (count($_POST["yvts_field_" . $fields[$i]->applicationid]) > 0)) {
										$techniquesSub = "";
										foreach ($_POST["yvts_field_" . $fields[$i]->applicationid] as $techn) {
											$techniquesSub = $techniquesSub . ", "  . $techn;
										}
										$text = $text . stripcslashes($fields[$i]->name) . " : " . $techniquesSub . "\r\n<br />";
										$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
										$csvoutputdata = $csvoutputdata . "\"" . $techniquesSub . "\",";
										if (($fields[$i]->minlength >= 1) && (count($_POST["yvts_field_" . $fields[$i]->applicationid]) < $fields[$i]->minlength)) {
											$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
										}
									} else {
										$text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
										$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
										$csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
										$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
									}
								}//end if this field is sent
							} //end if this field has techniques to select at all
                } else if (($fields[$i]->type == "techniquescheckbox") || ($fields[$i]->type == "leveltechniquescheckbox")) {
							/*	$text = $text . "Field $i is techniquescheckbox - ";
								$text = $text . var_dump($_POST["yvts_field_" . $fields[$i]->applicationid]);*/
							$valueArray = yvts_level::getTechniques($course->levelid); ////FIXME explode("|",$fields[$i]->hint);
							if ((count($valueArray) > 0) && ($valueArray[0]!= "")) {
								$minlength = $fields[$i]->minlength;
								if ($minlength > count($valueArray)) {
									$minlength = count($valueArray);
								}
								if (isset($_POST["yvts_field_" . $fields[$i]->applicationid])) {
									if ((is_array($_POST["yvts_field_" . $fields[$i]->applicationid])) && (count($_POST["yvts_field_" . $fields[$i]->applicationid]) > 0)) {
										$techniquesSub = "";
										foreach ($_POST["yvts_field_" . $fields[$i]->applicationid] as $techn) {
											$techniquesSub = $techniquesSub . ", "  . $techn;
										}
										$text = $text . stripcslashes($fields[$i]->name) . " : " . $techniquesSub . "\r\n<br />";
										$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
										$csvoutputdata = $csvoutputdata . "\"" . $techniquesSub . "\",";
										if (($fields[$i]->minlength >= 1) && (count($_POST["yvts_field_" . $fields[$i]->applicationid]) < $fields[$i]->minlength)) {
											$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
										}
									} else {
										$text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
										$csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
										$csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
										$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
									}
								}//end if this field is sent
							} //end if this field has techniques to select at all
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
                
                } else if (($fields[$i]->minlength > 0) && ($fields[$i]->type == "dateselector")) {
                    //this date field is only requested if the end date unix is non-zero
                    if (!($course->endtimeU > 0)) {
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
                    }
                } else if ($fields[$i]->type == "fileupload") {
                    //use selected a file to attach to the email (maybe)
					//$_FILES["yvts_field_" . $fields[$i]->applicationid]
                  //  if (!($course->endtimeU > 0)) {
						//if file has been uploaded and is valid
                        if ((isset($_FILES["yvts_field_" . $fields[$i]->applicationid])) && ($_FILES["yvts_field_" . $fields[$i]->applicationid]['size'] > 1) && ($_FILES["yvts_field_" . $fields[$i]->applicationid]['size'] < $MAX_FILESIZE)) {
							
							$mime_type = mime_content_type($_FILES["yvts_field_" . $fields[$i]->applicationid]['tmp_name']);

							// If you want to allow certain files
							$allowed_file_types = ['image/png', 'image/jpeg', 'application/pdf'];
							if (! in_array($mime_type, $allowed_file_types)) {
								//this file is the wrong mime type
								$errors = $errors . stripcslashes($fields[$i]->name) . ": (File error - not an accepted type) " . stripcslashes($fields[$i]->hint) . "\n";
							}
							//move file to filename
							$uploadTempname = tempnam(get_temp_dir(),"YVTSCoursemanager-Upload-");
							switch ($mime_type) {
								case "image/png":
									$uploadTempname = $uploadTempname . ".png";
									break;
								case "image/jpeg":
									$uploadTempname = $uploadTempname . ".jpeg";
									break;
								case "application/pdf":
									$uploadTempname = $uploadTempname . ".pdf";
									break;
							}
							move_uploaded_file($_FILES["yvts_field_" . $fields[$i]->applicationid]['tmp_name'],$uploadTempname);

							$attachments[] = $uploadTempname;

							$text = $text . stripcslashes($fields[$i]->name) . " : " . basename($uploadTempname) . "\r\n<br />";
                            $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                            $csvoutputdata = $csvoutputdata . "\"" . $uploadTempname . "\",";
						//else if required but NOT submitted ok, throw error
                        } elseif ($fields[$i]->minlength > 0) {
                            $text = $text . stripcslashes($fields[$i]->name) . " : " . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\r\n<br />";
                            $csvoutputtop = $csvoutputtop . "\"" . $fields[$i]->name . "\",";
                            $csvoutputdata = $csvoutputdata . "\"" . $_POST["yvts_field_" . $fields[$i]->applicationid] . "\",";
							//why is file not ok?
							if ((isset($_FILES["yvts_field_" . $fields[$i]->applicationid])) && (($_FILES["yvts_field_" . $fields[$i]->applicationid]['size'] < 1) || ($_FILES["yvts_field_" . $fields[$i]->applicationid]['size'] > $MAX_FILESIZE))) {
								//file submitted but invalid size
                           		$errors = $errors . stripcslashes($fields[$i]->name) . ": (File size invalid) " . stripcslashes($fields[$i]->hint) . "\n";
							} else { //file not submitted
                           		$errors = $errors . stripcslashes($fields[$i]->name) . ": " . stripcslashes($fields[$i]->hint) . "\n";
							}
                        }/* else {
							$text = $text . stripcslashes($fields[$i]->name) . " : " . "File situation?" . var_dump($_FILES);
						}*/
						//$text = $text . stripcslashes($fields[$i]->name) . " : " . "File check?" . var_dump($_FILES);
							
					//    }
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
            } //end iterator of fields

            if ($errors == "") {
            $thanksmessage = get_option("yvts_coursemanager_thanksmessage");
            $emailtarget = get_option("yvts_coursemanager_email");
            $emailsubject = get_option("yvts_coursemanager_email_subject");
            if ($emailsubject == false) { $emailsubject = "Course data submitted"; }
            $emailfrom = get_option("yvts_coursemanager_email_from");
            if ($emailfrom == false) { $emailfrom = $emailtarget; }
            $sendoutput = "";
            if ($emailtarget != false) {
                $headers = "Content-Type: text/html; charset=UTF-8";
                //$attachmentname = tempnam(get_temp_dir(),"YVTSCoursemanager-" . $course->coursename . "-" . $course->levelname . "-");
                $attachmentname = tempnam(get_temp_dir(),"YVTSCoursemanager-").".csv";
                $attachmenthandle = fopen($attachmentname, "w");
                fwrite($attachmenthandle, $csvoutputtop . "\r\n");
                fwrite($attachmenthandle, $csvoutputdata);
                fclose($attachmenthandle);
				$attachments[] = $attachmentname;

				//attachments should always be at least the CSV file
                $email_status = wp_mail($emailtarget,$emailsubject,$text,$headers,$attachments);
                if ($email_status) {
                    $sendoutput = $thanksmessage;
						  $submittedform = true;
                } else {
                    $errors = "yes";
                    $sendoutput = "Your application could not be sent to our team, sorry";
                    //$sendoutput = $soundoutput . "<br />wp_mail($emailtarget,$emailsubject,$text,$headers);";
                }
                foreach ($attachments as $attachfile) { //dump all temp files
					unlink ( $attachfile );
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
        } // end if form is submitted

if ($submittedform == false) {
        $output = $output . "
        <form method=\"post\" action=\"" . add_query_arg(null,null) . "\" enctype=\"multipart/form-data\" onsubmit=\"return yvts_validate_application();\" >
            <input type=\"hidden\" name=\"yvtscourse\" value=\"" . $_GET["yvtscourse"] . "\" />\n";
			
			if ($course->choosedaywithin > 0) {
				$selectorStartDay=  date("d M Y",$course->starttimeU);
				$selectorEndDay=  date("d M Y",$course->endtimeU);
				
				$output .= "<div class=\"yvts_input_dates\">";
				$output .= "<label class=\"yvts_date_header\">Please Select Dates</label>";
				for ($dateU = $course->starttimeU; $dateU <= ($course->endtimeU+4000); $dateU = $dateU + 86400) {
					$output .= "<div class=\"yvts_date_chooser\"><label for=\"yvts_datecheck" . $dateU . "\">" . date("D, d M Y",$dateU) . "</label><input id=\"yvts_datecheck" . $dateU . "\" type=\"checkbox\"";
					if (isset($userDates) ) {
						for ($i = 0; $i < count($userDates); $i++) {
							if ($userDates[$i] == date("d M Y",$dateU)) { $output .= " checked=\"checked\" "; }
						}
					}
					$output .= " onchange=\"yvts_validate_datechooser();\" name=\"yvts_dates[]\" value=\"" . date("d M Y",$dateU) . "\" /><div id=\"yvts_datecheck" . $dateU . "-tag\" class=\"yvts_tag\">&nbsp;</div></div>";
				}
				$output = $output . "</div>";
			}
			
			for ($i = 0; $i < count($fields); $i++) {
				//foreach field
				//$fields[$i]->name;
				//$fields[$i]->type;
				//$fields[$i]->position;
				//$fields[$i]->minlength;
				//$fields[$i]->hint;
				//$fields[$i]->applicationid;

				//for every type
				$output = $output . "<div class=\"yvts_input_text";
				if ($fields[$i]->minlength > 0) { $output = $output . " yvts_mandatory"; }
                $output = $output . "\">";

				//per type
                if (($fields[$i]->type != "heading") && ($fields[$i]->type != "dateselector")) {
                    $output = $output . "<label for=\"yvts_field_" . $fields[$i]->applicationid . "\">" . stripcslashes($fields[$i]->name) . ":</label>";
                }
                if ($fields[$i]->type == "textbox") {
                    $output = $output . "<input type=\"text\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"";
						  if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { $output = $output . $_POST["yvts_field_" . $fields[$i]->applicationid . ""]; }
						  $output = $output . "\"";
						  
                if ($fields[$i]->minlength > 0) { //set hint colours
                if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength)) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
                }
                $output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" />";

				//show hint text:
				$output = $output . "<div class=\"yvts_field_textbox\">" . stripcslashes($fields[$i]->hint) . "</div>";

                } elseif ($fields[$i]->type == "dropdown") { //dropdown list
					//dropdown values
					$values = explode("|",$fields[$i]->hint);
                    $output = $output . "<select id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
					if ($fields[$i]->minlength > 0) { //set hint colours
					if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength)) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
					}
					$output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\">";
					foreach ($values as $value) {
						$output = $output . "\t<option";
						if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && ($_POST["yvts_field_" . $fields[$i]->applicationid . ""] == $value)) {
							$output = $output . " selected=\"selected\" ";
						}
						$output = $output . ">" . $value . "</option>\r\n";
					}
					$output = $output . "</select>\r\n";
                } elseif ($fields[$i]->type == "leveldropdown") { //dropdown list
					//dropdown values are derived from "leveldropdown" entry for this level ID.
					//collect them
					$valueString = yvts_level::getDropdown($course->levelid); ////FIXME explode("|",$fields[$i]->hint);
					if (strlen($valueString) > 0) {
					$values = explode("|",$valueString);
                    $output = $output . "<select id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
					if ($fields[$i]->minlength > 0) { //set hint colours
					if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
					}
					$output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\">";
					$output = $output . "<option ";
						if (! isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) {
							$output = $output . " selected=\"selected\" ";
						}

					$output = $output . ">&nbsp;</option>\n";
					foreach ($values as $value) {
						$output = $output . "\t<option";
						if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && ($_POST["yvts_field_" . $fields[$i]->applicationid . ""] == $value)) {
							$output = $output . " selected=\"selected\" ";
						}
						$output = $output . ">" . $value . "</option>\r\n";
					}
					$output = $output . "</select>\r\n";
					} else {
					$output = $output . "<input type=\"hidden\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"No level dropdown options are set for this level.\" />(No Selection Required)";
					}
                } elseif ($fields[$i]->type == "techniques") { //technique multi select
					//dropdown values are derived from "leveldropdown" entry for this level ID.
					//collect them
					$valueArray = yvts_level::getTechniques($course->levelid); ////FIXME explode("|",$fields[$i]->hint);
					if ((count($valueArray) > 0) && ($valueArray[0]!= "")) {
						$minlength = $fields[$i]->minlength;
						if ($minlength > count($valueArray)) {
							$minlength = count($valueArray);
						}
					
                    $output = $output . "<select id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "[]\" class=\"yvts_input_multiple\" ";
					if ($minlength > 0) { //set hint colours
					if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (is_array($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (count($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $minlength)) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
					}
					$output = $output . " oninput=\"yvts_validate_multiselect('yvts_field_" . $fields[$i]->applicationid . "'," . $minlength . ");\" multiple=\"multiple\" size=\"" . count($valueArray) . "\">";

					foreach ($valueArray as $value) {
						$output = $output . "\t<option";
						if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) {
							foreach ($_POST["yvts_field_" . $fields[$i]->applicationid . ""] as $valueSelected) {
								if ($valueSelected ==$value) { $output = $output . " selected=\"selected\" "; }
							}
						}
						$output = $output . ">" . $value . "</option>\r\n";
					}
					$output = $output . "</select>\r\n";
					$output = $output . stripcslashes($fields[$i]->hint);
					} else {
					$output = $output . "<input type=\"hidden\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"No technique options are set for this level.\" />(No Selection Required)";
					}
                } elseif ($fields[$i]->type == "techniquescheckbox") { //techniquescheckbox - checkbox options
					//dropdown values are derived from "techniques" entry for this level ID.
					//collect them
					$valueArray = yvts_level::getTechniques($course->levelid); ////FIXME explode("|",$fields[$i]->hint);
					if ((count($valueArray) > 0) && ($valueArray[0]!= "")) {
						$minlength = $fields[$i]->minlength;
						if ($minlength > count($valueArray)) {
							$minlength = count($valueArray);
						}
					
                //    $output = $output . "<select id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "[]\" class=\"yvts_input_multiple\" ";
				/*	if ($minlength > 0) { //set hint colours
					if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (is_array($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (count($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $minlength)) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
					}*/
				//	$output = $output . " oninput=\"yvts_validate_multicheckbox('yvts_field_" . $fields[$i]->applicationid . "'," . $minlength . ");\" multiple=\"multiple\" size=\"" . count($valueArray) . "\">";

					$techcheckboxI = 0;
						$output = $output . "<div class=\"yvts_techniquecheckboxbox\">";
					foreach ($valueArray as $value) {
						$output = $output . "<div class=\"yvts_techniquecheckbox\">";
						$output = $output . "\t<input id=\"techcheck-" . $fields[$i]->applicationid . "-" . $techcheckboxI . "\" type=\"checkbox\" onchange=\"yvts_validate_multicheckbox('yvts_field_" . $fields[$i]->applicationid . "[]'," . $minlength . ")\" ";
						if ($minlength > 0) { //set hint colours
							if ((isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (is_array($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) && (count($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $minlength)) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
						}
						if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) {
							foreach ($_POST["yvts_field_" . $fields[$i]->applicationid . ""] as $valueSelected) {
								if ($valueSelected ==$value) { $output = $output . " selected=\"selected\" "; }
							}
						}
						$output = $output . " name=\"yvts_field_" . $fields[$i]->applicationid . "[]\" value=\"" . $value . "\"  /><label id=\"techcheck-" . $fields[$i]->applicationid . "-" . $techcheckboxI . "-label\"class=\"yvts_field_checkbox\" style=\"border-left: 10px solid; border-color: pink;\" for=\"techcheck-" . $fields[$i]->applicationid . "-" . $techcheckboxI . "\">" . $value . "</label><br />\r\n";
						$techcheckboxI++;
						$output = $output . "</div>";
					}
					$output = $output . "</div>";
					
				//	$output = $output . "</select>\r\n";
				
						$output = $output . "<div class=\"yvts_techniquecheckhint\">";
					$output = $output . stripcslashes($fields[$i]->hint);
					$output = $output . "</div>";
					
					} else {
					$output = $output . "<input type=\"hidden\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"No technique options are set for this level.\" />(No Selection Required)";
					}
                } elseif ($fields[$i]->type == "textarea") {
                    $output = $output . "<div class=\"yvts_field_textarea\">"  . stripcslashes($fields[$i]->hint) . "</div> <textarea id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                    if ($fields[$i]->minlength > 0) { //set hint colours
                        if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
                    }
                    $output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\">\n";
						  if (isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { $output = $output . $_POST["yvts_field_" . $fields[$i]->applicationid . ""]; }
							$output = $output . "</textarea>\n";

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
                } elseif ($fields[$i]->type == "dateselector") {
                    if ($course->endtimeU < 1) { //show date selector ONLY if not scheduled already
                        
                    $output = $output . "<label for=\"yvts_field_" . $fields[$i]->applicationid . "\">" . stripcslashes($fields[$i]->name) . ":</label>";
                        $output = $output . "<input type=\"date\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"";
								if(isset($_POST["yvts_field_" . $fields[$i]->applicationid . ""])) { $output = $output . $_POST["yvts_field_" . $fields[$i]->applicationid . ""]; }
								$output = $output . "\"";
                    if ($fields[$i]->minlength > 0) { //set hint colours
                    if (strlen($_POST["yvts_field_" . $fields[$i]->applicationid . ""]) >= $fields[$i]->minlength) { $output = $output . " style=\"background-color: #d1ffd2\" "; } else { $output = $output . " style=\"background-color: pink\" "; }
                    }
                    $output = $output . " oninput=\"yvts_validate_box('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" />";
                    } //if this is a dated course, ignore this field.
                    else {
                        $output = $output . "<input type=\"hidden\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" value=\"xxxxxxxxxx\" />\n";
                    }
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
                } elseif ($fields[$i]->type == "fileupload") {
                    $output = $output . "<div class=\"yvts_file_box\">";

                        $output = $output . "<input type=\"file\" id=\"yvts_field_" . $fields[$i]->applicationid . "\" name=\"yvts_field_" . $fields[$i]->applicationid . "\" ";
                        $output = $output . " onchange=\"yvts_validate_file('yvts_field_" . $fields[$i]->applicationid . "'," . $fields[$i]->minlength . ");\" ";
                        $output = $output . "/>\n";
                        

                    $output = $output . "</div>";
                    
					$output = $output . "<div class=\"yvts_filelabel\" for=\"yvts_field_" . $fields[$i]->applicationid . "\">" . stripcslashes($fields[$i]->hint) . "</div>";
                } else {
                    $output = $output . "<p>Can't handle type: " . $fields[$i]->type . "</p>\n";
                }
				$output = $output . "</div>";
			}
			
			$output = $output . "
            <div class=\"yvts_input_text\">\n
            <input type=\"submit\" id=\"submit_button\" name=\"submit_button\" value=\"Send Application Form\" />\n
            </div>\n";
			if ($course->choosedaywithin > 0) { $output .= "<script type=\"text/javascript\">yvts_validate_datechooser();</script>"; }
        $output .= "</form>\n";
}//endif not submitted form
	 } //end if course is not set (ie above only runs if it is)
    return $output;
}

?>