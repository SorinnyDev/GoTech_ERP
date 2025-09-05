<?php
include_once('./_common.php');

header('Content-Type: application/json');

// POST 요청으로부터 모든 데이터 받기
$product_id = $_POST['product_id'] ?? '';
$variant_id = $_POST['variant_id'] ?? '';
$site = $_POST['site'] ?? '';
$url = $_POST['url'] ?? '';
$target_price = $_POST['target_price'] ?? '';
$is_active = $_POST['is_active'] ?? 0; // 자바스크립트에서 이미 0으로 설정하여 보냄

// 유효성 검사 (필수 값 확인)
if (empty($product_id) || empty($variant_id) || empty($site) || empty($url)) {
    echo json_encode(['success' => false, 'message' => '필수 데이터가 누락되었습니다.']);
    exit();
}

$product_id = sql_escape_string($product_id);
$variant_id = sql_escape_string($variant_id);
$site = sql_escape_string($site);
$url = sql_escape_string($url);

$target_price = (float)$target_price; // 숫자로 형 변환하여 SQL Injection 위험 감소

$sql = "INSERT INTO crawling_target (
            product_id, 
            variant_id, 
            site, 
            url, 
            target_price, 
            is_active
        ) VALUES (
            '" . addslashes($product_id) . "', 
            '" . addslashes($variant_id) . "', 
            '" . addslashes($site) . "', 
            '" . addslashes($url) . "', 
            " . $target_price . ", 
            " . (int)$is_active . "
        )";

// 그누보드의 sql_query() 함수를 사용하여 쿼리 실행
$result = sql_query($sql, false); // 두 번째 인자는 오류 발생 시 에러 메시지 출력 여부

if ($result) {
    echo json_encode(['success' => true, 'message' => '데이터가 성공적으로 삽입되었습니다.']);
} else {
    // 쿼리 실패 시, sql_error() 함수로 상세 에러 메시지를 얻을 수 있습니다.
    $error_message = '알 수 없는 오류';
    if (function_exists('sql_error')) {
        $error_info = sql_error_info($sql); // 쿼리 정보를 함께 전달하여 상세 오류 확인
        $error_message = $error_info['message'] ?? '쿼리 실행 실패';
        // 개발 환경에서만 상세 오류를 출력하고, 운영 환경에서는 일반적인 메시지 출력
        // error_log("SQL Error: " . $error_info['message'] . " Query: " . $sql);
    }
    echo json_encode(['success' => false, 'message' => '데이터 삽입 실패: ' . $error_message]);
}
?>