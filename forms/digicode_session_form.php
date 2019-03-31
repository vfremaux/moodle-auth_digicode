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

class digicode_session_form extends moodleform {

    public $editoroptions;

    public function definition() {
        global $SITE;

        $context = context_system::instance();
        $config = get_config('auth_digicode');

        $maxfiles = 99;                // TODO: add some setting.
        $maxbytes = $SITE->maxbytes; // TODO: add some setting.
        $this->editoroptions = array('trusttext' => true,
                                     'subdirs' => false,
                                     'maxfiles' => $maxfiles,
                                     'maxbytes' => $maxbytes,
                                     'context' => $context);

        $mform =& $this->_form;

        // Session id.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $label = get_string('sessiontime', 'auth_digicode');
        $startyear = date('Y', time());
        $options = array('startyear' => $startyear, 'stopyear' => $startyear + 1);
        $mform->addElement('date_time_selector', 'sessiontime', $label, $options);

        $durationoptions = array(
            '0' => new lang_string('neverexpires', 'auth_digicode'),
            '30' => new lang_string('minutes', 'auth_digicode', 30),
            '45' => new lang_string('minutes', 'auth_digicode', 45),
            '60' => new lang_string('minutes', 'auth_digicode', 60),
            '75' => new lang_string('minutes', 'auth_digicode', 75),
            '90' => new lang_string('minutes', 'auth_digicode', 90),
            '105' => new lang_string('minutes', 'auth_digicode', 105),
            '120' => new lang_string('minutes', 'auth_digicode', 120),
            '135' => new lang_string('minutes', 'auth_digicode', 135),
            '150' => new lang_string('minutes', 'auth_digicode', 150),
            '180' => new lang_string('minutes', 'auth_digicode', 180),
            '210' => new lang_string('minutes', 'auth_digicode', 210),
            '240' => new lang_string('minutes', 'auth_digicode', 240),
        );
        $label = get_string('duration', 'auth_digicode');
        $mform->addElement('select', 'duration', $label, $durationoptions);
        $mform->setDefault('duration', $config->defaultduration);

        $label = get_string('name');
        $mform->addElement('text', 'name', $label, array('size' => 100));
        $mform->setType('name', PARAM_CLEANHTML);

        $label = get_string('instructions', 'auth_digicode');
        $mform->addElement('editor', 'instructions_editor', $label, null, $this->editoroptions);
        $mform->setType('instructions_editor', PARAM_CLEANHTML);

        $label = get_string('preopentime', 'auth_digicode');
        $mform->addElement('text', 'preopentime', $label);
        $mform->setType('preopentime', PARAM_INT);

        $label = get_string('course', 'auth_digicode');
        $mform->addElement('text', 'course', $label);
        $mform->setType('course', PARAM_TEXT);

        $label = get_string('generatecodes', 'auth_digicode');
        $mform->addElement('checkbox', 'generatecodes', $label);

        $label = get_string('sendcodes', 'auth_digicode');
        $mform->addElement('checkbox', 'sendcodes', $label);

        $rtoptions = array(
            'none' => new lang_string('restrictiontype:none', 'auth_digicode'),
            'profilefield' => new lang_string('restrictiontype:profilefield', 'auth_digicode'),
            'role' => new lang_string('restrictiontype:role', 'auth_digicode'),
            'capability' => new lang_string('restrictiontype:capability', 'auth_digicode')
        );

        $label = get_string('restrictiontype', 'auth_digicode');
        $mform->addElement('select', 'restrictiontype', $label, $rtoptions);
        $mform->setDefault('restrictiontype', $config->restrictiontype);

        $rcoptions = array(
            'none' => new lang_string('restrictiontype:none', 'auth_digicode'),
            'site' => new lang_string('restrictioncontext:site', 'auth_digicode'),
            'course' => new lang_string('restrictioncontext:course', 'auth_digicode'),
            'user' => new lang_string('restrictioncontext:user', 'auth_digicode')
        );

        $label = get_string('restrictioncontextlevel', 'auth_digicode');
        $mform->addElement('select', 'restrictioncontextlevel', $label, $rcoptions);
        $mform->setDefault('restrictionlevel', $config->restrictioncontext);

        $label = get_string('restrictionid', 'auth_digicode');
        $mform->addElement('text', 'restrictionid', $label);
        $mform->setType('restrictionid', PARAM_TEXT);
        $mform->setDefault('restrictionid', $config->restrictionid);

        $label = get_string('restrictionvalue', 'auth_digicode');
        $mform->addElement('text', 'restrictionvalue', $label);
        $mform->setType('restrictionvalue', PARAM_TEXT);
        $mform->setDefault('restrictionvalue', $config->restrictionvalue);

        $this->add_action_buttons();
    }

    public function set_data($defaults) {
        $context = context_system::instance();

        $defaults = file_prepare_standard_editor($defaults, 'instructions', $this->editoroptions, $context, 'auth_digicode',
                                                 'instructions', $defaults->id);

        parent::set_data($defaults);
    }

    public function validation($data, $files = null) {
        global $DB;

        $errors = array();

        // Avoid recording a session when another session is programmed.

        $select = ' (:endtime > sessiontime + duration * 60 AND :prestart < sessiontime + duration * 60) OR ';
        $select = ' (:endtime > sessiontime - preopentime * 60 AND :prestart < sessiontime - preopentime * 60) OR ';
        $select = ' (:endtime > sessiontime + duration * 60 AND :prestart < sessiontime - preopentime * 60) ';

        $endtime = $data['sessiontime'] + $data['duration'] * MINSECS;
        $prestart = $data['sessiontime'] - $data['preopentime'] * MINSECS;
        $params = array('prestart' => $prestart, 'endtime' => $endtime);
        if ($DB->get_records_select('auth_digicode', $select, $params)) {
            $errors['sessiontime'] = get_string('othersessioncollides', 'auth_digicode');
            $errors['duration'] = get_string('othersessioncollides', 'auth_digicode');
        }

        // Check the course (must exist).
        if (!$DB->record_exists('course', array('shortname' => $data['course']))) {
            $errors['course'] = get_string('coursemustexists', 'auth_digicode');
        }

        return $errors;
    }
}