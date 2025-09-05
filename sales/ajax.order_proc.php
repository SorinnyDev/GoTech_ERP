<?
include_once("./_common.php");

# 결과 리턴
$ret_arr = array();
$ret_arr['ret_code'] = true;

# 매출등록 정보 있을 경우 수정
if($sales0_id){
	$sql = "UPDATE g5_sales0_list SET \n";
	$sql .= "	wr_mb_id='".$wr_mb_id."' \n";
	$sql .= "	,wr_mb_name='".$wr_mb_name."' \n";
	$sql .= "	,wr_zip='".$wr_zip."' \n";
	$sql .= "	,wr_addr1='".$wr_addr1."' \n";
	$sql .= "	,wr_addr2='".$wr_addr2."' \n";
	$sql .= "	,wr_city='".$wr_city."' \n";
	$sql .= "	,wr_ju='".$wr_ju."' \n";
	$sql .= "	,wr_country='".$wr_country."' \n";
	$sql .= "	,wr_tel='".$wr_tel."' \n";
	if($wr_box != ""){
		$sql .= "	,wr_box='".$wr_box."' \n";
	}
	$sql .= "	,wr_danga='".$wr_danga."' \n";
	$sql .= "	,wr_singo='".$wr_singo."' \n";
	$sql .= "	,wr_currency='".$wr_currency."' \n";
	if($wr_weight1 != ""){
		$sql .= "	,wr_weight1='".$wr_weight1."' \n";
	}
	if($wr_weight2 != ""){
		$sql .= "	,wr_weight2='".$wr_weight2."' \n";
	}
	if($wr_weight3 != ""){
		$sql .= "	,wr_weight3='".$wr_weight3."' \n";
	}
	if($wr_weight_dan != ""){
		$sql .= "	,wr_weight_dan='".$wr_weight_dan."' \n";
	}
	if($wr_hscode != ""){
		$sql .= "	,wr_hscode='".$wr_hscode."' \n";
	}
	if($wr_make_country != ""){
		$sql .= "	,wr_make_country='".$wr_make_country."' \n";
	}
	if($wr_delivery != ""){
		$sql .= "	,wr_delivery='".$wr_delivery."' \n";
	}
	if($wr_delivery_fee != ""){
		$sql .= "	,wr_delivery_fee='".$wr_delivery_fee."' \n";
	}
	if($wr_email != ""){
		$sql .= "	,wr_email='".$wr_email."' \n";
	}
	if($wr_servicetype != ""){
		$sql .= "	,wr_servicetype='".$wr_servicetype."' \n";
	}
	if($wr_packaging != ""){
		$sql .= "	,wr_packaging='".$wr_packaging."' \n";
	}
	if($wr_country_code != ""){
		$sql .= "	,wr_country_code='".$wr_country_code."' \n";
	}
	if($wr_delivery_oil != ""){
		$sql .= "	,wr_delivery_oil = '".$wr_delivery_oil."' \n";
	}
	$sql .= "	,wr_etc='".$wr_bigo."' \n";
	$sql .= "WHERE seq='".$sales0_id."'";
	$rs = sql_query($sql);
	if(!$rs){
		$ret_arr['ret_code'] = false;
		$ret_arr['message'] = "데이터 수정에 실패하였습니다.[매출등록]";
	}

}

