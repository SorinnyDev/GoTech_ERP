<?php
include_once('./_common.php');

if ($type === 'waste') {
  $filepath = G5_DATA_PATH . '/uploads/폐기재고등록_엑셀양식.xls'; // 업로드된 파일 경로
  $title = '폐기재고등록_엑셀양식.xls';
} else {
  $filepath = G5_DATA_PATH . '/uploads/반품재고등록_엑셀양식.xls'; // 업로드된 파일 경로
  $title = '반품재고등록_엑셀양식.xls';
}


// 파일이 존재하는지 확인
if (!file_exists($filepath)) {
  die('파일이 존재하지 않습니다.');
}

// 파일 다운로드 처리
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $title);
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));

// 파일 읽기 및 출력
readfile($filepath);
exit;
?>
