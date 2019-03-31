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
 * Javascript for the digicode interface.
 *
 * @module     auth_digicode/digicode
 * @package    auth_digicode
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// jshint unused: true, undef:true
define(['jquery', 'core/log'], function($, log) {

    var chronotime;
    var chronodir;

    var digicode = {
        init: function(args) {
            $('.digicode-digit').bind('mouseover', this.highlight_digit);
            $('#digicode-panel').bind('mouseout', this.lightoff_digits);
            $('.digicode-digit').bind('mousedown', this.add_digit);
            $('.digicode-digit').bind('mouseup', this.release_digits);
            $('#digicode-input').val(''); // Ensure it is empty at start.

            if (args > 0) {
                window.chronotime = args;
                window.chronodir = 1;
                $('#chrono-display').addClass('chrono-countup');
            } else {
                window.chronotime = -args;
                window.chronodir = -1;
                $('#chrono-display').addClass('chrono-down');
            }

            setInterval(this.chronotimer, 1000);

            log.debug("ADM Digicode initialized");
        },

        add_digit: function() {
            that = $(this);

            var value = $('#digicode-input').val();
            var shadow = $('#digicode-shadow').html();
            value += that.html();
            shadow += '*';
            $('#digicode-input').val(value);
            $('#digicode-shadow').html(shadow);
            that.addClass('active');
        },

        highlight_digit: function() {
            that = $(this);

            digicode.lightoff_digits();
            that.addClass('highlighted');
        },

        lightoff_digits: function() {
            $('.digicode-digit').removeClass('highlighted');
        },

        release_digits: function() {
            $('.digicode-digit').removeClass('active');
        },

        chronotimer: function() {
            chronotime += window.chronodir;

            h = Math.round(chronotime / 3600);
            rawm = chronotime - h * 3600;
            m = Math.round(rawm / 60);
            s = rawm - m * 60;

            if (h < 10) {
                ht = '0' + h;
            } else {
                ht = h;
            }

            if (m < 10){
                 mt = '0' + m;
            } else {
               mt = m;
            }

            if (s < 10){
                st = '0' + s;
            } else {
                st = s;
            }

            $('#chrono-display').html(ht + ':' + mt + ':' + st);

        },

    };

    return digicode;
});
