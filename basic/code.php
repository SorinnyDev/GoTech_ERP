<?php 
include_once('./_common.php');

if(!$member)
    alert("로그인 후 이용해주세요.");

if(count($_POST['chk'])==0)
    alert("선택된 목록이 없습니다.");


$succ_cnt = 0;
$fail_cnt = 0;

for($i=0;$i<count($_POST['chk']);$i++){
    $k = $_POST['chk'][$i];
    $idx = $_POST['idx'][$k];

    $code_type = $_POST['code_type'][$idx];
    $code_name = $_POST['code_name'][$idx];
    $code_value = $_POST['code_value'][$idx];
    $code_order = $_POST['code_order'][$idx];
    $code_use = $_POST['code_use'][$idx];
    $code_persent = $_POST['code_persent'][$idx];

    $obj_cnt = sql_fetch("SELECT count(*) AS cnt FROM g5_code_list WHERE code_type = '{$code_type}' AND code_value = '{$code_value}' ")['cnt'];
    $add_sql = " mb_id = '{$member['mb_id']}', code_type = '{$code_type}', code_name = '{$code_name}', code_value = '{$code_value}', code_order = '{$code_order}', ";

    switch($code_type){
        case "1" :
            $add_sql .= "";
            break;
  
        case "2" :
            $add_sql .= "";
            break;

        case "3" :
            $add_sql .= " code_percent = '{$code_persent}', ";
            break;    

    }
    
    if($obj_cnt > 0){
        $sql = "UPDATE g5_code_list SET {$add_sql} up_datetime = now() WHERE idx = '{$idx}' ";
    }else{
        $sql = "INSERT INTO g5_code_list SET {$add_sql} wr_datetime = now() ";
    }

    if(sql_query($sql)){
        $succ_cnt++;
    }else{
        $fail_cnt++;
    }

}


alert("수정이 완료되었습니다.\\n성공 : {$succ_cnt}건\\n실패 : {$fail_cnt}건",G5_URL."/charge/charge_list.php?".$_POST['qstr']);

?>