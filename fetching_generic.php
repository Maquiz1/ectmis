<?php
require_once 'php/core/init.php';
$user = new User();
$override = new OverideData();
$email = new Email();
$random = new Random();

header('Content-Type: application/json');

$output = array();
$all_generic = $override->getDataAsc('generic', 'status', 1,'name');
foreach ($all_generic as $name) {
    $output[] = $name['name'];
}
echo json_encode($output);
