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
namespace auth_digicode\task;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/auth/digicode/auth.php');

class generatecodes_task extends \core\task\adhoc_task {

    public $nextruntime;

    public function __construct($nextruntime = 0) {

        if ($nextruntime < time()) {
            // Run it now (in one minute) if in the past.
            $this->nextruntime = time() + 60;
        }

        $this->nextruntime = $nextruntime;
    }

    /**
     * change all digicodes for the targeted population.
     * All task required information is in customdata stub.
     */
    public function execute() {
        global $DB, $SITE, $CFG;

        $customdata = $this->get_custom_data();

        if (auth_digicode_supports_feature('digicode/shadowed')) {
            include_once($CFG->dirroot.'/auth/digicode/pro/lib.php');
        }

        $config = get_config('auth_digicode');

        $targetusers = \auth_plugin_digicode::get_target_users($customdata);
        $targetsize = count($targetusers);

        if (!empty($targetusers)) {
            $usercounter = 0;
            foreach ($targetusers as $tg) {
                $dg = $this->generate($config->length);

                if (auth_digicode_supports_feature('digicode/shadowed')) {
                    // Secure digicode.
                    $dg = auth_digicode_process($dg, $tg);
                }

                set_user_preference('digicode', $dg, $tg);

                $attmap['DG'] = $dg;
                $attmap['USERNAME'] = fullname($tg);
                $attmap['SITE'] = $SITE->fullname;
                if ($customdata->sendcodes) {
                    $subject = get_string('newdigicode_subject', $SITE->shortname);
                    $notification = auth_digicode_compile_mail_template('newdigicode', $attmap);
                    $notificationhtml = auth_digicode_compile_mail_template('newdigicode_html', $attmap);
                    email_to_user($tg, null, $subject, $notification, $notificationhtml);
                }
                $usercounter++;
                $progress = round($usercounter / $targetsize * 100);
                $params = array('id' => $customdata->id);
                $DB->set_field('auth_digicode', 'generatecodesprogress', $progress, $params);
            }
        } else {
            mtrace('No users. Nothing done.');
        }
    }

    /**
     * Generates a randomized digicode of some length.
     */
    protected function generate($length = 4) {

        $range = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        shuffle($range);
        $dg = '';
        for ($i = 0; $i < $length; $i++) {
            $dg .= $range[$i];
        }

        return $dg;
    }

    function get_component() {
        return 'auth_digicode';
    }

    function get_next_run_time() {
        return $this->nextruntime;
    }
}
