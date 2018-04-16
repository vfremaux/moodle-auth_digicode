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
 * Authentication Plugin: Manual Authentication
 * Just does a simple check against the moodle database.
 *
 * @package    auth_manual
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/auth/digicode/lib.php');

/**
 * Manual authentication plugin.
 *
 * @package    auth
 * @subpackage digicode
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_plugin_digicode extends auth_plugin_base {

    /**
     * The name of the component. Used by the configuration.
     */
    const COMPONENT_NAME = 'auth_digicode';
    const LEGACY_COMPONENT_NAME = 'auth/digicode';

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'digicode';
        $config = get_config(self::COMPONENT_NAME);
        $legacyconfig = get_config(self::LEGACY_COMPONENT_NAME);
        $this->config = (object)array_merge((array)$legacyconfig, (array)$config);
    }

    /**
     * Authentication choice (CAS or other)
     * Redirection to the digicode form or to login/index.php
     * for other authentication
     */
    function loginpage_hook() {
        global $frm;
        global $CFG;
        global $SESSION, $OUTPUT, $PAGE;

        if (isloggedin()) {
            if (!empty($SESSION->wantsurl)) {
                redirect($SESSION->wantsurl);
            }
            redirect($CFG->wwwroot);
        }

        // Return if digicode auth is not enabled.
        if (empty($this->config->enabled)) {
            return;
        }

        // Return if no session running or in prerun.
        if (!$activesession = $this->has_valid_session()) {
            return;
        }

        $site = get_site();

        $digicodethrough = optional_param('authDC', 'DC', PARAM_TEXT);
        if ($digicodethrough != 'DC') {
            return;
        }

        $username = optional_param('username', false, PARAM_TEXT);
        $password = optional_param('password', false, PARAM_TEXT);
        $password = preg_replace('/[^0-9]/', '', $password); // Trim out all non strictly digits.

        if (!empty($username)) {
            if ($this->user_login($username, $password)) {
                // Give back to the real authentifcation process. come back to user_login.
                redirect($CFG->wwwroot);
            }
        }

        // If digicode is enabled, we divert to a special form with digicode input.
        $digicodeformurl = new moodle_url('/auth/digicode/login.php', array('sessionid' => $activesession->id, 'sesskey' => sesskey()));
        redirect($digicodeformurl);
    }


    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_digicode() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Returns true if the username and password work and false if they are
     * wrong or don't exist. (Non-mnet accounts only!)
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    function user_login($username, $password) {
        global $CFG, $DB, $user, $USER;

        if (!$user = $DB->get_record('user', array('username' => $username, 'mnethostid' => $CFG->mnet_localhost_id))) {
            return false;
        }
        if (!$this->check_digicode($user, $password)) {
            return false;
        }

        $USER = $user;
        return true;
    }

    /**
     * Updates the user's digicode.
     *
     * Called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     */
    function user_update_digicode($user, $newdigicode) {
        global $DB;

        $params = array('userid' => $userid, 'name' => 'digicode');
        if ($oldrec = $DB->get_record('user_preferences', $params));
            $DB->set_field('user_preferences', 'value', $newdigicode, $params);
    }

    public function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    public function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    public function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    public function can_be_manually_set() {
        return true;
    }

    // Very simple check, as short as possible.
    public function check_digicode(&$user, $digicode) {
        global $DB, $CFG;

        if (auth_digicode_supports_feature('digicode/shadowed')) {
            include_once($CFG->dirroot.'/auth/digicode/pro/lib.php');
            auth_digicode_check($dg, $user);
        } else {
            $params = array('userid' => $user->id, 'name' => 'digicode', 'value' => $digicode);
            return $DB->record_exists('user_preferences', $params);
        }
    }

    public function has_valid_session() {
        global $DB;

        $time = time();

        $select = '
            sessiontime - (preopentime * 60) <= ? AND
            ? <= sessiontime + duration * 60
        ';

        $activesession = $DB->get_record_select('auth_digicode', $select, array($time, $time));

        if ($activesession) {
            $activesession->is_running = $time >= $activesession->sessiontime;
        }

        return $activesession;
    }

    /**
     * TODO : refine conditions.
     */
    public static function session_has_valid_restriction($session) {

        if (!empty($session->restrictiontype) && !empty($session->restrictionvalue)) {
            return true;
        }
        return false;
    }

    /**
     * Get the target users
     */
    public static function get_target_users($taskconfig, $limit = 0, $offset = 0) {
        global $DB;

        $targetusers = array();

        if (!empty($taskconfig->restrictiontype)) {
            switch ($taskconfig->restrictiontype) {

                case 'role': {
                    if ($taskconfig->restrictioncontextlevel == 'system') {
                        $context = context_system::instance();
                    } else if ($taskconfig->restrictioncontextlevel == 'site') {
                        $context = context_course::instance(SITEID);
                    } else if ($taskconfig->restrictioncontextlevel == 'course') {
                        $context = context_course::instance($taskconfig->courseid);
                    } else {
                        return;
                    }

                    $role = $DB->get_record('role', array('shortname' => $taskconfig->restrictionvalue));
                    if ($ras = get_users_from_role_on_context($role, $context)) {
                        foreach ($ras as $ra) {
                            // singlify result.
                            $userids[$ra->userid] = $ra->userid;
                        }

                        // Unify results with other methods.
                        $fields = 'id, '.get_all_user_name_fields(true, '');
                        foreach (array_keys($userids) as $uid) {
                            $targetusers[$uid] = $DB->get_record('user', array('id' => $uid), $fields);
                        }
                    }

                    break;
                }

                case 'capability': {

                    if ($taskconfig->restrictioncontextlevel == 'system') {
                        $context = context_system::instance();
                    } else if ($taskconfig->restrictioncontextlevel == 'site') {
                        $context = context_course::instance(SITEID);
                    } else if ($taskconfig->restrictioncontextlevel == 'course') {
                        $context = context_course::instance($customdata->courseid);
                    }

                    $fields = 'u.id, '.get_all_user_name_fields(true, 'u');
                    $targetusers = get_users_by_capability($context, $taskconfig->restrictionvalue, $fields);

                    break;
                }

                case 'profilefield': {
                    if (strpos($taskconfig->restrictionid, 'user:') === 0) {
                        // By standard profile field.
                        $fieldname = str_replace('user:', '', $taskconfig->restrictionid);
                        $select = $fieldname.' LIKE ?';
                        $fields = 'id, '.get_all_user_name_fields(true, '');
                        $targetusers = $DB->get_records_select('user', $select, array($taskconfig->restrictionvalue), $fields);
                    } else if (strpos($taskconfig->restrictionid, 'profile_field:') === 0) {
                        // By custom profile field.
                        $fieldname = str_replace('profile_field:', '', $taskconfig->restrictionid);
                        $field = $DB->get_record('user_info_field', array('shortname' => $fieldname));
                        $fields = 'u.id, '.get_all_user_name_fields(true, 'u');
                        $sql = "
                            SELECT
                                {$fields}
                            FROM
                                {user_info_data} uid,
                                {user} u
                            WHERE
                                uid.userid = u.id AND
                                uid.fieldid = ?
                                data LIKE ?
                        ";
                        $params = array($field->id, $taskconfig->restrictionvalue);
                        $targetusers = $DB->get_records_sql($sql, $params);
                    }
                    break;
                }

                default:
                    // Might be huge !
                    return $DB->get_records('user', array(), 'id', $fields, $limit, $offset);
            }
        }

        return $targetusers;
    }

    public static function delete_user_info($userorid) {
        global $DB;

        if (is_numeric($userorid)) {
            $userid = $userorid;
        } else {
            $userid = $userorid->id;
        }

        $params = array('userid' => $userid, 'name' => 'digicode');
        $DB->delete_records('user_preferences', $params);
    }

    public static function get_user_info($userorid) {
        global $DB;

        if (is_numeric($userorid)) {
            $userid = $userorid;
        } else {
            $userid = $userorid->id;
        }

        $params = array('userid' => $userid, 'name' => 'digicode');
        if ($digicode = $DB->get_record('user_preferences', $params)) {
            return array('digicode' => $digicode->value);
        }
        return null;
    }
}


