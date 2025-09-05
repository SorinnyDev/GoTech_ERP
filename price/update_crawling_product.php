<?php
include_once('./_common.php');

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$productId = isset($_POST['product_id']) ? sql_escape_string(trim($_POST['product_id'])) : '';
	$variantId = isset($_POST['variant_id']) ? sql_escape_string(trim($_POST['variant_id'])) : '';
	$variantPrice = isset($_POST['variant_price']) ? sql_escape_string(trim($_POST['variant_price'])) : '';

	if (empty($productId) || empty($variantId) || $variantPrice === '') {
		$response['message'] = '필수 데이터(product_id, variant_id, variant_price)가 누락되었습니다.';
		echo json_encode($response);
		exit;
	}

	try {
		$sql = " UPDATE crawling_product
                 SET variant_price = '{$variantPrice}'
                 WHERE product_id = '{$productId}' AND variant_id = '{$variantId}' ";

		$result = sql_query($sql);

		if ($result) {
			$response['success'] = true;
			$response['message'] = '성공적으로 가격을 업데이트했습니다.';
		} else {
			global $g5;
			$db_error = isset($g5['db_error']) ? $g5['db_error'] : '알 수 없는 데이터베이스 오류';
			$response['message'] = '데이터베이스 쿼리 실행 중 오류가 발생했습니다: ' . $db_error;

			error_log('Gnuboard DB Query Error: ' . $db_error . ' SQL: ' . $sql);
		}
	} catch (Exception $e) {

		$response['message'] = '서버 처리 중 예기치 않은 오류가 발생했습니다: ' . $e->getMessage();
		error_log('Unexpected Server Error: ' . $e->getMessage());
	}
} else {

	$response['message'] = '잘못된 요청 방식입니다.';
}

echo json_encode($response);
