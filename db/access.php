<?php
defined('MOODLE_INTERNAL') || die();

$capabilities = array(
    'report/seriescompletion:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes' => array(
            'manager' => CAP_ALLOW
        ),
        'clonepermissionsfrom' => 'moodle/site:config',
    )
);
