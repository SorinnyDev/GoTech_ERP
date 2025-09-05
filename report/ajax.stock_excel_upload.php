<?
include_once('../common.php');
//
//error_reporting( E_ALL );
//ini_set( "display_errors", 1 );

// 제품이 많을 경우 대비 설정변경
set_time_limit ( 0 );
ini_set('memory_limit', '50M');

$is_upload_file = (isset($_FILES['excelFile']['tmp_name']) && $_FILES['excelFile']['tmp_name']) ? 1 : 0;

$start_row = 3;

if($is_upload_file) {
    $file = $_FILES['excelFile']['tmp_name'];

    include_once(G5_LIB_PATH.'/PHPExcel/IOFactory.php');

    $objPHPExcel = PHPExcel_IOFactory::load($file);
    $sheet = $objPHPExcel->getSheet(0);

    $num_rows = $sheet->getHighestRow();
    $highestColumn = $sheet->getHighestColumn();

	# 창고 배열
	$warehouse_arr = array('1000','3000','4000','5000','6000','7000','8000');

	# 랙 시퀀스 조회 안됐을 경우 랙명 담을 배열
	$no_rack_arr = array();
	# 상품 조회 안될때 SKU 담을 배열
	$no_item_arr = array();
	# 잘못된 창고 번호 입력시 담을 배열
	$no_warehouse_arr = array();

	for ($i = $start_row; $i <= $num_rows; $i++) {
		$rowData = $sheet->rangeToArray('A' . $i . ':' . $highestColumn . $i,
                                            NULL,
                                            TRUE,
                                            FALSE);

		$gc_name = $rowData[0][0];
		$wr_warehouse = $rowData[0][1];
		$wr_1 = $rowData[0][2];
		$wr_subject = $rowData[0][3];
		$wr_stock = $rowData[0][4];

		$error = $gc_name."|".$wr_warehouse."|".$wr_1."|".$wr_subject."|".$wr_stock;

		# 랙 시퀀스 가져오기
		$sql = "SELECT * FROM g5_rack WHERE gc_warehouse = '".trim($wr_warehouse)."' AND gc_name='".trim($gc_name)."'";
		$rack = sql_fetch($sql);
		$wr_rack = $rack['seq'];
		if(!$wr_rack){
			$no_rack_arr[] = $error;
			continue;
		}

		# 상품 시퀀스 가져오기
		$sql = "SELECT * FROM g5_write_product WHERE wr_1='".trim($wr_1)."'";
		$item = sql_fetch($sql);
		$wr_product_id = $item['wr_id'];
		if(!$item['wr_id']){
			$no_item_arr[] = $error;
			continue;
		}

		# 창고 잘못 입력시 
		if(!in_array($wr_warehouse,$warehouse_arr)){
			$no_warehouse_arr[] = $error;
			continue;
		}
	
		$sql = "INSERT INTO g5_rack_stock SET wr_warehouse='".trim($wr_warehouse)."',wr_rack = '".$wr_rack."', wr_stock='".trim($wr_stock)."',wr_product_id='".trim(addslashes($wr_product_id))."',wr_mb_id='".$member['mb_id']."',wr_datetime=NOW(),wr_move_log='직접 변경';";
		sql_query($sql);
	}

	
	# 시간이 지났는데 출고등록 및 출고 처리가 안된 데이터 강제로 출고 처리 등록
	$sql = "SELECT A.* FROM g5_sales2_list A 
			LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num = A.wr_order_num 
			WHERE A.wr_etc_use = '0' AND A.wr_direct_use = '1' AND A.wr_product_id <> 0  
				AND A.wr_date3 <= '2024-08-14' AND B.wr_release_use IS NULL
			ORDER BY B.wr_date4 ASC,A.wr_date3 ASC";
	$rs = sql_query($sql);
	while($row = sql_fetch_array($rs)){

		$sql = "UPDATE g5_sales2_list SET wr_chk='1' WHERE seq='".$row['seq']."'";
		sql_query($sql);

		$sql = "INSERT INTO g5_sales3_list SET \n";
		$sql .= "	mb_id = '".$row['mb_id']."' \n";
		$sql .= ",wr_id = '".$row['seq']."' \n";
		$sql .= ",sales0_id = '".$row['sales0_id']."' \n";
		$sql .= ",sales1_id = '".$row['sales1_id']."' \n";
		$sql .= ",sales2_id = '".$row['seq']."' \n";
		$sql .= ",wr_domain = '".$row['wr_domain']."' \n";
		$sql .= ",wr_date = '".$row['wr_date']."' \n";
		$sql .= ",wr_date2 = '".$row['wr_date2']."' \n";
		$sql .= ",wr_date3 = '".$row['wr_date3']."' \n";
		$sql .= ",wr_date4 = '".$row['wr_date3']."' \n";
		$sql .= ",wr_ori_order_num = '".$row['wr_ori_order_num']."' \n";
		$sql .= ",wr_order_num = '".$row['wr_order_num']."' \n";
		$sql .= ",wr_mb_id = '".$row['wr_mb_id']."' \n";
		$sql .= ",wr_mb_name = '".$row['wr_mb_name']."' \n";
		$sql .= ",wr_zip = '".$row['wr_zip']."' \n";
		$sql .= ",wr_addr1 = '".$row['wr_addr1']."' \n";
		$sql .= ",wr_addr2 = '".$row['wr_addr2']."' \n";
		$sql .= ",wr_city = '".$row['wr_city']."' \n";
		$sql .= ",wr_ju = '".$row['wr_ju']."' \n";
		$sql .= ",wr_country = '".$row['wr_country']."' \n";
		$sql .= ",wr_tel = '".$row['wr_tel']."' \n";
		$sql .= ",wr_deli_nm = '".$row['wr_deli_nm']."' \n";
		$sql .= ",wr_deli_addr1 = '".$row['wr_deli_addr1']."' \n";
		$sql .= ",wr_deli_addr2 = '".$row['wr_deli_addr2']."' \n";
		$sql .= ",wr_deli_city = '".$row['wr_deli_city']."' \n";
		$sql .= ",wr_deli_ju = '".$row['wr_deli_ju']."' \n";
		$sql .= ",wr_deli_country = '".$row['wr_deli_country']."' \n";
		$sql .= ",wr_deli_zip = '".$row['wr_deli_zip']."' \n";
		$sql .= ",wr_deli_tel = '".$row['wr_deli_tel']."' \n";
		$sql .= ",wr_code = '".$row['wr_code']."' \n";
		$sql .= ",wr_ea = '".$row['wr_ea']."' \n";
		$sql .= ",wr_box = '".$row['wr_box']."' \n";
		$sql .= ",wr_danga = '".$row['wr_danga']."' \n";
		$sql .= ",wr_singo = '".$row['wr_singo']."' \n";
		$sql .= ",wr_add_price = '".$row['wr_add_price']."' \n";
		$sql .= ",wr_exchange_rate = '".$row['wr_exchange_rate']."' \n";
		$sql .= ",wr_sales_fee_type = '".$row['wr_sales_fee_type']."' \n";
		$sql .= ",wr_sales_fee = '".$row['wr_sales_fee']."' \n";
		$sql .= ",wr_currency = '".$row['wr_currency']."' \n";
		$sql .= ",wr_weight1 = '".$row['wr_weight1']."' \n";
		$sql .= ",wr_weight2 = '".$row['wr_weight2']."' \n";
		$sql .= ",wr_weight3 = '".$row['wr_weight3']."' \n";
		$sql .= ",wr_weight_dan = '".$row['wr_weight_dan']."' \n";
		$sql .= ",wr_hscode = '".$row['wr_hscode']."' \n";
		$sql .= ",wr_make_country = '".$row['wr_make_country']."' \n";
		$sql .= ",wr_delivery = '".$row['wr_delivery']."' \n";
		$sql .= ",wr_delivery_fee = '".$row['wr_delivery_fee']."' \n";
		$sql .= ",wr_delivery_fee2 = '".$row['wr_delivery_fee2']."' \n";
		$sql .= ",wr_delivery_oil = '".$row['wr_delivery_oil']."' \n";
		$sql .= ",wr_email = '".$row['wr_email']."' \n";
		$sql .= ",wr_servicetype = '".$row['wr_servicetype']."' \n";
		$sql .= ",wr_packaging = '".$row['wr_packaging']."' \n";
		$sql .= ",wr_country_code = '".$row['wr_country_code']."' \n";
		$sql .= ",wr_name2 = '".$row['wr_name2']."' \n";
		$sql .= ",wr_etc = '".$row['wr_etc']."' \n";
		$sql .= ",wr_datetime = '".$row['wr_datetime']."' \n";
		$sql .= ",wr_order_num2 = '".$row['wr_order_num2']."' \n";
		$sql .= ",wr_orderer = '".$row['wr_orderer']."' \n";
		$sql .= ",wr_order_ea = '".$row['wr_order_ea']."' \n";
		$sql .= ",wr_order_price = '".$row['wr_order_price']."' \n";
		$sql .= ",wr_order_fee = '".$row['wr_order_fee']."' \n";
		$sql .= ",wr_order_total = '".$row['wr_order_total']."' \n";
		$sql .= ",wr_order_traking = '".$row['wr_order_traking']."' \n";
		$sql .= ",wr_order_etc = '".$row['wr_order_etc']."' \n";
		$sql .= ",wr_warehouse = '".$row['wr_warehouse']."' \n";
		$sql .= ",wr_rack = '".$row['wr_rack']."' \n";
		$sql .= ",wr_warehouse_etc = '".$row['wr_warehouse_etc']."' \n";
		$sql .= ",wr_release_etc = '".$row['wr_release_etc']."' \n";
		$sql .= ",wr_release_traking = '".$row['wr_release_traking']."' \n";
		$sql .= ",wr_direct_use = '".$row['wr_direct_use']."' \n";
		$sql .= ",wr_misu = '".$row['wr_misu']."' \n";
		$sql .= ",wr_release_use = '1' \n";
		$sql .= ",wr_release_date = NOW() \n";
		$sql .= ",wr_release_mbid = '".$row['wr_release_mbid']."' \n";
		$sql .= ",wr_product_id = '".$row['wr_product_id']."' \n";
		$sql .= ",wr_product_nm = '".$row['wr_product_nm']."' \n";
		$sql .= ",wr_cal_chk = '".$row['wr_cal_chk']."' \n";
		$sql .= ",wr_hab_x = '".$row['wr_hab_x']."' \n";
		$sql .= ",wr_hab_y = '".$row['wr_hab_y']."' \n";
		$sql .= ",wr_hab_z = '".$row['wr_hab_z']."' \n";
		$sql .= ",wr_weight_sum1 = '".$row['wr_weight_sum1']."' \n";
		$sql .= ",wr_weight_sum2 = '".$row['wr_weight_sum2']."' \n";
		$sql .= ",wr_weight_sum3 = '".$row['wr_weight_sum3']."' \n";
		$sql .= ",wr_excel_release = '".$row['wr_excel_release']."' \n";
		$sql .= ",wr_excel_mbid = '".$row['wr_excel_mbid']."' \n";
		$sql .= ",wr_set_sku = '".$row['wr_set_sku']."'";
		sql_query($sql);
	}

	# 임시창고 재고 업데이트
	$sql = "SELECT wr_product_id,SUM(wr_stock) AS temp_stock,SUM(wr_stock) AS sales_stock FROM g5_temp_warehouse GROUP BY wr_product_id";
	$rs = sql_query($sql);
	while($row = sql_fetch_array($rs)){

		# 출고전 재고
		$sql = "SELECT SUM(A.wr_ea) AS sales_ea,SUM(A.wr_order_ea) AS temp_ea \n";
		$sql .= "FROM g5_sales2_list A \n";
		$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num=A.wr_order_num \n";
		$sql .= "WHERE A.wr_direct_use='0'AND  A.wr_etc_use='0' AND IFNULL(B.wr_release_use,0)='0' AND A.wr_product_id='".$row['wr_product_id']."'";
		$salesData = sql_fetch($sql);
		
		$wr_37 = $row['temp_stock'];
		$wr_37_real = (int)$row['temp_stock'] + $salesData['sales_ea'];

		$sql = "UPDATE g5_write_product SET wr_37 = ".$wr_37.", wr_37_real=".$wr_37_real." WHERE wr_id='".$row['wr_product_id']."'";
		sql_query($sql);

	}

	# 실제 재고 상품에 업데이트
	$sql = "UPDATE g5_write_product A \n";
	$sql .= "LEFT OUTER JOIN ( \n";
	$sql .= "	SELECT wr_product_id \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='1000',wr_stock,0)),0) AS kor_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='3000',wr_stock,0)),0) AS usa_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='4000',wr_stock,0)),0) AS fba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='5000',wr_stock,0)),0) AS wfba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='6000',wr_stock,0)),0) AS ufba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='7000',wr_stock,0)),0) AS kor_re_ea \n";
	$sql .= "		,IFNULL(SUM(IF(wr_warehouse='8000',wr_stock,0)),0) AS usa_re_ea \n";
	$sql .= "	FROM g5_rack_stock GROUP BY wr_product_id \n";
	$sql .= ")B ON B.wr_product_id=A.wr_id \n";
	$sql .= "SET  \n";
	$sql .= "	wr_32_real = IFNULL(kor_ea,0) \n";
	$sql .= "	,wr_36_real = IFNULL(usa_ea,0) \n";
	$sql .= "	,wr_42_real = IFNULL(fba_ea,0) \n";
	$sql .= "	,wr_43_real = IFNULL(wfba_ea,0) \n";
	$sql .= "	,wr_44_real = IFNULL(ufba_ea,0) \n";
	$sql .= "	,wr_40_real = IFNULL(kor_re_ea,0) \n";
	$sql .= "	,wr_41_real = IFNULL(usa_re_ea,0)";
	sql_query($sql);

	# 출고 예정 데이터 입력
	$sql = "SELECT A.* FROM g5_sales2_list A \n";
	$sql .= "LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num=A.wr_order_num \n";
	$sql .= "WHERE A.wr_etc_use='0' AND A.wr_direct_use='1' AND IFNULL(B.wr_release_use,0)='0' AND A.wr_product_id <> 0";
	$rs = sql_query($sql);
	while($row = sql_fetch_array($rs)){
		if($row['wr_product_id']){
			$sql = "INSERT INTO g5_rack_stock SET wr_sales1_id='".$row['sales0_id']."',wr_sales2_id='".$row['sales1_id']."',wr_sales3_id='".$row['seq']."',wr_warehouse='".$row['wr_warehouse']."',wr_rack='".$row['wr_rack']."',wr_stock='-".$row['wr_ea']."',wr_product_id='".$row['wr_product_id']."',wr_mb_id='".$row['mb_id']."',wr_datetime=NOW(),wr_move_log=''";
			sql_query($sql);
		}
	}

	# 상품 데이터에 실재고 - 출고 예정 재고 업데이트
	$sql = "UPDATE g5_write_product A \n";
	$sql .= "LEFT OUTER JOIN( \n";
	$sql .= "	SELECT A.wr_product_id \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='1000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS kor_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='3000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS usa_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='4000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS fba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='5000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS wfba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='6000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS ufba_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='7000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS kor_re_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_warehouse='8000' AND A.wr_direct_use='1',A.wr_ea,0)),0) AS usa_re_ea \n";
	$sql .= "		,IFNULL(SUM(IF(A.wr_direct_use='0',A.wr_ea,0)),0) AS temp_ea \n";
	$sql .= "	FROM g5_sales2_list A \n";
	$sql .= "	LEFT OUTER JOIN g5_sales3_list B ON B.wr_order_num=A.wr_order_num \n";
	$sql .= "	WHERE IFNULL(B.wr_release_use,0) = 0 AND A.wr_etc_use = '0' AND A.wr_order_num NOT LIKE 'ETC_%' \n";
	$sql .= "		AND A.wr_product_id <> 0 \n";
	$sql .= "	GROUP BY wr_product_id \n";
	$sql .= ")B ON B.wr_product_id=A.wr_id \n";
	$sql .= "SET  \n";
	$sql .= "	wr_32 = wr_32_real - IFNULL(B.kor_ea,0), \n";
	$sql .= "	wr_36 = wr_36_real - IFNULL(B.usa_ea,0), \n";
	$sql .= "	wr_42 = wr_42_real - IFNULL(B.fba_ea,0), \n";
	$sql .= "	wr_43 = wr_43_real - IFNULL(B.wfba_ea,0), \n";
	$sql .= "	wr_44 = wr_44_real - IFNULL(B.ufba_ea,0), \n";
	$sql .= "	wr_40 = wr_40_real - IFNULL(B.kor_re_ea,0), \n";
	$sql .= "	wr_41 = wr_41_real - IFNULL(B.usa_re_ea,0), \n";
	$sql .= "	wr_37 = wr_37_real - IFNULL(B.temp_ea,0);";
	sql_query($sql);

}

//echo json_encode(array("no_rack_arr"=>$no_rack_arr,"no_item_arr"=>$no_item_arr,"no_warehouse_arr"=>$no_warehouse_arr));
//exit;
?>