<?
include_once("./_common.php");

if(!$seq)
    die('n');

$sql = " UPDATE g5_sales3_list SET wr_release_etc = '{$wr_release_etc}' WHERE seq = '{$seq}' ";

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}


?>