<?php

defined( 'ABSPATH' ) or die( 'No Direct Access' );

class yvts_level {
    public static function getLevels($courseID) {
        global $wpdb;

        //fetch array of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d ORDER BY `name` ASC", $courseID );
        $result = $wpdb->get_results($sql);
        for ($i = 0; $i < count($result); $i++) {
            //collect levels
            $result[$i]->exams = yvts_exam::getExams($result[$i]->levelid);
        }
        return $result;
    }
    
    public static function getCount() {
        global $wpdb;

        //fetch total number of levels or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_levels"; 
    
        $result = $wpdb->get_results( "SELECT COUNT(`levelid`) AS `count` FROM `$table_name`");
        if (count($result) == 1) {
            //collect levels
            return $result[0]->count;
        }
        return $result;
    }

    public static function getCoursesAndLevels() {
        global $wpdb;
        //returns levelid, coursename and levelname

        $table_name_courses = $wpdb->prefix . "yvts_courses"; 
        $table_name_levels = $wpdb->prefix . "yvts_levels"; 
        
        $result = $wpdb->get_results( "SELECT `levelid`,`$table_name_levels`.`name` as `levelname`,`$table_name_levels`.`levelprice` AS `levelprice`, `$table_name_courses`.`name` as `coursename` FROM `$table_name_levels` LEFT JOIN `$table_name_courses` ON `$table_name_levels`.`courseid`=`$table_name_courses`.`courseid` ORDER BY `$table_name_courses`.`name`, `$table_name_levels`.`name` ASC");
        return $result;

    }

    public static function getLevelDetails($levelID) {
        global $wpdb;
        //returns levelid, coursename and levelname

        if (! is_numeric($levelID)) { return false; }

        $table_name_courses = $wpdb->prefix . "yvts_courses"; 
        $table_name_levels = $wpdb->prefix . "yvts_levels"; 
        
        $result = $wpdb->get_results( "SELECT `levelid`,`$table_name_levels`.`levelname` as `levelname`, `$table_name_levels`.`levelid` AS `levelid`,`$table_name_levels`.`levelprice` AS `levelprice`, `$table_name_courses`.`name` as `coursename`, `$table_name_courses`.`description` AS `coursedesc` FROM `$table_name_levels` LEFT JOIN `$table_name_courses` ON `$table_name_levels`.`courseid`=`$table_name_courses`.`courseid` WHERE `$table_name_levels`.`levelid` = $levelID");
        
        if (count($result) == 1) {
            return $result[0];
        } else {
            return false;
        }
    }
    
    public static function createLevel($courseID, $newlevelname, $newlevelprice = 0) {
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
                $result = $wpdb->insert($table_name_levels,array("name" => $newlevelname, "courseid" => $courseID, "levelprice" => $newlevelprice),array('%s','%d', '%f'));
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
    
    public static function updateLevel($levelID, $newname, $newprice) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `levelid` = %d", $levelID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `courseid` = %d AND `name` = \"%s\" AND `levelid` != %d", $result[0]->courseid, $newname,$levelID );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to update
                $result = $wpdb->update($table_name,array("name" => $newname, "levelprice" => $newprice),array("levelid" => $levelID),array('%s'),array('%d','%f'));
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

    public static function deleteLevel($levelID) {
        global $wpdb;
        $table_name_sub_sub = $wpdb->prefix . "yvts_courseRunning"; 
        $table_name_sub = $wpdb->prefix . "yvts_exams"; 
        $table_name = $wpdb->prefix . "yvts_levels"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `levelid` = %d", $levelID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $result = $wpdb->delete($table_name_sub_sub,array("levelid" => $levelID),array('%d'));
            $result = $wpdb->delete($table_name_sub,array("levelid" => $levelID),array('%d'));
            $result = $wpdb->delete($table_name,array("levelid" => $levelID),array('%d'));
            if ($result === 1) {
                return true;
            } else {
                return "Error deletng level: " . $wpdb->last_error;
            }
        } else {
            return "Level with ID $levelID does not exist";
        }

    }

}
?>