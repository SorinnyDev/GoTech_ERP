<?php
define('_GNUBOARD_', true);
require_once(dirname(__DIR__) . '/common.php');
// include_once('./_common.php');

header('Content-Type: application/json');

$secret_api_key = "j2pGqRx7NfLsYvUm9dZt3BxC5QkW8eUaH4zFmKcPwVb";
$received_api_key = $_SERVER['HTTP_X_API_KEY'] ?? $_POST['api_key'] ?? '';

if ($received_api_key !== $secret_api_key) {
  http_response_code(401); // Unauthorized
  echo json_encode(["error" => "인증 실패: 잘못된 API 키"]);
  exit();
}

$tableName = 'crawling_product';

$json_data = file_get_contents('php://input');
$products = json_decode($json_data, true);

// 데이터 유효성 검사
if (!is_array($products) || empty($products)) {
  echo json_encode([
    'status' => 'error',
    'message' => '유효하지 않은 데이터 배열 또는 빈 요청입니다.'
  ]);
  exit;
}

if (json_last_error() !== JSON_ERROR_NONE || !is_array($products) || empty($products)) {
  echo json_encode([
    'status' => 'error',
    'message' => '유효하지 않은 JSON 데이터 또는 빈 요청입니다.'
  ]);
  exit;
}

$inserted_count = 0;
$skipped_variants = []; // 중복으로 건너뛴 variant_id 목록

try {
  sql_trans_start();

  $fields = [
    'product_id',
    'product_title',
    'online_store_url',
    'status',
    'variant_id',
    'variant_title',
    'variant_sku',
    'variant_price',
    'variant_inventory_quantity'
  ];
  $field_names = implode(', ', $fields);

  foreach ($products as $product) {
    $variant_id = $product['variant_id'] ?? null;

    if (empty($variant_id)) {
      continue;
    }

    $check_sql = "SELECT COUNT(*) AS cnt FROM {$tableName} WHERE variant_id = '" . sql_escape_string($variant_id) . "'";
    $check_result = sql_fetch($check_sql); // sql_fetch는 mysqli_result 객체를 인수로 받습니다.

    if ($check_result && $check_result['cnt'] > 0) {
      $skipped_variants[] = $variant_id;
      continue;
    }

    $single_product_values_for_sql = [];
    foreach ($fields as $field) {
      $value = $product[$field] ?? null;

      if ($field === 'variant_price') {
        $val_for_sql = is_numeric($value) ? (float)$value : 0.0;
      } elseif ($field === 'variant_inventory_quantity') {
        $val_for_sql = is_numeric($value) ? (int)$value : 0;
      } else {
        $val_for_sql = "'" . sql_escape_string($value) . "'";
      }
      $single_product_values_for_sql[] = $val_for_sql;
    }

    $insert_sql = "INSERT INTO {$tableName} ({$field_names}) VALUES (" . implode(', ', $single_product_values_for_sql) . ")";
    $insert_result = sql_query($insert_sql, false);

    if ($insert_result === false) {
      throw new Exception("Variant ID {$variant_id} 삽입 중 오류 발생.");
    }
    $inserted_count++;
  }

  sql_trans_commit();

  echo json_encode([
    'status' => 'success',
    'message' => "총 {$inserted_count}개 레코드가 성공적으로 삽입되었습니다. 중복으로 인해 " . count($skipped_variants) . "개 레코드를 건너뛰었습니다.",
    'inserted_count' => $inserted_count,
    'skipped_variants' => $skipped_variants // 건너뛴 variant_id 목록 반환
  ]);
} catch (Exception $e) {
  sql_trans_rollback();
  echo json_encode([
    'status' => 'error',
    'message' => '데이터 처리 중 오류 발생: ' . $e->getMessage()
  ]);
}
