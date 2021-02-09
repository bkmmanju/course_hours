<?php
require_once('../../config.php');
$startdt = optional_param('startdate','',PARAM_RAW);
$enddt = optional_param('enddate','',PARAM_RAW);
global $DB;
$results = $DB->get_records_sql("SELECT cc.id, u.id as userid,c.id as courseid,u.username, u.firstname, u.lastname, u.email, ch.hours as CDuration, ch.hpclcategory,ch.coursecode, c.fullname, cc.timeenrolled as timeenrolled,
FROM_UNIXTIME(cc.timecompleted) as timecompleted,cc.timecompleted as unixtime,ch.facultycode,ch.learnigtype,ch.programtype,ch.vendor,ch.summativeassessment
FROM {course} AS c
JOIN {hpcl_coursehours} as ch ON c.id = ch.course_id
JOIN {course_completions} AS cc ON cc.course = c.id
JOIN {user} AS u ON u.id = cc.userid
WHERE cc.timecompleted is not null 
AND cc.timecompleted BETWEEN $startdt AND $enddt ORDER by cc.timeenrolled");

if(!empty($results)){
foreach ($results as $result) {
//userid.
$uid = $result->userid;
//courseid.
$cid = $result->courseid;
//Rachita : If the enrollment date is '0' then find the enrollment date from the 'user_enrolment' table. 01/02/2021.
$enroltime = "";
if ($result->timeenrolled == 0) {
//here I am checking userid and courseid are empty or not.
if(!empty($uid) && !empty($cid)){
$query="SELECT ue.timecreated FROM {user_enrolments} AS ue
JOIN {enrol} AS en ON ue.enrolid = en.id
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
//manju: inserting all the date to table.
//checking if the courseid and userid is already present or not.
$check = $DB->get_records('hpcl_report_query_data',array('courseid'=>$cid,'userid'=>$uid));
if(empty($check)){
$insert = new stdClass();
$insert->userid = $uid;
$insert->courseid = $cid;
$insert->username = $result->username;
$insert->firstname = $result->firstname;
$insert->lastname = $result->lastname;
$insert->email = $result->email;
$insert->courseduration = $result->cduration;
$insert->coursename = $result->fullname;
$insert->enrolldate = $result->timeenrolled;
$insert->hpclcategory = $result->hpclcategory;
$insert->coursecode = $result->coursecode;
$insert->facultycode = $result->facultycode;
$insert->coursecompletion = $result->timecompleted;
$insert->learningtype = $result->learnigtype;
$insert->programtype = $result->programtype;
$insert->vendorname = $result->vendor;
$insert->summative_assessment = $result->summativeassessment;
$DB->insert_record('hpcl_report_query_data',$insert);
}
}
}
//Manju: Redirecting back to formpage.
$url = $CFG->wwwroot.'/local/course_hours/report.php';
$massage = get_string('notify','local_course_hours');
redirect($url,$massage);