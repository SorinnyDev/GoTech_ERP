<?
include_once("./_common.php");

$sql = " UPDATE g5_sales3_list SET wr_release_traking = '{$_POST['wr_release_traking']}' WHERE seq = '{$_POST['seq']}' ";

if(sql_query($sql)){
    die('y');
}else{
    die('n');
}

?>