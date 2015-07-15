<?php
require_once('../../../moodle/config.php');
defined('MOODLE_INTERNAL') || die('Invalid access');

require_once $CFG->dirroot . '/local/mediacore/lib.php';

$url = $_SERVER['REQUEST_URI'];
$mcore_client = new mediacore_client();

$pos = strpos($url, '?');
if ($pos === false) {
    $response_string = 'HTTP/1.1 400 Bad Request';
    header($response_string);
    echo $response_string;
    return;
}
// The url contains query params, so split out the query string
// params as an array so we can pass them to the lti signing
// method
$qs = substr($url, $pos + 1);
$params = array();
parse_str($qs, $params);
$url = substr($url, 0, $pos);

$courseid = $params['context_id'];
$site_url = $mcore_client->get_siteurl();
$url = str_replace($_SERVER['SCRIPT_NAME'], $site_url, $url);

$auth = $mcore_client->get_auth();
$lti_params = $mcore_client->get_lti_params($courseid);
$signed_url = $auth->buildRequestUrl($url, 'GET', $lti_params);
redirect($signed_url);
