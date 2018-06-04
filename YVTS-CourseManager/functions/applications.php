<?php

defined( 'ABSPATH' ) or die( 'No Direct Access' );

class yvts_application {
    public static function getApplicationFields() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "yvts_application"; 

        $result = $wpdb->get_results("SELECT * FROM `$table_name` ORDER BY `position` ASC");
        return $result;
    }
    
    public static function getCount() {
        global $wpdb;

        //fetch total number of applications or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_application"; 
    
        $result = $wpdb->get_results( "SELECT COUNT(`applicationid`) AS `count` FROM `$table_name`");
        if (count($result) == 1) {
            //collect levels
            return $result[0]->count;
        }
        return $result;
    }
    
    public static function createApplication($newapplication_name, $newapplication_type, $newapplication_position, $newapplication_minlength, $newapplication_note) {
        global $wpdb;

        $table_name = $wpdb->prefix . "yvts_application"; 
        
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `name` = \"%s\"", $newapplication_name );
        $result = $wpdb->get_results($sql);
        if (count($result) == 0) {
            //proceed to insert
            $result = $wpdb->insert($table_name,array("name" => $newapplication_name, "type" => $newapplication_type, "position" => $newapplication_position, "minlength" => $newapplication_minlength, "hint" => $newapplication_note),array('%s','%s', '%d', '%d', '%s'));
            if ($result === 1) {
                return true;
            } else {
                return "Error inserting new application: " . $wpdb->last_error;
            }
        } else {
            return "Form Field Name " . $newapplication_name . " already exists";
        }
    }
    
    public static function updateApplication($editedID, $newapplication_name, $newapplication_type, $newapplication_position, $newapplication_minlength, $newapplication_note) { 
        global $wpdb;

        $table_name = $wpdb->prefix . "yvts_application"; 
        $result = $wpdb->update($table_name,array("name" => $newapplication_name, "type" => $newapplication_type, "position" => $newapplication_position, "minlength" => $newapplication_minlength, "hint" => $newapplication_note),array("applicationid" => $editedID),array('%s','%s', '%d', '%d', '%s'),array('%d'));
        if ($result === 1) {
            return true;
        } else {
            return "Error updating application: " . $wpdb->last_error;
        }

    }
    
    public static function deleteApplication() { }
    
    public static function moveupApplication() { }
    
    public static function movedownApplication() { }

}
?>