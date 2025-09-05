<?
include_once("./_common.php");

$max_cnt = code_increment($_POST['type']);

echo json_encode($max_cnt);
?>