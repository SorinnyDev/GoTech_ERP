<?php
include_once('./_common.php');

if ($is_guest) die('n');

# 수량 변경이 있는지 체크
$chg_flag = false; // 주문 수량 변경 flag
$stock_flag = false; // 재고 변경 flag
$upColumn = "";

# 매출등록 조회
$sql = "SELECT * FROM g5_sales0_list WHERE seq='".$seq."'";
$row = sql_fetch($sql);

# 출고등록 조회
$sql = "SELECT * FROM g5_sales3_list WHERE sales0_id='".$seq."'";
$sales3 = sql_fetch($sql);

# 입고등록 조회
$sql = "SELECT * FROM g5_sales2_list WHERE sales0_id='".$seq."'";
$sales2 = sql_fetch($sql);

# 발주등록 조회
$sql = "SELECT * FRO mg5_sales1_list WHERE sales0_id='".$seq."'";
$sales1 = sql_fetch($sql);

# 재고 정보 불러오기
$field = $storage_arr[$row['wr_warehouse']]['field'];
$sql = "SELECT ".$field." AS product_stock FROM g5_write_product WHERE wr_id='".$row['wr_product_id']."'";
$item = sql_fetch($sql);

# 출고된 주문인지 체크
if($row['wr_ea'] != $wr_ea){
	if($sales3['wr_release_use'] == "1"){
		alert("출고가 완료된 주문입니다.");
		exit;
	}else{
		# 입고가 된 상품인지 체크
		if($sales2['wr_direct_use'] == "0"){
			alert("입고가 된 상품입니다.");
			exit;
		}else if($sales2['wr_direct_use'] == "1"){
			# 변경된 수량의 재고가 있는지 확인
			if($wr_ea > $item['product_stock'] + $row['wr_ea']){
				alert("주문가능 재고수량을 초과하였습니다.");
			}else{
				$stock_flag = true;
				$chg_flag = true;
				$upColumn .= " wr_ea = '".$wr_ea."', ";
			}
		}else if(!$sales2['seq']){
			# 발주 정보 있는지 확인
			if($sales1['seq'] > 0){
				# 발주정보가 있을 경우 발주 수량 확인
				if($sales1['wr_order_ea'] > 0){
					if($sales1['wr_order_ea'] > $wr_ea){
						$chg_flag = true;
						$upColumn .= " wr_ea = '".$wr_ea."', ";
					}else{
						alert("발주 수량을 초과하였습니다.");
						exit;
					}
				}
			}else{
				$chg_flag = true;
				$upColumn .= " wr_ea = '".$wr_ea."', ";
			}
		}
	}
}

$upColumn .= "wr_mb_name = '".addslashes($wr_mb_name)."'
			,wr_danga = '".$wr_danga."'
			,wr_singo = '".$wr_singo."'
			,wr_exchange_rate = '".$wr_exchange_rate."'
			,wr_tel = '".addslashes($wr_tel)."'
			,wr_email = '".addslashes($wr_email)."'
			,wr_city = '".addslashes($wr_city)."'
			,wr_ju = '".addslashes($wr_ju)."'
			,wr_country = '".addslashes($wr_country)."'
			,wr_zip = '".addslashes($wr_zip)."'
			,wr_addr1 = '".addslashes($wr_addr1)."'
			,wr_addr2 = '".addslashes($wr_addr2)."'
			,wr_hscode = '".addslashes($wr_hscode)."'
			,wr_weight3 = '".addslashes($wr_weight3)."'
			,wr_weight2 = '".addslashes($wr_weight2)."'
			,wr_weight1 = '".addslashes($wr_weight1)."'
			,wr_deli_nm = '".addslashes($wr_deli_nm)."'
			,wr_deli_tel = '".addslashes($wr_deli_tel)."'
			,wr_deli_city = '".addslashes($wr_deli_city)."'
			,wr_deli_ju = '".addslashes($wr_deli_ju)."'
			,wr_deli_country = '".addslashes($wr_deli_country)."'
			,wr_deli_zip = '".addslashes($wr_deli_zip)."'
			,wr_deli_addr1 = '".addslashes($wr_deli_addr1)."'
			,wr_deli_addr2 = '".addslashes($wr_deli_addr2)."'
			,wr_delivery = '".addslashes($wr_delivery)."'
			,wr_servicetype = '".addslashes($wr_servicetype)."'
			,wr_delivery_fee = '".$wr_delivery_fee."'
			,wr_delivery_fee2 = '".$wr_delivery_fee2."'
			,wr_delivery_oil = '".$wr_delivery_oil."'
			,mb_id = '".addslashes($wmb_id)."'
			,wr_etc = '".addslashes($wr_etc)."'
			,wr_danga = '".$wr_danga."'
			,wr_singo = '".$wr_singo."'
			,wr_fee1 = '".$wr_fee1."'
			,wr_fee2 = '".$wr_fee2."'";
