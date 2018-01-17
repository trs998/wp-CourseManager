<?php
/*
Plugin Name: YVTS Course Manager
*/

defined( 'ABSPATH' ) or die( 'No Direct Access' );

include "functions/exams.php";
include "functions/levels.php";
include "functions/courses.php";
include "functions/coursesRunning.php";

function yvts_coursemanager_install () {

    add_option( "yvts_coursemanager_db_version", "3.0" );

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
    $table_name = $wpdb->prefix . "yvts_courseRunning"; 
    
    $sql = "CREATE TABLE $table_name (
      `courseRunning_ID` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
      `edittime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `levelid` mediumint(9) NOT NULL,
      `starttime` date,
      `endtime` date,
      `note` text,
        PRIMARY KEY  (courseRunning_ID)
    ) $charset_collate;";
    
    dbDelta( $sql );
    
    $table_name = $wpdb->prefix . "yvts_exams"; 
    
    $sql = "CREATE TABLE $table_name (
        examid mediumint(9) NOT NULL AUTO_INCREMENT,
        levelid mediumint(9) NOT NULL,
        name tinytext NOT NULL,
        PRIMARY KEY  (examid)
    ) $charset_collate;";
    
    dbDelta( $sql );
}

function yvts_coursemanager_upgrade_db_2_to_3() {
    return "3.0";
}

function yvts_coursemanager_upgrade() {
    //from 2 to 3 DB format
    global $wpdb;
    $DBVersion = get_option("yvts_coursemanager_db_version");
    if ($DBVersion == "2.0") { $DBVersion = yvts_coursemanager_upgrade_db_2_to_3(); }
    if ($DBVersion != get_option("yvts_coursemanager_db_version")) {
        update_option( "yvts_coursemanager_db_version", "$DBVersion" );
    }
}

function yvts_coursemanager_admin_menu() {
    /* Builds admin functions */
    add_menu_page('YVTS Course Manager','YVTS Course Manager', 'manage_options', 'yvts_coursemanager' ,'yvts_coursemanager_admin', '', 7);
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Courses', 'Courses', 'manage_options', 'yvts_coursemanager_admin_courses', 'yvts_coursemanager_admin_courses' );
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Schedules', 'Schedules', 'manage_options', 'yvts_coursemanager_admin_schedules', 'yvts_coursemanager_admin_schedules' );
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Applications', 'Applications', 'manage_options', 'yvts_coursemanager_admin_submissions', 'yvts_coursemanager_admin_submissions' );
}

function yvts_coursemanager_admin() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }
    echo "<p>List summary information</p>";
    echo "<p>Show counts of data - courses, levels and exams.</p>";
    echo "<p>The system contains " . yvts_course::getCount() . " courses, with " . yvts_level::getCount() . " levels and " . yvts_exam::getCount() . " exams.</p>";
}

function yvts_coursemanager_admin_submissions() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
    echo "<h2>List of submissions made against scheduled courses with exams selected.</h2>";
    echo "<h3>TODO: View list, export list since last export, export whole list</h3>";
	echo '</div>';
}

include "functions/yvts_coursemanager_admin_courses.php";
include "functions/yvts_coursemanager_admin_schedules.php";

function yvts_coursemanager_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'yvts_style', $plugin_url . 'css/style.css' );
}
add_action( 'admin_enqueue_scripts', 'yvts_coursemanager_load_plugin_css' );
add_action( 'wp_enqueue_scripts', 'yvts_coursemanager_load_plugin_css' );

register_activation_hook( __FILE__, 'yvts_coursemanager_install' );

add_action( 'admin_menu', 'yvts_coursemanager_admin_menu' );

add_action( 'plugins_loaded', 'yvts_coursemanager_upgrade' );

?>