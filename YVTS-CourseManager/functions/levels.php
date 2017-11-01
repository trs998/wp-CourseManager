<?php

defined( 'ABSPATH' ) or die( 'No Direct Access' );

class yvts_level {
    public static function getLevels($courseID) {
        global $wpdb;

        //fetch array of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d ORDER BY `name` ASC", $courseID );
        $result = $wpdb->get_results($sql);
        return $result;
    }
    
    public static function createLevel($courseID, $newlevelname) {
        global $wpdb;

        //check does not exist
        //create
        //return true on success, text message on failure

        $table_name_courses = $wpdb->prefix . "yvts_courses"; 
        $table_name_levels = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name_courses` WHERE `courseid` = %d", $courseID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name_levels` WHERE `courseid` = %d AND `name` = \"%s\"", $courseID, $newlevelname );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to insert
                $result = $wpdb->insert($table_name_levels,array("name" => $newlevelname, "courseid" => $courseID),array('%s','%d'));
                if ($result === 1) {
                    return true;
                } else {
                    return "Error inserting new level: " . $wpdb->last_error;
                }
            } else {
                return "Level Name " . $newlevelname . " already exists";
            }
        } else {
            return "Course ID " . $courseID . " does not exist";
        }
    }
    
    public static function updateLevel($levelID, $newname) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `levelid` = %d", $levelID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d AND `name` = \"%s\"", $result[0]->courseid, $newname );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to update
                $result = $wpdb->update($table_name,array("name" => $newname),array("levelid" => $levelID),array('%s'),array('%d'));
                if ($result === 1) {
                    return true;
                } else {
                    return "Error updating level: " . $wpdb->last_error;
                }
            } else {
                return "Level Name \"$newname\" would result in a duplicate name.";
            }
        } else {
            return "Level with ID $levelID does not exist";
        }
    }

}
?>