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
    if (isset($_POST["yvts_email"])) { update_option("yvts_coursemanager_email",$_POST["yvts_email"]); }
    if (isset($_POST["yvts_email_subject"])) { update_option("yvts_coursemanager_email_subject",$_POST["yvts_email_subject"]); }
    if (isset($_POST["yvts_email_from"])) { update_option("yvts_coursemanager_email_from",$_POST["yvts_email_from"]); }
    if (isset($_POST["yvts_suppressdescriptiononnonscheduled"])) { update_option("yvts_coursemanager_suppress_description",true); } else { update_option("yvts_coursemanager_suppress_description",false); }
    

    echo "<h1>Course Manager Settings</h1>";
    
    echo "<form method=\"post\" novalidate=\"novalidate\">";
    echo "<table class=\"form-table\">";
    echo "<tbody>";
    
    $fieldname = "yvts_applicationpage";
    echo "<tr><th scope=\"row\">
    Application Page
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_application_page");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    The URL of the course application page - which contains the application shortcode [yvts_application]
    </p>
    </td></tr>";
    
    $fieldname = "yvts_schedulepage";
    echo "<tr><th scope=\"row\">
    Schedule Page
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_schedule_page");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    The URL of the course schedule page - where users are linked to if arriving at the application page with no course requested.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_email";
    echo "<tr><th scope=\"row\">
    Email for applications
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_email");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Email address submitted course applications should go to.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_email_subject";
    echo "<tr><th scope=\"row\">
    Email Subject
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_email_subject");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Email subject line for submitted course applications.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_email_from";
    echo "<tr><th scope=\"row\">
    Email From
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_email_from");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Email address submitted course applications come from.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_captchapublic";
    echo "<tr><th scope=\"row\">
    ReCaptcha Public Key
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_captcha_public");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Public key for the Google ReCaptcha - you can obtain a key here <a href=\"https://www.google.com/recaptcha/\">Recaptcha</a> - if you then add a form field under \"applications\" of type \"captcha\" and these boxes are filled in, a recaptcha will be added to the application form at that location.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_captchaprivate";
    echo "<tr><th scope=\"row\">
    ReCaptcha Private Key
    </th><td>
    <input name=\"$fieldname\" type=\"url\" id=\"$fieldname\" value=\"";
    $applicationpage = get_option("yvts_coursemanager_captcha_private");
    if ($applicationpage != false) { echo "$applicationpage"; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Private key for the Google ReCaptcha.
    </p>
    </td></tr>";
    
    $fieldname = "yvts_suppressdescriptiononnonscheduled";
    echo "<tr><th scope=\"row\">
    Suppress course description on unscheduled courses
    </th><td>
    <input name=\"$fieldname\" type=\"checkbox\" id=\"$fieldname\"";
    $applicationpage = get_option("yvts_coursemanager_suppress_description");
    if ($applicationpage != false) { echo " checked=\"checked\" "; };
    echo "\" aria-described-by=\"" . $fieldname . "-description\" class=\"regular-text code\" />
    <p class=\"description\" id=\"" . $fieldname . "-description\">
    Suppress the display of the course description for applications for courses without scheduled dates. This could be useful if you're using it to describe details of the booking or length, which do not apply to offered versions of this course which do not have fixed lengths, such as re-sits.
    </p>
    </td></tr>";
    
    echo "</tbody>";
    echo "</table>";

    echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" id=\"submit\" class=\"button button-primary\" value=\"Save Changes\"  /></p>";

    echo "</form>";

    echo "<p>The system contains " . yvts_course::getCount() . " courses, with " . yvts_level::getCount() . " levels, " . yvts_courseRunning::getCount() . " scheduled courses and " . yvts_exam::getCount() . " exams. There are " . yvts_application::getCount() . " form fields on the application page.</p>";
    echo "<p>To display the scheduled courses, use shortcode [yvts_schedule year=\"2019\"] - if you use the shortcode with no year attribute like [yvts_schedule] the displayed schedule will use the current year.</p>";
    echo "<p>To display the application page, use shortcode [yvts_application] - you'll also need to enter the url of the page you'd added this code to below, so the schedule can link to your chosen application page.</p>";

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
add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);
function log_mailer_errors($mailer){
  $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
  $fp = fopen($fn, 'a');
  fputs($fp, date("c")." Mailer Error: " . print_r($mailer->ErrorInfo) . "\n");
  fclose($fp);
}

register_activation_hook( __FILE__, 'yvts_coursemanager_install' );

add_action( 'admin_menu', 'yvts_coursemanager_admin_menu' );

add_action( 'plugins_loaded', 'yvts_coursemanager_upgrade' );

add_shortcode( 'yvts_application', 'yvts_coursemanager_application' );

add_shortcode( 'yvts_schedule', 'yvts_coursemanager_render' );

?>