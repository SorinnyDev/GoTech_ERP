<?
include_once("./_common.php");

$result = false;

switch($_POST['type']){
    case "create" :
        $sql = " INSERT INTO g5_charge_sub SET mb_id = '{$member['mb_id']}', cl_name = '{$_POST['cl_name']}', cl_order = '{$_POST['cl_order']}', wr_datetime = now()  ";
        $result = sql_query($sql);
        break;

    case "update" :
        $sql = " UPDATE g5_charge_sub SET mb_id = '{$member['mb_id']}', cl_name = '{$_POST['cl_name']}', cl_order = '{$_POST['cl_order']}', up_datetime = now() WHERE cl_idx = '{$_POST['cl_idx']}'  ";
        $result = sql_query($sql);
        break;
    case "delete" :
        $sql = " DELETE FROM g5_charge_sub WHERE cl_idx = '{$_POST['cl_idx']}' ";
        if(sql_query($sql)){
            $result = sql_query("DELETE FROM g5_charge WHERE cl_idx = '{$_POST['cl_idx']}' ");
        }
        break;

    default :     
}

echo $result;

?>