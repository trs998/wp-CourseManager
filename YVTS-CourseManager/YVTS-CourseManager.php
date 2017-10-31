<?php
defined( 'ABSPATH' ) or die( 'No Direct Access' );

function yvts_coursemanager_install () {

    add_option( "yvts_coursemanager_db_version", "1.0" );

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "yvts_courses"; 
    
    $sql = "CREATE TABLE $table_name (
        courseid mediumint(9) NOT NULL AUTO_INCREMENT,
        edittime datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
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

    //FIXME - needs more tables:  CourseRunning and Exams 
}

function yvts_coursemanager_admin_menu() {
    /* Builds admin functions */
	add_options_page( 'Course Manager Options', 'YVTS Course Manager', 'manage_options', 'my-unique-identifier', 'yvts_coursemanager_admin_courses' );
}

function yvts_coursemanager_admin_courses() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo '<p>TODO: show existing list of courses, and allow the user to add new ones.</p>';
	echo '</div>';
}

register_activation_hook( __FILE__, 'yvts_coursemanager_install' );

add_action( 'admin_menu', 'yvts_coursemanager_admin_menu' );

?>