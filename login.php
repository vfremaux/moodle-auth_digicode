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
 *
 * @package    auth_digicode
 * @author     Valery Fremaux <valery.fremaux@club-internet.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/login/lib.php');

if (isloggedin()) {
    $urltogo = core_login_get_return_url();
    redirect($urltogo);
}

// Protect door from session faking.
$sessionid = required_param('sessionid', PARAM_INT);
require_sesskey();

$plugin = get_auth_plugin('digicode');
$validsession = $plugin->has_valid_session();
if ($validsession->id != $sessionid) {
    // Session id has been faked ?
    print_error(get_string('invalidsession', 'auth_digicode'));
}

$url = new moodle_url('/auth/digicode/login.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->requires->js_call_amd('auth_digicode/digicode', 'init', array(time() - $validsession->sessiontime));
$PAGE->requires->jquery();

$renderer = $PAGE->get_renderer('auth_digicode');

echo $OUTPUT->header();

echo $renderer->login_form($validsession);

echo $OUTPUT->footer();