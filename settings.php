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
 * Admin settings and defaults
 *
 * @package auth_manual
 * @copyright  2017 Stephen Bourget
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->dirroot.'/auth/digicode/lib.php');

$label = get_string('managesessions', 'auth_digicode');
$pageurl = new moodle_url('/auth/digicode/digicode_sessions.php');
$settingspage = new admin_externalpage('digicode', $label , $pageurl, 'auth/digicode:managesessions');
$ADMIN->add('users', $settingspage);

if ($ADMIN->fulltree) {

    // Introductory explanation.
    $key = 'auth_digicode/pluginname';
    $header = get_string('digicode_settings', 'auth_digicode');
    $desc = get_string('auth_digicodedescription', 'auth_digicode');
    $settings->add(new admin_setting_heading($key, $header, $desc));

    $lengthoptions = array(
        4 => 4,
        5 => 5,
        6 => 6,
    );

    $key = 'auth_digicode/length';
    $label = get_string('configlength', 'auth_digicode');
    $desc = get_string('configlength_desc', 'auth_digicode');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 4, $lengthoptions));

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

    $key = 'auth_digicode/defaultduration';
    $label = get_string('configexpiration', 'auth_digicode');
    $desc = get_string('configexpiration_desc', 'auth_digicode');
    $default = '60';
    $settings->add(new admin_setting_configselect($key, $label, $desc, $default, $durationoptions));

    $key = 'auth_digicode/generatecodespredelay';
    $label = get_string('configgeneratepredelay', 'auth_digicode');
    $desc = get_string('configgeneratepredelay_desc', 'auth_digicode');
    $default = '72';
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));

    $key = 'auth_digicode/instructions';
    $label = get_string('configinstructions', 'auth_digicode');
    $desc = get_string('configinstructions_desc', 'auth_digicode');
    $settings->add(new admin_setting_configtextarea($key, $label, $desc, ''));

    $rtoptions = array(
        'none' => new lang_string('restrictiontype:none', 'auth_digicode'),
        'profilefield' => new lang_string('restrictiontype:profilefield', 'auth_digicode'),
        'role' => new lang_string('restrictiontype:role', 'auth_digicode'),
        'capability' => new lang_string('restrictiontype:capability', 'auth_digicode')
    );

    $key = 'auth_digicode/restrictiontype';
    $label = get_string('configrestrictiontype', 'auth_digicode');
    $desc = get_string('configrestrictiontype_desc', 'auth_digicode');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'role', $rtoptions));

    $rcoptions = array(
        'none' => new lang_string('restrictiontype:none', 'auth_digicode'),
        'system' => new lang_string('restrictioncontext:system', 'auth_digicode'),
        'site' => new lang_string('restrictioncontext:site', 'auth_digicode'),
        'course' => new lang_string('restrictioncontext:course', 'auth_digicode'),
        'user' => new lang_string('restrictioncontext:user', 'auth_digicode')
    );

    $key = 'auth_digicode/restrictioncontext';
    $label = get_string('configrestrictioncontext', 'auth_digicode');
    $desc = get_string('configrestrictioncontext_desc', 'auth_digicode');
    $settings->add(new admin_setting_configselect($key, $label, $desc, 'course', $rcoptions));

    $key = 'auth_digicode/restrictionid';
    $label = get_string('configrestrictionid', 'auth_digicode');
    $desc = get_string('configrestrictionid_desc', 'auth_digicode');
    $settings->add(new admin_setting_configtext($key, $label, $desc, ''));

    $key = 'auth_digicode/restrictionvalue';
    $label = get_string('configrestrictionvalue', 'auth_digicode');
    $desc = get_string('configrestrictionvalue_desc', 'auth_digicode');
    $settings->add(new admin_setting_configtext($key, $label, $desc, 'student'));

    if (auth_digicode_supports_feature('emulate/community')) {
        // This will accept any.
        include_once($CFG->dirroot.'/auth/digicode/pro/prolib.php');
        \auth_digicode\pro_manager::add_settings($ADMIN, $settings);
    } else {
        $label = get_string('plugindist', 'auth_digicode');
        $desc = get_string('plugindist_desc', 'auth_digicode');
        $settings->add(new admin_setting_heading('plugindisthdr', $label, $desc));
    }
}
