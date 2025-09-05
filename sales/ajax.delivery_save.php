<?
include_once("./_common.php");

$result = false;
$obj = [];
$sql = " UPDATE g5_sales3_list SET wr_delivery = '{$_POST['wr_delivery']}', wr_delivery_fee = '{$_POST['wr_delivery_fee']}', wr_delivery_fee2 = '{$_POST['wr_delivery_fee2']}' WHERE seq = '{$_POST['seq']}' ";

$obj['result'] = false;
if(sql_query($sql)){
    $cnt = sql_fetch("SELECT COUNT(*) AS cnt FROM g5_delivery_company WHERE wr_code = '{$_POST['wr_delivery']}' ")['cnt'];
    
    if($cnt > 0){
        $row = sql_fetch(" SELECT * FROM g5_delivery_company WHERE wr_code = '{$_POST['wr_delivery']}' ");
        $obj['delivery_name'] = $row['wr_name'];
        $obj['result'] = true;
    }
}else{
    $obj['result'] = false;
}

echo json_encode($obj);
?>