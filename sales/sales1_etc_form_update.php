<?php
include_once('./_common.php');

if ($is_guest)
    alert('로그인 후 이용하세요.');

if(!$_POST['wr_id']) alert('잘못 된 접근입니다.');


$item = sql_fetch("select * from g5_write_product where wr_id = '{$wr_id}' ");
$wr_weight2 = (int)$wr_weight1 * (int)$wr_order_ea;

//기타발주 주문번호는 ETC_{갯수}로 
//$max=sql_fetch("select count(*) as cnt from g5_sales1_list where wr_etc_chk = 1");
//24.03.19 변경, 입력시간에따라 중복되는 경우가 발생함. time으로 대체
$order_num = "ETC_".substr(time(), 5, 5)."_".$wr_domain;

$chk = sql_fetch("select * from g5_sales1_list where wr_order_num = '$order_num'");

if($chk)
	$order_num = "ETC_".substr(time(), 5, 5).rand(0,10)."_".$wr_domain;

# 안전재고
$ed_date = date("Y-m-d");
$st_date = date("Y-m-d", strtotime("-3 months"));
$product_id = $item['wr_id'];

# 안전재고 수량
$sql = "
      SELECT
      SUM(G0.wr_ea) AS total_sales_ea
      FROM g5_sales3_list G0
      LEFT JOIN g5_write_product WP ON WP.wr_id = G0.wr_product_id
      WHERE wr_release_use = '1'
      AND G0.wr_date4 BETWEEN '$st_date' AND '$ed_date' 
      AND G0.wr_product_id = '{$product_id}'
      GROUP BY
      G0.wr_product_id    
    ";

$safe_result = sql_fetch($sql);
$safe_ea = round(($safe_result['total_sales_ea'] / 3) * 2);


$sql = "insert into g5_sales1_list set 
		mb_id = '{$member['mb_id']}',
		wr_id = '{$wr_id}',
		wr_product_id = '{$wr_id}',
		wr_product_nm = '".addslashes($item['wr_subject'])."',
		wr_domain = '{$wr_domain}',
		wr_date = '".G5_TIME_YMD."',
		wr_order_num = '{$order_num}',
		wr_order_ea = '{$wr_order_ea}',
		wr_order_price = '{$wr_order_price}',
		wr_order_fee = '{$wr_order_fee}',
		wr_order_total = '{$wr_order_total}',
		wr_order_traking = '{$wr_order_traking}',
		wr_order_etc = '{$wr_order_etc}',
		wr_order_num2 = '{$wr_order_num2}',
		wr_orderer = '{$wr_orderer}',
		wr_safe_ea = '{$safe_ea}',
		wr_code = '".addslashes($item['wr_1'])."',
		wr_ea = '{$wr_order_ea}',
		wr_box = '{$wr_order_ea}',
	
		wr_weight1 = '{$wr_weight1}',
		wr_weight2 = '{$wr_weight2}',
		wr_weight_dan = '{$wr_weight_dan}',
		wr_hscode = '{$wr_hscode}',
		wr_make_country = '{$wr_make_country}',
		wr_etc_chk = 1,
		wr_date2 = '{$_POST['wr_date2']}'
		
		";
if(sql_query($sql, true)){
  $seq = sql_insert_id();

	# 상품의 발주단가 변경
	$sql = "UPDATE g5_write_product SET wr_22='".$wr_order_price."' WHERE wr_id='".$wr_id."'";
	sql_query($sql);

  # 메타데이터 결제카드 수정
  if ($metadata_code_card) {
    $sql = "SELECT * FROM g5_sales_metadata WHERE entity_type = 'g5_sales1_list' AND entity_id = '{$seq}' AND `key` = 'code_card'";
    $card = sql_fetch($sql);

    if (!empty($card['id'])) {
      $sql = "UPDATE g5_sales_metadata SET `value` = '".$metadata_code_card."' WHERE id = {$card['id']}";
    } else {
      $sql = "INSERT INTO g5_sales_metadata SET entity_type = 'g5_sales1_list', entity_id = '{$seq}', `key` = 'code_card', `value` = '".$metadata_code_card."'";
    }
    sql_query($sql);
  }


  alert_close('기타 발주 등록이 완료되었습니다.');
} else {
	alert('처리 중 오류가 발생했습니다.');
}