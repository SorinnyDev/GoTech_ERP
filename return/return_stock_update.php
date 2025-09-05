<?php

include_once('./_common.php');
require_once('../warehouse/warehouse_list.php');

if ($is_guest) {
    alert('로그인 후 이용하세요.');
}

$stock = (int)$_POST['wr_stock'];

if (!$_POST['wr_id'] || !$_POST['wr_warehouse'] || !$_POST['wr_rack'] || !$stock) {
    alert('잘못 된 접근입니다.');
}

//이동창고 재고증감
$sql = "insert into g5_rack_stock set wr_warehouse = '{$wr_warehouse}', wr_rack = '{$wr_rack}',
      	wr_stock = '{$stock}', wr_product_id = '{$wr_id}', wr_mb_id = '{$member['mb_id']}',
		wr_datetime = '" . G5_TIME_YMDHIS . "', wr_move_log = '반품재고이동:{$wr_type}:{$return_id}' ";
sql_query($sql);

if (isset($warehouseConfig[$wr_warehouse])) {
    $warehouse_config = $warehouseConfig[$wr_warehouse];
    $filed = $warehouse_config['filed'];
    $filed_real = $warehouse_config['filed_real'];
    $ware_name = $warehouse_config['ware_name'];
    $quality = $warehouse_config['quality']; // quality 변수도 함께 설정됩니다.
} else {
    // 해당 $warehouse 값이 배열에 없을 경우의 처리 (선택 사항)
    echo "<script type='text/javascript'>";
    echo "alert('알 수 없는 창고 코드입니다: ' . $wr_warehouse . '\n');";
    echo "self.close();";
    echo "</script>";
}

// switch ($wr_warehouse) {
//     case "1000":
//         $filed = 'wr_32';
//         $filed_real = 'wr_32_real';
//         $ware_name = "한국창고";
//         break;
//     case "3000":
//         $filed = 'wr_36';
//         $filed_real = 'wr_36_real';
//         $ware_name = "미국창고";
//         break;
//     case "4000":
//         $filed = 'wr_42';
//         $filed_real = 'wr_42_real';
//         $ware_name = "FBA창고";
//         break;
//     case "5000":
//         $filed = 'wr_43';
//         $filed_real = 'wr_43_real';
//         $ware_name = "W-FBA창고";
//         break;
//     case "6000":
//         $filed = 'wr_44';
//         $filed_real = 'wr_44_real';
//         $ware_name = "U-FBA창고";
//         break;

//     case "7000":
//         $filed = 'wr_40';
//         $filed_real = 'wr_40_real';
//         $ware_name = "한국 반품창고";
//         break;
//     case "8000":
//         $filed = 'wr_41';
//         $filed_real = 'wr_41_real';
//         $ware_name = "미국 반품창고";
//         break;
//     case "9000":
//         $filed = 'wr_47';
//         $filed_real = 'wr_47_real';
//         $ware_name = "FBA 반품창고";
//         break;
//     case "9100":
//         $filed = 'wr_48';
//         $filed_real = 'wr_48_real';
//         $ware_name = "W-FBA 반품창고";
//         break;
//     case "9200":
//         $filed = 'wr_49';
//         $filed_real = 'wr_49_real';
//         $ware_name = "FBA 반품창고";
//         break;

//     case "11000":
//         $filed = 'wr_45';
//         $filed_real = 'wr_45_real';
//         $ware_name = '한국 폐기창고';
//         break;
//     case "12000":
//         $filed = 'wr_46';
//         $filed_real = 'wr_46_real';
//         $ware_name = '미국 폐기창고';
//         break;
//     case "13000":
//         $filed = 'wr_50';
//         $filed_real = 'wr_50_real';
//         $ware_name = 'FBA 폐기창고';
//         break;
//     case "13100":
//         $filed = 'wr_51';
//         $filed_real = 'wr_51_real';
//         $ware_name = 'W-FBA 폐기창고';
//         break;
//     case "13200":
//         $filed = 'wr_52';
//         $filed_real = 'wr_52_real';
//         $ware_name = 'U-FBA 폐기창고';
//         break;
// }

//상품마스터 재고 업데이트
$sql = "update g5_write_product set {$filed} = {$filed} + {$stock}, {$filed_real} = {$filed_real} + {$stock} where wr_id = '{$wr_id}'";
sql_query($sql);

//반품창고 이동 기록
$rack_name = get_rack_name($wr_rack);
$sql = "insert into g5_return_stock set return_id = '{$return_id}', 
wr_stock = '{$stock}', 
wr_rack = '{$rack_name}', 
wr_warehouse = '{$ware_name}', 
wr_datetime = '" . G5_TIME_YMDHIS . "'";
sql_query($sql);


//남은수량 체크 후 상태 업데이트
$chk = sql_fetch("select SUM(wr_stock) as qty from g5_return_list where seq = '{$return_id}'");

$chk2 = sql_fetch("select SUM(wr_stock) as qty from g5_return_stock where return_id = '{$return_id}'");

$wr_product_state = 0;
if ($wr_type === "양품") {
    $wr_product_state = '1';
} elseif ($wr_type === "리퍼") {
    $wr_product_state = '2';

    if (in_array($wr_warehouse, ['11000', '12000'])) {
        # 리퍼일 경우 폐기재고와 연동
        $sql = "insert into g5_discard_list 
        		set mb_id = '{$member['mb_id']}',
        		product_id = '{$wr_id}',
        		wr_stock = '{$stock}',
        		wr_datetime = '" . G5_TIME_YMDHIS . "',
        		wr_memo = '{$memo}'
        		";

        $result = sql_query($sql);
        $discard_id = sql_insert_id();

        $sql = "insert into g5_discard_stock 
          		set discard_id = '{$discard_id}', 
      			wr_stock = '{$stock}', 
      			wr_rack = '{$rack_name}', 
      			wr_warehouse = '{$ware_name}', 
      			wr_datetime = '" . G5_TIME_YMDHIS . "'
  				";
        sql_query($sql);
    }
}

if ($chk['qty'] == $chk2['qty']) {
    sql_query("update g5_return_list set wr_state = 2, wr_product_state = '{$wr_product_state}' where seq = '{$return_id}'");
}

alert('반품재고가 이동되었습니다.');
