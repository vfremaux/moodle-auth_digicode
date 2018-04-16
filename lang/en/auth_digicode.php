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
 * Strings for component 'auth_digicode', language 'en'.
 *
 * @package   auth_digicode
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['digicode:managesessions'] = 'Manage quick digicode access sessions';

$string['auth_digicodedescription'] = 'This method is alike manual account, but using a very simple and lightweight authenticaton check to speed up boot time.';
$string['configexpiration'] = 'Enable password expiry';
$string['configexpiration_desc'] = 'Allow passwords to expire after a specified time.';
$string['configgeneratepredelay'] = 'Generation pre-delay';
$string['configinstructions'] = 'Instructions';
$string['configinstructions_desc'] = 'Some instructions to help student in the process';
$string['configlength'] = 'Length';
$string['configlength_desc'] = 'The digicode length. Affects future generations (not currently stored digicodes).';
$string['configopentime'] = 'Open time';
$string['configopentime_desc'] = 'Open time';
$string['configrestrictioncontext'] = 'Restriction context';
$string['configrestrictioncontext_desc'] = 'The context of the restriction (where the criteria will be evaluated on)';
$string['configrestrictionid'] = 'Restriction target id';
$string['configrestrictionid_desc'] = 'the item shortname (role, capability, or profilefield name)';
$string['configrestrictiontype'] = 'Restriction type';
$string['configrestrictiontype_desc'] = 'The type of target for the restriction';
$string['configrestrictionvalue'] = 'Restriction value or filter';
$string['configrestrictionvalue_desc'] = 'A value or filter expression to restrict digicode allowed users';
$string['configencodedigicodes'] = 'Encode digicodes (Pro feature)';
$string['course'] = 'Target course';
$string['digicode'] = 'Enter your code';
$string['digicode_settings'] = 'Digicode access settings';
$string['duration'] = 'Session duration';
$string['emulatecommunity'] = 'Emulate community version';
$string['emulatecommunity_desc'] = 'If enabled, the plugin will behave as the public community version. This might loose features !';
$string['generatecodes'] = 'Generate codes for participants';
$string['hasrestrictions'] = 'The session has restrictions on users';
$string['instructions'] = 'Instructions';
$string['invalidsessions'] = 'Invalid session';
$string['licenseprovider'] = 'Pro License provider';
$string['licenseprovider_desc'] = 'Input here your provider key';
$string['licensekey'] = 'Pro license key';
$string['licensekey_desc'] = 'Input here the product license key you got from your provider';
$string['login'] = 'Log in';
$string['managesessions'] = 'Manage access sessions';
$string['minutes'] = '{$a} minutes';
$string['neverexpires'] = 'Never expires';
$string['newsession'] = 'New session';
$string['nosessions'] = 'No sessions';
$string['options'] = 'Options';
$string['othersessioncollides'] = 'Another session is colliding';
$string['otherusers'] = 'Other users';
$string['passwordfailure'] = 'Invalid password given';
$string['plugindist'] = 'Plugin distribution';
$string['pluginname'] = 'Digicode accounts';
$string['preopentime'] = 'Presentation pre-delay';
$string['profiling'] = 'Profiling';
$string['restrictioncontextlevel'] = 'Restriction context level';
$string['restrictionid'] = 'Restriction object identifier';
$string['restrictiontype'] = 'Restriction type';
$string['restrictionvalue'] = 'Restriction value or filter';
$string['runnow'] = 'Run code generator now !';
$string['cgrunning'] = 'Code generator is running';
$string['cgcompleted'] = 'Code generation is complete';
$string['cgwaiting'] = 'Code generation is waiting for starting time on {$a}.';
$string['sendcodes'] = 'Send codes to participants';
$string['session'] = 'Digicode access session';
$string['sessions'] = 'Digicode access sessions';
$string['sessiontime'] = 'Session start';
$string['sessiontarget'] = 'Target course';
$string['setdigicode'] = 'Set my digicode';

$string['restrictiontype:none'] = 'No restriction';
$string['restrictiontype:profilefield'] = 'User profile field';
$string['restrictiontype:role'] = 'Role in context';
$string['restrictiontype:capability'] = 'Capability on context';

$string['restrictioncontext:user'] = 'User';
$string['restrictioncontext:site'] = 'Site Course';
$string['restrictioncontext:system'] = 'System';
$string['restrictioncontext:course'] = 'Course (target course)';

$string['configgeneratepredelay_desc'] = 'The backwards delay (in hours) the digicode generation task will be launched before an access session.';

$string['newdigicode_subject'] = 'Your access digicode has changed on {$a}';
$string['newdigicode_tpl'] = '
Site: <%%SITE%%>
--------------------------------------

<%%USERNAME%%>,

Your personal digicode has changed to <%%DG%%>

You may use it for the next QuickAccess sessions on this site.
Please record it in a safe place and keep it personal.

';

$string['newdigicode_html_tpl'] = '
<b>Site:</b> <%%SITE%%>
<hr>

<p><%%USERNAME%%>,</p>

<p>Your personal digicode has changed to <b><%%DG%%></b></p>

<p>You may use it for the next QuickAccess sessions on this site.
Please record it in a safe place and keep it personal.</p>

';

$string['plugindist_desc'] = '
<p>This plugin is the community version and is published for anyone to use as is and check the plugin\'s
core application. A "pro" version of this plugin exists and is distributed under conditions to feed the life cycle, upgrade, documentation
and improvement effort.</p>
<p>Please contact one of our distributors to get "Pro" version support.</p>
<p><a href="http://www.mylearningfactory.com/index.php/documentation/Distributeurs?lang=en_utf8">MyLF Distributors</a></p>';

$string['configencodedigicodes_desc'] = 'If enabled, encodes dificodes in data base for avoiding trivial spoofing. This
method is slight, less robust, but very quick and sufficiant for trivial protection.';
