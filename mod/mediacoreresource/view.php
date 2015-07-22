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
 *       __  _____________   _______   __________  ____  ______
 *      /  |/  / ____/ __ \ /  _/   | / ____/ __ \/ __ \/ ____/
 *     / /|_/ / __/ / / / / / // /| |/ /   / / / / /_/ / __/
 *    / /  / / /___/ /_/ /_/ // ___ / /___/ /_/ / _, _/ /___
 *   /_/  /_/_____/_____//___/_/  |_\____/\____/_/ |_/_____/
 *
 * MediaCore mod video resource
 *
 * @package    mediacoreresource
 * @category   mod
 * @copyright  2015 MediaCore Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once realpath(dirname(__FILE__) .'/../../../moodle') . '/config.php';
defined('MOODLE_INTERNAL') || die('Invalid access');

global $CFG;
require_once 'lib.php';

$id = required_param('id', PARAM_INT);    // Course Module ID

// Retrieve module instance.
if (empty($id)) {
    print_error('invalidid', 'mediacoreresource');
    return;
}

if (!$cm = get_coursemodule_from_id('mediacoreresource', $id)) {
    //TODO i18n
    print_error('Course Module ID was incorrect');
}

if (!$course = $DB->get_record('course', array('id'=> $cm->course))) {
    //TODO i18n
    print_error('course is misconfigured');
}

if (!$mediacore = $DB->get_record('mediacoreresource', array('id'=> $cm->instance))) {
    //TODO i18n
    print_error('course module is incorrect');
}

require_course_login($course->id, true, $cm);

global $SESSION, $CFG;

$PAGE->set_url('/mod/mediacoreresource/view.php', array('id' => $id));
$PAGE->set_title(format_string($mediacore->name));
$PAGE->set_heading($course->fullname);
$pageclass = 'mediacore-resource-body';
$PAGE->add_body_class($pageclass);

$context = $PAGE->context;

add_to_log($course->id, 'mediacoreresource', 'view video resource',
    'view.php?id='.$cm->id, $mediacore->id, $cm->id
);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$renderer = $PAGE->get_renderer('mod_mediacoreresource');

echo $OUTPUT->header();

// TODO Title

// IFrame
echo $renderer->display_iframe($mediacore, $course->id);

// Description
$description = format_module_intro('mediacoreresource', $mediacore, $cm->id);
if (!empty($description)) {
    echo $OUTPUT->box_start('generalbox');
    echo $description;
    echo $OUTPUT->box_end();
}

echo $OUTPUT->footer();