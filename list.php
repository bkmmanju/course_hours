<?php

// This file is part of the Certificate module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


/**
 * Handles uploading files
 *
 * @package    local_course_hours
 * @copyright  Manoj Prabahar<manojprabahar@elearn10.com>
 * @copyright  Dhruv Infoline Pvt Ltd <lmsofindia.com>
 * @license    http://www.lmsofindia.com 2017 or later
 */

require_once('../../config.php');

require_once('lib.php');
global $CFG;
global $PAGE,$OUTPUT;
require_login();

$courseid = optional_param('cid','',PARAM_INT);
$context = context_course::instance($courseid);
$create = optional_param('create','',PARAM_INT);
$update = optional_param('update','',PARAM_INT);

$cid = optional_param('cid','',PARAM_INT);
$local = get_string('local','local_course_hours');
$url = $CFG->wwwroot;
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($CFG->wwwroot .'/local/course_hours/list.php');
$title = get_string('manage_datas','local_course_hours');
$PAGE->set_title($title);
$PAGE->set_heading($title);

$csname = $DB->get_record('course', array('id'=>$courseid));
$coursename = $csname->fullname;
$PAGE->navbar->add($coursename,new moodle_url('/course/view.php?id='.$courseid));
//$previewnode = $PAGE->navbar->add($local,$url);
//$thingnode = $previewnode->add($title);
//$thingnode->make_active();

echo $OUTPUT->header();

$list = list_course_hours_datas($cid);

if(!empty($list))
{
	$table = create_course_hours_table($list,$cid);
}
else
{
	$prodismsg = get_string('promsgdisplay','local_course_hours');
    echo $OUTPUT->notification($prodismsg);
}

if(!empty($create))
{
	$sucssmsg = get_string('createscss','local_course_hours');
	echo $OUTPUT->notification($sucssmsg,'notifysuccess');
}
if(!empty($update))
{
	$sucssmsg = get_string('upadatescss','local_course_hours');
	echo $OUTPUT->notification($sucssmsg,'notifysuccess');
}
global $DB,$USER;
$sql = "select * from {hpcl_coursehours} where course_id='".$courseid."' ";
$check_record = $DB->record_exists_sql($sql);
if(empty($check_record)){
	$link =  new moodle_url('/local/course_hours/add_course_hours.php',array('cid'=>$courseid));

	$html = "";
	$html .= html_writer::start_tag('a',array('role'=>'button','href'=>$link,'style'=>'float:right;','class'=>'btn btn-primary'));
	$html .='Add Course Duration';
	$html .= html_writer::end_tag('a');
	echo $html;
	echo "<br>";
	echo "<br>";
	echo "<br>";
}


if(!empty($table)){
	echo $table;
}
echo $OUTPUT->footer();
