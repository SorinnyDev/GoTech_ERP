<?
include_once("./_common.php");

$cnt = sql_fetch(" SELECT COUNT(*) AS cnt FROM g5_member WHERE mb_id = '{$_POST['mb_id']}' AND del_yn = 'N' ")['cnt'];

if($cnt > 0){
    die('y');
}else{
    die('n');
}

?>