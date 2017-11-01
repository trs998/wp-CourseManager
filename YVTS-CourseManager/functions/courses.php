<?php
/* Functions for courses */

//create course

//delete course (cascade delete subitems)

//list courses
class yvts_course {
    public static function getCourses() {
        global $wpdb;

        //fetch array of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_courses"; 
    
        $sql = "SELECT * FROM $table_name ORDER BY name ASC";
        $result = $wpdb->get_results($sql);
        return $result;
    }

    public static function createCourse($newcoursename) {
        global $wpdb;

        //check does not exist
        //create
        //return true on success, text message on failure

        $table_name = $wpdb->prefix . "yvts_courses"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `name` = \"%s\"", $newcoursename );
        $result = $wpdb->get_results($sql);
        if (count($result) == 0) {
            //proceed to insert
            $result = $wpdb->insert($table_name,array("name" => $newcoursename, "description" => ""),array('%s','%s'));
            if ($result === 1) {
                return true;
            } else {
                return "Error inserting new course: " . $wpdb->last_error;
            }
        } else {
            return "Course Name \"$newcoursename\" already exists";
        }
    }

    public static function updateCourse($courseID, $newcoursename) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_courses"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d", $courseID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            //proceed to update
            $result = $wpdb->update($table_name,array("name" => $newcoursename, "description" => ""),array("courseid" => $courseID),array('%s','%s'),array('%d'));
            if ($result === 1) {
                return true;
            } else {
                return "Error updating course: " . $wpdb->last_error;
            }
        } else {
            return "Course Name \"$newcoursename\" with ID $courseID does not exist";
        }
    }
}
?>