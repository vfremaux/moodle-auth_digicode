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
require('../../config.php');
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/auth/digicode/forms/digicode_session_form.php');
require_once($CFG->dirroot.'/auth/digicode/digicode_session_controller.php');

// Security.

$context = context_system::instance();
require_login();
require_capability('auth/digicode:managesessions', $context);

$sessionid = optional_param('id', 0, PARAM_INT);

// Make page header and navigation.

$url = new moodle_url('/auth/digicode/digicode_session.php', array('sesisonid' => $sessionid));
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'auth_digicode'));
$PAGE->set_heading(get_string('session', 'auth_digicode'));

$mform = new digicode_session_form();

if ($sessionid) {

    $dgsession = $DB->get_record('auth_digicode', array('id' => $sessionid));

    if (!empty($dgsession->courseid)) {
        $dgsession->course = $DB->get_field('course', 'shortname', array('id' => $dgsession->courseid));
    }

    $mform->set_data($dgsession);
}

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/auth/digicode/digicode_sessions.php'));
}

if ($data = $mform->get_data()) {

    include_once($CFG->dirroot.'/auth/digicode/digicode_session_controller.php');
    $controller = new \auth_digicode\session_controller();
    $controller->receive('edit', $data, $mform);
    $controller->process('edit');

    redirect(new moodle_url('/auth/digicode/digicode_sessions.php'));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('session', 'auth_digicode'));
$mform->display();
echo $OUTPUT->footer();