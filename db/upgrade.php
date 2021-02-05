<?php
// This file keeps track of upgrades to
// the assignment module

// Sometimes, changes between versions involve
// alterations to database structures and other
// major things that may break installations.

// The upgrade function in this file will attempt
// to perform all the necessary actions to upgrade
// your older installation to the current version.

// If there's something it cannot do itself, it
// will tell you what you need to do.

// The commands in here will all be database-neutral,
// using the methods of database_manager class

// Please do not forget to use upgrade_set_timeout()
// before any action that may take longer time to finish.

defined('MOODLE_INTERNAL') || die();

function xmldb_local_course_hours_upgrade($oldversion) {
   global $CFG,$DB;
   $dbman = $DB->get_manager();

   if ($oldversion < 2020092803) {
        // Define field patient_id to be added to patient_complete_details.
    $table = new xmldb_table('hpcl_coursehours_categories');
        //organization Address
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, 
        XMLDB_NOTNULL, XMLDB_SEQUENCE, null); 
    $table->add_field('hpclcategory', XMLDB_TYPE_TEXT, '255',
        null, null,null, null, null);
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        // Conditionally launch add field organization address.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    //--------------------------------
    $table2 = new xmldb_table('hpcl_coursehours');

    $field1 = new xmldb_field('hpclcategory', XMLDB_TYPE_TEXT, null, null, null, null, null, 'date');

    $field2 = new xmldb_field('coursecode', XMLDB_TYPE_TEXT, null, null, null, null, null, 'hpclcategory');

    $field3 = new xmldb_field('facultycode', XMLDB_TYPE_TEXT, null, null, null, null, null, 'coursecode');

    $field4 = new xmldb_field('extra1', XMLDB_TYPE_TEXT, null, null, null, null, null, 'facultycode');

    $field5 = new xmldb_field('extra2', XMLDB_TYPE_TEXT, null, null, null, null, null, 'extra1');

    $field6 = new xmldb_field('extra3', XMLDB_TYPE_TEXT, null, null, null, null, null, 'extra2');

        // Conditionally launch add field encompetence.
    if (!$dbman->field_exists($table2, $field1)) {
        $dbman->add_field($table2, $field1);
    }
    if (!$dbman->field_exists($table2, $field2)) {
        $dbman->add_field($table2, $field2);
    }
    if (!$dbman->field_exists($table2, $field3)) {
        $dbman->add_field($table2, $field3);
    }
    if (!$dbman->field_exists($table2, $field4)) {
        $dbman->add_field($table2, $field4);
    }
    if (!$dbman->field_exists($table2, $field5)) {
        $dbman->add_field($table2, $field5);
    }
    if (!$dbman->field_exists($table2, $field6)) {
        $dbman->add_field($table2, $field6);
    }
        // Patientrecord savepoint reached.
    upgrade_plugin_savepoint(true, 2020092803,'local', 'course_hours');
}

//Manju: 27/11/2020.
if ($oldversion < 2020092806) {
    $table = new xmldb_table('hpcl_coursehours');
    $field1 = new xmldb_field('extra1', XMLDB_TYPE_TEXT, null, null, null, null, null, 'facultycode');

    $field2 = new xmldb_field('extra2', XMLDB_TYPE_TEXT, null, null, null, null, null, 'extra1');

    $field3 = new xmldb_field('extra3', XMLDB_TYPE_TEXT, null, null, null, null, null, 'extra2');
    /// To rename one field:
    $dbman->rename_field($table, $field1, "learnigtype", $continue=true, $feedback=true);
    $dbman->rename_field($table, $field2, "programtype", $continue=true, $feedback=true);
    $dbman->rename_field($table, $field3, "vendor", $continue=true, $feedback=true);
    $field4 = new xmldb_field('summativeassessment', XMLDB_TYPE_TEXT, '255', null, null, null, null, 'vendor');
    if (!$dbman->field_exists($table, $field4)) {
        $dbman->add_field($table, $field4);
    }
   
    upgrade_plugin_savepoint(true, 2020092806,'local', 'course_hours');
}
    return true;
}
