<?php
defined( 'ABSPATH' ) or die( 'No Direct Access' );
/* Functions for course running */

class yvts_courseRunning {

    static function addCourse($newlevel, $newstarttimeU, $newendtimeU, $newnote) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_courseRunning";
        $result = $wpdb->insert($table_name,array("levelid" => $newlevel, "starttime" => date('Y-m-d', $newstarttimeU), "endtime" => date('Y-m-d', $newendtimeU), "note" => $newnote),array('%d','%s','%s','%s'));
        if ($result === 1) {
            return true;
        } else {
            return "Error inserting new courseRunning: " . $wpdb->last_error;
        }
      }

    static function editCourse($editItemID, $editlevel, $editstarttimeU, $editendtimeU, $editnote) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_courseRunning";
        $result = $wpdb->update($table_name,array("levelid" => $editlevel, "starttime" => date('Y-m-d', $editstarttimeU), "endtime" => date('Y-m-d', $editendtimeU), "note" => $editnote),array('courseRunning_ID'=>$editItemID),array('%d','%s','%s','%s'),array('%d'));
        if ($result === 1) {
            return true;
        } else {
            return "Error updating courseRunning for $editItemID: " . $wpdb->last_error;
        }
      }
		
	static function setFullyBooked($editItemID, $fullyBooked = true) {
        global $wpdb;
        $table_name = $wpdb->prefix . "yvts_courseRunning";
		  if ($fullyBooked) { $fullyBooked = true; } else { $fullyBooked = false; }
        $result = $wpdb->update($table_name,array( "fullybooked" => $fullyBooked),array('courseRunning_ID'=>$editItemID),array('%d'),array('%d'));
        if ($result === 1) {
            return true;
        } else {
            return "Error updating courseRunning for $editItemID: " . $wpdb->last_error;
        }
		}

  static function getYears() {
    global $wpdb;
    $table_name = $wpdb->prefix . "yvts_courseRunning";
      //get years currently in the database.
    $sql = $wpdb->prepare( "SELECT distinct(year(`starttime`)) as `year` FROM `$table_name` WHERE 1 ORDER BY `year` DESC" );
    $result = $wpdb->get_results($sql);
    return $result;
  }

  public static function getCount() {
      global $wpdb;

      //fetch total number of courses or return false for error.
      
      $table_name = $wpdb->prefix . "yvts_courseRunning"; 
  
      $result = $wpdb->get_results( "SELECT COUNT(`courseRunning_ID`) AS `count` FROM `$table_name`");
      if (count($result) == 1) {
          //collect levels
          return $result[0]->count;
      }
      return $result;
  }

  static function getCoursesRunning($year = -1) {
    if ($year == -1) { $year = date("Y"); }
    if (!is_numeric($year)) { return false; }
    global $wpdb;
    $table_name_courseRunning = $wpdb->prefix . "yvts_courseRunning";
    $table_name_levels = $wpdb->prefix . "yvts_levels";
    $table_name_courses = $wpdb->prefix . "yvts_courses";
    $sql = $wpdb->prepare( "SELECT `courseRunning_ID`,`$table_name_courseRunning`.`edittime`,`$table_name_courseRunning`.`note`,`starttime`,(UNIX_TIMESTAMP(`starttime`)+7200) as `starttimeU`,`endtime`,`fullybooked`,(UNIX_TIMESTAMP(`endtime`)+7200) as `endtimeU`, (((UNIX_TIMESTAMP(`endtime`) - UNIX_TIMESTAMP(`starttime`))/86400)+1) AS `days`, `note`,`$table_name_levels`.`name` AS `levelname`,`$table_name_levels`.`description` AS `leveldesc`,`$table_name_levels`.`levelid` AS `levelid`,`$table_name_levels`.`levelprice` AS `levelprice`, `$table_name_courses`.`name` AS `coursename`, `$table_name_courses`.`description` AS `coursedesc` FROM `$table_name_courseRunning`
    LEFT JOIN `$table_name_levels` ON `$table_name_courseRunning`.`levelid`  = `$table_name_levels`.`levelid` 
    LEFT JOIN  `$table_name_courses` ON `$table_name_courses`.`courseid` = `$table_name_levels`.`courseid`
    WHERE year(`starttime`) = %d ORDER BY `$table_name_courses`.`name`, `$table_name_levels`.`name`, `starttime` ASC", $year);
    $result = $wpdb->get_results($sql);
    return $result;
  }

  static function getCourseRunningDetails($courseRunningID = -1) {
    
    if ((! is_numeric($courseRunningID)) || ($courseRunningID == -1)) { return false; }

    global $wpdb;
    $table_name_courseRunning = $wpdb->prefix . "yvts_courseRunning";
    $table_name_levels = $wpdb->prefix . "yvts_levels";
    $table_name_courses = $wpdb->prefix . "yvts_courses";
    $sql = $wpdb->prepare( "SELECT `courseRunning_ID`,`$table_name_courseRunning`.`edittime`,`$table_name_courseRunning`.`note`,`starttime`,UNIX_TIMESTAMP(`starttime`) as `starttimeU`,`endtime`,`fullybooked`,UNIX_TIMESTAMP(`endtime`) as `endtimeU`, (((UNIX_TIMESTAMP(`endtime`) - UNIX_TIMESTAMP(`starttime`))/86400)+1) AS `days`, `note`,`$table_name_levels`.`name` AS `levelname`,`$table_name_levels`.`levelid` AS `levelid`, `$table_name_courses`.`name` AS `coursename`, `$table_name_courses`.`description` AS `coursedesc`, `$table_name_levels`.`description` AS `leveldesc` FROM `$table_name_courseRunning`
    LEFT JOIN `$table_name_levels` ON `$table_name_courseRunning`.`levelid`  = `$table_name_levels`.`levelid` 
    LEFT JOIN  `$table_name_courses` ON `$table_name_courses`.`courseid` = `$table_name_levels`.`courseid`
    WHERE `courseRunning_ID` = %d", $courseRunningID);
    $result = $wpdb->get_results($sql);

    if (count($result) == 1) {
        return $result[0];
    } else {
        return false;
    }
  }

  static function getNextCourse($courseRunningID = -1) {
    if ((! is_numeric($courseRunningID)) || ($courseRunningID == -1)) { return false; }
    $details = yvts_courseRunning::getCourseRunningDetails($courseRunningID);
    if ($details == false) { return false; }
    global $wpdb;
    $table_name_courseRunning = $wpdb->prefix . "yvts_courseRunning";
    $sql = $wpdb->prepare("SELECT `courseRunning_ID` FROM `$table_name_courseRunning` WHERE UNIX_TIMESTAMP(`starttime`) > " . $details->endtimeU . " ORDER BY `starttime` ASC LIMIT 1");
    $result = $wpdb->get_results($sql);

    if (count($result) == 1) {
        return $result[0]->courseRunning_ID;
    } else {
        return false;
    }
  }

  static function getPreviousCourse($courseRunningID = -1) {
    if ((! is_numeric($courseRunningID)) || ($courseRunningID == -1)) { return false; }
    $details = yvts_courseRunning::getCourseRunningDetails($courseRunningID);
    if ($details == false) { return false; }
    global $wpdb;
    $table_name_courseRunning = $wpdb->prefix . "yvts_courseRunning";
    $sql = $wpdb->prepare("SELECT `courseRunning_ID` FROM `$table_name_courseRunning` WHERE UNIX_TIMESTAMP(`endtime`) < " . $details->starttimeU . " AND `starttime` IS NOT NULL AND `starttime`>NOW() ORDER BY `starttime` DESC LIMIT 1");
    $result = $wpdb->get_results($sql);

    if (count($result) == 1) {
        return $result[0]->courseRunning_ID;
    } else {
        return false;
    }
  }

  static function deleteCourseRunning($courseRunningID = -1) {
    if (!is_numeric($courseRunningID)) { return false; }
    global $wpdb;
    $table_name_courseRunning = $wpdb->prefix . "yvts_courseRunning";
    $result = $wpdb->delete($table_name_courseRunning,array("courseRunning_ID" => $courseRunningID),array('%d'));
  }
}

?>