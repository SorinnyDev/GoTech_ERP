<?php 
include_once('./_common.php');

if(!$member)
    alert("로그인 후 이용해주세요.");

if(count($_POST['chk'])==0)
    alert("선택된 목록이 없습니다.");


$succ_cnt = 0;
$fail_cnt = 0;

$sql = " SELECT cl_idx,cl_name,cl_order FROM g5_charge2_sub ORDER BY cl_order ASC ";
$result = sql_query($sql);

$cl_arr = [];

while($row=sql_fetch_array($result)){
    array_push($cl_arr,$row);
}

for($i=0;$i<count($_POST['chk']);$i++){
    $k = $_POST['chk'][$i];
    $cl_date = $_POST['cl_date'][$k];

    for($j=0;$j<count($cl_arr);$j++){
        $cl_idx = $_POST['cl_idx'][$cl_date][$j];
        $rate = $_POST['rate'][$cl_date][$j];
        $obj_cnt = sql_fetch("SELECT count(*) AS cnt FROM g5_charge2 WHERE cl_date = '{$cl_date}' and cl_idx = '{$cl_idx}' ")['cnt'];

        if($obj_cnt > 0){
            $sql = "UPDATE g5_charge2 SET rate = '{$rate}', mb_id = '{$member['mb_id']}', up_datetime = now() WHERE cl_date = '{$cl_date}' and cl_idx = '{$cl_idx}' ";
        }else{
            $sql = "INSERT INTO g5_charge2 SET cl_idx = '{$cl_idx}', rate = '{$rate}', mb_id = '{$member['mb_id']}', cl_date = '{$cl_date}', wr_datetime = now() ";
        }

        if(sql_query($sql)){
            $succ_cnt++;
        }else{
            $fail_cnt++;
        }
    }

}


alert("수정이 완료되었습니다.\\n성공 : {$succ_cnt}건\\n실패 : {$fail_cnt}건",G5_URL."/charge/charge2_list.php?".$_POST['qstr']);

?>