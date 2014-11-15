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

function d($var)
{
	$backtrace = debug_backtrace();
	echo '<div style="display: block; margin: 5px; padding: 5px; background-color: #fff; border: 1px solid black; z-index: 999999999; position:relative;">';
    echo '<h3 style="font-size: 14px; margin: 3px">'.$backtrace[0]['file'] .':'. $backtrace[1]['function'].':'. $backtrace[0]['line'].'</h3>';
    $backtrace = array_slice($backtrace, 1, 4);
    foreach ($backtrace as $bt) {
        if (!isset($bt['file'])) { continue; }
        echo '<div style="font-size: 12px; margin: 1px">'.$bt['file'] .':'. $bt['function'].':'. $bt['line'].'</div>';
    }
    echo '<hr />';
    echo '<pre>';
    echo var_dump($var);
    echo '</pre>';
    echo '</div>';
}
/**
 * Config changes report
 *
 * @package    report
 * @subpackage seriescompletion
 * @copyright  2009 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

ini_set('memory_limit', -1);

require(dirname(__FILE__).'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/mod/certificate/lib.php');

require_once($CFG->dirroot.'/enrol/externallib.php');


$requestedLevel = optional_param('roll_type', false, PARAM_SAFEDIR);
if ($requestedLevel) {
	\report_seriescompletion\event\report_viewed::create(array('other' => array('roll_type' => $requestedLevel)))->trigger();
	$reporter = new \report_seriescompletion\extract($requestedLevel);
	$reporter->serve();
	exit(0);
}
admin_externalpage_setup('reportseriescompletion', '', null, '', array('pagelayout'=>'report'));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'report_seriescompletion'));
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter centerpara');
echo '<form method="get" action="." id="settingsform"><div>';
echo $OUTPUT->heading(get_string('choose_rollup', 'report_seriescompletion'));
echo '<p id="intro">', get_string('intro', 'report_seriescompletion') , '</p>';
echo '<p>';
echo html_writer::select(['course' => 'Course', 'series' => 'Series'], 'roll_type', '', array());
echo '</p>';
echo '<p><input type="submit" id="settingssubmit" value="' .
        get_string('export', 'report_seriescompletion') . '" /></p>';
echo '</div></form>';
echo $OUTPUT->box_end();


echo $OUTPUT->footer();
