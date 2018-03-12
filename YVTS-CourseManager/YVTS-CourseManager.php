<?php
/*
Plugin Name: YVTS Course Manager
*/

defined( 'ABSPATH' ) or die( 'No Direct Access' );

include "functions/exams.php";
include "functions/levels.php";
include "functions/courses.php";
include "functions/coursesRunning.php";
include "functions/applications.php";

function yvts_coursemanager_install () {

    add_option( "yvts_coursemanager_db_version", "6" );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "yvts_courses"; 
    
    $sql = "CREATE TABLE $table_name (
        courseid mediumint(9) NOT NULL AUTO_INCREMENT,
        edittime timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
        name tinytext NOT NULL,
        description text NULL,
        PRIMARY KEY courseid (courseid)
    ) $charset_collate;";
    
    dbDelta( $sql );
    $table_name = $wpdb->prefix . "yvts_levels"; 
    
    $sql = "CREATE TABLE $table_name (
        levelid mediumint(9) NOT NULL AUTO_INCREMENT,
        courseid mediumint(9) NOT NULL,
        levelprice float DEFAULT 0,
        name tinytext NOT NULL,
        PRIMARY KEY levelid (levelid)
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
        PRIMARY KEY courseRunning_ID (courseRunning_ID)
    ) $charset_collate;";
    
    dbDelta( $sql );
    
    $table_name = $wpdb->prefix . "yvts_exams"; 
    
    $sql = "CREATE TABLE $table_name (
        examid mediumint(9) NOT NULL AUTO_INCREMENT,
        levelid mediumint(9) NOT NULL,
        name tinytext NOT NULL,
        PRIMARY KEY examid (examid)
    ) $charset_collate;";
    
    dbDelta( $sql );
    
    $table_name = $wpdb->prefix . "yvts_application"; 
    
    $sql = "CREATE TABLE $table_name (
        `applicationid` mediumint(9) NOT NULL AUTO_INCREMENT,
        `position` mediumint(9) NOT NULL,
        `name` text NOT NULL,
        `type` tinytext NOT NULL,
        `minlength` mediumint(9) NOT NULL,
        `hint` text NOT NULL DEFAULT '',
        PRIMARY KEY `applicationid` (`applicationid`)
    ) $charset_collate;";
    
    dbDelta( $sql );
}

function yvts_coursemanager_upgrade_db_2_to_3() {
    return "3.0";
}

function yvts_coursemanager_upgrade_db_3_to_4() {
    //add levelprice to level
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $table_name = $wpdb->prefix . "yvts_levels"; 
    
    $sql = "CREATE TABLE $table_name (
        levelid mediumint(9) NOT NULL AUTO_INCREMENT,
        courseid mediumint(9) NOT NULL,
        levelprice float DEFAULT 0,
        name tinytext NOT NULL,
        PRIMARY KEY  (levelid)
    ) $charset_collate;";
    
    dbDelta( $sql );

    return "4.0";
}

function yvts_coursemanager_upgrade_db_4_to_5() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;

    $table_name = $wpdb->prefix . "yvts_application"; 
    
    $sql = "CREATE TABLE $table_name (
        `applicationid` mediumint(9) NOT NULL AUTO_INCREMENT,
        `position` mediumint(9) NOT NULL,
        `name` tinytext NOT NULL,
        `type` tinytext NOT NULL,
        `minlength` mediumint(9) NOT NULL,
        PRIMARY KEY `applicationid` (`applicationid`)
    ) $charset_collate;";
    
    dbDelta( $sql );
    return "5.0";
}

function yvts_coursemanager_upgrade_db_5_to_5_1() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $table_name = $wpdb->prefix . "yvts_application"; 
    
    $sql = "CREATE TABLE $table_name (
        `applicationid` mediumint(9) NOT NULL AUTO_INCREMENT,
        `position` mediumint(9) NOT NULL,
        `name` tinytext NOT NULL,
        `type` tinytext NOT NULL,
        `minlength` mediumint(9) NOT NULL,
        `hint` tinytext NOT NULL DEFAULT '',
        PRIMARY KEY `applicationid` (`applicationid`)
    ) $charset_collate;";
    
    dbDelta( $sql );

    return "5.1";
}

