<?php 
include_once('./_common.php');

if(!$member)
    alert("로그인 후 이용해주세요.");

if(count($_POST['chk'])==0)
    alert("선택된 목록이 없습니다.");

$succ_cnt = 0;
$fail_cnt = 0;
$code_fail_cnt = 0;

for($i=0;$i<count($_POST['chk']);$i++){
    $k = $_POST['chk'][$i];
    $mb_no = $_POST['mb_no'][$k];

    if($_POST['btn_submit']=="선택수정"){
        $mb_type = $_POST['mb_type'][$mb_no];
        $mb_id = $_POST['mb_id'][$mb_no];
        $orig_mb_id = $_POST['orig_mb_id'][$mb_no];
        $mb_name = $_POST['mb_name'][$mb_no];
        $mb_password = get_encrypt_string($_POST['mb_password'][$mb_no]);

        if($_POST['mb_password'][$mb_no]){
            $mb_pass_sql = " mb_password = '{$mb_password}', "; 
        }

        $obj_cnt = sql_fetch("SELECT count(*) AS cnt FROM g5_member WHERE mb_no = '{$mb_no}' ")['cnt'];
        $add_sql = " mb_type = '{$mb_type}', mb_id = '{$mb_id}', mb_name = '{$mb_name}', mb_nick = '{$mb_name}', {$mb_pass_sql} ";
        
        if($obj_cnt > 0){
            $sql = " UPDATE g5_member SET {$add_sql} up_datetime = now() WHERE mb_no = '{$mb_no}' ";

        }else{
            $sql = " INSERT INTO g5_member SET {$add_sql} mb_level = '10', wr_datetime = now() ";
        }

        if($orig_mb_id!=$mb_id){
            $chk_cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id = '{$mb_id}' AND del_yn = 'N' ")['cnt'];
            
            if($chk_cnt > 0){
                $code_fail_cnt++;
                continue;
            }
        }

    }else if($_POST['btn_submit']=="선택삭제"){
        $sql = " UPDATE g5_member SET del_yn = 'Y' WHERE mb_no = '{$mb_no}' ";
    }

    if(sql_query($sql)){
        $succ_cnt++;
    }else{
        $fail_cnt++;
    }

}


alert("수정이 완료되었습니다.\\n성공 : {$succ_cnt}건\\n실패 : {$fail_cnt}건\\n코드 중복으로 실패 : {$code_fail_cnt}건",G5_URL."/basic/member_list.php?".$_POST['qstr']);

?>