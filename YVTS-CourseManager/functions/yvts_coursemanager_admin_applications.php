<?php
//split out to avoid massive file

function yvts_coursemanager_admin_applications() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    $types = array("textbox","textarea","checkbox","captcha","heading","examchoice","dateselector");

    if (isset($_POST["yvts_newapplication"])) {
        $yvts_newapplication_errors = "";
        $yvts_newapplication_name = $_POST["yvts_newapplication_name"];
        $yvts_newapplication_type = $_POST["yvts_newapplication_type"];
        $yvts_newapplication_position = $_POST["yvts_newapplication_position"];
        $yvts_newapplication_minlength = $_POST["yvts_newapplication_minlength"];
        $yvts_newapplication_note = $_POST["yvts_newapplication_note"];

        if (strlen($yvts_newapplication_name) < 3) { 
            $yvts_newapplication_errors = $yvts_newapplication_errors . "Field Name must be entered<br />";
        }
        if (! is_numeric($yvts_newapplication_position)) { 
            $yvts_newapplication_errors = $yvts_newapplication_errors . "Field Position should be a number.<br />";
        }
        if (! in_array($yvts_newapplication_type,$types)) { 
            $yvts_newapplication_errors = $yvts_newapplication_errors . "Type of field \"" . $yvts_newapplication_type . "\" not supported.<br />";
        }
        
        if ($yvts_newapplication_errors == "") {
            $addResult = yvts_application::createApplication($yvts_newapplication_name, $yvts_newapplication_type, $yvts_newapplication_position, $yvts_newapplication_minlength, $yvts_newapplication_note);
            if ($addResult === 1) {
                echo "<div class=\"yvts_topmessage\">Application form field added successfully.</div>";
            } else {
                $yvts_newapplication_name = "";
                $yvts_newapplication_type = "";
                $yvts_newapplication_position = "";
                $yvts_newapplication_note = "";
                $yvts_newapplication_minlength = "";
            }
        }
    }

    if (isset($_GET["yvts_deleteItem"])) {
        if (yvts_application::deleteApplication($_GET["yvts_deleteItem"])) {
            echo "<div class=\"yvts_topmessage\">Application form field deleted successfully.</div>";
        }
    }

    if (isset($_POST["yvts_editfield"])) {
        $yvts_edited_errors = "";
        $yvts_edited_id = $_POST["yvts_editfield"];
        $yvts_edited_position = $_POST["yvts_editField_position_" . $yvts_edited_id . "_e"];
        $yvts_edited_name = $_POST["yvts_editField_name_" . $yvts_edited_id . "_e"];
        $yvts_edited_type = $_POST["yvts_editField_type_" . $yvts_edited_id . "_e"];
        $yvts_edited_minlength = $_POST["yvts_editField_minlength_" . $yvts_edited_id . "_e"];
        $yvts_edited_note = $_POST["yvts_editField_hint_" . $yvts_edited_id . "_e"];

        if (strlen($yvts_edited_name) < 3) { 
            $yvts_edited_errors = $yvts_edited_errors . "Field Name must be entered<br />";
        }
        if (! is_numeric($yvts_edited_position)) { 
            $yvts_edited_errors = $yvts_edited_errors . "Field Position should be a number.<br />";
        }
        if (! in_array($yvts_edited_type,$types)) { 
            $yvts_edited_errors = $yvts_edited_errors . "Type of field \"" . $yvts_edited_type . "\" not supported.<br />";
        }
        
        if ($yvts_edited_errors == "") {
            $addResult = yvts_application::updateApplication($yvts_edited_id, $yvts_edited_name, $yvts_edited_type, $yvts_edited_position, $yvts_edited_minlength, $yvts_edited_note);
            if ($addResult === 1) {
                echo "<div class=\"yvts_topmessage\">Application form field saved successfully.</div>";
            }
        }
    }

    $applications = yvts_application::getApplicationFields();

	echo '<div class="wrap">';
    echo "<h2>List of form fields on the application page.</h2>";
    
	if ($yvts_edited_errors != "") {
		echo "<div style=\"color: red\">$yvts_edited_errors</div>";
    }

    echo "<table class=\"yvts_admin_fields\"><tr><th>Position</th><th>Type</th><th>Name</th><th>Min Length</th><th>Note</th><th>Actions</th></tr>";
    for ($i = 0; $i < count($applications); $i++) {
       // var_dump($applications[$i]);
       echo "<form method=\"post\"><input type=\"hidden\" name=\"yvts_editfield\" value=\"" . $applications[$i]->applicationid . "\" />";
        echo "<tr>
        <td>
        <script type=\"text/javascript\">
        function editform" . $applications[$i]->applicationid . "() {
        document.getElementById(\"yvts_editField_position_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_position_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        document.getElementById(\"yvts_editField_name_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_name_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        document.getElementById(\"yvts_editField_minlength_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_minlength_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        document.getElementById(\"yvts_editField_type_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_type_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        document.getElementById(\"yvts_editField_hint_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_hint_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        document.getElementById(\"yvts_editField_submit_" . $applications[$i]->applicationid . "\").style.display = 'none';
        document.getElementById(\"yvts_editField_submit_" . $applications[$i]->applicationid . "_e\").style.display = 'block';
        }
        </script>
        <span id=\"yvts_editField_position_" . $applications[$i]->applicationid . "\">" . $applications[$i]->position . "</span><input style=\"display: none; width: 3em;\" type=\"text\" id=\"yvts_editField_position_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_position_" . $applications[$i]->applicationid . "_e\" value=\"" . $applications[$i]->position . "\" /></td>
        <td><span id=\"yvts_editField_type_" . $applications[$i]->applicationid . "\">" . $applications[$i]->type . "</span>
        <select style=\"display: none;\" id=\"yvts_editField_type_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_type_" . $applications[$i]->applicationid . "_e\">";
        for ($j = 0; $j < count($types); $j++) {
            echo "<option value=\"" . $types[$j] . "\"";
            if ($applications[$i]->type == $types[$j]) { echo " selected=\"selected\" "; }
            echo ">" . $types[$j] . "</option>";
        }
        echo "
        </select>
        </td>
        <td><span id=\"yvts_editField_name_" . $applications[$i]->applicationid . "\">" . stripcslashes($applications[$i]->name) . "</span><input style=\"display: none;\" type=\"text\" id=\"yvts_editField_name_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_name_" . $applications[$i]->applicationid . "_e\" value=\"" . stripcslashes($applications[$i]->name) . "\" /></td>
        <td><span id=\"yvts_editField_minlength_" . $applications[$i]->applicationid . "\">";
        if ($applications[$i]->minlength > 0) { echo $applications[$i]->minlength; } else {
            echo "none";
        }
        echo "</span><input style=\"display: none; width: 3em;\" type=\"text\" id=\"yvts_editField_minlength_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_minlength_" . $applications[$i]->applicationid . "_e\" value=\"" . $applications[$i]->minlength . "\" /></td>
        <td><span id=\"yvts_editField_hint_" . $applications[$i]->applicationid . "\"><i>" . stripcslashes($applications[$i]->hint) . "</i></span><input style=\"display: none; width: 20em;\" type=\"text\" id=\"yvts_editField_hint_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_hint_" . $applications[$i]->applicationid . "_e\" value=\"" . stripcslashes($applications[$i]->hint) . "\" /></td>";
        echo "<td><a id=\"yvts_editField_submit_" . $applications[$i]->applicationid . "\" onclick=\"editform" . $applications[$i]->applicationid . "();\">edit</a>
        <input type=\"submit\" style=\"display: none;\" id=\"yvts_editField_submit_" . $applications[$i]->applicationid . "_e\" name=\"yvts_editField_submit_" . $applications[$i]->applicationid . "_e\" value=\"Save\" />
        <a href=\"" . add_query_arg("yvts_deleteItem",$applications[$i]->applicationid) . "\" onclick=\"if (confirm('Really delete this item?')) { window.location.href = '" . add_query_arg("yvts_deleteItem",$applications[$i]->applicationid) . "'; } else { return false; }\">delete</a></td>";
        echo "</tr>";
        echo "</form>";
    }
    echo "</table>";

    echo "<div class=\"yvts_applications_new\">
    <h3>New form field on the application form.</h3><br />";
	if ($yvts_newapplication_errors != "") {
		echo "<div style=\"color: red\">$yvts_newapplication_errors</div>";
    }
    echo "
    <form method=\"post\">
    <label form=\"yvts_newapplication_name\">New Form Field Name:</label> <input type=\"text\" id=\"yvts_newapplication_name\" name=\"yvts_newapplication_name\" value=\"" . $yvts_newapplication_name . "\" /><br />
    
    <label for=\"yvts_newapplication_type\">Form Field Type:</label> <select id=\"yvts_newapplication_type\" name=\"yvts_newapplication_type\">";
        for ($i = 0; $i < count($types); $i++) {
            echo "<option value=\"" . $types[$i] . "\"";
            if ($yvts_newapplication_type == $types[$i]) { echo " selected=\"selected\" "; }
            echo ">" . $types[$i] . "</option>";
        }
    echo "
    </select><br />

    <label form=\"yvts_newapplication_position\">New Field Position Number:</label> <input style=\"width: 10em;\" type=\"text\" id=\"yvts_newapplication_position\" name=\"yvts_newapplication_position\" value=\"" . $yvts_newapplication_position . "\" /><br />
    <label form=\"yvts_newapplication_minlength\">Minimum Length of input:</label> <input type=\"text\" id=\"yvts_newapplication_minlength\" name=\"yvts_newapplication_minlength\" value=\"";
    if ((!isset($yvts_newapplication_minlength)) || (!is_numeric($yvts_newapplication_minlength))) { echo "0"; } else { echo $yvts_newapplication_minlength; }
    echo "\" /> (If this field is not mandatory, leave as 0 minimum length)<br />
    
    <label form=\"yvts_newapplication_note\">New Form Field Note:</label> <input style=\"width: 10em;\" type=\"text\" id=\"yvts_newapplication_note\" name=\"yvts_newapplication_note\" value=\"" . $yvts_newapplication_note . "\" /><br />
    <input type=\"submit\" name=\"yvts_newapplication\" value=\"Save New Form Field\" />
    </form>
    </div>";

	echo '</div>';

}

?>