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
?>

<?php

class local_course_hours_form extends moodleform
{
	public function definition()
	{
		global $CFG,$OUTPUT,$DB,$USER,$PAGE;
		$context = context_system::instance();

		$mform = $this->_form;
		// $editid = $this->_customdata['cid'];
		$attributes = array('size'=>'40');
		
		//hours field start here//
		$mform->addElement('text','hours',get_string('hours','local_course_hours'));
		$mform->settype('hours', PARAM_RAW);
		$mform->addRule('hours',null,'required',null,'client');
		//hours field end here//
		$allcats = $DB->get_records('hpcl_coursehours_categories');
		$categories=[];
		$categories['']=get_string('selectcategory', 'local_course_hours');
		foreach ($allcats as $cat) {
			$categories[$cat->hpclcategory]=$cat->hpclcategory;
		}
		//hpcl category dropdown.
		$mform->addElement('select', 'hpclcategory', get_string('selectcategory', 'local_course_hours'), $categories);
		$mform->settype('hpclcategory', PARAM_RAW);
		//$mform->addRule('category',null,'required',null,'client');

		//HPCL Course Code.
		$mform->addElement('text','coursecode',get_string('coursecode','local_course_hours'));
		$mform->settype('coursecode', PARAM_RAW);
		//$mform->addRule('coursecode',null,'required',null,'client');

		//Facultycode.
		$mform->addElement('text','facultycode',get_string('facultycode','local_course_hours'));
		$mform->settype('facultycode', PARAM_RAW);
		//$mform->addRule('facultycode',null,'required',null,'client');

		//Manju: Learning Type - dropdown.27/11/2020.
		$learnigtypes = array(
		get_string('videobasedlearning','local_course_hours')=>get_string('videobasedlearning','local_course_hours'),
		get_string('elearingmodule','local_course_hours')=>get_string('elearingmodule','local_course_hours'),
		get_string('others','local_course_hours')=>get_string('anyothers','local_course_hours')
		);
		$mform->addElement('select', 'learnigtype', get_string('selectlearnigtypes', 'local_course_hours'), $learnigtypes);
		$mform->settype('learnigtype', PARAM_RAW);

		//Manju: Program Type - dropdown.27/11/2020.
		$programtypes = array(
		get_string('internal','local_course_hours')=>get_string('internal','local_course_hours'),
		get_string('external','local_course_hours')=>get_string('external','local_course_hours')
		);
		$mform->addElement('select', 'programtype', get_string('selectprogramtype', 'local_course_hours'), $programtypes);
		$mform->settype('programtype', PARAM_RAW);

		//Manju: Vendor name .27/11/2020.
		$mform->addElement('text','vendor',get_string('vendorname','local_course_hours'));
		$mform->settype('vendor', PARAM_RAW);

		//Manju: Whether Summative Assessment available-Dropdown.27/11/2020.
		$available = array(get_string('yes','local_course_hours')=>get_string('yes','local_course_hours'),
		get_string('no','local_course_hours')=>get_string('no','local_course_hours'));
		$mform->addElement('select', 'available', get_string('available', 'local_course_hours'), $available);
		$mform->settype('available', PARAM_RAW);



		$mform->addElement('hidden','cid');
		$mform->settype('cid', PARAM_INT);

		$mform->addElement('hidden','hid');
		$mform->settype('hid', PARAM_INT);


		//action buttons start here//
		$buttonarray = array();
		$buttonarray[] = $mform->createElement('submit','submitbutton',get_string('savebutton','local_course_hours'));
		$buttonarray[] = $mform->createElement('cancel');

		$mform->addGroup($buttonarray,'buttonarray','','',false);
		//action buttons end here//
	}

	

}

?>
