<?php
include_once('./_common.php');

if (!$is_member) die('n');
// if (!$member['mb_id']=="test") die('n');

$result = 'n';


switch($type){

    // 단일 입금
    case "one" :
        $sql = " UPDATE g5_sales2_list SET wr_misu = wr_misu - {$wr_misu} WHERE seq = '{$seq}' ";
        
        if(sql_query($sql)){
            $result = "y";
        }
        break;

    // 부분적 입금
    case "part" :
        

        break;
    
    
    // 전체 입금
    case "all" :
        

        break;    

    // 입금 취소
    case "deposit_cancle" :
        $sql = " UPDATE g5_sales2_list SET wr_misu = wr_misu + {$wr_misu} WHERE seq = '{$seq}' ";
        
        if(sql_query($sql)){
            $result = "y";
        }

        break; 
        
        
    // 예치금 충전
    /*
    case "deposit" :
        if(sql_query(" UPDATE g5_member SET mb_point = mb_point + {$point} WHERE mb_id = '{$member['mb_id']}' ")){
            sql_query(" INSERT INTO g5_pointList SET  ");
            $result = 'y';
        }else{
            $result = 'err';
        }
        break;
    */    
}

echo $result;


