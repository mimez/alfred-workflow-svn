<?php
ini_set('display_errors', 0);
require(__DIR__ . "/../vendor/autoload.php");

if (getenv('SVN_URL') === false || getenv('SVN_USER') === false || getenv('SVN_PASS') === false) {
    die('missing environment variable SVN_URL or SVN_USER or SVN_PASS');
}

$projectsWorkflow = new \Mimez\AlfredWorkflow\SvnWorkflow\SvnWorkflow(
    getenv('SVN_URL'), 
    getenv('SVN_USER'), 
    getenv('SVN_PASS'),
    '/opt/homebrew/bin/svn'
);
$projectsWorkflow($_SERVER['argv'][1]);
