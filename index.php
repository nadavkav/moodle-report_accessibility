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
 * accessibility report page.
 *
 * @package    report_accessibility
 * @copyright  2016 Nadav Kavalerchik <nadavkav@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$pagecontextid = required_param('pagecontextid', PARAM_INT);
$context = context::instance_by_id($pagecontextid);

$courseid = optional_param('courseid', 1, PARAM_INT);

require_login();
$urlparams = array('pagecontextid' => $pagecontextid, 'courseid' => $courseid);

$url = new moodle_url('/report/accessibility/index.php', $urlparams);
$title = get_string('pluginname', 'report_accessibility');

//$PAGE->set_context($context);
//$PAGE->set_url($url);
//$PAGE->set_title($title);
//$PAGE->set_heading('accessibility report');
//$PAGE->set_pagelayout('course');

//$output = $PAGE->get_renderer('report_accessibility');

//echo $output->header();
//echo $output->heading($title);

echo '<html  dir="ltr" lang="en" xml:lang="en">';
echo '<head><title>Accessibility report for courseid = '.$courseid.'</title></head>';

global $DB;
$sections = $DB->get_records('course_sections', array('course' => $courseid));

echo html_writer::start_div('course-summaries', array('id' => 'course-summaries-id'));
foreach ($sections as $section) {
    echo html_writer::div("<h2>Section {$section->section}:</h2>");
    echo html_writer::div($section->summary, 'course-summary', array('id' => 'course-summary-id-'.$section->id));
    $labels = $DB->get_records_sql('SELECT * FROM mdl_label WHERE id IN (
                                      SELECT cm.instance FROM mdl_course_modules cm
                                      JOIN mdl_modules AS m ON m.id = cm.module
                                      WHERE m.name = "label" AND cm.course = ? AND cm.section = ?)',
                                    array($courseid, $section->id));
    foreach ($labels as $label) {
        echo html_writer::div(format_text($label->intro), 'section-label', array('id' => 'section-label-id-'.$label->id));
        echo html_writer::empty_tag('br');
    }
    echo html_writer::empty_tag('hr');
}
echo html_writer::end_div();

echo html_writer::start_div('axe-results', array('id' => 'axe-results-id'));
echo html_writer::end_div();
echo html_writer::empty_tag('br');
echo html_writer::start_div('axe-results', array('id' => 'axe-results-verbose-id'));
echo html_writer::end_div();

//echo html_writer::nonempty_tag('script', ' ', array('src' => '../node_modules/axe-core/axe.min.js'));
//echo html_writer::nonempty_tag('script', ' ', array('src' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/core.js'));
echo html_writer::nonempty_tag('script', ' ', array('src' => '//ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js'));
echo html_writer::nonempty_tag('script', ' ', array('src' => '//cdnjs.cloudflare.com/ajax/libs/jquery-scrollTo/2.1.0/jquery.scrollTo.min.js'));
//echo html_writer::nonempty_tag('script', ' ', array('src' => 'js/jquery.scrollintoview.min.js'));
echo html_writer::nonempty_tag('script', ' ', array('src' => '//cdnjs.cloudflare.com/ajax/libs/axe-core/2.0.7/axe.min.js'));
echo html_writer::nonempty_tag('script', ' ', array('src' => '//cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.5/handlebars.js'));
//echo html_writer::nonempty_tag('script',
?>
<script>
    axe.a11yCheck(document.getElementById('#course-summaries-id'), function (results) {
        //ok(results.violations.length === 0, 'Should be no accessibility issues');

        if (results.violations.length) {
            var violations = results.violations.map(function (rule, i) {
                return {
                    impact: rule.impact,
                    help: rule.help.replace(/</gi, '&lt;').replace(/>/gi, '&gt;'),
                    bestpractice: (rule.tags.indexOf('best-practice') !== -1),
                    helpUrl: rule.helpUrl,
                    count: rule.nodes.length,
                    index: i
                };
            });

            html = compiledTableTemplate({violationList: violations});
            var display = document.getElementById('axe-results-id');
            display.innerHTML = html;
        }

        // complete the async call
        console.log(results);

        var displayviolations = document.getElementById('axe-results-verbose-id');
        var displayviolations_html = '<h3>Violation list</h3>';
        displayviolations.innerHTML = displayviolations_html;
        results.violations.map( function(violation) {
            violation.nodes.map( function(node) {
                displayviolations_html += summary(node);
            });
        });
        displayviolations.innerHTML = displayviolations_html;
    });

</script>

<script id="rowTemplate" type="text/x-handlebars-template">
    <tr>
        <th scope="row" class="help">
            <a onclick="summary(results.violations[{{index}}].node[0]));" href="javascript:;" class="rule" data-index="{{index}}">
                {{{help}}}
            </a>
        </th>
        <td scope="row">
            <a target="_blank" href="{{helpUrl}}">?</a>
        </td>
        <td class="count">
            {{count}}
        </td>
        <td class="impact">
            {{impact}}
        </td>
    </tr>
