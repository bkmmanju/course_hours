<?php
// This file is part of Moodle - http://moodle.org/
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
 * This plugin sends users a welcome message after logging in
 * and notify a moderator a new user has been added
 * it has a settings page that allow you to configure the messages
 * send.
 *
 * @package    local
 * @subpackage cs_reminder
 * @copyright  Manjunath
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course_hours;

defined('MOODLE_INTERNAL') || die();

class observer {

	public static function sync_data(\core\event\user_loggedin $event) {
		global $CFG, $SITE,$DB,$USER;
		$eventdata = $event->get_data();
		$user = \core_user::get_user($eventdata['objectid']);
		$userid = $user->id;
		$results = $DB->get_records_sql("SELECT cc.id, u.id as userid,c.id as courseid,u.username, u.firstname, u.lastname, u.email, ch.hours as CDuration, ch.hpclcategory,ch.coursecode, c.fullname, cc.timeenrolled as timeenrolled, 
			FROM_UNIXTIME(cc.timecompleted) as timecompleted,cc.timecompleted as unixtime,ch.facultycode,ch.learnigtype,ch.programtype,ch.vendor,ch.summativeassessment  
			FROM {course} AS c
			JOIN {hpcl_coursehours} as ch ON c.id = ch.course_id
			JOIN {course_completions} AS cc ON cc.course = c.id 
			JOIN {user} AS u ON u.id = cc.userid
			WHERE u.id= $userid AND cc.timecompleted is not null 
			ORDER by cc.timeenrolled");
		$i = 1;
		if(!empty($results)){
			foreach ($results as $result) {
		//userid.
				$uid = $result->userid;
		//courseid.
				$cid = $result->courseid;

				$coursecompletion = $result->timecompleted;
		//Rachita :  If the enrollment date is '0' then find the enrollment date from the 'user_enrolment' table. 01/02/2021.
		//we should check here itself 
				$check = $DB->get_record('hpcl_report_query_data',array('courseid'=>$cid,'userid'=>$uid));
				if(empty($check)){
					$i = $i +1;
					$enroltime = "";
					if ($result->timeenrolled == 0) {
				//here I am checking userid and courseid are empty or not.
						if(!empty($uid) && !empty($cid)){
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
			//manju: inserting all the date to table.
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
					echo 'inserted';
				} else {
					if (($check->coursecompletion == NULL) OR empty($check->coursecompletion) ) {
						echo $check->id;
						$update = new \stdClass();
						$update->id = $check->id;
						$update->coursecompletion = $result->timecompleted;
						$DB->update_record('hpcl_report_query_data',$update);
						echo 'updated '.$check->id;
					}

				}
			}
			echo 'Total insert '.$i;
		}
	}
}