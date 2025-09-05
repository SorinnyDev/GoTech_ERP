<?php
include_once('./_common.php');

/***************
24.05.17
기존 매출등록>발주등록>입고등록>출고등록
변경 매출등록>재고여부 판별> 발주 또는 출고등록 순으로 변경
***************/
if ($is_guest)
    alert('로그인 후 이용하세요.');

$count_chk_wr_id = (isset($_POST['chk_wr_id']) && is_array($_POST['chk_wr_id'])) ? count($_POST['chk_wr_id']) : 0;

if(!$count_chk_wr_id) alert('최소 1개이상 선택하세요.');

if(!$_POST['wr_warehouse']) alert('창고가 선택되지 않았습니다.');

if($wr_warehouse == 1000)
	$stock_field = 'wr_32';
else if($wr_warehouse == 3000)
	$stock_field = 'wr_36';
else if($wr_warehouse == 4000)
	$stock_field = 'wr_42';
else if($wr_warehouse == 5000)
	$stock_field = 'wr_43';
else if($wr_warehouse == 6000)
	$stock_field = 'wr_44';
else if($wr_warehouse == 7000)
	$stock_field = 'wr_40';
else if($wr_warehouse == 8000)
	$stock_field = 'wr_41';
else if($wr_warehouse == 9000)
	$stock_field = 'wr_37';


