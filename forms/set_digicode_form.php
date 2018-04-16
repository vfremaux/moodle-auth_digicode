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
 * @package     auth_digicode
 * @categroy    local
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

class set_digicode_form extends moodleform {

    public $editoroptions;

    public function definition() {
        global $SITE;

        $context = context_system::instance();
        $config = get_config('auth_digicode');

        $mform =& $this->_form;

        // User id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $label = get_string('currentpassword');
        $mform->addElement('password', 'password', $label);
        $mform->setType('password', PARAM_TEXT);

        $label = get_string('digicode');
        $mform->addElement('passwordunmask', 'digicode', $label);
        $mform->setType('digicode', PARAM_TEXT);

        $this->add_action_buttons();
    }

    public function validation($data, $files = null) {
        global $USER;

        if (!validate_internal_user_password($USER, $data->password)) {
            return array('password', get_string('passwordfailure', 'auth_digicode'));
        }
    }
}