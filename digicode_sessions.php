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
 * @category    local
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once($CFG->dirroot.'/auth/digicode/auth.php');
require_once($CFG->dirroot.'/auth/digicode/digicode_session_controller.php');

$action = optional_param('what', '', PARAM_TEXT);

$url = new moodle_url('/auth/digicode/digicode_sessions.php');

$context = context_system::instance();
require_login();
require_capability('auth/digicode:managesessions', $context);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'auth_digicode'));

if (!empty($action)) {
    $controller = new \auth_digicode\session_controller();
    $controller->receive($action);
    $controller->process($action);
}

$sessions = $DB->get_records('auth_digicode', array(), 'sessiontime');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('sessions', 'auth_digicode'));

$sessiontimestr = get_string('sessiontime', 'auth_digicode');
$durationstr = get_string('duration', 'auth_digicode');
$optionsstr = get_string('options', 'auth_digicode');
$coursestr = get_string('sessiontarget', 'auth_digicode');

if (!empty($sessions)) {

    $table = new html_table();
    $table->head = array($sessiontimestr, $durationstr, $coursestr, $optionsstr, '','');
    $table->size = array('20%', '20%', '20%', '20%', '10%', '10%');
    $table->align = array('left', 'left', 'left', 'left', 'center', 'right');

    foreach ($sessions as $s) {

        $sessionend = $s->sessiontime + $s->duration * MINSECS;
        if ($sessionend < time()) {
            $table->rowclasses[] = 'passed';
        } else if (($s->sessiontime - $s->preopentime * MINSECS) < time() &&
                $s->sessiontime > time()) {
            $table->rowclasses[] = 'opening';
        } else if ($s->sessiontime > time()) {
            $table->rowclasses[] = 'future';
        } else {
            $table->rowclasses[] = 'running';
        }

        $commands = '';

        if ($s->generatecodes > 0) {
            $params = array('what' => 'generatecodes', 'sessionid' => $s->id, 'sesskey' => sesskey());
            $linkurl = new moodle_url('/auth/digicode/digicode_sessions.php', $params);
            $pix = $OUTPUT->pix_icon('run', get_string('runnow', 'auth_digicode'), 'auth_digicode');
            $commands = ' '.html_writer::link($linkurl, $pix);
        }

        $linkurl = new moodle_url('/auth/digicode/digicode_session.php', array('id' => $s->id));
        $pix = $OUTPUT->pix_icon('t/edit', get_string('edit'));
        $commands .= ' '.html_writer::link($linkurl, $pix);

        $params = array('what' => 'deletesessions', 'sessionids[]' => $s->id, 'sesskey' => sesskey());
        $linkurl = new moodle_url('/auth/digicode/digicode_sessions.php', $params);
        $pix = $OUTPUT->pix_icon('t/delete', get_string('delete'));
        $commands .= ' '.html_writer::link($linkurl, $pix);

        $options = '';
        if (!empty($s->generatecodes)) {
            $options .= $OUTPUT->pix_icon('generatecodes', get_string('generatecodes', 'auth_digicode'), 'auth_digicode');
        }

        if (!empty($s->sendcodes)) {
            $options .= ' '.$OUTPUT->pix_icon('i/email', get_string('sendcodes', 'auth_digicode'), 'core');
        }

        if (auth_plugin_digicode::session_has_valid_restriction($s)) {
            $options .= ' '.$OUTPUT->pix_icon('i/filter', get_string('hasrestrictions', 'auth_digicode'), 'core');
        }

        $statusicon = '';
        if ($s->generatecodes > 1) {
            $task = $DB->get_record('task_adhoc', array('component' => 'auth_digicode', 'id' => $s->generatecodes));
            if ($s->generatecodesprogress > 0 && $s->generatecodesprogress < 100) {
                $statusicon = $OUTPUT->pix_icon('running', get_string('cgrunning', 'auth_digicode'), 'auth_digicode');
            } else if ($s->generatecodesprogress == 100) {
                $statusicon = $OUTPUT->pix_icon('go', get_string('cgcompleted', 'auth_digicode'));
            } else {
                if ($task) {
                    $statusicon = $OUTPUT->pix_icon('i/test', get_string('cgwaiting', 'auth_digicode', userdate($task->nextruntime)));
                }
            }
            if (is_dir($CFG->dirroot.'/admin/tool/adhoc')) {
                $linkurl = new moodle_url('/admin/tool/adhoc/index.php');
                $status = html_writer::link($linkurl, $statusicon, array('target' => '_blank'));
            } else {
                $status = $statusicon;
            }
        }

        $coursename = '';
        if ($s->courseid) {
            $course = $DB->get_record('course', array('id' => $s->courseid));
            $coursename = '['.$course->shortname.'] '.$course->fullname;
            $courseurl = new moodle_url('/course/view.php', array('id' => $course->id));
            $courselink = html_writer::link($courseurl, $coursename);
        }

        $table->data[] = array(userdate($s->sessiontime), $s->duration.' '.get_string('minutes'), $courselink, $options, $status, $commands);
    }

    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nosessions', 'auth_digicode'));
}

echo '<center>';
$buttonurl = new moodle_url('/auth/digicode/digicode_session.php');
echo $OUTPUT->single_button($buttonurl, get_string('newsession', 'auth_digicode'));
echo '</center>';

echo $OUTPUT->footer();
