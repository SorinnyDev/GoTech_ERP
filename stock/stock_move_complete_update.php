<?php
include_once('./_common.php');

// ajax 로 넘어온 값들
$seq = isset($_POST['seq']) ? (int)$_POST['seq'] : 0;
$items = isset($_POST['items']) && is_array($_POST['items']) ? $_POST['items'] : array();

if(!$seq || empty($items)) {
    echo json_encode(array("ret_code"=>false,"message"=>"필수 데이터가 누락되었습니다."));
    exit;
}

$row = sql_fetch("select * from g5_stock_move where seq = '{$seq}'");

if(!$row) {
    echo json_encode(array("ret_code"=>false,"message"=>"존재하지 않는 이관 요청입니다."));
    exit;
}


// 트랜잭션 시작
sql_query("START TRANSACTION");

$field = $storage_arr[$row['wr_in_warehouse']]['field'];
$field_real = $storage_arr[$row['wr_in_warehouse']]['field_real'];

$sql_product_update = "update g5_write_product set {$field} = {$field} + {$row['wr_stock']},{$field_real} = {$field_real} + {$row['wr_stock']} where wr_id = '{$row['product_id']}'";
$result_product_update = sql_query($sql_product_update);

$rack_array = [];

if($result_product_update) {

    $wr_move_log = $storage_arr[$row['wr_out_warehouse']]['code_nm'].">".$storage_arr[$row['wr_in_warehouse']]['code_nm']." 재고이관 완료";
    $count = 0;

    foreach ($items as $item) {

        $wr_stock = $item['wr_stock'];
        $wr_rack = $item['wr_rack'];
        $expired_date = $item['expired_date'];

        if ($wr_stock <= 0 || empty($wr_rack)) {
            sql_query("ROLLBACK");
            echo json_encode(array("ret_code"=>false,"message"=>"이관수량 또는 랙 정보가 올바르지 않습니다."));
            exit;
        }

        //랙 재고 입력
        $sql = "insert into g5_rack_stock 
                    set wr_warehouse = '{$row['wr_in_warehouse']}', 
                        wr_rack = '{$wr_rack}', 
                        wr_stock = '{$wr_stock}', 
                        wr_product_id = '{$row['product_id']}', 
                        wr_mb_id = '{$member['mb_id']}', 
                        wr_datetime = '".G5_TIME_YMDHIS."', 
                        wr_move_log = '{$wr_move_log}'";
        $result = sql_query($sql);

        if (!$result) {
            sql_query("ROLLBACK"); // 오류 발생 시 롤백
            echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[랙 재고 등록 오류]"));
            exit;
        }

        if (isset($expired_date) && $expired_date) {
            $sql_expired = "insert into g5_rack_expired 
                            set rack_id = '{$wr_rack}', 
                            product_id = '{$row['product_id']}', 
                            expired_date = '{$expired_date}'";
            $result_expired_insert = sql_query($sql_expired);

            if (!$result_expired_insert) {
                sql_query("ROLLBACK");
                echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[유통기한 등록 오류]" . " " . $sql_expired . " - ".$result_expired_insert . " - ".$count));
                exit;
            }
        }
        array_push($rack_array, $wr_rack);

        $count = $count +1;
    }


    $imple_rack = implode("/", $rack_array);

    // 3. 모든 작업이 성공하면 g5_stock_move 상태 업데이트
    $sql_move_update = "update g5_stock_move set 
                        wr_state_date = '".G5_TIME_YMDHIS."',
                        wr_state = 1,
                        wr_rack = '{$imple_rack}',
                        mb_id2 = '{$member['mb_id']}'
                        where seq = '{$row['seq']}'";

    if(sql_query($sql_move_update)) {
        sql_query("COMMIT"); // 모든 쿼리 최종 적용
        echo json_encode(array("ret_code"=>true,"message"=>"이관 처리가 완료되었습니다."));
        exit;
    } else {
        sql_query("ROLLBACK"); // 오류 발생 시 롤백
        echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[상태 업데이트 오류]"));
        exit;
    }







} else {
    sql_query("ROLLBACK"); // 오류 발생 시 롤백
    echo json_encode(array("ret_code"=>false,"message"=>"이관 처리에 실패하였습니다.[상품 재고 수정 오류]"));
    exit;
}


?>