<?php
// This file is part of the Local welcome plugin
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


if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.'); // It must be included from a Moodle page.
}
global $CFG,$USER;

/** Manoj
* Date : 28 SEP 2020
*this function is used to insert_course_hours_data
*
**/
function insert_course_hours_data($data){
    global $CFG,$USER,$DB;
    $insert_course_object = new stdClass();
    $insert_course_object->course_id = $data->cid;
    $insert_course_object->hours = $data->hours;
    $insert_course_object->date = time();
    $insert_course_object->hpclcategory = $data->hpclcategory;
    $insert_course_object->coursecode = $data->coursecode;
    $insert_course_object->facultycode = $data->facultycode;
    //Manju: 27/11/2020.
    $insert_course_object->learnigtype = $data->learnigtype;
    $insert_course_object->programtype = $data->programtype;
    $insert_course_object->vendor = $data->vendor;
    $insert_course_object->summativeassessment = $data->available;

    $insert_datas = $DB->insert_record('hpcl_coursehours',$insert_course_object);
    return $insert_datas;
}

/** Manoj
* Date : 28 SEP 2020
*this function is used to insert_course_hours_data
*
**/
function update_course_hours_data($data){
    global $CFG,$USER,$DB;
    $update_course_object = new stdClass();
    $update_course_object->id = $data->hid;
    $update_course_object->course_id = $data->cid;
    $update_course_object->hours = $data->hours;
    $update_course_object->date = time();
    $update_course_object->hpclcategory = $data->hpclcategory;
    $update_course_object->coursecode = $data->coursecode;
    $update_course_object->facultycode = $data->facultycode;
    //Manju: 27/11/2020.
    $update_course_object->learnigtype = $data->learnigtype;
    $update_course_object->programtype = $data->programtype;
    $update_course_object->vendor = $data->vendor;
    $update_course_object->summativeassessment = $data->available;

    $insert_datas = $DB->update_record('hpcl_coursehours',$update_course_object);
    return $insert_datas;
}
/** Manoj
* Date : 28 SEP 2020
**/

function local_course_hours_extend_settings_navigation(settings_navigation $nav, $context) {
    global $CFG;

    if ($context->contextlevel >= CONTEXT_COURSE and ($branch = $nav->get('courseadmin'))
        and has_capability('moodle/course:update', $context)) {

        $url = new moodle_url($CFG->wwwroot .'/local/course_hours/list.php', array('cid' => $context->instanceid));
    $branch->add(get_string('add_course_hours', 'local_course_hours'), $url, $nav::TYPE_CONTAINER, null, 'add_course_hours' . $context->instanceid, new pix_icon('i/settings', ''));

}
}

 /** Manoj
* Date : 28 SEP 2020
*this function is used for print the heading of page
*
**/
function get_course_hours_heading($headingtext,$subheadingtext,$buttonlink,$buttontext){
    global $CFG;
    $headingdetails = html_writer::start_tag('div',  array('class' => 'row'));
    $headingdetails .= html_writer::start_tag('div',  array('class' => 'col-md-6'));
    $headingdetails .= html_writer::start_tag('h4');
    $headingdetails .= $headingtext;
    $headingdetails .= html_writer::end_tag('h4');
    $headingdetails .= html_writer::start_tag('p');
    $headingdetails .= $subheadingtext;
    $headingdetails .= html_writer::end_tag('p');
    $headingdetails .= html_writer::end_tag('div');
    $headingdetails .= html_writer::end_tag('div');
    return $headingdetails;
}

