<?php
defined( 'ABSPATH' ) or die( 'No Direct Access' );
/* Functions for courses */

//create course

//delete course (cascade delete subitems)

//list courses
class yvts_course {
    public static function getCourses() {
        global $wpdb;

        //fetch array of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_courses"; 
    
        $result = $wpdb->get_results( "SELECT * FROM `$table_name` ORDER BY `name` ASC");
        for ($i = 0; $i < count($result); $i++) {
            //collect levels
            $result[$i]->levels = yvts_level::getLevels($result[$i]->courseid);
        }
        return $result;
    }

    public static function getCount() {
        global $wpdb;

        //fetch total number of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_courses"; 
    
        $result = $wpdb->get_results( "SELECT COUNT(`courseid`) AS `count` FROM `$table_name`");
        if (count($result) == 1) {
            //collect levels
            return $result[0]->count;
        }
        return $result;
    }

    public static function createCourse($newcoursename,$newcoursedesc) {
        global $wpdb;

        //check does not exist
        //create
        //return true on success, text message on failure

        $table_name = $wpdb->prefix . "yvts_courses"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `name` = \"%s\"", $newcoursename );
        $result = $wpdb->get_results($sql);
        if (count($result) == 0) {
            //proceed to insert
            $result = $wpdb->insert($table_name,array("name" => $newcoursename, "description" => $newcoursedesc),array('%s','%s'));
            if ($result === 1) {
                return true;
            } else {
                return "Error inserting new course: " . $wpdb->last_error;
            }
        } else {
            return "Course Name \"$newcoursename\" already exists";
        }
    }

    public static function updateCourse($courseID, $newcoursename, $newcoursedesc) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_courses"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d", $courseID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `name` = \"%s\" AND `courseid` != %d", $newcoursename, $courseID );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to update
                $result = $wpdb->update($table_name,array("name" => $newcoursename, "description" => $newcoursedesc),array("courseid" => $courseID),array('%s','%s'),array('%d'));
                if ($result === 1) {
                    return true;
                } else {
                    return "Error updating course: " . $wpdb->last_error;
                }
            } else {
                return "Course Name \"$newcoursename\" would result in a duplicate name.";
            }
        } else {
            return "Course Name \"$newcoursename\" with ID $courseID does not exist";
        }
    }

    //delete Course function TODO
    //check course exists
    //fetch levels
    //for each level, delete it
    //delete course
}
?>