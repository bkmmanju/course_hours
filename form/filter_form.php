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
 * @license    http://www.lmsofindia.com 2019 or later
 */

if (!defined('MOODLE_INTERNAL')) 
{
    die('Direct access to this script is forbidden.'); /// It must be included from a Moodle page
}
require_once($CFG->libdir.'/formslib.php');
class local_course_hours_filter extends moodleform
{
	public function definition()
	{
		global $CFG,$OUTPUT,$DB,$USER,$PAGE;
		$context = context_system::instance();

		$mform = $this->_form;
		$date_options = array(
			'startyear' => 2010, 
			'stopyear'  => 2050,
			'timezone'  => 99,
			'optional'  => false
		);
		$mform->addElement('date_selector', 'reportstart', get_string('reportstart','local_course_hours'), $date_options);
		$mform->addElement('date_selector', 'reportend', get_string('reportend','local_course_hours'), $date_options);
		//action buttons start here//
		$buttonarray = array();
		$buttonarray[] = $mform->createElement('submit','submitbutton',get_string('savebutton','local_course_hours'));
		$buttonarray[] = $mform->createElement('cancel');
		//manju : adding new button (sync data).
		$buttonarray[] = $mform->createElement('submit', 'syncdata',get_string('syncdata','local_course_hours'),array('onclick'=>'syncdata();'));
		//Manju: adding new button (enroll and completion report)
		$buttonarray[] = $mform->createElement('submit', 'enrolcomplete',get_string('enrolandcompletion','local_course_hours'));

		$mform->addGroup($buttonarray,'buttonarray','','',false);
		//action buttons end here//


	}
}