/** Manoj
* Date : 28 SEP 2020
*function name : list_course_hours_datas($courseid);
*return an $getdetailes object 
*
**/
function list_course_hours_datas($courseid){
    global $DB;
    $sql = 'SELECT * from {hpcl_coursehours} where course_id="'.$courseid.'" ';

    $getdetailes = $DB->get_record_sql($sql);

    return $getdetailes;
}
/** Manoj
* Date : 28 SEP 2020
*funtion name : create_course_hours_table();
*parameter : $data means form object
*return an $tabledisplay object 
*
**/
function create_course_hours_table($data,$courseid){
    global $DB;
    $tabledisplay = '';
    $tabledisplay .= html_writer::start_tag('div',  array('class' => 'table-responsive'));
    $table = new html_table();
    $table->head = (array) get_strings(array('sno','course_name','timing','date','actionbtn'),'local_course_hours');
    $table->id =  'example1';
    $i = 1;
    $actiontbtn = create_course_hours_button($data->id,$data->course_id);
    $get_course = $DB->get_record('course',array('id'=>$data->course_id));
    $date = date('Y-m-d',$data->date);
    $table->data[] = array($i,$get_course->fullname,$data->hours,$date,$actiontbtn);
    $tabledisplay .= html_writer::table($table);
    $tabledisplay .= html_writer::end_tag('div');

    return $tabledisplay;
}
/** Manoj
* Date : 12 MAY 2020  
*funtion name : create_course_hours_button();
*parameter : $data means form object
*return an $array object 
*
**/
function create_course_hours_button($data,$cid)
{
    global $CFG;
    $array = "";
    $array .= html_writer::start_tag('div',array('class'=>'dropdown menushow'));
    $array.=html_writer::start_tag('a',array('href' =>  '#','class'=>'btn btn-primary dropdown-toggle','role'=>"button", 'id'=>"dropdownMenuLink" ,'data-toggle'=>"dropdown", 'aria-haspopup'=>"true" ,'aria-expanded'=>"false"));
    $array .= get_string('actionbtn','local_course_hours');
    $array.=html_writer::end_tag('a');

    $array .= html_writer::start_tag('div', array('class' => 'dropdown-menu','aria-labelledby'=>"dropdownMenuLink"));

    $array .= html_writer::start_tag('a',array('href' => $CFG->wwwroot.'/local/course_hours/add_course_hours.php?hid='.$data.'&cid='.$cid, 'class'=>"dropdown-item"));
    $array .= html_writer::start_tag('i',  array('class' => 'fa fa-edit iconspad'));
    $array .= html_writer::end_tag('i');
    $array .= get_string('edit','local_course_hours');
    $array .= html_writer::end_tag('a'); 


    $array .= html_writer::end_tag('div');
    $array .= html_writer::end_tag('div');

	////////////////////////////


    $array1 = html_writer::start_tag('a',array('href' => $CFG->wwwroot.'/local/course_hours/add_course_hours.php?hid='.$data.'&cid='.$cid, 'class'=>"dropdown-item"));
    $array1 .= get_string('edit','local_course_hours');
    $array1 .= html_writer::end_tag('a'); 

    return $array1;
}

function report_data($startdate,$enddate){
    global $DB,$CFG;
	//WHERE cc.timeenrolled BETWEEN '$startdate' AND '$enddate' 
	/** old before jan 2021
	
	SELECT Distinct(ra.id), u.username, u.firstname, u.lastname, u.email, ch.hours as CDuration, ch.hpclcategory,ch.coursecode, c.fullname, FROM_UNIXTIME(cc.timeenrolled) as timeenrolled, 
		FROM_UNIXTIME(cc.timecompleted) as timecompleted,ch.facultycode,ch.learnigtype,ch.programtype,ch.vendor,ch.summativeassessment  
            FROM {role_assignments} AS ra
            JOIN {context} AS context ON context.id = ra.contextid AND context.contextlevel = 50
            JOIN {course} AS c ON c.id = context.instanceid
            join {hpcl_coursehours} as ch ON c.id = ch.course_id
            JOIN {user} AS u ON u.id = ra.userid
            JOIN {course_completions} AS cc ON cc.course = c.id AND cc.userid = u.id 
            WHERE (cc.timeenrolled BETWEEN '$startdate' AND '$enddate') OR (cc.timecompleted BETWEEN '$startdate' AND '$enddate')
            ORDER by cc.timeenrolled");
	**/
	/*
		SELECT u.username,ch.coursecode,c.fullname as programname, 
ch.hpclcategory,ch.learnigtype as deliverytype,
FROM_UNIXTIME(cc.timeenrolled) as startdate,
FROM_UNIXTIME(cc.timecompleted) as enddate,ch.hours as CDuration,
ch.facultycode,ch.vendor,ch.programtype 
FROM mdl_course AS c
JOIN mdl_context AS context ON context.instanceid = c.id AND context.contextlevel = 50 
JOIN mdl_hpcl_coursehours as ch ON c.id = ch.course_id
JOIN mdl_course_completions AS cc ON cc.course = c.id
JOIN mdl_user AS u ON u.id = cc.userid 
WHERE (cc.timeenrolled BETWEEN startdate AND enddate) OR (cc.timecompleted BETWEEN startdate AND enddate)

*/
    if(!empty($startdate) && !empty($enddate)){
        $result = $DB->get_records_sql("SELECT cc.id, u.id as userid,c.id as courseid,u.username, u.firstname, u.lastname, u.email, ch.hours as CDuration, ch.hpclcategory,ch.coursecode, c.fullname, cc.timeenrolled as timeenrolled, 
		FROM_UNIXTIME(cc.timecompleted) as timecompleted,cc.timecompleted as unixtime,ch.facultycode,ch.learnigtype,ch.programtype,ch.vendor,ch.summativeassessment  
            FROM {course} AS c
            JOIN {hpcl_coursehours} as ch ON c.id = ch.course_id
            JOIN {course_completions} AS cc ON cc.course = c.id 
			JOIN {user} AS u ON u.id = cc.userid
            WHERE (cc.timecompleted BETWEEN '$startdate' AND '$enddate')
            ORDER by cc.timeenrolled");
        return $result;

    }
}






