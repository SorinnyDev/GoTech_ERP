<?php
include_once('./_common.php');

// echo "테스트 진행중입니다.<br><br><br>";

// var_dump($_POST);


// exit;

if ($is_guest)
    alert('로그인 후 이용하세요.');


$count_chk_wr_id = (isset($_POST['chk_seq']) && is_array($_POST['chk_seq'])) ? count($_POST['chk_seq']) : 0;

if(!$count_chk_wr_id) alert('최소 1개이상 선택하세요.');

$suc = 0;
$fail = 0;
$msg = $btn_name=="출고등록" ? "출고등록이 완료되었습니다.\\n리스트에서 새로고침해주세요." : "선택삭제가 완료되었습니다.\\n리스트에서 새로고침해주세요."; // msg 초기값

for ($i=0; $i<$count_chk_wr_id; $i++) {
	
    // 출고등록
	if($btn_name=="출고등록"){
        $wr_id_val = isset($_POST['chk_seq'][$i]) ? preg_replace('/[^0-9]/', '', $_POST['chk_seq'][$i]) : 0;

        $excel = sql_fetch("select * from g5_sales2_list where seq = '{$wr_id_val}'");

        $item = sql_fetch("select * from g5_write_product where (wr_1 = '".addslashes($excel['wr_code'])."' or wr_27 = '".addslashes($excel['wr_code'])."' or wr_28 = '".addslashes($excel['wr_code'])."' or wr_29 = '".addslashes($excel['wr_code'])."' or wr_30 = '".addslashes($excel['wr_code'])."' or wr_31 = '".addslashes($excel['wr_code'])."') ");
        
        //담당자가 동시체크 및 등록을 실행처리하는 경우를 대비.
        
        //입고자료 가져오기에서 이미 처리한 항목 중복입력 안되도록 처리
        $chk = sql_fetch("select * from g5_sales2_list where wr_chk = 1 and seq = '{$excel['seq']}'");
        if($chk){
            $fail++;
            continue;
        }
        
        //출고등록에서 주문번호로 한번더 체크하여 중복등록 안되도록. 
        $chk2 = sql_fetch("select * from g5_sales3_list where wr_order_num = '{$excel['wr_order_num']}'");
        if($chk2){
            $fail++;
            continue;
        }
        
        $sql = "insert into g5_sales3_list set 
        mb_id = '{$excel['mb_id']}',
        wr_id = '{$wr_id_val}',
        wr_domain = '{$excel['wr_domain']}',
        wr_date = '{$excel['wr_date']}',
        wr_order_num = '{$excel['wr_order_num']}',
        wr_mb_id = '{$excel['wr_mb_id']}',
        wr_mb_name = '{$excel['wr_mb_name']}',
        wr_zip = '{$excel['wr_zip']}',
        wr_addr1 = '".addslashes($excel['wr_addr1'])."',
        wr_addr2 = '".addslashes($excel['wr_addr2'])."',
        wr_city = '{$excel['wr_city']}',
        wr_ju = '{$excel['wr_ju']}',
        wr_country = '{$excel['wr_country']}',
        wr_tel = '".addslashes($excel['wr_tel'])."',
        wr_code = '".addslashes($excel['wr_code'])."',
        wr_ea = '{$excel['wr_ea']}',
        wr_box = '{$excel['wr_box']}',
        wr_danga = '{$excel['wr_danga']}',
        wr_singo = '{$excel['wr_singo']}',
        wr_currency = '{$excel['wr_15']}',
        wr_weight1 = '{$excel['wr_weight1']}',
        wr_weight2 = '{$excel['wr_weight2']}',
        wr_weight_dan = '{$excel['wr_weight_dan']}',
        wr_hscode = '{$excel['wr_hscode']}',
        wr_make_country = '{$excel['wr_make_country']}',
        wr_delivery = '{$excel['wr_delivery']}',
        wr_delivery_fee = '{$excel['wr_delivery_fee']}',
        wr_email = '{$excel['wr_email']}',
        wr_servicetype = '{$excel['wr_servicetype']}',
        wr_packaging = '{$excel['wr_packaging']}',
        wr_country_code = '{$excel['wr_country_code']}',
        wr_name2 = '{$excel['wr_name2']}',
        wr_etc = '{$excel['wr_etc']}',
        wr_date2 = '{$excel['wr_date2']}',
        wr_date3 = '{$excel['wr_date3']}',
        wr_date4 = '{$_POST['wr_date4']}',
        wr_order_num2 = '{$excel['wr_order_num2']}',
        wr_orderer = '{$excel['wr_orderer']}',
        wr_order_ea = '{$excel['wr_order_ea']}',
        wr_order_price = '{$excel['wr_order_price']}',
        wr_order_total = '{$excel['wr_order_total']}',
        wr_order_traking = '{$excel['wr_order_traking']}',
        wr_order_etc = '{$excel['wr_order_etc']}',
        wr_warehouse = '{$excel['wr_warehouse']}',
        wr_rack = '{$excel['wr_rack']}',
        wr_warehouse_etc = '{$excel['wr_warehouse_etc']}',
        wr_direct_use = '{$excel['wr_direct_use']}',
        wr_product_id = '{$item['wr_id']}',
        wr_datetime = '".G5_TIME_YMDHIS."'
        
        
        
        
        ";
        sql_query($sql, true);
        
        
        sql_query("update g5_sales2_list set wr_chk = 1 where seq = '{$excel['seq']}'");
        
        /*231117 출고등록에서 출고완료처리시 재고 차감
        if($excel['wr_warehouse'] != '9000') {
            
            if($excel['wr_warehouse'] == '1000')
                $filed = 'wr_32';
            else if($excel['wr_warehouse'] == '3000')
                $filed = 'wr_36';
            
            sql_query("update g5_write_product set {$filed} = {$filed} - {$excel['wr_ea']} where (wr_1 = '{$excel['wr_code']}' or wr_27 = '{$excel['wr_code']}' or wr_28 = '{$excel['wr_code']}' or wr_29 = '{$excel['wr_code']}' or wr_30 = '{$excel['wr_code']}' or wr_31 = '{$excel['wr_code']}') limit 1");
        }*/
    
    // 선택삭제 = 이전 과정까지 전부 삭제    
    }else if($btn_name=="선택삭제"){
        sql_query("DELETE FROM g5_write_sales where wr_subject = '{$_POST['del_order_num'][$_POST['chk_seq'][$i]]}' ");    //매출자료 관리
        sql_query("DELETE FROM g5_sales0_list where wr_order_num = '{$_POST['del_order_num'][$_POST['chk_seq'][$i]]}' ");  //매출 관리
        sql_query("DELETE FROM g5_sales1_list where wr_order_num = '{$_POST['del_order_num'][$_POST['chk_seq'][$i]]}' ");  //발주 관리
        sql_query("DELETE FROM g5_sales2_list where wr_order_num = '{$_POST['del_order_num'][$_POST['chk_seq'][$i]]}' ");  //입고 관리
        sql_query("DELETE FROM g5_sales3_list where wr_order_num = '{$_POST['del_order_num'][$_POST['chk_seq'][$i]]}' ");  //출고 관리

    }else{
        $msg = "선택한 버튼이 없습니다. 다시 확인 후 이용해주세요.";
        break;
    }


	$suc++;
}

	alert_close($msg);
?>