# 발주등록 정보 있을 경우 수정
if($sales1_id){
	$sql = "UPDATE g5_sales1_list SET \n";
	$sql .= "	wr_mb_id='".$wr_mb_id."' \n";
	$sql .= "	,wr_mb_name='".$wr_mb_name."' \n";
	$sql .= "	,wr_deli_zip='".$wr_deli_zip."' \n";
	$sql .= "	,wr_deli_addr1='".$wr_deli_addr1."' \n";
	$sql .= "	,wr_deli_addr2='".$wr_deli_addr2."' \n";
	$sql .= "	,wr_deli_city='".$wr_deli_city."' \n";
	$sql .= "	,wr_deli_ju='".$wr_deli_ju."' \n";
	$sql .= "	,wr_deli_country='".$wr_deli_country."' \n";
	$sql .= "	,wr_deli_tel='".$wr_deli_tel."' \n";
	$sql .= "	,wr_box='".$wr_box."' \n";
	$sql .= "	,wr_danga='".$wr_danga."' \n";
	$sql .= "	,wr_singo='".$wr_singo."' \n";
	$sql .= "	,wr_currency='".$wr_currency."' \n";
	if($wr_weight1 != ""){
		$sql .= "	,wr_weight1='".$wr_weight1."' \n";
	}
	if($wr_weight2 != ""){
		$sql .= "	,wr_weight2='".$wr_weight2."' \n";
	}
	if($wr_weight3 != ""){
		$sql .= "	,wr_weight3='".$wr_weight3."' \n";
	}
	if($wr_weight_dan != ""){
		$sql .= "	,wr_weight_dan='".$wr_weight_dan."' \n";
	}
	if($wr_hscode != ""){
		$sql .= "	,wr_hscode='".$wr_hscode."' \n";
	}
	if($wr_make_country != ""){
		$sql .= "	,wr_make_country='".$wr_make_country."' \n";
	}
	if($wr_delivery != ""){
		$sql .= "	,wr_delivery='".$wr_delivery."' \n";
	}
	if($wr_delivery_fee != ""){
		$sql .= "	,wr_delivery_fee='".$wr_delivery_fee."' \n";
	}
	if($wr_email != ""){
		$sql .= "	,wr_email='".$wr_email."' \n";
	}
	if($wr_servicetype != ""){
		$sql .= "	,wr_servicetype='".$wr_servicetype."' \n";
	}
	if($wr_packaging != ""){
		$sql .= "	,wr_packaging='".$wr_packaging."' \n";
	}
	if($wr_country_code != ""){
		$sql .= "	,wr_country_code='".$wr_country_code."' \n";
	}
	if($wr_orderer){
		$sql .= "	,wr_orderer='".$wr_orderer."' \n";
	}
	if($wr_date2){
		$sql .= "	,wr_date2='".$wr_date2."' \n";
	}
	if($wr_order_num2){
		$sql .= "	,wr_order_num2='".$wr_order_num2."' \n";
	}
	if($wr_order_traking){
		$sql .= "	,wr_order_traking='".$wr_order_traking."' \n";
	}
	if($wr_order_ea){
		$sql .= "	,wr_order_ea='".$wr_order_ea."' \n";
	}
	if($wr_order_price){
		$sql .= "	,wr_order_price='".$wr_order_price."' \n";
	}
	if($wr_order_fee){
		$sql .= "	,wr_order_fee='".$wr_order_fee."' \n";
	}
	if($wr_order_total){
		$sql .= "	,wr_order_total='".$wr_order_total."' \n";
	}
	if($wr_delivery_oil != ""){
		$sql .= "	,wr_delivery_oil = '".$wr_delivery_oil."' \n";
	}
	$sql .= "	,wr_order_etc='".addslashes($wr_order_etc)."' \n";
	$sql .= "	,wr_etc='".addslashes($wr_bigo)."' \n";
	$sql .= "WHERE seq='".$sales1_id."'";
	$rs = sql_query($sql);

	# 발주등록 데이터 수정 시 상품 마스터의 발주단가 수정
	$sql = "SELECT * FROM g5_write_product WHERE wr_id = '$wr_product_id'";
	$wp = sql_fetch($sql);

	# 이전 값 메타데이터 삽입
	if ($wp['wr_id'] && $wp['wr_22'] !== $wr_order_price) {
		$sql = "INSERT INTO g5_product_metadata SET entity_type = 'g5_write_product', entity_id = '$wr_product_id', `key` = 'before_wr_22', `value` = '".$wp['wr_22']."'";
		sql_query($sql);

		# 값 변경
		$sql = "UPDATE g5_write_product SET wr_22 = '$wr_order_price' WHERE wr_id = '$wr_product_id'";
		sql_query($sql);
	}

	if(!$rs){
		$ret_arr['ret_code'] = false;
		$ret_arr['message'] = "데이터 수정에 실패하였습니다.[발주등록]";
	}
}

