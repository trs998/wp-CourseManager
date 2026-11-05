<?php

defined( 'ABSPATH' ) or die( 'No Direct Access' );

class yvts_application {
    public static function getApplicationFields($formID = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "yvts_application"; 

        if ($formID == null) {
          $result = $wpdb->get_results("SELECT * FROM `$table_name` WHERE `applicationformid` = 0 ORDER BY `position` ASC");
        } else {
          $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM `$table_name` WHERE `applicationformid` = %d ORDER BY `position` ASC",$formID)
            );
        }
        return $result;
    }

    public static function getApplicationForms() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "yvts_applicationform"; 

        $result = $wpdb->get_results("SELECT * FROM `$table_name` ORDER BY `name` ASC");
        return $result;
    }

    public static function getApplicationForm($formID) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "yvts_applicationform"; 

        $result = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM `$table_name` WHERE `applicationformid` = %d",$formID)
            );
        if (count($result) == 1) {
            return $result[0];
        } else {
            return null;
        }
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
    
    public static function getFormCount() {
        global $wpdb;

        //fetch total number of applications or return false for error.
        
        $table_name = $wpdb->prefix . "yvts_applicationform"; 
    
        $result = $wpdb->get_results( "SELECT COUNT(`applicationformid`) AS `count` FROM `$table_name`");
        if (count($result) == 1) {
            //collect levels
            return $result[0]->count;
        }
        return $result;
    }
    
    public static function createApplication($newapplication_formid, $newapplication_name, $newapplication_type, $newapplication_position, $newapplication_minlength, $newapplication_note) {
        global $wpdb;

        $formID = $newapplication_formid;
        if ($formID == null) { $formID = 0; }

        $table_name = $wpdb->prefix . "yvts_application"; 
        
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `applicationformid` = %d AND `name` = \"%s\"", $newapplication_formid, $newapplication_name );
        $result = $wpdb->get_results($sql);
        if (count($result) == 0) {
            //proceed to insert
            $result = $wpdb->insert($table_name,array("applicationformid"=> $newapplication_formid, "name" => $newapplication_name, "type" => $newapplication_type, "position" => $newapplication_position, "minlength" => $newapplication_minlength, "hint" => $newapplication_note),array('%d', '%s','%s', '%d', '%d', '%s'));
            if ($result === 1) {
                return true;
            } else {
                return "Error inserting new application: " . $wpdb->last_error;
            }
        } else {
            return "Form Field Name " . $newapplication_name . " already exists";
        }
    }
    
    public static function createApplicationForm($newapplicationform_name) {
        global $wpdb;

        $table_name = $wpdb->prefix . "yvts_applicationform"; 
        
        $sql = $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `name` = \"%s\"", $newapplicationform_name );
        $result = $wpdb->get_results($sql);
        if (count($result) == 0) {
            //proceed to insert
            $result = $wpdb->insert($table_name,array("name" => $newapplicationform_name),array('%s'));
            if ($result === 1) {
                return true;
            } else {
                return "Error inserting new application form: " . $wpdb->last_error;
            }
        } else {
            return "Form Name " . $newapplication_name . " already exists";
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
    
    public static function deleteApplication($deletedID) { 
        global $wpdb;

        $table_name = $wpdb->prefix . "yvts_application"; 
        return $wpdb->delete($table_name,array("applicationid" => $deletedID),array('%d'));

    }
    
    public static function moveupApplication() { }
    
    public static function movedownApplication() { }

}
?>