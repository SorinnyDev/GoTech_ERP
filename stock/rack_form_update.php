<?php

include_once('./_common.php');
require_once('../warehouse/warehouse_list.php');

if ($is_guest) {
    alert('로그인 후 이용하세요.');
}

if ($mode == "add") {
    if (!$_POST['rack_name'] || !$_POST['warehouse']) {
        alert('잘못 된 접근입니다.');
    }
    $gc_name = trim($_POST['rack_name']);
    $gc_name_arr = explode(',', $gc_name);

    if (count($gc_name_arr) > 1) {
        $message = [];

        foreach ($gc_name_arr as $item) {
            $gc_name = trim($item);

            $chk = sql_fetch("select * from g5_rack where gc_warehouse = '{$warehouse}' and gc_name = '{$gc_name}'");
            if ($chk) {
                $message[] = "[{$gc_name}] 랙 이름이 이미 존재 합니다.";
                continue;
            }

            $sql = "insert into g5_rack set 
                    gc_warehouse = '{$warehouse}',
      				gc_name = '{$gc_name}',
      				gc_use = 1";

            sql_query($sql);

            if (isset($warehouseConfig[$warehouse])) {
                $currentWarehouseInfo = $warehouseConfig[$warehouse];
                $warehouseName = $currentWarehouseInfo['ware_name'];
                $message[] = "[" . $warehouseName . "]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
            } else {
                $message[] = "[알 수 없는 창고 (코드: " . $warehouse . ")]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
            }

        }
        $message_str = implode(' ', $message);
        json_response(true, $message_str);

    } else {
        $chk = sql_fetch("select * from g5_rack where gc_warehouse = '{$warehouse}' and gc_name = '{$gc_name}'");

        if ($chk) {
            json_response(false, '해당창고에 랙 이름이 이미 존재 합니다.');
        }

        $sql = "insert into g5_rack set 
    			gc_warehouse = '{$warehouse}',
    			gc_name = '{$gc_name}',
    			gc_use = 1";

        sql_query($sql);

        if ($warehouse == 1000) {
            $msg = "[한국창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 3000) {
            $msg = "[미국창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 4000) {
            $msg = "[FBA창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 5000) {
            $msg = "[W-FBA창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 6000) {
            $msg = "[U-FBA창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 7000) {
            $msg = "[한국 반품창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 8000) {
            $msg = "[미국 반품창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 11000) {
            $msg = "[한국폐기창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        } elseif ($warehouse == 12000) {
            $msg = "[미국폐기창고]에 [" . $gc_name . "] 랙이 추가 되었습니다.";
        }
        json_response(true, $msg);
    }

} elseif ($mode == "mod") {

    if (!$_POST['rack_name'] || !$_POST['seq']) {
        die('n');
    }
    $gc_name = trim($_POST['rack_name']);

    $sql = "update g5_rack set 
	gc_name = '{$gc_name}'
	where seq = '{$seq}'
	";
    sql_query($sql);

    die('y');

} elseif ($mode == "del") {
    if (empty($member) || !in_array($member['mb_id'], ['test', 'admin'])) {
        json_response(false, '삭제 권한이 없는 계정입니다.');
    }

    if (!$_POST['seq']) {
        json_response(false, '잘못된 접근입니다.');
    }

    $sql = "delete from g5_rack where seq = '{$seq}'";
    sql_query($sql);

    json_response(true, '삭제되었습니다.');
}