</script>
<script id="tableTemplate" type="text/x-handlebars-template">
    <table>
        <tr>
            <th scope="col">Description</th>
            <th scope="col">Info</th>
            <th scope="col">Count</th>
            <th scope="col">Impact</th>
        </tr>
        {{#violations violationList}}{{/violations}}
    </table>
</script>
<script id="relatedListTemplate" type="text/x-handlebars-template">
    <ul>Related Nodes:
        {{#related relatedNodeList}}{{/related}}
    </ul>
</script>
<script id="relatedNodeTemplate" type="text/x-handlebars-template">
    <li>
        <a href="javascript:;" class="related-node" data-element="{{targetArrayString}}">
            {{targetString}}
        </a>
    </li>
</script>
<script id="reasonsTemplate" type="text/x-handlebars-template">
    <p class="summary">
    <ul class="failure-message">
        {{#reasons reasonsList}}{{/reasons}}
    </ul>
    </p>
</script>
<script id="failureTemplate" type="text/x-handlebars-template">
    <li>
        {{message}}
        {{{relatedNodesMessage}}}
    </li>
</script>

<script>

    function helperItemIterator(items, template) {
        var out = '';
        if (items) {
            for (var i = 0; i < items.length; i++) {
                out += template(items[i]);
            }
        }
        return out;
    }
    Handlebars.registerHelper('violations', function(items) {
        return helperItemIterator(items, compiledRowTemplate);
    });
    Handlebars.registerHelper('related', function(items) {
        return helperItemIterator(items, compiledRelatedNodeTemplate);
    });
    Handlebars.registerHelper('reasons', function(items) {
        return helperItemIterator(items, compiledFailureTemplate);
    });

    // Setup handlebars templates

    compiledRowTemplate = Handlebars.compile(rowTemplate.innerHTML);
    compiledTableTemplate = Handlebars.compile(tableTemplate.innerHTML);
    compiledRelatedListTemplate = Handlebars.compile(relatedListTemplate.innerHTML);
    compiledRelatedNodeTemplate = Handlebars.compile(relatedNodeTemplate.innerHTML);
    compiledFailureTemplate = Handlebars.compile(failureTemplate.innerHTML);
    compiledReasonsTemplate = Handlebars.compile(reasonsTemplate.innerHTML);

    function messageFromRelatedNodes(relatedNodes) {
        var retVal = '';
        if (relatedNodes.length) {
            var list = relatedNodes.map(function (node) {
                return {
                    targetArrayString: JSON.stringify(node.target),
                    targetString: node.target.join(' ')
                };
            });
            retVal += compiledRelatedListTemplate({relatedNodeList: list});
        }
        return retVal;
    }

    function messagesFromArray(nodes) {
        var list = nodes.map(function (failure) {
            return {
                message: failure.message.replace(/</gi, '&lt;').replace(/>/gi, '&gt;'),
                relatedNodesMessage: messageFromRelatedNodes(failure.relatedNodes)
            }
        });
        return compiledReasonsTemplate({reasonsList: list});
    }

    function summary(node) {
        var retVal = '';
        if (node.any.length) {
            retVal += '<h3 class="error-title">Fix any of the following - <span class="'+node.impact+'">' + node.impact + '</span></h3>';
            retVal += messagesFromArray(node.any);
            retVal += '<button onclick="showIt(\''+node.target[0]+'\')">Show</button>';
        }

        var all = node.all.concat(node.none);
        if (all.length) {
            retVal += '<h3 class="error-title">Fix all of the following - ' + node.impact + '</h3>';
            retVal += messagesFromArray(all);
        }
        return retVal;
    }

    function showIt(elID) {
        var target = $('body');
        target.scrollTo($(elID) , 800);

        //$(elID).scrollintoview({ duration: "slow", direction: "y", complete: function(){ alert("Done"); } });

        //var el = document.getElementById(elID);
        //el.scrollIntoView(true);
    }
    /*
     * This code will generate a table of the rules that failed including counts and links to the Deque University help
     * for each rule.
     *
     * When used, you should attach click handlers to the anchors in order to generate the details for each of the
     * violations for each rule.
     */
    /*
     if (results.violations.length) {
     var violations = results.violations.map(function (rule, i) {
     return {
     impact: rule.impact,
     help: rule.help.replace(/</gi, '&lt;').replace(/>/gi, '&gt;'),
     bestpractice: (rule.tags.indexOf('best-practice') !== -1),
     helpUrl: rule.helpUrl,
     count: rule.nodes.length,
     index: i
     };
     });

     html = compiledTableTemplate({violationList: violations});
     }
     */
    /*
     * To generate the human readable summary, call the `summary` function with the node. This will return HTML for that node.
     */

    //reasonHtml = summary(node);

</script>

<style>
    .serious {
        color:red;
    }
    .critical {
        color: #ff5401;
    }
    .moderate {
        color:orange;
    }

    th.help {
        text-align: left;
    }
    .section-label {
        border: 1px dashed;
    }
</style>

<?php

//echo $output->footer();

