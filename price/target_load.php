<?php
define('_GNUBOARD_', true);
require_once(dirname(__DIR__) . '/common.php');
// include_once('./_common.php');

header('Content-Type: application/json');

$secret_api_key = "j2pGqRx7NfLsYvUm9dZt3BxC5QkW8eUaH4zFmKcPwVb";
$received_api_key = $_SERVER['HTTP_X_API_KEY'] ?? $_POST['api_key'] ?? '';
$limit = $_GET['limit'];
$offset = $_GET['offset'];

if ($received_api_key !== $secret_api_key) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "인증 실패: 잘못된 API 키"]);
    exit();
}

$data = [];

$sql = "SELECT * FROM crawling_target WHERE is_active < 5 ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";

$result = sql_query($sql);

if ($result) {
    while ($row = sql_fetch_array($result)) {
        $data[] = $row;
    }
    echo json_encode($data);
} else {
    http_response_code(500);
    echo json_encode(["error" => "데이터 조회 실패: " . sql_error_info()]);
}
