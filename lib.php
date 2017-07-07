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
 * Libraries of the accessibility report.
 *
 * @package    report_accessibility
 * @copyright  2016 Nadav Kavalerchik <nadavkav@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * This function extends the course navigation
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the tool
 * @param context $coursecontext The context of the course
 */
function report_accessibility_extend_navigation_course($navigation, $course, $coursecontext) {
    if (!has_capability('report/accessibility:view', $coursecontext)) {
        return;
    }
    $url = new moodle_url('/report/accessibility/index.php', array('courseid' => $course->id, 'pagecontextid' => $coursecontext->id));
    $name = get_string('pluginname', 'report_accessibility');
    $settingsnode = navigation_node::create($name,
        $url,
        navigation_node::TYPE_SETTING,
        null,
        null,
        new pix_icon('i/report', ''));
    if (isset($settingsnode)) {
        $navigation->add_node($settingsnode);
    }
}


/**
 * This function extends the navigation with the report items
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $coursecategorycontext The context of the course category
 */
function report_accessibility_extend_navigation_category($navigation, $coursecategorycontext) {
    if (!has_capability('report/accessibility:view', $coursecategorycontext)) {
        return;
    }
    $url = new moodle_url('/report/accessibility/index.php', array('pagecontextid' => $coursecategorycontext->id));
    $name = get_string('pluginname', 'report_accessibility');
    $settingsnode = navigation_node::create($name,
                                            $url,
                                            navigation_node::TYPE_SETTING,
                                            null,
                                            null,
                                            new pix_icon('i/report', ''));
    if (isset($settingsnode)) {
        $navigation->add_node($settingsnode);
    }
}
