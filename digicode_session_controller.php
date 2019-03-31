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
 * Controller for digicode sessions.
 *
 * @package     auth_digicode
 * @category    local
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright   Valery Fremaux <valery.fremaux@gmail.com> (MyLearningFactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace auth_digicode;
require_once($CFG->dirroot.'/auth/digicode/classes/task/generatecodes_task.php');

defined('MOODLE_INTERNAL') || die();

class session_controller {

    protected $data;

    protected $received;

    protected $mform;

    public function receive($cmd, $data = array(), $mform = null) {

        $this->mform = $mform;

        if (!empty($data)) {
            $this->data = (object)$data;
            $this->received = true;
            return;
        } else {
            $this->data = new \StdClass;
        }

        switch ($cmd) {
            case 'deletesessions': {
                $this->data->sessionids = required_param_array('sessionids', PARAM_INT);
                break;
            }

            case 'generatecodes': {
                $this->data->sessionid = required_param('sessionid', PARAM_INT);
                break;
            }

            case 'edit': {
                // Get all data from $data attribute.
                break;
            }
        }

        $this->received = true;
    }

    public function process($cmd) {
        global $DB;

        $context = \context_system::instance();
        $config = get_config('auth_digicode');

        if (!$this->received) {
            throw new \coding_exception('Data must be received in controller before operation. this is a programming error.');
        }

        if ($cmd == 'deletesessions') {
            $this->data->sessionids;

            $DB->delete_records_list('auth_digicode', 'id', $this->data->sessionids);

            redirect(new \moodle_url('/auth/digicode/digicode_sessions.php'));
        }

        if ($cmd == 'generatecodes') {
            $this->data->sessionid;

            $dgsession = $DB->get_record('auth_digicode', array('id' => $this->data->sessionid));

            // Build an adhoc task and register it for now.
            $task = new task\generatecodes_task();
            $task->set_custom_data($dgsession);
            $task->id = \core\task\manager::queue_adhoc_task($task);
            $DB->set_field('auth_digicode', 'generatecodes', $task->id, array('id' => $this->data->sessionid));
            $DB->set_field('auth_digicode', 'generatecodesprogress', 0, array('id' => $this->data->sessionid));

            redirect(new \moodle_url('/auth/digicode/digicode_sessions.php'));
        }

        if ($cmd == 'edit') {
            $dgsession = $this->data;

            $dgsession->instructionsformat = $dgsession->instructions_editor['format'];
            $dgsession->instructions = $dgsession->instructions_editor['text'];

            $dgsession->courseid = $DB->get_field('course', 'id', array('shortname' => $this->data->course));
            unset($dgsession->course);
            unset($dgsession->submitbutton);

            if (empty($dgsession->instructionsformat)) {
                $dgsession->instructionsformat = FORMAT_MOODLE;
            }

            if (!empty($dgsession->id) && $oldrec = $DB->get_record('auth_digicode', array('id' => $dgsession->id))) {

                $DB->update_record('auth_digicode', $dgsession);
                $this->update_generation($dgsession);
            } else {
                $dgsession->id = $DB->insert_record('auth_digicode', $dgsession);

                if ($dgsession->generatecodes) {
                    $task = new task\generatecodes_task($dgsession->sessiontime - $config->generatecodespredelay * HOURSECS);
                    $task->set_custom_data($dgsession);
                    $task->id = \core\task\manager::queue_adhoc_task($task);
                    $DB->set_field('auth_digicode', 'generatecodes', $task->id, array('id' => $dgsession->id));
                    $DB->set_field('auth_digicode', 'generatecodesprogress', 0, array('id' => $dgsession->id));
                }
            }

            if ($this->mform) {
                // When playing tests we do not have form.

                $draftideditor = file_get_submitted_draft_itemid('instructions_editor');
                $dgsession->instructions = file_save_draft_area_files($draftideditor, $context->id, 'auth_digicode', 'instructions',
                                                                $dgsession->id, array('subdirs' => true), $dgsession->instructions);
                $dgsession = file_postupdate_standard_editor($dgsession, 'instructions', $this->mform->editoroptions, $context, 'auth_digicode',
                                                        'instructions', $dgsession->id);

                $DB->update_record('auth_digicode', $dgsession);
            }
        }
    }

    public static function info() {
        return array('deletesessions' => array('sessionids' => 'List of IDs of sessions to delete'),
                     'edit' => array(
                        'sessionid' => 'Numeric ID for update',
                        /* to finbish */
                     ));
    }

    protected function update_generation($dgsession) {
        global $DB;

        $config = get_config('auth_digicode');

        // Update or create generate ad hoc task.
        if ($dgsession->generatecodes) {
            $task = $DB->get_record('task_adhoc', array('id' => $dgsession->generatecodes));
            if (!$task) {
                // Register new task if not yet registered.
                $task = new task\generatecodes_task($dgsession->sessiontime - $config->generatecodespredelay * HOURSECS);
                $task->set_custom_data($dgsession);
                $task->id = \core\task\manager::queue_adhoc_task($task);
                $DB->set_field('auth_digicode', 'generatecodes', $task->id, array('id' => $dgsession->id));
            } else {
                // Reajust launchtime if something was changed in the meanwhile.
                $task->nextruntime = $sgsession->sessiontime - $config->generatecodespredelay * HOURSECS;
                $DB->update_record('task_adhoc', $task);
            }
        }
    }
}