/*
$sql = "update g5_sales0_list set 
	wr_domain = '{$wr_domain}',
	wr_date = '{$wr_date}',
	wr_order_num = '{$wr_order_num}',
	wr_mb_id = '{$wr_mb_id}',
	wr_mb_name = '{$wr_mb_name}',
	wr_zip = '{$wr_zip}',
	wr_addr1 = '{$wr_addr1}',
	wr_addr2 = '{$wr_addr2}',
	wr_city = '{$wr_city}',
	wr_ju = '{$wr_ju}',
	wr_country = '{$wr_country}',
	wr_tel = '{$wr_tel}',
	wr_code = '{$wr_code}',
	wr_ea = '{$wr_ea}',
	wr_box = '{$wr_box}',
	wr_danga = '{$wr_danga}',
	wr_singo = '{$wr_singo}',
	wr_currency = '{$wr_currency}',
	wr_weight1 = '{$wr_weight1}',
	wr_weight2 = '{$wr_weight2}',
	wr_weight3 = '{$wr_weight3}',
	wr_weight_dan = '{$wr_weight_dan}',
	wr_hscode = '{$wr_hscode}',
	wr_make_country = '{$wr_make_country}',
	wr_delivery = '{$wr_delivery}',
	wr_delivery_fee = '{$wr_delivery_fee}',
	wr_email = '{$wr_email}',
	wr_servicetype = '{$wr_servicetype}',
	wr_packaging = '{$wr_packaging}',
	wr_country_code = '{$wr_country_code}',
	wr_name2 = '{$wr_name2}',
	wr_etc = '{$wr_etc}',
    mb_id = '{$wmb_id}'
	where seq = '{$seq}'
	";
	*/
	$sql = "UPDATE g5_sales0_list SET ";
	$sql .= $upColumn;
	$sql .= "WHERE seq = '".$seq."'";
	if(sql_query($sql, true)) {
        $wr_16 = sql_fetch("select wr_16 from g5_write_sales where wr_subject = '{$wr_order_num}' ")['wr_16'];
        $wr_name = get_member($wmb_id)['mb_name'];

		# 세트 상품의 경우 단가,신고가격, 수수료1,수수료2 수정시 동일하게 적용
		$sql = "SELECT * FROM g5_sales0_list WHERE seq='".$seq."'";
		$row = sql_fetch($sql);
		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales0_list SET ";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num='".$row['wr_ori_order_num']."' AND wr_set_sku='".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		$sql = "UPDATE g5_sales1_list SET ";
		$sql .= $upColumn;
		$sql .= "WHERE sales0_id = '".$seq."'";
		sql_query($sql);
		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales1_list SET ";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num='".$row['wr_ori_order_num']."' AND wr_set_sku='".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		$sql = "UPDATE g5_sales2_list SET ";
		$sql .= $upColumn;
		$sql .= "WHERE sales0_id = '".$seq."'";
		sql_query($sql);
		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales2_list SET ";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num='".$row['wr_ori_order_num']."' AND wr_set_sku='".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		$sql = "UPDATE g5_sales3_list SET ";
		$sql .= $upColumn;
		$sql .= "WHERE sales0_id = '".$seq."'";
		sql_query($sql);
		if(isDefined($row['wr_set_sku']) == true){
			$sql = "UPDATE g5_sales3_list SET ";
			$sql .= "	wr_danga = '".$wr_danga."' \n";
			$sql .= "	,wr_singo = '".$wr_singo."' \n";
			$sql .= "	,wr_fee1 = '".$wr_fee1."' \n";
			$sql .= "	,wr_fee2 = '".$wr_fee2."' \n";
			$sql .= "WHERE wr_ori_order_num='".$row['wr_ori_order_num']."' AND wr_set_sku='".$row['wr_set_sku']."'";
			sql_query($sql);
		}

		# 재고 변경이 일어날 경우 수정
		if($stock_flag == true){
			# 차감된 재고 로그 복원
			$sql = "INSERT INTO g5_rack_stock SET \n";
			$sql .= "	wr_warehouse = '".$row['wr_warehouse']."' \n";
			$sql .= "	,wr_rack = '".$sales2['wr_rack']."' \n";
			$sql .= "	,wr_stock='".$row['wr_ea']."' \n";
			$sql .= "	,wr_product_id='".$row['wr_product_id']."' \n";
			$sql .= "	,wr_sales3_id='".$sales2['seq']."' \n";
			$sql .= "	,wr_mb_id='".$member['mb_id']."' \n";
			$sql .= "	,wr_datetime = NOW() \n";
			$sql .= "	,wr_move_log = '주문 수량 변경으로 인한 재고 복구'";
			sql_query($sql);

			# 변경된 재고 로그 차감
			$sql = "INSERT INTO g5_rack_stock SET \n";
			$sql .= "	wr_warehouse = '".$row['wr_warehouse']."' \n";
			$sql .= "	,wr_rack = '".$sales2['wr_rack']."' \n";
			$sql .= "	,wr_stock = '-".$wr_ea."' \n";
			$sql .= "	,wr_product_id='".$row['wr_product_id']."' \n";
			$sql .= "	,wr_sales3_id='".$sales2['seq']."' \n";
			$sql .= "	,wr_mb_id='".$member['mb_id']."' \n";
			$sql .= "	,wr_move_log='재고차감'";
			sql_query($sql);

			# 재고 수정
			$sql = "UPDATE g5_write_product SET ".$field." = ".$field." + ".$row['wr_ea']." - ".$wr_ea." WHERE wr_id='".$row['wr_product_id']."'";
			sql_query($sql);
		}
        
        sql_query("update g5_write_product set mb_id = '{$wmb_id}', wr_name = '{$wr_name}' where wr_1 = '{$wr_16}' "); 
        sql_query("update g5_write_sales set mb_id = '{$wmb_id}' where wr_subject = '{$wr_order_num}' ");
		die('y');
	} else {
		die('n');
	}
?>