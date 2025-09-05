<?php
include_once('./_common.php');

$file = $_GET['file'] ?? '';
$file_path = G5_DATA_PATH . "/discard/" . $file;

if (!$file || !file_exists($file_path)) {
  die("파일이 존재하지 않습니다.");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . str_replace('discard', '폐기이미지', $file) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

readfile($file_path);
exit;