$fail_count = 0;
$chk_arr = array_count_values($chk_wr_id);
for ($i=0; $i<$count_chk_wr_id; $i++) {
	$wr_id_val = isset($_POST['chk_wr_id'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_wr_id'][$i]) : 0;
	
	//세트상품 개념 다시 추가;
	if(strpos($_POST['chk_wr_id'][$i], '|') !== false) {
		
		$wr_id_val2 = explode('|', $_POST['chk_wr_id'][$i]);
		
		$excel = sql_fetch("select * from g5_write_sales where wr_id = '{$wr_id_val2[0]}'");
		
		$item = sql_fetch("select *, IF(wr_18 > wr_19 , wr_18 , wr_19) AS wr_weight3 from g5_write_product where wr_id = '{$wr_id_val2[1]}'");
		$item_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id='".$item['wr_id']."' AND domain='".$excel['wr_18']."' AND warehouse='".$wr_warehouse."'");
		$set_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id = '".$excel['wr_product_id']."' AND domain='".$excel['wr_18']."' AND warehouse='".$wr_warehouse."'");
	
		$sql = "SELECT * FROM g5_write_product WHERE wr_id= '".$excel['wr_product_id']."'";
		$setRow = sql_fetch($sql);
		$setData = array();
		$item_34 = explode('|@|', $setRow['wr_34']);
		$item_35 = explode('|@|', $setRow['wr_35']);
		for($setInt = 0; $setInt < count($item_34); $setInt++){
			$set_wr_id = $item_34[$setInt];
			$set_wr_ea = $item_35[$setInt];
			$setData[$set_wr_id] = $set_wr_ea;
		}
		
		// 과세 /면세 조회
		$wr_taxType = $excel['wr_37'];
		if(isDefined($item['taxType']) == true){
			$wr_taxType = $item['taxType'];
		}

		$total_weight = $item['wr_10'] * $excel['wr_11'];
		
		
		//세트상품은 제외이미 처리한 항목 중복입력 안되도록 처리
		/*$chk = sql_fetch("select * from g5_sales0_list where wr_order_num = '{$excel['wr_subject']}'");
		if($chk){
			//continue; //실행취소
		}*/ 
		
		$sql = "SELECT COUNT(*) AS chk_cnt FROM g5_sales0_list WHERE wr_order_num LIKE '".$excel['wr_subject']."%' AND wr_product_id='".$item['wr_id']."'";
		$chkRow = sql_fetch($sql);
		if($chk_arr[$chk_wr_id[$i]] <= $chkRow['chk_cnt']){
			$fail_count++;
			continue; //실행취소
		}

		if($item['wr_1'] == "") continue;

		# 환율 정보 불러오기
		$exchange = fnGetExcharge($excel['wr_15']);
		$wr_exchange_rate = str_replace(",","",$exchange['rate']);

		if(!$setData[$item['wr_1']]){
			$setData[$item['wr_1']] = 1;
		}

		$wr_ea = $excel['wr_11']*$setData[$item['wr_1']];

		// WM , WM CA의 경우 창고에 따른 수수료률이 다르다고 했음으로 창고 선택시 다시 한번 수수료1 계산을 진행
		// 해당 내용 담당자가 변경됨으로 인해 다시 한번 필요 -> 해당 내용과 다를 경우 상품 수수료 입력부분 수정 필요
//		if($excel['wr_18'] == "WM" || $excel['wr_18'] == "WM CA"){
//			if(isDefined($set_fee['product_fee']) == true){
//				$excel['wr_35'] = ($excel['wr_14'] * $set_fee['product_fee'])/100;
//			}else{
//				$excel['wr_35'] = 0;
//			}
//		}

    $delivery_fee2 = 0;
    # 서비스타입 0003 일 경우 추가배송비 자동기입
    if ($excel['wr_20'] === '0003') {
      $delivery_fee2 = 9000;
    }

		$sql = "insert into g5_sales0_list set 
		mb_id = '{$item['mb_id']}',
		wr_id = '{$wr_id_val2[0]}',
		wr_product_id = '{$item['wr_id']}',
		wr_product_nm = '".addslashes($excel['wr_17'])."',
		wr_domain = '{$excel['wr_18']}',
		wr_date = '{$_POST['wr_date']}',
		wr_ori_order_num = '".$excel['ori_order_num']."',
		wr_order_num = '{$excel['wr_subject']}', 
		wr_mb_id = '{$excel['wr_1']}',
		wr_mb_name = '".addslashes($excel['wr_2'])."',
		wr_zip = '".addslashes($excel['wr_8'])."',
		wr_addr1 = '".addslashes($excel['wr_3'])."', 
		wr_addr2 = '".addslashes($excel['wr_4'])."',
		wr_city = '".addslashes($excel['wr_5'])."',
		wr_ju = '".addslashes($excel['wr_6'])."',
		wr_country = '".addslashes($excel['wr_7'])."',
		wr_tel = '".addslashes($excel['wr_9'])."',
		wr_deli_nm = '".addslashes($excel['wr_27'])."',
		wr_deli_addr1 = '".addslashes($excel['wr_28'])."',
		wr_deli_addr2 = '".addslashes($excel['wr_29'])."',
		wr_deli_city = '".addslashes($excel['wr_30'])."',
		wr_deli_ju = '".addslashes($excel['wr_31'])."',
		wr_deli_country = '".addslashes($excel['wr_32'])."',
		wr_deli_zip = '".addslashes($excel['wr_33'])."',
		wr_deli_tel = '".addslashes($excel['wr_34'])."',
		wr_code = '".addslashes($item['wr_1'])."',
		wr_ea = '{$wr_ea}',
		wr_box = '{$excel['wr_12']}',
		wr_paymethod = '".addslashes($excel['wr_24'])."',
		wr_danga = '{$excel['wr_13']}',
		wr_singo = '{$excel['wr_14']}',
		wr_tax = '{$excel['wr_22']}',
		wr_shipping_price = '{$excel['wr_23']}',
		wr_fee1 = '{$excel['wr_35']}',
		wr_fee2 = '{$excel['wr_36']}',
		wr_taxType = '{$wr_taxType}',
		wr_exchange_rate = '".$wr_exchange_rate."',
		wr_sales_fee_type = '{$item_fee['fee_type']}',
		wr_sales_fee = '{$item_fee['product_fee']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$item['wr_10']}',
		wr_weight2 = '{$total_weight}',
		wr_weight3 = '{$item['wr_weight3']}',
		wr_weight_dan = '{$item['wr_11']}',
		wr_hscode = '{$item['wr_12']}',
		wr_make_country = '{$item['wr_13']}',
		wr_delivery = '',
		wr_delivery_fee = '',
		wr_delivery_fee2 = '{$delivery_fee2}',
		wr_email = '{$excel['wr_10']}',
		wr_servicetype = '{$excel['wr_20']}',
		wr_packaging = '',
		wr_country_code = '',
		wr_name2 = '".addslashes($item['wr_3'])."',
		wr_etc = '".addslashes($excel['wr_21'])."',
		wr_set_sku = '".addslashes($excel['wr_16'])."',
		
		wr_warehouse = '{$wr_warehouse}',
		wr_chk = 1
		
		";
		//echo $sql."<br>";
		sql_query($sql, true);
		$seq = sql_insert_id();
		
		//재고 체크 후 배열담기 하단에서 발주/출고 처리
		if($item[$stock_field] < $excel['wr_11']) {
			$add_seq1[] .= $seq;
		} else {
			$add_seq2[] .= $seq;
		}
		
		
		
	} else {
		
		$excel = sql_fetch("select * from g5_write_sales where wr_id = '{$wr_id_val}'");
		
		//SKU1~ 값으로 제품 DB조회
		//24.5.17 각 주문건db에 wr_product_id를 추가, 다만 업데이트 전건등은 해당 값이 없으므로 아래 유지후
		//추후 whare절 wr_id(pk) 로 찾을 수 있도록 연동하는것이 좋겠음.
		
		//$item = sql_fetch("select *, IF(wr_18 > wr_19 , wr_18 , wr_19) AS wr_weight3 from g5_write_product where (wr_1 = '".addslashes($excel['wr_16'])."' or wr_27 = '".addslashes($excel['wr_16'])."' or wr_28 = '".addslashes($excel['wr_16'])."' or wr_29 = '".addslashes($excel['wr_16'])."' or wr_30 = '".addslashes($excel['wr_16'])."' or wr_31 = '".addslashes($excel['wr_16'])."') ");
		$item = sql_fetch("select *, IF(wr_18 > wr_19 , wr_18 , wr_19) AS wr_weight3 from g5_write_product where wr_id='".$excel['wr_product_id']."' ");
		$item_fee = sql_fetch("SELECT * FROM g5_write_product_fee WHERE wr_id='".$item['wr_id']."' AND domain='".$excel['wr_18']."' AND warehouse='".$wr_warehouse."'");
		
		//이미 처리한 항목 중복입력 안되도록 처리
		$chk = sql_fetch("select * from g5_sales0_list where wr_order_num = '{$excel['wr_subject']}'");
		if($chk){
			$fail_count++;
			continue; //실행취소
		}
		
		//총무게
		$total_weight = $item['wr_10'] * $excel['wr_11'];

		# 환율 정보 불러오기
		$exchange = fnGetExcharge($excel['wr_15']);
		$wr_exchange_rate = str_replace(",","",$exchange['rate']);

		// 과세 / 면세 조회
		$wr_taxType = $excel['wr_37'];
		if(isDefined($item['taxType']) == true){
			$wr_taxType = $item['taxType'];
		}
		
		// WM , WM CA의 경우 창고에 따른 수수료률이 다르다고 했음으로 창고 선택시 다시 한번 수수료1 계산을 진행
		// 해당 내용 담당자가 변경됨으로 인해 다시 한번 필요 -> 해당 내용과 다를 경우 상품 수수료 입력부분 수정 필요
//		if($excel['wr_18'] == "WM" || $excel['wr_18'] == "WM CA" ){
//			if(isDefined($item_fee['product_fee']) == true){
//				$excel['wr_35'] = ($excel['wr_14'] * $item_fee['product_fee'])/100;
//			}else{
//				$excel['wr_35'] = 0;
//			}
//		}

    $delivery_fee2 = 0;
    # 서비스타입 0003 일 경우 추가배송비 자동기입
    if ($excel['wr_20'] === '0003') {
      $delivery_fee2 = 9000;
    }


    $sql = "insert into g5_sales0_list set 
		mb_id = '{$item['mb_id']}',
		wr_id = '{$wr_id_val}',
		wr_product_id = '{$item['wr_id']}',
		wr_product_nm = '".addslashes($excel['wr_17'])."',
		wr_domain = '{$excel['wr_18']}',
		wr_date = '{$_POST['wr_date']}',
		wr_ori_order_num = '".$excel['ori_order_num']."',
		wr_order_num = '{$excel['wr_subject']}',
		wr_mb_id = '{$excel['wr_1']}',
		wr_mb_name = '".addslashes($excel['wr_2'])."',
		wr_zip = '".addslashes($excel['wr_8'])."',
		wr_addr1 = '".addslashes($excel['wr_3'])."',
		wr_addr2 = '".addslashes($excel['wr_4'])."',
		wr_city = '".addslashes($excel['wr_5'])."',
		wr_ju = '".addslashes($excel['wr_6'])."',
		wr_country = '".addslashes($excel['wr_7'])."',
		wr_tel = '".addslashes($excel['wr_9'])."',
		wr_deli_nm = '".addslashes($excel['wr_27'])."',
		wr_deli_addr1 = '".addslashes($excel['wr_28'])."',
		wr_deli_addr2 = '".addslashes($excel['wr_29'])."',
		wr_deli_city = '".addslashes($excel['wr_30'])."',
		wr_deli_ju = '".addslashes($excel['wr_31'])."',
		wr_deli_country = '".addslashes($excel['wr_32'])."',
		wr_deli_zip = '".addslashes($excel['wr_33'])."',
		wr_deli_tel = '".addslashes($excel['wr_34'])."',
		wr_code = '".addslashes($excel['wr_16'])."',
		wr_ea = '{$excel['wr_11']}',
		wr_box = '{$excel['wr_12']}',
		wr_paymethod = '".addslashes($excel['wr_24'])."',
		wr_danga = '{$excel['wr_13']}',
		wr_singo = '{$excel['wr_14']}',
		wr_tax = '{$excel['wr_22']}',
		wr_shipping_price = '{$excel['wr_23']}',
		wr_fee1 = '{$excel['wr_35']}',
		wr_fee2 = '{$excel['wr_36']}',
		wr_taxType = '{$wr_taxType}',
		wr_exchange_rate = '".$wr_exchange_rate."',
		wr_sales_fee_type = '{$item_fee['fee_type']}',
		wr_sales_fee = '{$item_fee['product_fee']}',
		wr_currency = '{$excel['wr_15']}',
		wr_weight1 = '{$item['wr_10']}',
		wr_weight2 = '{$total_weight}',
		wr_weight3 = '{$item['wr_weight3']}',
		wr_weight_dan = '{$item['wr_11']}',
		wr_hscode = '{$item['wr_12']}',
		wr_make_country = '{$item['wr_13']}',
		wr_delivery = '',
		wr_delivery_fee = '',
		wr_delivery_fee2 = '{$delivery_fee2}',
		wr_email = '{$excel['wr_10']}',
		wr_servicetype = '{$excel['wr_20']}',
		wr_packaging = '',
		wr_country_code = '',
		wr_name2 = '".addslashes($item['wr_3'])."',
		wr_etc = '".addslashes($excel['wr_21'])."',
		wr_warehouse = '{$wr_warehouse}',
		wr_chk = 1
		
		";
		//echo $sql."<br>";
		sql_query($sql, true);
		
		$seq = sql_insert_id();
		
		//재고 체크 후 배열담기 하단에서 발주/출고 처리
		if($item[$stock_field] < $excel['wr_11']) {
			$add_seq1[] .= $seq;
		} else {
			$add_seq2[] .= $seq;
		}
	
	
	}

}

//세트상품은 주문번호에 다시 문자열 추가 24.03.02
if($excel['wr_18'] == "Ebay" || $excel['wr_18'] == "Ebay-dodoskin") {
		
//24.1.12 EBAY 합배송은 첫번째 열에 인적사항만 들어가있어 해당 정보만 추출하고 삭제처리해야됨 

$query = "SELECT *, COUNT(*) AS cnt FROM g5_sales0_list GROUP BY wr_order_num HAVING cnt > 1";
$result = sql_query($query);

while ($row = sql_fetch_array($result)) {
	
	$ordernum = $row['wr_order_num'];
	$cnt = $row['cnt'];
	$wr_2 = $row['wr_2'];
	
	for ($i = 1; $i <= $cnt; $i++) {
		// 중복 주문번호 뒤에 문자열 추가 및 업데이트
		$newOrderNumber = $row['wr_order_num'] . chr(64 + $i); // A, B, C, ...
		
		
		$updateQuery = "UPDATE g5_sales0_list SET wr_order_num = '$newOrderNumber'
		
		WHERE wr_order_num = '$ordernum' LIMIT 1";
		
		sql_query($updateQuery, true);
	}
	
	//합배송 업데이트 완료 후 첫번째 열 삭제(문자열 붙지 않은것)
	@sql_query("delete from g5_sales0_list WHERE wr_order_num = '$ordernum' LIMIT 1");
}

} else {

	$query = "SELECT *, COUNT(*) AS cnt FROM g5_sales0_list GROUP BY wr_order_num HAVING cnt > 1";
	$result = sql_query($query);

	while ($row = sql_fetch_array($result)) {
		
		
		$ordernum = $row['wr_order_num'];
		$cnt = $row['cnt'];
		$wr_2 = $row['wr_2'];

		for ($i = 1; $i <= $cnt; $i++) {
			// 중복 주문번호 뒤에 문자열 추가 및 업데이트
			$newOrderNumber = $row['wr_order_num'] . chr(64 + $i); // A, B, C, ...
			$updateQuery = "UPDATE g5_sales0_list SET wr_order_num = '$newOrderNumber'
		
			WHERE wr_order_num = '$ordernum' LIMIT 1";
			sql_query($updateQuery, true);
		}
	}

}

//재고없는경우 > 발주등록으로
$add_sql1 = implode(',', $add_seq1);
$sql = "select * from g5_sales0_list where seq IN({$add_sql1})";
$rst = sql_query($sql);
while($row=sql_fetch_array($rst)){

  $delivery_fee2 = 0;
  # 서비스타입 0003 일 경우 추가배송비 자동기입
  if ($row['wr_servicetype'] === '0003') {
    $delivery_fee2 = 9000;
  }

  $sql2 = "insert into g5_sales1_list set 
		mb_id = '{$row['mb_id']}',
		wr_id = '{$row['seq']}',
		sales0_id = '{$row['seq']}',
		wr_product_id = '{$row['wr_product_id']}',
		wr_product_nm = '".addslashes($row['wr_product_nm'])."',
		wr_domain = '{$row['wr_domain']}',
		wr_date = '{$row['wr_date']}',
		wr_ori_order_num = '".$row['wr_ori_order_num']."',
		wr_order_num = '{$row['wr_order_num']}',
		wr_mb_id = '{$row['wr_mb_id']}',
		wr_mb_name = '".addslashes($row['wr_mb_name'])."',
		wr_zip = '".addslashes($row['wr_zip'])."',
		wr_addr1 = '".addslashes($row['wr_addr1'])."',
		wr_addr2 = '".addslashes($row['wr_addr2'])."',
		wr_city = '".addslashes($row['wr_city'])."',
		wr_ju = '".addslashes($row['wr_ju'])."',
		wr_country = '".addslashes($row['wr_country'])."',
		wr_tel = '".addslashes($row['wr_tel'])."',
		wr_deli_nm = '".addslashes($row['wr_deli_nm'])."',
		wr_deli_addr1 = '".addslashes($row['wr_deli_addr1'])."',
		wr_deli_addr2 = '".addslashes($row['wr_deli_addr2'])."',
		wr_deli_city = '".addslashes($row['wr_deli_city'])."',
		wr_deli_ju = '".addslashes($row['wr_deli_ju'])."',
		wr_deli_country = '".addslashes($row['wr_deli_country'])."',
		wr_deli_zip = '".addslashes($row['wr_deli_zip'])."',
		wr_deli_tel = '".addslashes($row['wr_deli_tel'])."',
		wr_code = '".addslashes($row['wr_code'])."',
		wr_ea = '{$row['wr_ea']}',
		wr_box = '{$row['wr_box']}',
		wr_paymethod = '".addslashes($row['wr_paymethod'])."',
		wr_danga = '{$row['wr_danga']}',
		wr_singo = '{$row['wr_singo']}',
		wr_tax = '{$row['wr_tax']}',
		wr_shipping_price = '{$row['wr_shipping_price']}',
		wr_fee1 = '{$row['wr_fee1']}',
		wr_fee2 = '{$row['wr_fee2']}',
		wr_taxType = '{$row['wr_taxType']}',
		wr_exchange_rate = '{$row['wr_exchange_rate']}',
		wr_sales_fee_type = '{$row['wr_sales_fee_type']}',
		wr_sales_fee = '{$row['product_fee']}',
		wr_currency = '{$row['wr_currency']}',
		wr_weight1 = '{$row['wr_weight1']}',
		wr_weight2 = '{$row['wr_weight2']}',
		wr_weight3 = '{$row['wr_weight3']}',
		wr_weight_dan = '{$row['wr_weight_dan']}',
		wr_hscode = '{$row['wr_hscode']}',
		wr_make_country = '{$row['wr_make_country']}',
		wr_delivery = '{$row['wr_delivery']}',
		wr_delivery_fee = '{$row['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$delivery_fee2}',
		wr_email = '{$row['wr_email']}',
		wr_servicetype = '{$row['wr_servicetype']}',
		wr_packaging = '{$row['wr_packaging']}',
		wr_country_code = '{$row['wr_country_code']}',
		wr_name2 = '".addslashes($row['wr_name2'])."',
		wr_etc = '".addslashes($row['wr_etc'])."',
		wr_set_sku = '".addslashes($row['wr_set_sku'])."',
		wr_warehouse = '{$wr_warehouse}',
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
		sql_query($sql2, true);
	
}

//재고있을 경우 출고등록 > 입고자료 가져오기 (24.05.20 다시 변경)
//재고있을 경우 출고등록 > 입고자료 가져오기 (24.05.21 다시 변경)
//재고있을 경우 출고등록 > 입고자료 가져오기 (24.05.24 다시 변경)
//재고있을 경우 출고등록 > 입고자료 가져오기 (24.05.26 다시 변경)
$add_sql2 = implode(',', $add_seq2);
$sql = "select * from g5_sales0_list where seq IN({$add_sql2})";
$rst = sql_query($sql);
while($row=sql_fetch_array($rst)){
	
		//재고를 차감하고, 재고가 부족하면 다시 재고부족으로
		$item = sql_fetch("select {$stock_field},wr_22 from g5_write_product where wr_id = '{$row['wr_product_id']}'");
		
		if($item[$stock_field] < $row['wr_ea']) {
			$add_seq3[] .= $row['seq'];
			continue;
			
		} else {
			
		//재고가 있는 경우 처리 
		
		$rack = sql_fetch("SELECT b.seq, total_stock
		FROM g5_rack b
		LEFT JOIN (
			SELECT wr_rack, SUM(wr_stock) AS total_stock
			FROM g5_rack_stock
			WHERE wr_warehouse = '{$wr_warehouse}'
			AND wr_product_id = '{$row['wr_product_id']}'
			GROUP BY wr_rack
		) AS a ON a.wr_rack = b.seq
		WHERE a.wr_rack IS NOT NULL 
		AND a.total_stock > 0
		AND b.gc_name != '임시창고'
		ORDER BY b.seq
		LIMIT 1");
		
		//재고를 뺼 랙이 없는 경우 재고없음으로 처리 24.05.27
		if(!$rack['seq']) {
			$add_seq3[] .= $row['seq'];
			continue;
		}	
		$sales1Sql = "SELECT * FROM g5_sales0_list WHERE sales1_id='{$row['seq']}'";
		$sales1Data = sql_fetch($sales1Sql);
		$sql2 = "insert into g5_sales2_list set 
		mb_id = '{$row['mb_id']}',
		wr_id = '{$row['seq']}',
		sales0_id = '{$row['seq']}',
		sales1_id = '{$sales1Data['seq']}',
		wr_domain = '{$row['wr_domain']}',
		wr_date = '{$row['wr_date']}',
		wr_ori_order_num = '".$row['wr_ori_order_num']."',
		wr_order_num = '{$row['wr_order_num']}',
		wr_mb_id = '{$row['wr_mb_id']}',
		wr_mb_name = '".addslashes($row['wr_mb_name'])."',
		wr_zip = '".addslashes($row['wr_zip'])."',
		wr_addr1 = '".addslashes($row['wr_addr1'])."',
		wr_addr2 = '".addslashes($row['wr_addr2'])."',
		wr_city = '".addslashes($row['wr_city'])."',
		wr_ju = '".addslashes($row['wr_ju'])."',
		wr_country = '".addslashes($row['wr_country'])."',
		wr_tel = '".addslashes($row['wr_tel'])."',
		wr_deli_nm = '".addslashes($row['wr_deli_nm'])."',
		wr_deli_addr1 = '".addslashes($row['wr_deli_addr1'])."',
		wr_deli_addr2 = '".addslashes($row['wr_deli_addr2'])."',
		wr_deli_city = '".addslashes($row['wr_deli_city'])."',
		wr_deli_ju = '".addslashes($row['wr_deli_ju'])."',
		wr_deli_country = '".addslashes($row['wr_deli_country'])."',
		wr_deli_zip = '".addslashes($row['wr_deli_zip'])."',
		wr_deli_tel = '".addslashes($row['wr_deli_tel'])."',
		wr_code = '".addslashes($row['wr_code'])."',
		wr_ea = '{$row['wr_ea']}',
		wr_box = '{$row['wr_box']}',
		wr_paymethod = '".addslashes($row['wr_paymethod'])."',
		wr_danga = '{$row['wr_danga']}',
		wr_singo = '{$row['wr_singo']}',
		wr_tax = '{$row['wr_tax']}',
		wr_shipping_price = '{$row['wr_shipping_price']}',
		wr_fee1 = '{$row['wr_fee1']}',
		wr_fee2 = '{$row['wr_fee2']}',
		wr_taxType = '{$row['wr_taxType']}',
		wr_exchange_rate = '{$row['wr_exchange_rate']}',
		wr_sales_fee_type = '{$row['wr_sales_fee_type']}',
		wr_sales_fee = '{$row['product_fee']}',
		wr_currency = '{$row['wr_currency']}',
		wr_weight1 = '{$row['wr_weight1']}',
		wr_weight2 = '{$row['wr_weight2']}',
		wr_weight_dan = '{$row['wr_weight_dan']}',
		wr_hscode = '{$row['wr_hscode']}',
		wr_make_country = '{$row['wr_make_country']}',
		wr_delivery = '{$row['wr_delivery']}',
		wr_delivery_fee = '{$row['wr_delivery_fee']}',
		wr_order_price = '{$item['wr_22']}',
		wr_email = '{$row['wr_email']}',
		wr_servicetype = '{$row['wr_servicetype']}',
		wr_packaging = '{$row['wr_packaging']}',
		wr_country_code = '{$row['wr_country_code']}',
		wr_name2 = '".addslashes($row['wr_name2'])."',
		wr_etc = '".addslashes($row['wr_etc'])."',
		wr_date2 = '{$_POST['wr_date2']}',
		wr_date3 = '{$_POST['wr_date2']}',
		wr_rack = '{$row['wr_rack']}',
		wr_warehouse_etc = '{$row['wr_warehouse_etc']}',
		wr_warehouse = '{$wr_warehouse}',
		wr_direct_use = 1,
		wr_product_id = '{$row['wr_product_id']}',
		wr_product_nm = '".addslashes($row['wr_product_nm'])."',
		wr_set_sku = '".addslashes($row['wr_set_sku'])."',
        wr_datetime = '".G5_TIME_YMDHIS."'
		";
		
        sql_query($sql2, true);
		$sales3_id = sql_insert_id();
		
		
		//창고 재고 차감
		sql_query("update g5_write_product set {$stock_field} = {$stock_field} - {$row['wr_ea']} where wr_id = '{$row['wr_product_id']}' limit 1");
		
		//랙 재고 차감 기록
		$sql = "insert into g5_rack_stock set wr_warehouse = '{$wr_warehouse}', wr_rack = '{$rack['seq']}', wr_stock = '-{$row['wr_ea']}', wr_product_id = '{$row['wr_product_id']}', wr_sales3_id = '{$sales3_id}',  wr_mb_id = '{$member['mb_id']}', wr_datetime = '".G5_TIME_YMDHIS."'";
		sql_query($sql);
		
		//출고처리 된 랙번호 기록
		sql_query("update g5_sales2_list set wr_rack = '{$rack['seq']}' where seq = '{$sales3_id}' LIMIT 1");
		
		}
}


//재고없는경우 > 발주등록으로2
$add_sql3 = implode(',', $add_seq3);
$sql = "select * from g5_sales0_list where seq IN({$add_sql3})";
$rst = sql_query($sql);
while($row=sql_fetch_array($rst)){

  $delivery_fee2 = 0;
  # 서비스타입 0003 일 경우 추가배송비 자동기입
  if ($row['wr_servicetype'] === '0003') {
    $delivery_fee2 = 9000;
  }

  $sql2 = "insert into g5_sales1_list set 
		mb_id = '{$row['mb_id']}',
		wr_id = '{$row['wr_id']}',
		wr_product_id = '{$row['wr_product_id']}',
		wr_product_nm = '".addslashes($row['wr_product_nm'])."',
		wr_domain = '{$row['wr_domain']}',
		wr_date = '{$row['wr_date']}',
		wr_ori_order_num = '".$row['wr_ori_order_num']."',
		wr_order_num = '{$row['wr_order_num']}',
		wr_mb_id = '{$row['wr_mb_id']}',
		wr_mb_name = '".addslashes($row['wr_mb_name'])."',
		wr_zip = '".addslashes($row['wr_zip'])."',
		wr_addr1 = '".addslashes($row['wr_addr1'])."',
		wr_addr2 = '".addslashes($row['wr_addr2'])."',
		wr_city = '".addslashes($row['wr_city'])."',
		wr_ju = '".addslashes($row['wr_ju'])."',
		wr_country = '".addslashes($row['wr_country'])."',
		wr_tel = '".addslashes($row['wr_tel'])."',
		wr_deli_nm = '".addslashes($row['wr_deli_nm'])."',
		wr_deli_addr1 = '".addslashes($row['wr_deli_addr1'])."',
		wr_deli_addr2 = '".addslashes($row['wr_deli_addr2'])."',
		wr_deli_city = '".addslashes($row['wr_deli_city'])."',
		wr_deli_ju = '".addslashes($row['wr_deli_ju'])."',
		wr_deli_country = '".addslashes($row['wr_deli_country'])."',
		wr_deli_zip = '".addslashes($row['wr_deli_zip'])."',
		wr_deli_tel = '".addslashes($row['wr_deli_tel'])."',
		wr_code = '".addslashes($row['wr_code'])."',
		wr_ea = '{$row['wr_ea']}',
		wr_box = '{$row['wr_box']}',
		wr_paymethod = '".addslashes($row['wr_paymethod'])."',
		wr_danga = '{$row['wr_danga']}',
		wr_singo = '{$row['wr_singo']}',
		wr_tax = '{$row['wr_tax']}',
		wr_shipping_price = '{$row['wr_shipping_price']}',
		wr_fee1 = '{$row['wr_fee1']}',
		wr_fee2 = '{$row['wr_fee2']}',
		wr_taxType = '{$row['wr_taxType']}',
		wr_exchange_rate = '{$row['wr_exchange_rate']}',
		wr_sales_fee_type = '{$row['wr_sales_fee_type']}',
		wr_sales_fee = '{$row['product_fee']}',
		wr_currency = '{$row['wr_currency']}',
		wr_weight1 = '{$row['wr_weight1']}',
		wr_weight2 = '{$row['wr_weight2']}',
		wr_weight3 = '{$row['wr_weight3']}',
		wr_weight_dan = '{$row['wr_weight_dan']}',
		wr_hscode = '{$row['wr_hscode']}',
		wr_make_country = '{$row['wr_make_country']}',
		wr_delivery = '{$row['wr_delivery']}',
		wr_delivery_fee = '{$row['wr_delivery_fee']}',
		wr_delivery_fee2 = '{$delivery_fee2}',
		wr_email = '{$row['wr_email']}',
		wr_servicetype = '{$row['wr_servicetype']}',
		wr_packaging = '{$row['wr_packaging']}',
		wr_country_code = '{$row['wr_country_code']}',
		wr_name2 = '".addslashes($row['wr_name2'])."',
		wr_etc = '".addslashes($row['wr_etc'])."',
		wr_set_sku = '".addslashes($row['wr_set_sku'])."',
		wr_warehouse = '{$wr_warehouse}',
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
		sql_query($sql2, true);
	
}

//print_r($add_seq1);
//echo "<br>";
//print_r($add_seq2);

//출고창고가 미국일때 재고부족건은 발주등록으로 가지 않고 미국출고대기 24.05.29 추가

if($wr_warehouse == '3000') {
	$msg = "미국출고대기 : ".(count($add_seq1) + count($add_seq3))."\\n출고등록 : ".(count($add_seq2) - count($add_seq3))."\\n주문번호중복 : ".($fail_count)."\\n처리되었습니다.";
} else {
	$msg = "발주등록 : ".(count($add_seq1) + count($add_seq3))."\\n출고등록 : ".(count($add_seq2) - count($add_seq3))."\\n주문번호중복 : ".($fail_count)."\\n처리되었습니다.";
}
opener_reload();
alert($msg);
?>
<html>
    <head>
        <title>주문정보 기록</title>
        <script>
            // 결제 중 새로고침 방지 샘플 스크립트 (중복입력 방지)
            function noRefresh()
            {
                /* CTRL + N키 막음. */
                if ((event.keyCode == 78) && (event.ctrlKey == true))
                {
                    event.keyCode = 0;
                    return false;
                }
                /* F5 번키 막음. */
                if(event.keyCode == 116)
                {
                    event.keyCode = 0;
                    return false;
                }
            }

            document.onkeydown = noRefresh ;
        </script>
    </head>
</html>