function yvts_coursemanager_upgrade_db_5_1_to_6() {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    global $wpdb;
    $table_name = $wpdb->prefix . "yvts_application"; 
    
    $sql = "CREATE TABLE $table_name (
        `applicationid` mediumint(9) NOT NULL AUTO_INCREMENT,
        `position` mediumint(9) NOT NULL,
        `name` text NOT NULL,
        `type` tinytext NOT NULL,
        `minlength` mediumint(9) NOT NULL,
        `hint` text NOT NULL DEFAULT '',
        PRIMARY KEY `applicationid` (`applicationid`)
    ) $charset_collate;";
    
    dbDelta( $sql );

    return "6";
}

function yvts_coursemanager_upgrade() {
    //from old to new DB formats
    global $wpdb;
    $DBVersion = get_option("yvts_coursemanager_db_version");
    if ($DBVersion == "2.0") { $DBVersion = yvts_coursemanager_upgrade_db_2_to_3(); }
    if ($DBVersion == "3.0") { $DBVersion = yvts_coursemanager_upgrade_db_3_to_4(); }
    if ($DBVersion == "4.0") { $DBVersion = yvts_coursemanager_upgrade_db_4_to_5(); }
    if ($DBVersion == "5.0") { $DBVersion = yvts_coursemanager_upgrade_db_5_to_5_1(); }
    if ($DBVersion == "5.1") { $DBVersion = yvts_coursemanager_upgrade_db_5_1_to_6(); }
    if ($DBVersion != get_option("yvts_coursemanager_db_version")) {
        update_option( "yvts_coursemanager_db_version", "$DBVersion" );
    }
}

function yvts_coursemanager_admin_menu() {
    /* Builds admin functions */
    add_menu_page('YVTS Course Manager','YVTS Course Manager', 'manage_options', 'yvts_coursemanager' ,'yvts_coursemanager_admin', '', 7);
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Courses', 'Courses', 'manage_options', 'yvts_coursemanager_admin_courses', 'yvts_coursemanager_admin_courses' );
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Schedules', 'Schedules', 'manage_options', 'yvts_coursemanager_admin_schedules', 'yvts_coursemanager_admin_schedules' );
    add_submenu_page( 'yvts_coursemanager', 'YVTS Course Manager - Applications', 'Applications', 'manage_options', 'yvts_coursemanager_admin_applications', 'yvts_coursemanager_admin_applications' );
}

