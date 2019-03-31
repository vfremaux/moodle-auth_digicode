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

// Make page header and navigation.

$url = new moodle_url('/auth/digicode/set.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'auth_digicode'));
$PAGE->set_heading(get_string('setdigicode', 'auth_digicode'));

$mform = new set_digicode_form();

if ($mform->iscancelled()) {
    redirect($CFG->wwwroot);
}

if ($data = $mform->get_data()) {
    if (auth_digicode_supports_feature('digicode' => 'shadowed')) {
        // Secure digicode.
        $dg = auth_digicode_process($data->digicode, $user);
    }
    set_user_preference('digicode', $dg, $USER->id);
    redirect($CFG->wwwroot);
}

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();