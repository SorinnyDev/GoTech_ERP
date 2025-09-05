<?php
include_once('./_common.php');

if ($is_guest) alert('로그인 후 이용하세요.');

if (!$wr_id) alert('잘못된 접근입니다.');

$query = "SELECT * FROM g5_write_product as wp WHERE wr_id = {$wr_id}";

$result = sql_fetch($query);

$date = date('Y-m-d H:i:s');

$add_query = [];
$add_rack_query = [];

if ($is_force_update == 'Y') {
    if ((is_numeric($wr_32) || $wr_32) && $wr_32 !== $result['wr_32']) {
        $diff = $wr_32 - $result['wr_32'];

        $add_query[] = "wr_32 = '{$wr_32}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '1000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_32', before_value = '{$result['wr_32']}', after_value = '{$wr_32}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_36) || $wr_36) && $wr_36 !== $result['wr_36']) {
        $diff = $wr_36 - $result['wr_36'];

        $add_query[] = "wr_36 = '{$wr_36}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '3000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_36', before_value = '{$result['wr_36']}', after_value = '{$wr_36}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_42) || $wr_42) && $wr_42 !== $result['wr_42']) {
        $diff = $wr_42 - $result['wr_42'];

        $add_query[] = "wr_42 = '{$wr_42}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '4000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_42', before_value = '{$result['wr_42']}', after_value = '{$wr_42}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_43) || $wr_43) && $wr_43 !== $result['wr_43']) {
        $diff = $wr_43 - $result['wr_43'];

        $add_query[] = "wr_43 = '{$wr_43}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '5000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_43', before_value = '{$result['wr_43']}', after_value = '{$wr_43}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_44) || $wr_44) && $wr_44 !== $result['wr_44']) {
        $diff = $wr_44 - $result['wr_44'];

        $add_query[] = "wr_44 = '{$wr_44}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '6000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_44', before_value = '{$result['wr_44']}', after_value = '{$wr_44}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_40) || $wr_40) && $wr_40 !== $result['wr_40']) {
        $diff = $wr_44 - $result['wr_40'];

        $add_query[] = "wr_40 = '{$wr_40}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '7000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_40', before_value = '{$result['wr_40']}', after_value = '{$wr_40}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_41) || $wr_41) && $wr_41 !== $result['wr_41']) {
        $diff = $wr_41 - $result['wr_41'];

        $add_query[] = "wr_41 = '{$wr_41}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '8000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_41', before_value = '{$result['wr_41']}', after_value = '{$wr_41}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_45) || $wr_45) && $wr_45 !== $result['wr_45']) {
        $diff = $wr_45 - $result['wr_45'];

        $add_query[] = "wr_45 = '{$wr_45}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '11000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_45', before_value = '{$result['wr_45']}', after_value = '{$wr_44}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_46) || $wr_46) && $wr_46 !== $result['wr_46']) {
        $diff = $wr_46 - $result['wr_46'];

        $add_query[] = "wr_46 = '{$wr_46}'";

        sql_query("INSERT INTO g5_rack_stock SET wr_warehouse = '12000', wr_rack = '1', wr_stock = '$diff', wr_product_id = '$wr_id', wr_mb_id = '$mb_id', wr_datetime = '$date', wr_move_log = '관리자 강제 수정'");
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_46', before_value = '{$result['wr_46']}', after_value = '{$wr_46}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }


    if ((is_numeric($wr_37) || $wr_37) && $wr_37 !== $result['wr_37']) {
        $add_query[] = "wr_37 = '{$wr_37}'";

        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_37', before_value = '{$result['wr_37']}', after_value = '{$wr_37}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
} else {
    if ((is_numeric($wr_32) || $wr_32) && $wr_32 !== $result['wr_32_real']) {
        $add_query[] = "wr_32_real = '{$wr_32}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_32_real', before_value = '{$result['wr_32_real']}', after_value = '{$wr_32}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_36) || $wr_36) && $wr_36 !== $result['wr_36_real']) {
        $add_query[] = "wr_36_real = '{$wr_36}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_36_real', before_value = '{$result['wr_36_real']}', after_value = '{$wr_36}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_42) || $wr_42) && $wr_42 !== $result['wr_42_real']) {
        $add_query[] = "wr_42_real = '{$wr_42}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_42_real', before_value = '{$result['wr_42_real']}', after_value = '{$wr_42}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_43) || $wr_43) && $wr_43 !== $result['wr_43_real']) {
        $add_query[] = "wr_43_real = '{$wr_43}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_43_real', before_value = '{$result['wr_43_real']}', after_value = '{$wr_43}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_44) || $wr_44) && $wr_44 !== $result['wr_44_real']) {
        $add_query[] = "wr_44_real = '{$wr_44}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_44_real', before_value = '{$result['wr_44_real']}', after_value = '{$wr_44}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }
    
    if ((is_numeric($wr_37) || $wr_37) && $wr_37 !== $result['wr_37']) {
        $add_query[] = "wr_37 = '{$wr_37}'";
    
        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_37', before_value = '{$result['wr_37']}', after_value = '{$wr_37}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_40) || $wr_40) && $wr_40 !== $result['wr_40_real']) {
        $add_query[] = "wr_40_real = '{$wr_40}'";

        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_40', before_value = '{$result['wr_40']}', after_value = '{$wr_40}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_41) || $wr_41) && $wr_41 !== $result['wr_41_real']) {
        $add_query[] = "wr_41_real = '{$wr_41}'";

        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_41', before_value = '{$result['wr_41']}', after_value = '{$wr_41}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_45) || $wr_45) && $wr_45 !== $result['wr_45_real']) {
        $add_query[] = "wr_45_real = '{$wr_45}'";

        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_45', before_value = '{$result['wr_45']}', after_value = '{$wr_45}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }

    if ((is_numeric($wr_46) || $wr_46) && $wr_46 !== $result['wr_46_real']) {
        $add_query[] = "wr_46_real = '{$wr_46}'";

        sql_query("INSERT INTO g5_wr_stock_history SET wr_id = '{$wr_id}', target_warehouse = 'wr_46', before_value = '{$result['wr_46']}', after_value = '{$wr_46}', mb_id = '{$mb_id}', reg_datetime = '{$date}'");
    }


}


if (!count($add_query)) {
    echo json_encode(['success' => false,'message' => "잘못된 접근입니다."]);
    exit;
}

$add_query_str = implode(',', $add_query);

$query = "UPDATE g5_write_product SET {$add_query_str} WHERE wr_id = {$wr_id}";

sql_query($query);


echo json_encode(['success' => true, 'message' => '수정되었습니다.']);