function yvts_coursemanager_admin() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    if (isset($_POST["yvts_applicationpage"])) { update_option("yvts_coursemanager_application_page",$_POST["yvts_applicationpage"]); }
    if (isset($_POST["yvts_schedulepage"])) { update_option("yvts_coursemanager_schedule_page",$_POST["yvts_schedulepage"]); }
    if (isset($_POST["yvts_captchapublic"])) { update_option("yvts_coursemanager_captcha_public",$_POST["yvts_captchapublic"]); }
    if (isset($_POST["yvts_captchaprivate"])) { update_option("yvts_coursemanager_captcha_private",$_POST["yvts_captchaprivate"]); }

    echo "<p>List summary information:</p>";
    echo "<p>Show counts of data - courses, levels and exams.</p>";
    echo "<p>The system contains " . yvts_course::getCount() . " courses, with " . yvts_level::getCount() . " levels, " . yvts_courseRunning::getCount() . " scheduled courses and " . yvts_exam::getCount() . " exams.</p>";
    echo "<p>There are " . yvts_application::getCount() . " form fields on the application page.</p>";
    echo "<p>To display the scheduled courses, use shortcode [yvts_schedule year=\"2019\"] - if you use the shortcode with no year attribute like [yvts_schedule] the displayed schedule will use the current year.</p>";
    echo "<p>To display the application page, use shortcode [yvts_application] - you'll also need to enter the url of the page you'd added this code to below, so the schedule can link to your chosen application page.</p>";
    
    echo "<h3>Application page.</h3>";
    echo "<form method=\"post\">";
    echo "<p><label for=\"yvts_applicationpage\">Application Page (with the  application shortcode in place):</label><input style=\"width: 40em;\" name=\"yvts_applicationpage\" id=\"yvts_applicationpage\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_application_page");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" />";
    echo "<p><label for=\"yvts_schedulepage\">Schedule Page (where users are linked to if arriving at the application page with no course requested):</label><input style=\"width: 40em;\" name=\"yvts_schedulepage\" id=\"yvts_schedulepage\" value=\"";
    $schedulepage = get_option("yvts_coursemanager_schedule_page");
    if ($schedulepage != false) { echo "$schedulepage"; };
    echo "\" /><input name=\"yvts_applicationpage_sub\" type=\"submit\" value=\"Save Pages\" /></form>";
    echo "</p>";

    echo "<h3>reCaptcha settings.</h3>";
    echo "<p>Below you can add the details for <a href=\"https://www.google.com/recaptcha/\">Recaptcha</a> - if you then add a form field under \"applications\" of type \"captcha\" and these boxes are filled in, a recaptcha will be added to the application form at that location.</p>";
    echo "<p><label for=\"yvts_captchapublic\">ReCaptcha Site Key:</label> ";
    echo "<form method=\"post\"><input style=\"width: 40em;\" name=\"yvts_captchapublic\" id=\"yvts_captchapublic\" value=\"";
    $captchapublic = get_option("yvts_coursemanager_captcha_public");
    if ($captchapublic != false) { echo "$captchapublic"; };
    echo "\" /><br /><label for=\"yvts_captchaprivate\">Recaptcha Private Key:</label> ";
    echo "<form method=\"post\"><input style=\"width: 40em;\" name=\"yvts_captchaprivate\" id=\"yvts_captchaprivate\" value=\"";
    $captchaprivate = get_option("yvts_coursemanager_captcha_private");
    if ($captchaprivate != false) { echo "$captchaprivate"; };
    echo "\" /><br /><input name=\"yvts_captcha_sub\" type=\"submit\" value=\"Save Captcha\" />";
    echo "</p></form>";
    echo "<p style=\"font-size: 80%; text-align: right;\">Database Version: " . get_option("yvts_coursemanager_db_version") . "</p>";
}

include "functions/yvts_coursemanager_admin_courses.php";
include "functions/yvts_coursemanager_admin_schedules.php";
include "functions/yvts_coursemanager_admin_applications.php";

include "functions/yvts_coursemanager_render.php";
include "functions/yvts_coursemanager_application.php";

function yvts_coursemanager_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_enqueue_style( 'yvts_style', $plugin_url . 'css/style.css',array(), filemtime( plugin_dir_path( __FILE__ ) .  'css/style.css' ) );
    wp_enqueue_script('google-captcha', '//www.google.com/recaptcha/api.js', array(), '3', true);
}

yvts_coursemanager_upgrade();

add_action( 'admin_enqueue_scripts', 'yvts_coursemanager_load_plugin_css' );
add_action( 'wp_enqueue_scripts', 'yvts_coursemanager_load_plugin_css' );

register_activation_hook( __FILE__, 'yvts_coursemanager_install' );

add_action( 'admin_menu', 'yvts_coursemanager_admin_menu' );

add_action( 'plugins_loaded', 'yvts_coursemanager_upgrade' );

add_shortcode( 'yvts_application', 'yvts_coursemanager_application' );

add_shortcode( 'yvts_schedule', 'yvts_coursemanager_render' );

?>