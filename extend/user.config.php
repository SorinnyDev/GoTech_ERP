<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

if(($bo_table=='product' || $bo_table=='sales') && $is_member){
    $is_admin ='board'; $board['bo_admin'] = $member['mb_id'];
}

?>