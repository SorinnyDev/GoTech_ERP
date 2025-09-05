<?php
include_once('./_common.php');

if ($is_guest)
  alert('로그인 후 이용하세요.');

header('Content-Type: application/json');
$input = file_get_contents('php://input');
$payload = json_decode($input, true);

$response = ['success' => false, 'message' => ''];

if (json_last_error() !== JSON_ERROR_NONE || !is_array($payload)) {
  $response['message'] = 'Invalid JSON received.';
  echo json_encode($response);
  exit;
}

$warehouse = $payload['warehouse'] ?? null;
$orderList = $payload['orderList'] ?? null;

$rackData = sql_fetch_all("SELECT seq, gc_name FROM g5_rack WHERE gc_warehouse = {$warehouse}");

$rackList = [];

foreach ($rackData as $item) {
  $rackList[$item['gc_name']] = $item['seq'];
}

$loop_count = 0;
$fail_count = 0;

foreach ($orderList as $excel) {
  sql_trans_start();

  $wr_id_val = isset($excel['wr_id']) ? preg_replace('/[^0-9]/', '', $excel['wr_id']) : 0;

  $sales0_sql = "insert into g5_sales0_list set
                  mb_id = '{$excel['mb_id']}',
                  wr_id = '{$wr_id_val}',
                  wr_product_id = '{$excel['wr_product_id']}',
                  wr_product_nm = '" . addslashes($excel['wr_17']) . "',
                  wr_domain = '{$excel['wr_18']}',
                  wr_date = DATE('{$excel['wr_datetime']}'),
                  wr_ori_order_num = '{$excel['ori_order_num']}',
                  wr_order_num = '{$excel['wr_subject']}',
                  wr_mb_id = '{$excel['mb_1']}',
                  wr_mb_name = '" . addslashes($excel['wr_2']) . "',
                  wr_zip = '" . addslashes($excel['wr_8']) . "',
                  wr_addr1 = '" . addslashes($excel['wr_3']) . "',
                  wr_addr2 = '" . addslashes($excel['wr_4']) . "',
                  wr_city = '" . addslashes($excel['wr_5']) . "',
                  wr_ju = '" . addslashes($excel['wr_6']) . "',
                  wr_country = '" . addslashes($excel['wr_7']) . "',
                  wr_tel = '" . addslashes($excel['wr_9']) . "',
                  wr_deli_nm = '" . addslashes($excel['wr_27']) . "',                    
                  wr_deli_addr1 = '" . addslashes($excel['wr_28']) . "',
                  wr_deli_addr2 = '" . addslashes($excel['wr_29']) . "',
                  wr_deli_city = '" . addslashes($excel['wr_30']) . "',
                  wr_deli_ju = '" . addslashes($excel['wr_31']) . "',
                  wr_deli_country = '" . addslashes($excel['wr_32']) . "',
                  wr_deli_zip = '" . addslashes($excel['wr_33']) . "',
                  wr_deli_tel = '" . addslashes($excel['wr_34']) . "',
                  wr_code = '" . addslashes($excel['wr_16']) . "',
                  wr_servicetype = '{$excel['wr_20']}',
                  wr_ea = '{$excel['wr_11']}',
                  wr_box = '{$excel['wr_12']}',
                  wr_danga = '{$excel['wr_13']}',
                  wr_singo = '{$excel['wr_14']}',
                  wr_tax = '{$excel['wr_22']}',              
                  wr_fee1 = '{$excel['wr_35']}',
                  wr_fee2 = '{$excel['wr_36']}',                            
                  wr_currency = '{$excel['wr_15']}',                            
                  wr_email = '{$excel['wr_10']}',              
                  wr_warehouse = '{$warehouse}',                    
                  wr_datetime = '" . G5_TIME_YMDHIS . "'
                  ";

  $result = sql_query($sales0_sql, true);

  if (!$result) {
    sql_trans_rollback();
    $loop_count++;
    $fail_count++;
    continue;
  }

  $sales0_id = sql_insert_id();

  $wr_release_etc = '';
  $order_results = [];

  if (isset($excel['orderData']) && is_array($excel['orderData'])) {
    foreach ($excel['orderData'] as $seq => $item_data) {
      if (is_array($item_data)) {
        $current_order_num = $item_data['order_num'] ?? '';
        $current_rack_name = $item_data['rack_name'] ?? '';
        $current_value = $item_data['quantity'] ?? '';

        if (!empty($current_order_num) && !empty($current_rack_name) && is_numeric($current_value)) {
          $order_results[] = $current_order_num . ', ' . $current_rack_name . ', ' . $current_value . '개';
        }
      }
    }
    $wr_release_etc = '반품출고 ' . implode('; ', $order_results);
  }

  $sales3_sql = "insert into g5_sales3_list set 
                  mb_id = '{$excel['mb_id']}',
                  wr_id = '{$wr_id_val}',
                  sales0_id = '{$sales0_id}',
                  wr_domain = '{$excel['wr_18']}',
                  wr_date = DATE('{$excel['wr_datetime']}'),
                  wr_date2 = DATE('{$excel['wr_last']}'),
                  wr_date3 = '" . G5_TIME_YMD . "',
                  wr_date4 = '" . G5_TIME_YMD . "',
                  wr_ori_order_num = '{$excel['ori_order_num']}',
                  wr_order_num = '{$excel['wr_subject']}',
                  wr_mb_id = '{$excel['mb_1']}',
                  wr_mb_name = '" . addslashes($excel['wr_2']) . "',
                  wr_zip = '" . addslashes($excel['wr_8']) . "',
                  wr_addr1 = '" . addslashes($excel['wr_3']) . "',
                  wr_addr2 = '" . addslashes($excel['wr_4']) . "',
                  wr_city = '" . addslashes($excel['wr_5']) . "',
                  wr_ju = '" . addslashes($excel['wr_6']) . "',
                  wr_country = '" . addslashes($excel['wr_7']) . "',
                  wr_tel = '" . addslashes($excel['wr_9']) . "',
                  wr_deli_nm = '" . addslashes($excel['wr_27']) . "',
                  wr_deli_zip = '" . addslashes($excel['wr_33']) . "',
                  wr_deli_addr1 = '" . addslashes($excel['wr_28']) . "',
                  wr_deli_addr2 = '" . addslashes($excel['wr_29']) . "',
                  wr_deli_city = '" . addslashes($excel['wr_30']) . "',
                  wr_deli_ju = '" . addslashes($excel['wr_31']) . "',
                  wr_deli_country = '" . addslashes($excel['wr_32']) . "',
                  wr_deli_tel = '" . addslashes($excel['wr_34']) . "',
                  wr_code = '" . addslashes($excel['wr_16']) . "',
                  wr_servicetype = '{$excel['wr_20']}',
                  wr_ea = '{$excel['wr_11']}',
                  wr_box = '{$excel['wr_12']}',
                  wr_danga = '{$excel['wr_13']}',
                  wr_singo = '{$excel['wr_14']}',
                  wr_tax = '{$excel['wr_22']}',              
                  wr_fee1 = '{$excel['wr_35']}',
                  wr_fee2 = '{$excel['wr_36']}',                            
                  wr_currency = '{$excel['wr_15']}',                            
                  wr_email = '{$excel['wr_10']}',              
                  wr_warehouse = '{$warehouse}',
                  wr_release_etc = '{$wr_release_etc}',
                  wr_product_id = '{$excel['wr_product_id']}',
                  wr_product_nm = '" . addslashes($excel['wr_17']) . "',              
                  wr_datetime = '" . G5_TIME_YMDHIS . "',
                  wr_weight1 = '{$excel['wr_weight1']}',
                  wr_weight2 = '{$excel['wr_weight2']}',
                  wr_weight3 = '{$excel['wr_weight3']}',
                  wr_weight_dan = '{$excel['wr_weight_dan']}',
                  wr_make_country = '{$excel['wr_make_country']}'
                  ";

  $result = sql_query($sales3_sql, true);

  if (!$result) {
    sql_trans_rollback();
    $loop_count++;
    $fail_count++;
    continue;
  }

  $sales3_id = sql_insert_id();

  $totalSum = 0;

  if (isset($excel['orderData']) && is_array($excel['orderData'])) {
    $inner_loop_failed_flag = false;

    foreach ($excel['orderData'] as $seq => $item_data) {
      if (is_array($item_data) && isset($item_data['quantity']) && isset($item_data['rack_name'])) {

        $quantity_value = $item_data['quantity'];
        $rack_name_from_data = $item_data['rack_name'];

        if (is_numeric($quantity_value)) {
          $totalSum += $quantity_value;
          $rack_id = isset($rackList[$rack_name_from_data]) ? $rackList[$rack_name_from_data] : null;

          if ($rack_id === null) continue;

          $rack_stock_sql = "insert into g5_rack_stock set 
                                wr_warehouse = '{$warehouse}',
                                wr_rack = '{$rack_id}',
                                wr_stock = '-{$quantity_value}',
                                wr_product_id = '{$excel['wr_product_id']}',
                                wr_sales3_id = '{$sales3_id}',
                                wr_mb_id = '{$excel['mb_id']}',
                                wr_datetime = '" . G5_TIME_YMDHIS . "',
                                wr_move_log = '반품 출고'
                                ";

          $result = sql_query($rack_stock_sql, true);

          if (!$result) {
            $inner_loop_failed_flag = true;
            break;
          }
        }

        if ($inner_loop_failed_flag) {
          break;
        }
      }
    }

    if ($inner_loop_failed_flag) {
      sql_trans_rollback();
      $loop_count++;
      $fail_count++;
      continue;
    }
  }

  $escaped_warehouse = intval($warehouse);
  $escaped_totalSum = intval($totalSum);
  $escaped_wr_product_id = $connect_db->real_escape_string($excel['wr_product_id']);

  $total_sum_sql = "UPDATE g5_write_product
                    SET wr_40 = CASE
                    WHEN " . $escaped_warehouse . " = 7000 THEN wr_40 - " . $escaped_totalSum . "
                    ELSE wr_40
                    END,
                    wr_40_real = CASE
                        WHEN " . $escaped_warehouse . " = 7000 THEN wr_40_real - " . $escaped_totalSum . "
                        ELSE wr_40_real
                      END,
                    wr_41 = CASE
                        WHEN " . $escaped_warehouse . " = 8000 THEN wr_41 - " . "
                            CASE WHEN " . $escaped_warehouse . " = 8000 THEN " . $escaped_totalSum . " ELSE 0 END
                        ELSE wr_41
                      END,
                    wr_41_real = CASE
                      WHEN " . $escaped_warehouse . " = 8000 THEN wr_41_real - " . "
                          CASE WHEN " . $escaped_warehouse . " = 8000 THEN " . $escaped_totalSum . " ELSE 0 END
                      ELSE wr_41_real
                      END
                    WHERE
                      wr_id = '" . $escaped_wr_product_id . "'";

  $result = sql_query($total_sum_sql, true);

  if (!$result) {
    sql_trans_rollback();
    $loop_count++;
    $fail_count++;
    continue;
  }

  $loop_count++;
  sql_trans_commit();
}

$success_count = $loop_count - $fail_count;

// --- 4. 응답 데이터 배열 구성 ---
$response = [
  'status' => ($fail_count === 0) ? 'success' : 'failure', // 전체 성공/실패 여부
  'message' => ($fail_count === 0) ? '모든 작업이 성공했습니다.' : '일부 작업이 실패했습니다.',
  'total_loops' => $loop_count,   // 전체 작업/루프 횟수
  'failed_count' => $fail_count, // 실패 횟수
  'success_count' => $success_count // 성공 횟수
];

// --- 5. JSON 형식으로 인코딩하여 클라이언트에 전송 ---
echo json_encode($response);
