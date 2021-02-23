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
require_once('form/filter_form.php');
require_once($CFG->libdir . '/formslib.php');
require_once ('lib.php');
defined('MOODLE_INTERNAL') || die();
require_login();
global $DB;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_url($CFG->wwwroot . '/local/course_hours/report.php');
$title = get_string('reportpage','local_course_hours');
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/jquery.dataTables.min.js'),true);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/course_hours/css/jquery.dataTables.min.css'));
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/dataTables.buttons.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/buttons.print.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/buttons.colVis.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/buttons.flash.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/buttons.html5.min.js'),true);
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/local/course_hours/css/buttons.dataTables.min.css'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/jszip.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/pdfmake.min.js'),true);
$PAGE->requires->js(new moodle_url($CFG->wwwroot.'/local/course_hours/js/vfs_fonts.js'),true);
$mform = new local_course_hours_filter();
$html='';
if ($mform->is_cancelled()) {
} else if ($data = $mform->get_data()) {
	$startdate = $data->reportstart;
	$enddate = $data->reportend;
	//manju : Checking for whether form submitted for sync data.
	if(!empty($data->syncdata)){
		$url = $CFG->wwwroot.'/local/course_hours/syncdata.php?startdate='.$startdate.'&enddate='.$enddate;
		redirect($url);
	}
  $dataarray = (array)$data;
	if (array_key_exists("enrolcomplete",$dataarray))
	{
		$url = $CFG->wwwroot.'/local/course_hours/enrol_completion.php?startdate='.$startdate.'&enddate='.$enddate;
		redirect($url);
	}
	
	$results = report_data($startdate,$enddate);
	if(!empty($results)){
		$report = new html_table();
		$report->id = "reporttable";
		$report->head = array(get_string('serial','local_course_hours'),
			get_string('username','local_course_hours'),
			get_string('firstname','local_course_hours'),
			get_string('lastname','local_course_hours'),
			get_string('email','local_course_hours'),
			get_string('cduration','local_course_hours'),
			get_string('coursename','local_course_hours'),
			get_string('enrolldate','local_course_hours'),
			get_string('hpclcategory','local_course_hours'),
			get_string('coursecode','local_course_hours'),
			get_string('rfacultycode','local_course_hours'),
			get_string('completiondate','local_course_hours'),
			get_string('selectlearnigtypes','local_course_hours'),
			get_string('selectprogramtype','local_course_hours'),
			get_string('vendorname','local_course_hours'),
			get_string('available','local_course_hours') );
		$counter = 1;
		foreach ($results as $result) {
			//userid.
			$uid = $result->userid;
			//courseid.
			$cid = $result->courseid;

			//Rachita :  If the enrollment date is '0' then find the enrollment date from the 'user_enrolment' table. 01/02/2021.
			$enroltime = "";
			if ($result->timeenrolled == 0) {
				//here I am checking userid and courseid are empty or not.
				if(!empty($userid) && !empty($courseid)){
					$query="SELECT ue.timecreated FROM {user_enrolments} AS ue 
					JOIN {enrol} AS en  ON ue.enrolid = en.id 
					WHERE en.courseid = $cid AND ue.userid = $uid";
					$data = $DB->get_record_sql($query);
					$enroltime = $data->timecreated;
					$result->timeenrolled = $enroltime;
				}else{
					//Manju: if the enrolldate is 0 then completion date - 58 days.
					$endate = date('Y-m-d', strtotime('-58 day', $result->unixtime));
					$result->timeenrolled = strtotime($endate);
				}
			}
			$report->data[]=array($counter,
				$result->username,
				$result->firstname,
				$result->lastname,
				$result->email,
				$result->cduration,
				$result->fullname,
				date("d-m-Y",$result->timeenrolled),
				$result->hpclcategory,
				$result->coursecode,
				$result->facultycode,
				$result->timecompleted,
				$result->learnigtype,
				$result->programtype,
				$result->vendor,
				$result->summativeassessment );
			$counter++;
		}
		$html.=html_writer::start_div('container-fluid');
		$html.=html_writer::start_div('row');
		$html.=html_writer::start_div('col-md-12');
		$html.=html_writer::table($report);
		$html.="  <script>$(document).ready(function() {
    $('#reporttable').DataTable( {
        dom: 'Bfrtip',
                        buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Excel',
                        text:'Export to excel'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'PDF',
                        text: 'Export to PDF'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'CSV',
                        text: 'Export to CSV'
                    }
                ]
    } );
} );</script>";
		$html.=html_writer::end_div();
		$html.=html_writer::end_div();
		$html.=html_writer::end_div();
	}else{
		$html.=html_writer::start_div('container-fluid');
		$html.=html_writer::start_div('row');
		$html.=html_writer::start_div('col-md-12 text-center');
		$html.=html_writer::start_tag('h2');
		$html.=get_string('noresultsfound','local_course_hours');
		$html.=html_writer::end_tag('h2');
		$html.=html_writer::end_div();
		$html.=html_writer::end_div();
		$html.=html_writer::end_div();		
	}
}

echo $OUTPUT->header();
$mform->display();
echo $html;
echo $OUTPUT->footer();