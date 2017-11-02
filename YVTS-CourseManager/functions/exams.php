<?php

defined( 'ABSPATH' ) or die( 'No Direct Access' );

class yvts_exam {
    public static function getExams($levelID) {
        global $wpdb;

        //fetch array of courses or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_exams"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `levelid` = %d ORDER BY `name` ASC", $levelID );
        $result = $wpdb->get_results($sql);
        return $result;
    }
    
    public static function createExam($levelID, $newname) {
        global $wpdb;

        //check does not exist
        //create
        //return true on success, text message on failure

        $table_name_levels = $wpdb->prefix . "yvts_levels"; 
        $table_name_exams = $wpdb->prefix . "yvts_exams"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name_levels` WHERE `levelid` = %d", $levelID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name_exams` WHERE `levelid` = %d AND `name` = \"%s\"", $levelID, $newname );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to insert
                $result = $wpdb->insert($table_name_exams,array("name" => $newname, "levelid" => $levelID),array('%s','%d'));
                if ($result === 1) {
                    return true;
                } else {
                    return "Error inserting new level: " . $wpdb->last_error;
                }
            } else {
                return "Exam Name " . $newname . " already exists";
            }
        } else {
            return "Level ID " . $levelID . " does not exist";
        }
    }
    
    public static function updateExam($examID, $newname) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_exams"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `examid` = %d", $examID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `levelid` = %d AND `name` = \"%s\"", $result[0]->levelid, $newname );
            $result = $wpdb->get_results($sql);
            if (count($result) == 0) {
                //proceed to update
                $result = $wpdb->update($table_name,array("name" => $newname),array("examid" => $examID),array('%s'),array('%d'));
                if ($result === 1) {
                    return true;
                } else {
                    return "Error updating exam: " . $wpdb->last_error;
                }
            } else {
                return "Exam Name \"$newname\" would result in a duplicate name.";
            }
        } else {
            return "Exam with ID $examID does not exist";
        }
    }

    public static function deleteExam($examID) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_exams"; 

        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `examid` = %d", $examID );
        $result = $wpdb->get_results($sql);
        if (count($result) == 1) {
            $result = $wpdb->delete($table_name,array("examid" => $examID),array('%d'));
            if ($result === 1) {
                return true;
            } else {
                return "Error deletng exams: " . $wpdb->last_error;
            }
        } else {
            return "Exam with ID $examID does not exist";
        }

    }

}
?>