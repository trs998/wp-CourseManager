<?php
/*
Plugin Name: YVTS Course Manager
*/

defined( 'ABSPATH' ) or die( 'No Direct Access' );

include "functions/levels.php";
include "functions/courses.php";

function yvts_coursemanager_install () {

    add_option( "yvts_coursemanager_db_version", "1.0" );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "yvts_courses"; 
    
    $sql = "CREATE TABLE $table_name (
        courseid mediumint(9) NOT NULL AUTO_INCREMENT,
        edittime timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
        name tinytext NOT NULL,
        description text NULL,
        PRIMARY KEY  (courseid)
    ) $charset_collate;";
    
    dbDelta( $sql );
    $table_name = $wpdb->prefix . "yvts_levels"; 
    
    $sql = "CREATE TABLE $table_name (
        levelid mediumint(9) NOT NULL AUTO_INCREMENT,
        courseid mediumint(9) NOT NULL,
        name tinytext NOT NULL,
        PRIMARY KEY  (levelid)
    ) $charset_collate;";
    
    dbDelta( $sql );

    //FIXME - needs more tables:  CourseRunning and Exams 
}


function yvts_coursemanager_admin_menu() {
    /* Builds admin functions */
    add_options_page( 'YVTS Course Manager - Courses', 'Course Manager - Courses', 'manage_options', 'yvts_coursemanager_admin_courses', 'yvts_coursemanager_admin_courses' );
    add_options_page( 'YVTS Course Manager - Applications', 'Course Manager - Submissions', 'manage_options', 'yvts_coursemanager_admin_submissions', 'yvts_coursemanager_admin_submissions' );
}

function yvts_coursemanager_admin_courses() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    
    //Add_new_course and newcourse
    $newcoursemessage = "";
    if (isset($_POST["Add_new_course"])) {
        $newcourse = trim($_POST["newcourse"]);
        $newcoursedesc = trim($_POST["newcoursedesc"]);
        if (strlen($newcourse) < 1) {
            $newcoursemessage = "<span style=\"color: red\">New Course Name needs to be entered</span>";
        } else {
            //add new course
            $newcourseresult = yvts_course::createCourse($newcourse,$newcoursedesc);
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
        if (strlen($editedcourse) < 1) {
            $editcoursemessage = "<span style=\"color: red\">Edited Course Name needs to be entered</span>";
        } else {
            $editcourseresult = yvts_course::updateCourse($editedcourseID, $editedcourse, $editedcoursedesc);
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
        if (strlen($newlevel) < 1) {
            $newlevelmessage = "<span style=\"color: red\">New Level Name needs to be entered</span>";
        } else {
            //add new level
            $newlevelresult = yvts_level::createLevel($newlevelcourseID,$newlevel);
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
        if (strlen($editedlevel) < 1) {
            $editlevelmessage = "<span style=\"color: red\">Edited Level Name needs to be entered</span>";
        } else {
            $editlevelresult = yvts_level::updateLevel($editedlevelID, $editedlevel);
            if ($editlevelresult === true) {
                $editlevelmessage = "<span style=\"color: green\">Edited Level \"$editedlevel\" saved successfully</span>";
            } else {
                $editlevelmessage = "<span style=\"color: red\">$editlevelresult</span>";
            }
        }
    }

	echo '<div class="wrap">';
    echo '<p>TODO: show existing list of courses, and allow the user to add new ones.</p>';
    $courseList = yvts_course::getCourses();
    echo "<p><strong>Course List: " . count($courseList) . " items</strong></p>";
    echo "<p>$editcoursemessage . $newlevelmessage . $editlevelmessage</p>";
    foreach ($courseList as $course) {
        echo "<p class=\"yvts_course\"><span id=\"yvtsDisplayCourse" . $course->courseid . "\"><h3>" . $course->name . "</h3>";
        if (strlen($course->description) > 0) { echo "<b>Description:</b>" . $course->description . "<br />"; }
        echo " Actions: Delete ";
        echo " <a onclick=\"document.getElementById('yvtsDisplayCourse" . $course->courseid . "').style.display = 'none'; document.getElementById('yvtsEditCourse" . $course->courseid . "').style.display = 'block';\">(edit)</a></span>";
        echo "<div id=\"yvtsEditCourse" . $course->courseid . "\" style=\"display: none;\"><form method=\"post\"><label for=\"editcourse" . $course->courseid . "\">Name</label><input type=\"text\" name=\"editcourse" . $course->courseid . "\" value=\"" . $course->name . "\" /><br /><label for=\"editcoursedesc" . $course->courseid . "\">Description:</label><input type=\"text\" name=\"editcoursedesc" . $course->courseid . "\" value=\"" . $course->description . "\" /><br /><input type=\"hidden\" name=\"editcourseID\" value=\"" . $course->courseid . "\" /><input type=\"submit\" name=\"Edit_Course\" value=\"Save Course\" /></form></div>";
        echo "<br />Levels within this course: " . count($course->levels) . "";
        foreach($course->levels as $level)
        { 
            echo "<br />Level: <span id=\"yvtsLevel" . $level->levelid . "\">" . $level->name . " ";
            echo " <a onclick=\"document.getElementById('yvtsLevel" . $level->levelid . "').style.display = 'none'; document.getElementById('yvtsEditLevel" . $level->levelid . "').style.display = 'block';\">(edit)</a></span> ";
            echo "<div id=\"yvtsEditLevel" . $level->levelid . "\" style=\"display: none;\"><form method=\"post\"><label for=\"editlevel" . $level->levelid . "\">Name</label><input type=\"text\" name=\"editlevel" . $level->levelid . "\" value=\"" . $level->name . "\" /><input type=\"hidden\" name=\"editlevelID\" value=\"" . $level->levelid . "\" /><input type=\"submit\" name=\"Edit_Level\" value=\"Save Level\" /></form></div>";
        }
        echo "<p>Add a new level within " . $course->name . ": <form method=\"post\" style=\"display: inline\"><label for=\"newlevel" . $course->courseid . "\">Name</label><input type=\"text\" name=\"newlevel" . $course->courseid . "\" value=\"\" /><br /><input type=\"hidden\" name=\"newLevelCourseID\" value=\"" . $course->courseid . "\" /><input type=\"submit\" name=\"Add_New_Level\" value=\"Add New Level to " . $course->name . "\" /></form></p>";
        
        echo "</p>";
    }
    echo "<p>$newcoursemessage<br />Add a new course: <form method=\"post\"><label for=\"newcourse\">Name</label><input type=\"text\" name=\"newcourse\" value=\"$newcourse\" /><br /><label for=\"newcoursedesc\">Description</label><input type=\"text\" name=\"newcoursedesc\" value=\"$newcoursedesc\" /><br /><input type=\"submit\" name=\"Add_new_course\" value=\"Add New Course\" /></form></p>";
	echo '</div>';
}

function yvts_coursemanager_admin_submissions() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>TODO: Show submitted applications.</p>';
	echo '</div>';
}

register_activation_hook( __FILE__, 'yvts_coursemanager_install' );

add_action( 'admin_menu', 'yvts_coursemanager_admin_menu' );

?>