# 입고등록 정보 있을 경우 수정
if($sales2_id){
	$sql = "UPDATE g5_sales2_list SET \n";
	$sql .= "	wr_mb_id='".$wr_mb_id."' \n";
	$sql .= "	,wr_mb_name='".$wr_mb_name."' \n";
	$sql .= "	,wr_deli_zip='".$wr_deli_zip."' \n";
	$sql .= "	,wr_deli_addr1='".$wr_deli_addr1."' \n";
	$sql .= "	,wr_deli_addr2='".$wr_deli_addr2."' \n";
	$sql .= "	,wr_deli_city='".$wr_deli_city."' \n";
	$sql .= "	,wr_deli_ju='".$wr_deli_ju."' \n";
	$sql .= "	,wr_deli_country='".$wr_deli_country."' \n";
	$sql .= "	,wr_deli_tel='".$wr_deli_tel."' \n";
	$sql .= "	,wr_box='".$wr_box."' \n";
	$sql .= "	,wr_danga='".$wr_danga."' \n";
	$sql .= "	,wr_singo='".$wr_singo."' \n";
	$sql .= "	,wr_currency='".$wr_currency."' \n";
	if($wr_weight1 != ""){
		$sql .= "	,wr_weight1='".$wr_weight1."' \n";
	}
	if($wr_weight2 != ""){
		$sql .= "	,wr_weight2='".$wr_weight2."' \n";
	}
	if($wr_weight3 != ""){
		$sql .= "	,wr_weight3='".$wr_weight3."' \n";
	}
	if($wr_weight_dan != ""){
		$sql .= "	,wr_weight_dan='".$wr_weight_dan."' \n";
	}
	if($wr_hscode != ""){
		$sql .= "	,wr_hscode='".$wr_hscode."' \n";
	}
	if($wr_make_country != ""){
		$sql .= "	,wr_make_country='".$wr_make_country."' \n";
	}
	if($wr_delivery != ""){
		$sql .= "	,wr_delivery='".$wr_delivery."' \n";
	}
	if($wr_delivery_fee != ""){
		$sql .= "	,wr_delivery_fee='".$wr_delivery_fee."' \n";
	}
	if($wr_email != ""){
		$sql .= "	,wr_email='".$wr_email."' \n";
	}
	if($wr_servicetype != ""){
		$sql .= "	,wr_servicetype='".$wr_servicetype."' \n";
	}
	if($wr_packaging != ""){
		$sql .= "	,wr_packaging='".$wr_packaging."' \n";
	}
	if($wr_country_code != ""){
		$sql .= "	,wr_country_code='".$wr_country_code."' \n";
	}
	if($wr_orderer){
		$sql .= "	,wr_orderer='".$wr_orderer."' \n";
	}
	if($wr_date2){
		$sql .= "	,wr_date2='".$wr_date2."' \n";
	}
	if($wr_order_num2){
		$sql .= "	,wr_order_num2='".$wr_order_num2."' \n";
	}
	if($wr_order_traking){
		$sql .= "	,wr_order_traking='".$wr_order_traking."' \n";
	}
	if($wr_order_ea){
		$sql .= "	,wr_order_ea='".$wr_order_ea."' \n";
	}
	if($wr_order_price){
		$sql .= "	,wr_order_price='".$wr_order_price."' \n";
	}
	if($wr_order_fee){
		$sql .= "	,wr_order_fee='".$wr_order_fee."' \n";
	}
	if($wr_order_total){
		$sql .= "	,wr_order_total='".$wr_order_total."' \n";
	}
	if($wr_date3){
		$sql .= "	,wr_date3='".$wr_date3."' \n";
	}
	if($wr_pay_type){
		$sql .= "	,wr_pay_type='".$wr_pay_type."' \n";
	}
	if($wr_warehouse_price){
		$sql .= "	,wr_warehouse_price='".$wr_warehouse_price."' \n";
	}
	if($wr_misu){
		$sql .= "	,wr_misu='".$wr_misu."' \n";
	}
	if($wr_delivery_oil != ""){
		$sql .= "	,wr_delivery_oil = '".$wr_delivery_oil."' \n";
	}
	$sql .= "	,wr_warehouse_etc='".addslashes($wr_warehouse_etc)."' \n";
	$sql .= "	,wr_order_etc='".addslashes($wr_order_etc)."' \n";
	$sql .= "	,wr_etc='".addslashes($wr_bigo)."' \n";
	$sql .= "WHERE seq='".$sales2_id."'";
	$rs = sql_query($sql);
	if(!$rs){
		$ret_arr['ret_code'] = false;
		$ret_arr['message'] = "데이터 수정에 실패하였습니다.[입고등록]";
	}
}

