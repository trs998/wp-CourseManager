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
        echo "Application form for " . $course->coursename . " " . $course->levelname . " <span class=\"yvts_course_description\"> " . $course->coursedesc . "</span>";
        if ($course->endtimeU > 0) {
            echo "<p>Course running from " . date("d M Y",$course->starttimeU) . " to " . date("d M Y",$course->endtimeU) . "</p>";
        } else {
            echo "<p>Course in " . date("Y",$course->starttimeU) . " scheduled on demand.</p>";
        }
        echo "
        <form method=\"post\" action=\"" . add_query_arg(null,null) . "\">
            <input type=\"hidden\" name=\"yvtscourse\" value=\"" . $_GET["yvtscourse"] . "\" />
            <label for=\"yvtsexam\">Exam Specification:</label> <select id=\"yvtsexam\" name=\"yvtsexam\">";
            $exams = yvts_exam::getExams($course->levelid);
            if (count($exams) == 0) {
                echo "<option selected>Contact ISA for your specification</option>";
            } else {
                for ($i = 0; $i < count($exams); $i++) {
                    echo "<option value=\"" . $exams[$i]->examid . "\">" . $exams[$i]->name . "</option>";
                }
                echo "<option>Contact ISA for your specification if it's not in this list.</option>";
            }
            echo "
            </select>

            <label for=\"yvts_company\">Company:</label>
            <input id=\"yvts_company\" name=\"yvts_company\" value=\"" . $_POST["yvts_company"] . "\" />
        </form>
        ";
    } else {
        echo " show application form for all levels";
    }
}

?>