<?php
include_once('./_common.php');

if ($is_guest)
  alert('로그인 후 이용하세요.');

$count_chk_wr_id = (isset($_POST['chk_seq']) && is_array($_POST['chk_seq'])) ? count($_POST['chk_seq']) : 0;

if (!$count_chk_wr_id) alert('최소 1개이상 선택하세요.');
$suc = 0;
$fail = 0;
$msg = $btn_name == "출고등록" ? "출고등록이 완료되었습니다.\\n리스트에서 새로고침해주세요." : "선택삭제가 완료되었습니다.\\n리스트에서 새로고침해주세요."; // msg 초기값

$cancel_arr = array();
for ($i = 0; $i < $count_chk_wr_id; $i++) {

  // 출고등록
  if ($btn_name == "출고등록") {
    $wr_id_val = isset($_POST['chk_seq'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_seq'][$i]) : 0;

    $excel = sql_fetch("select * from g5_sales2_list where seq = '{$wr_id_val}'");

    $item = sql_fetch("select * from g5_write_product where wr_id = '{$excel['wr_product_id']}'");

    if (isEmpty($item)) {
      $fail++;
      continue;
    }

    //담당자가 동시체크 및 등록을 실행처리하는 경우를 대비.

    //입고자료 가져오기에서 이미 처리한 항목 중복입력 안되도록 처리
    $chk = sql_fetch("select * from g5_sales2_list where wr_chk = 1 and wr_order_num = '{$excel['wr_order_num']}' and wr_set_sku = '' ");
    if ($chk) {
      $fail++;
      continue;
    }


    //출고등록에서 주문번호로 한번더 체크하여 중복등록 안되도록.
    $chk2 = sql_fetch("select * from g5_sales3_list where wr_order_num = '{$excel['wr_order_num']}' and wr_set_sku = '' ");
    if ($chk2) {
      $fail++;
      continue;
    }
    $sql = "insert into g5_sales3_list set 
        mb_id = '{$excel['mb_id']}',
        wr_id = '{$wr_id_val}',
		sales0_id = '{$excel['sales0_id']}',
		sales1_id = '{$excel['sales1_id']}',
		sales2_id = '{$excel['seq']}',
        wr_domain = '{$excel['wr_domain']}',
        wr_date = '{$excel['wr_date']}',
		wr_ori_order_num = '{$excel['wr_ori_order_num']}',
        wr_order_num = '{$excel['wr_order_num']}',
        wr_mb_id = '{$excel['wr_mb_id']}',
        wr_mb_name = '" . addslashes($excel['wr_mb_name']) . "',
        wr_zip = '" . addslashes($excel['wr_zip']) . "',
        wr_addr1 = '" . addslashes($excel['wr_addr1']) . "',
        wr_addr2 = '" . addslashes($excel['wr_addr2']) . "',
        wr_city = '" . addslashes($excel['wr_city']) . "',
        wr_ju = '" . addslashes($excel['wr_ju']) . "',
        wr_country = '" . addslashes($excel['wr_country']) . "',
        wr_tel = '" . addslashes($excel['wr_tel']) . "',
		wr_deli_nm = '" . addslashes($excel['wr_deli_nm']) . "',
		wr_deli_zip = '" . addslashes($excel['wr_deli_zip']) . "',
		wr_deli_addr1 = '" . addslashes($excel['wr_deli_addr1']) . "',
		wr_deli_addr2 = '" . addslashes($excel['wr_deli_addr2']) . "',
		wr_deli_city = '" . addslashes($excel['wr_deli_city']) . "',
		wr_deli_ju = '" . addslashes($excel['wr_deli_ju']) . "',
		wr_deli_country = '" . addslashes($excel['wr_deli_country']) . "',
		wr_deli_tel = '" . addslashes($excel['wr_deli_tel']) . "',
        wr_code = '" . addslashes($excel['wr_code']) . "',
        wr_ea = '{$excel['wr_ea']}',
        wr_box = '{$excel['wr_box']}',
		wr_paymethod = '" . addslashes($excel['wr_paymethod']) . "',
        wr_danga = '{$excel['wr_danga']}',
        wr_singo = '{$excel['wr_singo']}',
		wr_tax = '{$excel['wr_tax']}',
		wr_shipping_price = '{$excel['wr_shipping_price']}',
		wr_fee1 = '{$excel['wr_fee1']}',
		wr_fee2 = '{$excel['wr_fee2']}',
		wr_taxType = '{$excel['wr_taxType']}',
		wr_exchange_rate = '{$excel['wr_exchange_rate']}',
		wr_sales_fee_type = '{$excel['wr_sales_fee_type']}',
		wr_sales_fee = '{$excel['wr_sales_fee']}',
        wr_currency = '{$excel['wr_currency']}',
        wr_weight1 = '{$excel['wr_weight1']}',
        wr_weight2 = '{$excel['wr_weight2']}',
        wr_weight3 = '{$excel['wr_weight3']}',
        wr_weight_dan = '{$excel['wr_weight_dan']}',
        wr_hscode = '{$excel['wr_hscode']}',
        wr_make_country = '{$excel['wr_make_country']}',
        wr_delivery = '{$excel['wr_delivery']}',
        wr_delivery_fee = '{$excel['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$excel['wr_delivery_fee2']}',
		wr_delivery_oil = '{$excel['wr_delivery_oil']}',
        wr_email = '{$excel['wr_email']}',
        wr_servicetype = '{$excel['wr_servicetype']}',
        wr_packaging = '{$excel['wr_packaging']}',
        wr_country_code = '{$excel['wr_country_code']}',
        wr_name2 = '" . addslashes($excel['wr_name2']) . "',
        wr_etc = '" . addslashes($excel['wr_etc']) . "',
        wr_date2 = '{$excel['wr_date2']}',
        wr_date3 = '{$excel['wr_date3']}',
        wr_date4 = '{$_POST['wr_date4']}',
        wr_order_num2 = '{$excel['wr_order_num2']}',
        wr_orderer = '{$excel['wr_orderer']}',
        wr_order_ea = '{$excel['wr_order_ea']}',
        wr_order_price = '{$excel['wr_order_price']}',
        wr_order_total = '{$excel['wr_order_total']}',
        wr_order_traking = '{$excel['wr_order_traking']}',
		wr_order_fee = '{$excel['wr_order_fee']}',
        wr_order_etc = '{$excel['wr_order_etc']}',
        wr_warehouse = '{$excel['wr_warehouse']}',
        wr_rack = '{$excel['wr_rack']}',
        wr_warehouse_etc = '{$excel['wr_warehouse_etc']}',
        wr_direct_use = '{$excel['wr_direct_use']}',
		wr_misu = '{$excel['wr_misu']}',
        wr_product_id = '{$item['wr_id']}',
		wr_product_nm = '" . addslashes($excel['wr_product_nm']) . "',
		wr_set_sku = '" . addslashes($excel['wr_set_sku']) . "',
        wr_datetime = '" . G5_TIME_YMDHIS . "'
        ";
    sql_query($sql, true);
    $sales3_id = sql_insert_id();

    sql_query("update g5_sales2_list set wr_chk = 1 where seq = '{$excel['seq']}'");

    # g5_sales3_det 데이터 등록(선입 선출 데이터 등록)
    # 입고 데이터 중 출고 가능 데이터 조회
    $sql = "SELECT * FROM g5_sales2_list WHERE wr_product_id='" . $excel['wr_product_id'] . "' AND wr_chul_ea > 0 ORDER BY wr_date3 ASC";
    $rs = sql_query($sql);
    $wr_ea = $excel['wr_ea'];
    $ibgo_ea = 0;
    while ($ibRow = sql_fetch_array($rs)) {
      if ($wr_ea > 0) {
        $ori_wr_ea = $wr_ea;
        $wr_ea = $wr_ea - $ibRow['wr_chul_ea'];
        if ($wr_ea >= 0) {
          # 입고 데이터의 출고 가능 잔여 수량 변경
          sql_query("UPDATE g5_sales2_list SET wr_chul_ea = '0' WHERE seq='" . $ibRow['seq'] . "'");

          $sql = "INSERT INTO g5_sales3_det SET \n";
          $sql .= "	sales2_id = '" . $ibRow['seq'] . "' \n";
          $sql .= "	,sales3_id = '" . $sales3_id . "' \n";
          $sql .= "	,wr_order_num = '" . $excel['wr_order_num'] . "' \n";
          $sql .= "	,wr_product_id = '" . $excel['wr_product_id'] . "' \n";
          $sql .= "	,wr_warehouse = '" . $excel['wr_warehouse'] . "' \n";
          $sql .= "	,wr_rack = '" . $excel['wr_rack'] . "' \n";
          $sql .= "	,ibgo_taxType = '" . $ibRow['wr_taxType'] . "' \n";
          $sql .= "	,ibgo_danga = '" . $ibRow['wr_order_price'] . "' \n";
          $sql .= "	,chul_ea = '" . $ibRow['wr_chul_ea'] . "' \n";
          $sql .= "	,chul_date = '" . $_POST['wr_date4'] . "' \n";
          $sql .= "	,reg_date = NOW() \n";
          $sql .= "	,del_yn = 'N'";
          sql_query($sql);
          $ibgo_ea = $ibgo_ea + $ibRow['wr_chul_ea'];
        } else {
          # 입고 데이터의 출고 가능 잔여 수량 변경
          sql_query("UPDATE g5_sales2_list SET wr_chul_ea = '" . (-1 * $wr_ea) . "' WHERE seq='" . $ibRow['seq'] . "'");

          $sql = "INSERT INTO g5_sales3_det SET \n";
          $sql .= "	sales2_id = '" . $ibRow['seq'] . "' \n";
          $sql .= "	,sales3_id = '" . $sales3_id . "' \n";
          $sql .= "	,wr_order_num = '" . $excel['wr_order_num'] . "' \n";
          $sql .= "	,wr_product_id = '" . $excel['wr_product_id'] . "' \n";
          $sql .= "	,wr_warehouse = '" . $excel['wr_warehouse'] . "' \n";
          $sql .= "	,wr_rack = '" . $excel['wr_rack'] . "' \n";
          $sql .= "	,ibgo_taxType = '" . $ibRow['wr_taxType'] . "' \n";
          $sql .= "	,ibgo_danga = '" . $ibRow['wr_order_price'] . "' \n";
          $sql .= "	,chul_ea = '" . $ori_wr_ea . "' \n";
          $sql .= "	,chul_date = '" . $_POST['wr_date4'] . "' \n";
          $sql .= "	,reg_date = NOW() \n";
          $sql .= "	,del_yn = 'N'";
          sql_query($sql);
          $ibgo_ea = $ibgo_ea + $ori_wr_ea;
        }
      }
    }

    if (($excel['wr_ea'] - $ibgo_ea) > 0) {
      # 상품 정보 가져오기
      $sql = "SELECT * FROM g5_write_product WHERE wr_id='" . $excel['wr_product_id'] . "'";
      $item2 = sql_fetch($sql);

      $sql = "INSERT INTO g5_sales3_det SET \n";
      $sql .= "	sales2_id = '0' \n";
      $sql .= "	,sales3_id = '" . $sales3_id . "' \n";
      $sql .= "	,wr_order_num = '" . $excel['wr_order_num'] . "' \n";
      $sql .= "	,wr_product_id = '" . $excel['wr_product_id'] . "' \n";
      $sql .= "	,wr_warehouse = '" . $excel['wr_warehouse'] . "' \n";
      $sql .= "	,wr_rack = '" . $excel['wr_rack'] . "' \n";
      $sql .= "	,ibgo_taxType = '" . $item2['taxType'] . "' \n";
      $sql .= "	,ibgo_danga = '" . $item2['wr_22'] . "' \n";
      $sql .= "	,chul_ea = '" . ($excel['wr_ea'] - $ibgo_ea) . "' \n";
      $sql .= "	,chul_date = '" . $_POST['wr_date4'] . "' \n";
      $sql .= "	,reg_date = NOW() \n";
      $sql .= "	,del_yn = 'N'";
      sql_query($sql);
    }


    if ($excel['wr_warehouse'] == '1000') {
      $field = 'wr_32_real';
    } else if ($excel['wr_warehouse'] == '3000') {
      $field = 'wr_36_real';
    } else if ($excel['wr_warehouse'] == '4000') {
      $field = 'wr_42_real';
    } else if ($excel['wr_warehouse'] == '5000') {
      $field = 'wr_43_real';
    } else if ($excel['wr_warehouse'] == '6000') {
      $field = 'wr_44_real';
    }

    if (isset($field)) {
      sql_query("update g5_write_product set {$field} = {$field} - {$excel['wr_ea']} where wr_id = '{$excel['wr_product_id']}' limit 1");
    }

    // 선택완전삭제 = 이전 과정까지 전부 삭제    
  } else if ($btn_name == "선택완전삭제") {

    # 출고 등록 전 이므로 출고 예상 수량만 복구

    $info = sql_fetch("SELECT * FROM g5_sales2_list WHERE seq = '{$_POST['chk_seq'][$i]}' ");
    if ($info['wr_ibgo_ea'] != $info['wr_chul_ea']) {
      $cancel_arr[] = $info['wr_order_num'];
      continue;
    }

    $rack = sql_fetch("select * from g5_rack_stock where 
			wr_warehouse = '{$info['wr_warehouse']}' and
			wr_sales3_id = '{$info['seq']}' and
			wr_product_id = '{$info['wr_product_id']}'");


    //출고기록이 있을때만
    if ($info['wr_rack']) {
      if ($info['wr_direct_use'] == "1") {
        $sql = "insert into g5_rack_stock set wr_warehouse = '{$info['wr_warehouse']}', wr_rack = '{$info['wr_rack']}', wr_stock = '{$info['wr_ea']}', wr_product_id = '{$info['wr_product_id']}', wr_sales3_id = '{$rack['wr_sales3_id']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '출고처리건 완전삭제'";
        sql_query($sql);

        $filed = $storage_arr[$info['wr_warehouse']]['field'];
        sql_query("update g5_write_product set {$filed} = {$filed} + {$info['wr_ea']} where wr_id = '{$info['wr_product_id']}'");
      } else if ($info['wr_direct_use'] != "1" && $info['wr_warehouse'] == "3000") {
        $sql = "insert into g5_rack_stock set wr_warehouse = '{$info['wr_warehouse']}', wr_rack = '{$rack['wr_rack']}', wr_stock = '{$info['wr_ea']}', wr_product_id = '{$info['wr_product_id']}', wr_sales3_id = '{$rack['wr_sales3_id']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '출고처리건 완전삭제'";
        sql_query($sql);

        $filed = $storage_arr[$info['wr_warehouse']]['field'];
        sql_query("update g5_write_product set {$filed} = {$filed} + {$info['wr_ea']} where wr_id = '{$info['wr_product_id']}'");
      } else {
        $sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$info['wr_ea']} WHERE wr_id = '{$info['wr_product_id']}'";
        sql_query($sql);

        # 임시창고에서 가져온 재고이므로 임시창고 정보에 업데이트
        $sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock + {$info['wr_ea']},wr_stock2 = wr_stock2 - {$info['wr_ea']} WHERE sales2_id = '{$info['seq']}'";
        sql_query($sql);
      }
    }

    sql_query("DELETE FROM g5_write_sales WHERE wr_subject = '{$info['wr_order_num']}' ");    //매출자료 관리
    sql_query("DELETE FROM g5_sales0_list WHERE wr_order_num = '{$info['wr_order_num']}' ");  //매출 관리
    sql_query("DELETE FROM g5_sales1_list WHERE wr_order_num = '{$info['wr_order_num']}' ");  //발주 관리
    sql_query("DELETE FROM g5_sales2_list WHERE wr_order_num = '{$info['wr_order_num']}' ");  //입고 관리
    sql_query("DELETE FROM g5_sales3_list WHERE wr_order_num = '{$info['wr_order_num']}' ");  //출고 관리

    //06.04 완전삭제도 출고 된 랙 찾아 재고차감추가.


    // $seq0 = sql_fetch("select wr_id from g5_sales2_list where seq = '{$_POST['chk_seq'][$i]}'")['wr_id'];
    // $wr_seq = sql_fetch("select wr_id from g5_sales0_list where seq = '{$seq0}'")['wr_id'];

    // echo $wr_seq." ".$seq0." ".$_POST['chk_seq'][$i];
    // sql_query("delete from g5_write_sales where wr_id = '{$wr_seq}' ");                 //매출자료 관리
    // sql_query("delete from g5_sales0_list where seq = '{$seq0}' ");                     //매출 관리
    // sql_query("delete from g5_sales2_list where seq = '{$_POST['chk_seq'][$i]}' ");     //입고 관리

  } else if ($btn_name == "선택삭제") {


    $info = sql_fetch("SELECT * FROM g5_sales2_list WHERE seq = '{$_POST['chk_seq'][$i]}' ");

    $info2 = sql_fetch("SELECT seq, wr_id FROM g5_sales0_list WHERE seq = '{$info['wr_id']}' ");
    if ($info['wr_direct_use'] == "0") {
      if ($info['wr_ibgo_ea'] != $info['wr_chul_ea'] + $info['wr_ea']) {
        $cancel_arr[] = $info['wr_order_num'];
        continue;
      }
    }

    //바로출고건은 체크하여 출고랙을 찾아 재고차감 및 창고 재고복구  24.05.30
    //모든 출고건 출고랙 찾아 재고차감. 24.06.04

    $rack = sql_fetch("select * from g5_rack_stock where 
		wr_warehouse = '{$info['wr_warehouse']}' and
		wr_sales3_id = '{$info['seq']}' and
		wr_product_id = '{$info['wr_product_id']}' AND wr_stock < 0");

    //출고기록이 있을때만
    if ($info['wr_rack']) {


      $filed = $storage_arr[$info['wr_warehouse']]['field'];
      $field_real = $storage_arr[$info['wr_warehouse']]['field_real'];
      if ($info['wr_direct_use'] == "1") {
        $sql = "insert into g5_rack_stock set wr_warehouse = '{$info['wr_warehouse']}', wr_rack = '{$info['wr_rack']}', wr_stock = '{$info['wr_ea']}', wr_product_id = '{$info['wr_product_id']}', wr_sales3_id = '{$rack['wr_sales3_id']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '출고처리건 선택삭제'";
        sql_query($sql);

        sql_query("update g5_write_product set {$filed} = {$filed} + {$info['wr_ea']} where wr_id = '{$info['wr_product_id']}'");
      } else if ($info['wr_direct_use'] != "1" && $info['wr_warehouse'] == "3000") {
        $sql = "insert into g5_rack_stock set wr_warehouse = '{$info['wr_warehouse']}', wr_rack = '{$info['wr_rack']}', wr_stock = '{$info['wr_ea']}', wr_product_id = '{$info['wr_product_id']}', wr_sales3_id = '{$rack['wr_sales3_id']}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '출고처리건 선택삭제'";
        sql_query($sql);

        $filed = $storage_arr[$info['wr_warehouse']]['field'];
        sql_query("update g5_write_product set {$filed} = {$filed} + {$info['wr_ea']} where wr_id = '{$info['wr_product_id']}'");
      } else {

        $sql = "UPDATE g5_write_product SET wr_37 = wr_37 + {$info['wr_ea']} WHERE wr_id = '{$info['wr_product_id']}'";
        sql_query($sql);

        # 임시창고에서 가져온 재고이므로 임시창고 정보에 업데이트
        $sql = "UPDATE g5_temp_warehouse SET wr_stock = wr_stock + {$info['wr_ea']},wr_stock2 = wr_stock2 - {$info['wr_ea']} WHERE sales2_id = '{$info['seq']}'";
        sql_query($sql);

      }
    }

    $update = "update g5_sales0_list set wr_chk = 0 where wr_order_num = '{$info['wr_order_num']}' LIMIT 1";
    if (sql_query($update)) {
      @sql_query("delete from g5_sales1_list where wr_order_num = '{$info['wr_order_num']}' LIMIT 1");

      $sql = "delete from g5_sales2_list where wr_order_num = '{$info['wr_order_num']}' LIMIT 1";
      sql_query($sql);

      $sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('" . addslashes($sql) . "','" . addslashes(json_encode($_REQUEST)) . "','" . $member['mb_id'] . "',NOW(),'" . $info['wr_order_num'] . "')";
      sql_query($sql);

      $sql = "delete from g5_sales3_list where wr_order_num = '{$info['wr_order_num']}' LIMIT 1";
      sql_query($sql);

      $sql = "INSERT INTO g5_del_history(query_string,params,mb_id,reg_date,wr_order_num)VALUES('" . addslashes($sql) . "','" . addslashes(json_encode($_REQUEST)) . "','" . $member['mb_id'] . "',NOW(),'" . addslashes($info['wr_order_num']) . "')";
      sql_query($sql);
    }

    // $seq0 = sql_fetch("select wr_id from g5_sales2_list where seq = '{$_POST['chk_seq'][$i]}'")['wr_id'];
    // $wr_seq = sql_fetch("select wr_id from g5_sales0_list where seq = '{$seq0}'")['wr_id'];

    // echo $wr_seq." ".$seq0." ".$_POST['chk_seq'][$i];
    // sql_query("delete from g5_write_sales where wr_id = '{$wr_seq}' ");                 //매출자료 관리
    // sql_query("delete from g5_sales0_list where seq = '{$seq0}' ");                     //매출 관리
    // sql_query("delete from g5_sales2_list where seq = '{$_POST['chk_seq'][$i]}' ");     //입고 관리

  } else {
    $msg = "선택한 버튼이 없습니다. 다시 확인 후 이용해주세요.";
    break;
  }

  $suc++;
}

if (count($cancel_arr) > 0) {
  $msg = "삭제 불가능한 데이터가 있습니다.";
}

opener_reload();
alert($msg);
?>