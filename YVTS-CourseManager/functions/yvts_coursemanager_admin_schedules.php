<?php
//farmed out to avoid massive file

function yvts_coursemanager_admin_schedules() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	echo "<h2>List of courses scheduled.</h2>";
	echo "<h1>" . date("Y") . "</h1>";
    echo "<h3>TODO: show years as options, default to current year</h3>";
    echo '<p>TODO: Show Scheduled courses, add ability to schedule a course</p>';
    echo "<p></p>";
	echo '</div>';
}

?>