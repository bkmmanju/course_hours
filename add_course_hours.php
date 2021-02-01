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
require_once('form/course_hours_form.php');
require_once($CFG->libdir . '/formslib.php');
require_once ('lib.php');
defined('MOODLE_INTERNAL') || die();
require_login();
$cid = optional_param('cid','',PARAM_INT);

$context = context_course::instance($cid);
$contextid = $context->contextlevel;
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_url($CFG->wwwroot . '/local/course_hours/add_course_hours.php');
$title = get_string('edit_course_hours', 'local_course_hours');
$title1 = get_string('add_course_hours', 'local_course_hours');
$hid = optional_param('hid','',PARAM_INT);
$local = get_string('local','local_course_hours');
$url = $CFG->wwwroot.'/local/course_hours/add_course_hours.php';

$csname = $DB->get_record('course', array('id'=>$cid));
$coursename = $csname->fullname;
$PAGE->navbar->add($coursename,new moodle_url('/course/view.php?id='.$cid));

$mform  = '';
global $DB;
// display the heading of page
if(!empty($hid)){	
	$PAGE->set_title($title);
	$PAGE->set_heading($title);
	
	/*
	$previewnode = $PAGE->navbar->add($local,$url);
    $thingnode = $previewnode->add($title);
    $thingnode->make_active();
    $headingtext1 = get_string('add_course_hours','local_course_hours');
	$heading = get_course_hours_heading($headingtext1,'','','');
*/
	$mform = new local_course_hours_form($CFG->wwwroot .'/local/course_hours/add_course_hours.php',array('hid'=>$hid));

 }else{

	$PAGE->set_title($title1);
	$PAGE->set_heading($title1);
	
	/*
	$previewnode = $PAGE->navbar->add($local,$url);
    $thingnode = $previewnode->add($title1);
    $thingnode->make_active();
	$headingtext1 = get_string('add_course_hours','local_course_hours');
	$heading = get_course_hours_heading($headingtext1,'','','');
	
	*/
	
	$mform = new local_course_hours_form($CFG->wwwroot .'/local/course_hours/add_course_hours.php');

}
echo $OUTPUT->header();

$data = $mform->get_data();
// print_object($data);
// exit;
$flag = '';
$returnurl = new moodle_url('/local/course_hours/list.php',array('id'=>$cid));
if ($mform->is_cancelled()){
    // click on cancle button then page redirect to home page...
    redirect($returnurl);
}elseif(!empty($data)){
	if($data->hid == 0 or $data->hid == ""){
		global $DB,$USER,$CFG;
	    $insert_datas = insert_course_hours_data($data);
	   	$list_url = new moodle_url ('/local/course_hours/list.php',array('create'=>1,'cid'=>$cid));
		$redirect = redirect($list_url);
	}else{
		global $DB,$USER,$CFG;
	    $insert_datas = update_course_hours_data($data);
	   	$list_url = new moodle_url ('/local/course_hours/list.php',array('update'=>1,'cid'=>$cid));
		$redirect = redirect($list_url);
	}
    
}
if(!empty($cid)){
	$data = new stdClass();
	$data->cid = $cid;
	$mform->set_data($data);
}

if(!empty($hid) && !empty($cid)){
	$get_record = $DB->get_record('hpcl_coursehours',array('id'=>$hid));
	$data = new stdClass();
	$data->hours = $get_record->hours;
	$data->cid = $cid;
	$data->hid = $hid;

	$data->hpclcategory = $get_record->hpclcategory;
	$data->coursecode = $get_record->coursecode;
	$data->facultycode = $get_record->facultycode;
	//Manju: 27/11/2020.
	$data->learnigtype = $get_record->learnigtype;
	$data->programtype = $get_record->programtype;
	$data->vendor = $get_record->vendor;
	$data->available = $get_record->summativeassessment;
	
	$mform->set_data($data);
}

echo '<hr>';
$mform->display();
echo $OUTPUT->footer();