# 출고등록 정보 있을 경우 수정
if($sales3_id){
	$sql = "UPDATE g5_sales3_list SET \n";
	$sql .= "	wr_mb_id='".$wr_mb_id."' \n";
	$sql .= "	,wr_mb_name='".$wr_mb_name."' \n";
	$sql .= "	,wr_deli_zip='".$wr_deli_zip."' \n";
	$sql .= "	,wr_deli_addr1='".$wr_deli_addr1."' \n";
	$sql .= "	,wr_deli_addr2='".$wr_deli_addr2."' \n";
	$sql .= "	,wr_deli_city='".$wr_deli_city."' \n";
	$sql .= "	,wr_deli_ju='".$wr_deli_ju."' \n";
	$sql .= "	,wr_deli_country='".$wr_deli_country."' \n";
	$sql .= "	,wr_deli_tel='".$wr_deli_tel."' \n";
	$sql .= "	,wr_box='".$wr_box."' \n";
	$sql .= "	,wr_danga='".$wr_danga."' \n";
	$sql .= "	,wr_singo='".$wr_singo."' \n";
	$sql .= "	,wr_currency='".$wr_currency."' \n";
	if($wr_weight1 != ""){
		$sql .= "	,wr_weight1='".$wr_weight1."' \n";
	}
	if($wr_weight2 != ""){
		$sql .= "	,wr_weight2='".$wr_weight2."' \n";
	}
	if($wr_weight3 != ""){
		$sql .= "	,wr_weight3='".$wr_weight3."' \n";
	}
	if($wr_weight_dan != ""){
		$sql .= "	,wr_weight_dan='".$wr_weight_dan."' \n";
	}
	if($wr_hscode != ""){
		$sql .= "	,wr_hscode='".$wr_hscode."' \n";
	}
	if($wr_make_country != ""){
		$sql .= "	,wr_make_country='".$wr_make_country."' \n";
	}
	if($wr_delivery != ""){
		$sql .= "	,wr_delivery='".$wr_delivery."' \n";
	}
	if($wr_delivery_fee != ""){
		$sql .= "	,wr_delivery_fee='".$wr_delivery_fee."' \n";
	}
	if($wr_email != ""){
		$sql .= "	,wr_email='".$wr_email."' \n";
	}
	if($wr_servicetype != ""){
		$sql .= "	,wr_servicetype='".$wr_servicetype."' \n";
	}
	if($wr_packaging != ""){
		$sql .= "	,wr_packaging='".$wr_packaging."' \n";
	}
	if($wr_country_code != ""){
		$sql .= "	,wr_country_code='".$wr_country_code."' \n";
	}
	if($wr_orderer){
		$sql .= "	,wr_orderer='".$wr_orderer."' \n";
	}
	if($wr_date2){
		$sql .= "	,wr_date2='".$wr_date2."' \n";
	}
	if($wr_order_num2){
		$sql .= "	,wr_order_num2='".$wr_order_num2."' \n";
	}
	if($wr_order_traking){
		$sql .= "	,wr_order_traking='".$wr_order_traking."' \n";
	}
	if($wr_order_ea){
		$sql .= "	,wr_order_ea='".$wr_order_ea."' \n";
	}
	if($wr_order_price){
		$sql .= "	,wr_order_price='".$wr_order_price."' \n";
	}
	if($wr_order_fee){
		$sql .= "	,wr_order_fee='".$wr_order_fee."' \n";
	}
	if($wr_order_total){
		$sql .= "	,wr_order_total='".$wr_order_total."' \n";
	}
	if($wr_date3){
		$sql .= "	,wr_date3='".$wr_date3."' \n";
	}
	if($wr_pay_type){
		$sql .= "	,wr_pay_type='".$wr_pay_type."' \n";
	}
	if($wr_warehouse_price){
		$sql .= "	,wr_warehouse_price='".$wr_warehouse_price."' \n";
	}
	if($wr_misu){
		$sql .= "	,wr_misu='".$wr_misu."' \n";
	}
	if($wr_release_traking){
		$sql .= "	,wr_release_traking='".$wr_release_traking."' \n";
	}
	if($wr_date4){
		$sql .= "	,wr_date4='".$wr_date4."' \n";
	}
	if($wr_servicetype){
		$sql .= "	,wr_servicetype='".$wr_servicetype."' \n";
	}
	if($wr_packaging){
		$sql .= "	,wr_packaging='".$wr_packaging."' \n";
	}
	if($wr_box){
		$sql .= "	,wr_box='".$wr_box."' \n";
	}
	if($wr_weight2){
		$sql .= "	,wr_weight2='".$wr_box."' \n";
	}
	if($wr_delivery){
		$sql .= "	,wr_delivery='".$wr_delivery."' \n";
	}
	if($wr_delivery_fee){
		$sql .= "	,wr_delivery_fee='".$wr_delivery_fee."' \n";
	}
	if($wr_delivery_fee2){
		$sql .= "	,wr_delivery_fee2='".$wr_delivery_fee2."' \n";
	}
	if($wr_delivery_total){
		$sql .= "	,wr_delivery_total='".$wr_delivery_total."' \n";
	}
	if($wr_deli_country){
		$sql .= "	,wr_deli_country='".$wr_deli_country."' \n";
	}
	if($wr_deli_ju){
		$sql .= "	,wr_deli_ju='".$wr_deli_ju."' \n";
	}
	if($wr_deli_city){
		$sql .= "	,wr_deli_city='".$wr_deli_city."' \n";
	}
	if($wr_add_price){
		$sql .= "	,wr_add_price='".$wr_add_price."' \n";
	}
	if($wr_deli_nm){
		$sql .= "	,wr_deli_nm='".$wr_deli_nm."' \n";
	}
	if($wr_deli_tel){
		$sql .= "	,wr_deli_tel='".$wr_deli_tel."' \n";
	}
	if($wr_deli_zip){
		$sql .= "	,wr_deli_zip='".$wr_deli_zip."' \n";
	}
	if($wr_deli_addr1){
		$sql .= "	,wr_deli_addr1='".$wr_deli_addr1."' \n";
	}
	if($wr_deli_addr2){
		$sql .= "	,wr_deli_addr2='".$wr_deli_addr2."' \n";
	}
	if($wr_delivery_oil != ""){
		$sql .= "	,wr_delivery_oil = '".$wr_delivery_oil."' \n";
	}
	$sql .= "	,wr_release_etc='".addslashes($wr_release_etc)."' \n";
	$sql .= "	,wr_warehouse_etc='".addslashes($wr_warehouse_etc)."' \n";
	$sql .= "	,wr_order_etc='".addslashes($wr_order_etc)."' \n";
	$sql .= "	,wr_etc='".addslashes($wr_bigo)."' \n";
	$sql .= "WHERE seq='".$sales3_id."'";
	$rs = sql_query($sql);
	if(!$rs){
		$ret_arr['ret_code'] = false;
		$ret_arr['message'] = "데이터 수정에 실패하였습니다.[출고등록]";
	}
}

echo json_encode($ret_arr);
exit;
?>